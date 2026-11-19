<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Notifications\DocumentStatusUpdated;
use App\Services\DocumentAnalysisClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentStatusController extends Controller
{
    public function show(Document $document, DocumentAnalysisClient $analysisClient): View
    {
        $this->ensureAdmin();

        $document->load(['student', 'analysis']);
        if ($document->analysis) {
            $analysisClient->refreshPending($document->analysis);
        }

        return view('admin.review', compact('document'));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'status' => ['required', 'in:verified,needs_correction'],
            'review_notes' => ['nullable', 'string', 'max:5000', 'required_if:status,needs_correction'],
        ], [
            'review_notes.required_if' => 'Please explain what needs to be corrected.',
        ]);

        DB::transaction(function () use ($document, $request, $validated): void {
            $document->status = $validated['status'];
            $document->review_notes = $validated['review_notes'] ?? null;
            $document->reviewed_by = $request->user()->id;
            $document->reviewed_at = now();
            $document->save();

            $document->student->notify(new DocumentStatusUpdated($document));
        });

        return redirect()
            ->route('admin.review.document', $document)
            ->with('success', 'The document status was updated and the student was notified.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(strtolower((string) auth()->user()->account_role) === 'admin', 403);
    }
}
