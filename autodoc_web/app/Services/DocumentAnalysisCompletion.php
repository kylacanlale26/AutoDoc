<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentAnalysis;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Support\Facades\DB;

class DocumentAnalysisCompletion
{
    /** Save a completed OCR result and apply its mapped AutoDoc status. */
    public function complete(DocumentAnalysis $analysis, array $result): void
    {
        DB::transaction(function () use ($analysis, $result): void {
            $lockedAnalysis = DocumentAnalysis::query()->lockForUpdate()->find($analysis->id);
            if (! $lockedAnalysis || ! in_array($lockedAnalysis->status, ['pending', 'processing'], true)) {
                return;
            }

            $verdict = data_get($result, 'verification.overallStatus');
            $lockedAnalysis->status = in_array($verdict, ['verified', 'needs_review', 'flagged'], true)
                ? $verdict
                : 'completed';
            $lockedAnalysis->raw_text = data_get($result, 'extraction.rawText');
            $lockedAnalysis->result = $result;
            $lockedAnalysis->processed_at = now();
            $lockedAnalysis->save();

            $newDocumentStatus = match ($verdict) {
                'verified' => 'verified',
                'needs_review', 'flagged' => 'needs_correction',
                default => null,
            };

            if ($newDocumentStatus) {
                $this->updatePendingDocument(
                    $lockedAnalysis,
                    $newDocumentStatus,
                    $newDocumentStatus === 'needs_correction' ? $this->analysisReason($result) : null,
                );
            }
        });
    }

    /** Save an OCR failure and mark the document for correction as requested. */
    public function fail(DocumentAnalysis $analysis, string $message): void
    {
        DB::transaction(function () use ($analysis, $message): void {
            $lockedAnalysis = DocumentAnalysis::query()->lockForUpdate()->find($analysis->id);
            if (! $lockedAnalysis || ! in_array($lockedAnalysis->status, ['pending', 'processing'], true)) {
                return;
            }

            $lockedAnalysis->status = 'failed';
            $lockedAnalysis->result = [
                'error' => $message,
                'manual_review_required' => true,
            ];
            $lockedAnalysis->processed_at = now();
            $lockedAnalysis->save();

            $this->updatePendingDocument(
                $lockedAnalysis,
                'needs_correction',
                'Automated analysis failed: '.$message,
            );
        });
    }

    private function updatePendingDocument(DocumentAnalysis $analysis, string $status, ?string $reason): void
    {
        $document = $analysis->document()->lockForUpdate()->first();
        if (! $document || $document->status !== 'pending') {
            return;
        }

        $document->status = $status;
        $document->reviewed_by = null;
        $document->reviewed_at = null;
        $document->review_notes = $reason !== null ? mb_substr($reason, 0, 5000) : null;
        $document->save();

        $document->student?->notify(new DocumentStatusUpdated($document));
    }

    private function analysisReason(array $result): string
    {
        $summary = trim((string) data_get($result, 'verification.verdictSummary'));
        $flags = data_get($result, 'verification.anomalyFlags', []);
        $flagText = is_array($flags) ? implode('; ', array_filter($flags, 'is_string')) : '';

        return trim(implode(' ', array_filter([
            'Automated analysis requested correction; administrator review is still available.',
            $summary,
            $flagText,
        ])));
    }
}
