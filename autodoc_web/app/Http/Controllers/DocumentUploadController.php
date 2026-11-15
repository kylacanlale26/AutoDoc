<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\UploadSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DocumentUploadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->account_role === 'student', 403);

        $uploadSettings = UploadSetting::current();
        $allowedFormats = $uploadSettings->accepted_formats ?: ['pdf', 'png', 'jpg', 'jpeg'];
        $maxFileSizeKilobytes = $uploadSettings->max_file_size_mb * 1024;

        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:120', 'exists:document_types,name'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*' => [
                'required',
                'file',
                'mimes:' . implode(',', $allowedFormats),
                'max:' . $maxFileSizeKilobytes,
            ],
        ]);

        $user = $request->user();
        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $validated, $user, &$storedPaths): void {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store("students/{$user->id}", 's3');

                    if (! $path) {
                        throw new RuntimeException('The document could not be saved to storage.');
                    }

                    $storedPaths[] = $path;

                    Document::create([
                        'user_id' => $user->id,
                        'document_type' => $validated['document_type'],
                        'document_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                        'storage_path' => $path,
                        'mime_type' => $file->getMimeType() ?: 'application/pdf',
                        'file_size_bytes' => $file->getSize(),
                        'status' => 'pending',
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('s3')->delete($storedPaths);
            }

            throw $exception;
        }

        return redirect()
            ->route('student.upload')
            ->with('success', 'Your documents were uploaded successfully.');
    }
}
