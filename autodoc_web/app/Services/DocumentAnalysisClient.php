<?php

namespace App\Services;

use App\Models\DocumentAnalysis;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Throwable;

class DocumentAnalysisClient
{
    public function __construct(private DocumentAnalysisCompletion $completion) {}

    /** Submit quickly; Gemini runs asynchronously in the OCR API. */
    public function submit(UploadedFile $file, User $student, string $documentType, int $analysisId): array
    {
        $stream = null;

        try {
            [$url, $token] = $this->connectionSettings();
            $sourcePath = $file->getRealPath();
            if (! is_string($sourcePath) || ! is_file($sourcePath)) {
                throw new \RuntimeException('Could not open the uploaded file for document analysis.');
            }

            $stream = fopen($sourcePath, 'rb');
            if ($stream === false) {
                throw new \RuntimeException('Could not open the uploaded file for document analysis.');
            }

            $response = Http::withToken($token)
                ->connectTimeout(5)
                ->timeout(30)
                ->attach(
                    'file',
                    $stream,
                    $file->getClientOriginalName(),
                    ['Content-Type' => $file->getMimeType() ?: 'application/octet-stream'],
                )
                ->post($this->jobsUrl($url), [
                    ...$this->requestFields($student, $documentType),
                    'clientReference' => (string) $analysisId,
                ]);

            $response->throw();
            $job = $response->json();
            if (! is_array($job) || empty($job['jobId'])) {
                throw new \RuntimeException('The OCR API did not return a processing job ID.');
            }

            return [
                'status' => 'processing',
                'raw_text' => null,
                'result' => [
                    'job_id' => $job['jobId'],
                    'poll_url' => $job['pollUrl'] ?? null,
                ],
            ];
        } catch (Throwable $exception) {
            report($exception);

            return [
                'status' => 'failed',
                'raw_text' => null,
                'result' => [
                    'error' => $exception->getMessage(),
                    'manual_review_required' => true,
                ],
            ];
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    /** Refresh one API job when an administrator opens its review page. */
    public function refreshPending(DocumentAnalysis $analysis): void
    {
        $jobId = data_get($analysis->result, 'job_id');
        if ($analysis->status !== 'processing' || ! is_string($jobId) || $jobId === '') {
            return;
        }

        try {
            [$url, $token] = $this->connectionSettings();
            $response = Http::withToken($token)
                ->connectTimeout(3)
                ->timeout(8)
                ->get($this->jobStatusUrl($url, $jobId));

            if ($response->status() === 404) {
                $this->completion->fail(
                    $analysis,
                    'The OCR service no longer has this job. Review the original file manually or upload it again.',
                );

                return;
            }

            $response->throw();
            $job = $response->json();
            if (! is_array($job)) {
                return;
            }

            if (($job['status'] ?? null) === 'completed' && is_array($job['result'] ?? null)) {
                $this->completion->complete($analysis, $job['result']);
            } elseif (($job['status'] ?? null) === 'failed') {
                $this->completion->fail($analysis, $job['error'] ?? 'Document analysis failed.');
            }
        } catch (Throwable $exception) {
            // Keep the job as processing if a short status check is temporarily unavailable.
            report($exception);
        }
    }

    /** @return array{0: string, 1: string} */
    private function connectionSettings(): array
    {
        $url = trim((string) config('document_analysis.url'));
        $token = trim((string) config('document_analysis.token'));

        if ($url === '' || $token === '') {
            throw new \RuntimeException('Set OCR_API_URL and OCR_API_TOKEN in the Laravel .env file.');
        }

        return [$url, $token];
    }

    private function jobsUrl(string $processUrl): string
    {
        return preg_replace('~/process/?$~', '/jobs', rtrim($processUrl, '/')) ?: $processUrl;
    }

    private function jobStatusUrl(string $processUrl, string $jobId): string
    {
        $jobsUrl = $this->jobsUrl($processUrl);

        return rtrim(substr($jobsUrl, 0, -strlen('/jobs')), '/').'/jobs/'.rawurlencode($jobId);
    }

    private function requestFields(User $student, string $documentType): array
    {
        $name = trim(implode(' ', array_filter([
            trim((string) $student->first_name),
            trim((string) $student->last_name),
        ])));
        $fullName = $name !== '' ? $name : trim((string) $student->name);
        $normalizedType = mb_strtolower(trim($documentType));
        $isTranscriptOfRecords = $normalizedType === 'tor'
            || str_contains($normalizedType, 'transcript of records');
        $isAdvisementSlip = str_contains($normalizedType, 'advisement slip');
        $isStudentProfileForm = str_contains($normalizedType, 'student profile form');
        $doesNotUseStudentNumber = in_array($normalizedType, ['honorable dismissal', 'certificate of good moral'], true);
        $requiredFields = match (true) {
            $isAdvisementSlip => [
                'document title',
                'student full name',
                'student number',
                'course code',
                'descriptive title',
                'unit',
                'dean signature',
                'college registrar signature',
            ],
            $isStudentProfileForm => [
                'document title',
                'student full name',
                'student number',
                'all required labeled fields',
                'student signature',
            ],
            $doesNotUseStudentNumber => ['document title', 'student full name'],
            default => ['document title', 'student full name', 'student number'],
        };

        $verificationRules = [
            "Document type must be {$documentType}; verify that the document title supports this classification.",
            "Compare the printed or handwritten student name with {$fullName}. Accept a middle initial in place of a middle name; do not treat an absent middle name as a mismatch.",
        ];

        if ($isTranscriptOfRecords) {
            $verificationRules[] = 'Verify that the transcript has a visible, non-empty student number from the previous school. Check only that the field is filled; do not compare it with the student\'s current Mabalacat City College number.';
        } elseif ($isAdvisementSlip || $isStudentProfileForm) {
            if (trim((string) $student->student_number) !== '') {
                $verificationRules[] = 'Verify that the student number field is filled and matches the student\'s current Mabalacat City College student number '.$student->student_number.'.';
            } else {
                $verificationRules[] = 'Verify that the student number field is visible and filled in.';
            }
        } elseif (! $doesNotUseStudentNumber && trim((string) $student->student_number) !== '') {
            $verificationRules[] = 'Check for student number '.$student->student_number.' where the document normally includes it.';
        }

        if ($isAdvisementSlip) {
            $verificationRules[] = 'Verify that the course code field is visible and filled in.';
            $verificationRules[] = 'Verify that the descriptive title field is visible and filled in.';
            $verificationRules[] = 'Verify that the unit field is visible and filled in.';
            $verificationRules[] = 'Verify that a handwritten or electronic signature is visible in the Dean signature area; do not infer a signature from a printed name alone.';
            $verificationRules[] = 'Verify that a handwritten or electronic signature is visible in the College Registrar signature area; do not infer a signature from a printed name alone.';
        }

        if ($isStudentProfileForm) {
            $verificationRules[] = 'Check every required labeled field and applicable entry on the student profile form. Report each blank or missing required value; do not flag fields clearly marked optional or not applicable.';
            $verificationRules[] = 'Verify that a handwritten or electronic signature is visible in the student signature area; do not infer a signature from a printed name alone.';
        }

        return [
            'mode' => 'extract_and_verify',
            'language' => 'English',
            'includeSignatures' => 'true',
            // JSON strings are parsed by the Node API and avoid multipart array key ambiguity.
            'customFields' => json_encode($requiredFields, JSON_THROW_ON_ERROR),
            'verificationRules' => json_encode($verificationRules, JSON_THROW_ON_ERROR),
        ];
    }
}
