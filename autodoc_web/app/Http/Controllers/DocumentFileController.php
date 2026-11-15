<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DocumentFileController extends Controller
{
    public function preview(Document $document): RedirectResponse
    {
        return redirect()->away($this->signedFileUrl($document, 'inline'));
    }

    public function download(Document $document): RedirectResponse
    {
        return redirect()->away($this->signedFileUrl($document, 'attachment'));
    }

    private function signedFileUrl(Document $document, string $disposition): string
    {
        $user = auth()->user();
        $canView = $user && (
            strtolower((string) $user->account_role) === 'admin'
            || (
                strtolower((string) $user->account_role) === 'student'
                && (int) $document->user_id === (int) $user->id
            )
        );

        abort_unless($canView, 403);

        $filename = str_replace(["\r", "\n", '"', chr(92)], '_', $document->document_name);

        return Storage::disk('s3')->temporaryUrl(
            $document->storage_path,
            now()->addMinutes(5),
            [
                'ResponseContentType' => $document->mime_type ?: 'application/pdf',
                'ResponseContentDisposition' => $disposition . '; filename="' . $filename . '"',
            ]
        );
    }
}
