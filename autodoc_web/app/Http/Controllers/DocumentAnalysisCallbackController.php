<?php

namespace App\Http\Controllers;

use App\Models\DocumentAnalysis;
use App\Services\DocumentAnalysisCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentAnalysisCallbackController extends Controller
{
    public function __invoke(Request $request, DocumentAnalysisCompletion $completion): JsonResponse
    {
        $expectedToken = (string) config('document_analysis.token');
        $providedToken = (string) $request->bearerToken();

        abort_unless(
            $expectedToken !== ''
                && strlen($expectedToken) === strlen($providedToken)
                && hash_equals($expectedToken, $providedToken),
            401,
        );

        $validated = $request->validate([
            'clientReference' => ['required', 'integer', 'min:1'],
            'jobId' => ['required', 'string', 'max:120'],
            'status' => ['required', 'in:completed,failed'],
            'result' => ['required_if:status,completed', 'array'],
            'error' => ['nullable', 'string', 'max:2000'],
        ]);

        $analysis = DocumentAnalysis::find($validated['clientReference']);
        if (! $analysis) {
            return response()->json(['accepted' => false, 'message' => 'Analysis record not found.'], 404);
        }

        $storedJobId = data_get($analysis->result, 'job_id');
        if (is_string($storedJobId) && $storedJobId !== '' && ! hash_equals($storedJobId, $validated['jobId'])) {
            return response()->json(['accepted' => false, 'message' => 'Job ID mismatch.'], 409);
        }

        // Ignore duplicate/late callbacks after the record has reached a terminal state.
        if (! in_array($analysis->status, ['pending', 'processing'], true)) {
            return response()->json(['accepted' => true, 'duplicate' => true]);
        }

        if ($validated['status'] === 'completed') {
            $completion->complete($analysis, $validated['result']);
        } else {
            $completion->fail($analysis, $validated['error'] ?? 'Document analysis failed.');
        }

        return response()->json(['accepted' => true]);
    }
}
