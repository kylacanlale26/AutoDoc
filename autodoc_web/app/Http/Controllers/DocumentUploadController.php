<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\UploadSetting;
use App\Services\DocumentAnalysisClient;
use App\Services\DocumentAnalysisCompletion;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class DocumentUploadController extends Controller
{
    public function store(
        Request $request,
        DocumentAnalysisClient $analysisClient,
        DocumentAnalysisCompletion $analysisCompletion,
    ): Response
    {
        abort_unless($request->user()->account_role === 'student', 403);

        $uploadSettings = UploadSetting::current();
        $allowedFormats = ['pdf', 'png', 'jpg', 'jpeg'];
        $maxFileSizeKilobytes = $uploadSettings->max_file_size_mb * 1024;

        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:120', 'exists:document_types,name'],
            'documents' => ['required', 'array', 'min:1', 'max:1'],
            'documents.*' => [
                'required',
                'file',
                'mimes:' . implode(',', $allowedFormats),
                'max:' . $maxFileSizeKilobytes,
            ],
        ]);

        $user = $request->user();
        $storedPaths = [];
        $createdDocuments = [];

        try {
            DB::transaction(function () use ($request, $validated, $user, &$storedPaths, &$createdDocuments): void {
                // Serialize uploads by this student so concurrent requests cannot create two active files.
                DB::table('users')->where('id', $user->id)->lockForUpdate()->first();

                $hasActiveSubmission = Document::query()
                    ->where('user_id', $user->id)
                    ->where('document_type', $validated['document_type'])
                    ->whereRaw("LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_')) <> ?", ['needs_correction'])
                    ->exists();

                if ($hasActiveSubmission) {
                    throw ValidationException::withMessages([
                        'document_type' => 'You already submitted this document type. You can upload another file after it needs correction.',
                    ]);
                }

                foreach ($request->file('documents') as $file) {
                    $path = $file->store("students/{$user->id}", 's3');

                    if (! $path) {
                        throw new RuntimeException('The document could not be saved to storage.');
                    }

                    $storedPaths[] = $path;

                    $document = Document::create([
                        'user_id' => $user->id,
                        'document_type' => $validated['document_type'],
                        'document_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                        'storage_path' => $path,
                        'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                        'file_size_bytes' => $file->getSize(),
                        'status' => 'pending',
                    ]);

                    $document->analysis()->create(['status' => 'pending']);
                    $createdDocuments[] = [$document, $file];
                }
            });
        } catch (\Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('s3')->delete($storedPaths);
            }

            throw $exception;
        }

        foreach ($createdDocuments as [$document, $file]) {
            $analysisRecord = $document->analysis()->firstOrFail();
            $submission = $analysisClient->submit(
                $file,
                $user,
                $validated['document_type'],
                $analysisRecord->id,
            );

            // A very fast OCR job may callback before the submit request returns.
            $analysisRecord->refresh();
            if ($analysisRecord->status === 'pending') {
                if ($submission['status'] === 'failed') {
                    $analysisCompletion->fail(
                        $analysisRecord,
                        (string) data_get($submission['result'], 'error', 'Document analysis could not start.'),
                    );
                } else {
                    $analysisRecord->update([
                        'status' => 'processing',
                        'raw_text' => null,
                        'result' => $submission['result'],
                        'processed_at' => null,
                    ]);
                }
            }
        }

        $message = 'Successfully uploaded, wait for your status';
        if ($request->expectsJson()) {
            $uploadedDocuments = collect($createdDocuments)->map(function (array $created): array {
                [$document] = $created;
                $document->refresh();

                return [
                    'id' => $document->id,
                    'document_name' => $document->document_name,
                    'document_type' => $document->document_type,
                    'status' => $document->status,
                ];
            })->values();

            return response()->json([
                'message' => $message,
                'documents' => $uploadedDocuments,
            ], 201);
        }

        return redirect()->route('student.upload')->with('success', $message);
    }
}
