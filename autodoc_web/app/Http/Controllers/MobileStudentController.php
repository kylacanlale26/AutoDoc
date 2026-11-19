<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\UploadSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class MobileStudentController extends Controller
{
    private const COURSES = [
        'Bachelor of Arts in History (ABH)',
        'Bachelor of Science in Biology (BSBIO)',
        'Bachelor of Early Childhood Education (BECED)',
        'Bachelor of Elementary Education (BEED)',
        'Bachelor of Physical Education (BPED)',
        'Bachelor of Secondary Education, Major in English (BSED-ENG)',
        'Bachelor of Secondary Education, Major in Filipino (BSED-FIL)',
        'Bachelor of Secondary Education, Major in Math (BSED-MATH)',
        'Bachelor of Secondary Education, Major in Science (BSED-SCI)',
        'Bachelor of Secondary Education, Major in Social Studies (BSED-SS)',
        'Bachelor of Technical-Vocational Teacher Education (BTVTE)',
        'Bachelor of Science in Customs Administration (BSCA)',
        'Bachelor of Science in Information Technology (BSIT)',
        'Bachelor of Science in Hospitality Management (BSHM)',
        'Bachelor of Science in Tourism Management (BSTM)',
    ];

    public function dashboard(Request $request): JsonResponse
    {
        $student = $request->user();
        $query = Document::query()->where('user_id', $student->id);

        return response()->json([
            'student' => [
                'name' => $student->name,
                'first_name' => $student->first_name,
                'student_number' => $student->student_number,
                'last_name' => $student->last_name,
                'profile_completed_at' => $student->profile_completed_at?->toIso8601String(),
                'profile_picture_url' => $this->profilePictureUrl($student),
            ],
            'counts' => [
                'total' => (clone $query)->count(),
                'verified' => (clone $query)->whereRaw('LOWER(status) = ?', ['verified'])->count(),
                'pending' => (clone $query)->whereRaw('LOWER(status) = ?', ['pending'])->count(),
                'needs_correction' => (clone $query)
                    ->whereRaw("LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_')) = ?", ['needs_correction'])
                    ->count(),
            ],
            'recent_documents' => (clone $query)->latest()->limit(5)->get()->map(fn (Document $document): array => $this->documentPayload($document)),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'student_number' => $user->student_number,
            'email' => $user->email,
            'course' => $user->course,
            'year_level' => $user->year_level,
            'section' => $user->section,
            'profile_completed_at' => $user->profile_completed_at?->toIso8601String(),
            'profile_picture_url' => $this->profilePictureUrl($user),
        ]);
    }

    public function completeProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless(strtolower((string) $user->account_role) === 'student', 403);

        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:50', Rule::unique('users', 'student_number')->ignore($user->id)],
            'course' => ['required', 'string', Rule::in(self::COURSES)],
            'year_level' => ['required', 'string', Rule::in(['1', '2', '3', '4'])],
            'section' => ['required', 'string', Rule::in(['A', 'B', 'C'])],
            'profile_picture' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'terms_accepted' => ['accepted'],
        ], [
            'terms_accepted.accepted' => 'You must confirm that you are a student and accept these terms to continue.',
        ]);

        try {
            $path = $request->file('profile_picture')->store("students/{$user->id}/profile", 's3');
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'The profile picture could not be uploaded. Check storage settings and try again.'], 500);
        }

        if (! $path) {
            return response()->json(['message' => 'The profile picture could not be saved. Please try again.'], 500);
        }

        $user->student_number = trim($validated['student_number']);
        $user->course = $validated['course'];
        $user->year_level = $validated['year_level'];
        $user->section = $validated['section'];
        $user->profile_picture_path = $path;
        $user->terms_accepted_at = now();
        $user->profile_completed_at = now();
        $user->save();

        return response()->json(['message' => 'Student profile completed.', 'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'student_number' => $user->student_number,
            'email' => $user->email,
            'course' => $user->course,
            'year_level' => $user->year_level,
            'section' => $user->section,
            'profile_completed_at' => $user->profile_completed_at?->toIso8601String(),
            'profile_picture_url' => $this->profilePictureUrl($user),
        ]]);
    }

    public function documents(Request $request): JsonResponse
    {
        $documents = Document::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(200)
            ->get()
            ->map(fn (Document $document): array => $this->documentPayload($document));

        return response()->json(['documents' => $documents]);
    }

    public function file(Document $document, Request $request): JsonResponse
    {
        abort_unless((string) $document->user_id === (string) $request->user()->id, 404);

        $disposition = $request->query('disposition') === 'attachment' ? 'attachment' : 'inline';
        $filename = str_replace(["\r", "\n", '"', chr(92)], '_', $document->document_name);
        $url = Storage::disk('s3')->temporaryUrl(
            $document->storage_path,
            now()->addMinutes(5),
            [
                'ResponseContentType' => $document->mime_type ?: 'application/pdf',
                'ResponseContentDisposition' => $disposition.'; filename="'.$filename.'"',
            ],
        );

        return response()->json(['url' => $url, 'expires_in' => 300]);
    }

    public function showDocument(Document $document, Request $request): JsonResponse
    {
        abort_unless((string) $document->user_id === (string) $request->user()->id, 404);
        $document->loadMissing('analysis');
        $analysisResult = $document->analysis?->result ?? [];
        $analysisNote = data_get($analysisResult, 'verification.verdictSummary')
            ?: data_get($analysisResult, 'error')
            ?: ($document->reviewed_by ? null : $document->review_notes);

        return response()->json(['document' => [
            ...$this->documentPayload($document),
            'admin_recommendation' => $document->reviewed_by ? $document->review_notes : null,
            'analysis_note' => $analysisNote,
            'notification_message' => null,
            'preview_url' => route('mobile.documents.file', ['document' => $document, 'disposition' => 'inline']),
        ]]);
    }

    public function documentTypes(Request $request): JsonResponse
    {
        $settings = UploadSetting::current();
        $unavailableDocumentTypes = Document::query()
            ->where('user_id', $request->user()->id)
            ->whereRaw("LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_')) <> ?", ['needs_correction'])
            ->distinct()
            ->pluck('document_type');

        return response()->json([
            'document_types' => DocumentType::query()->orderBy('name')->pluck('name'),
            'unavailable_document_types' => $unavailableDocumentTypes,
            'upload_rules' => [
                'max_file_size_mb' => $settings->max_file_size_mb,
                'accepted_formats' => $settings->accepted_formats,
            ],
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $items = $request->user()->notifications()->latest()->limit(100)->get()->map(fn ($notification): array => [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => 'Document status update',
            'message' => data_get($notification->data, 'message', 'Your document has an update.'),
            'document_id' => data_get($notification->data, 'document_id'),
            'document_type' => data_get($notification->data, 'document_type'),
            'document_name' => data_get($notification->data, 'document_name'),
            'status' => data_get($notification->data, 'status'),
            'review_notes' => data_get($notification->data, 'review_notes'),
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
        ]);

        return response()->json([
            'notifications' => $items,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markNotificationsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['message' => 'Notifications marked as read.']);
    }

    public function markNotificationRead(string $notificationId, Request $request): JsonResponse
    {
        $notification = $request->user()->notifications()->whereKey($notificationId)->firstOrFail();
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read.']);
    }

    private function profilePictureUrl($user): string
    {
        if (! $user->profile_picture_path) {
            return asset('icons/profile_placeholder.svg');
        }

        try {
            return Storage::disk('s3')->temporaryUrl($user->profile_picture_path, now()->addMinutes(30));
        } catch (Throwable $exception) {
            report($exception);
            return asset('icons/profile_placeholder.svg');
        }
    }

    private function documentPayload(Document $document): array
    {
        return [
            'id' => $document->id,
            'document_type' => $document->document_type,
            'document_name' => $document->document_name,
            'mime_type' => $document->mime_type,
            'file_size_bytes' => $document->file_size_bytes,
            'status' => $document->status,
            'review_notes' => $document->review_notes,
            'created_at' => $document->created_at?->toIso8601String(),
        ];
    }
}
