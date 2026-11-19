<?php

use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentFileController;
use App\Http\Controllers\DocumentStatusController;
use App\Http\Controllers\DocumentUploadController;
use App\Http\Controllers\MicrosoftAuthController;
use App\Http\Controllers\StudentNotificationController;
use App\Http\Controllers\StudentProfileController;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\UploadSetting;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

$withStudentProfileUrls = static function ($students) {
    return $students->each(static function (User $student): void {
        $url = asset('icons/profile_placeholder.svg');
        if ($student->profile_picture_path) {
            try {
                $url = Storage::disk('s3')->temporaryUrl($student->profile_picture_path, now()->addMinutes(30));
            } catch (\Throwable) {
                // Keep the placeholder if the private storage URL cannot be generated.
            }
        }
        $student->setAttribute('profile_picture_url', $url);
    });
};

Route::get('/', function () {
    $user = auth()->user();

    if ($user) {
        return match (strtolower(trim((string) $user->account_role))) {
            'admin' => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => view('welcome')->with('error', 'Your account does not have an authorized role.'),
        };
    }

    return view('welcome');
})->name('welcome');

// Keep OAuth routes available for an existing session so users can retry or switch accounts.
Route::get('/auth/microsoft', [MicrosoftAuthController::class, 'redirect'])
    ->name('microsoft.login');
Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class, 'callback'])
    ->name('microsoft.callback');

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::prefix('student')->name('student.')
    ->middleware(['auth', 'role:student', 'student.profile'])
    ->group(function (): void {
        Route::get('/profile/complete', [StudentProfileController::class, 'show'])
            ->name('profile.complete');
        Route::post('/profile/complete', [StudentProfileController::class, 'store'])
            ->name('profile.complete.store');

        Route::get('/dashboard', function () {
            $studentDocuments = Document::where('user_id', auth()->id());
            $documentCounts = [
                'total' => (clone $studentDocuments)->count(),
                'verified' => (clone $studentDocuments)->whereRaw('LOWER(status) = ?', ['verified'])->count(),
                'pending' => (clone $studentDocuments)->whereRaw('LOWER(status) = ?', ['pending'])->count(),
                'needs_correction' => (clone $studentDocuments)
                    ->whereRaw("LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_')) = ?", ['needs_correction'])
                    ->count(),
            ];
            $documents = (clone $studentDocuments)->latest()->limit(5)->get();

            return view('student.dashboard', compact('documents', 'documentCounts'));
        })->name('dashboard');

        Route::get('/documents', function () {
            $documents = Document::where('user_id', auth()->id())->latest()->get();

            return view('student.documents', compact('documents'));
        })->name('documents');

        Route::get('/documents/{document}/preview', [DocumentFileController::class, 'preview'])
            ->name('documents.preview');
        Route::get('/documents/{document}/download', [DocumentFileController::class, 'download'])
            ->name('documents.download');
        Route::get('/documents/{document}', function (Document $document) {
            abort_unless((int) $document->user_id === (int) auth()->id(), 403);
            $document->load('analysis');

            return view('student.document-detail', compact('document'));
        })->name('documents.show');

        Route::get('/upload', function () {
            $documentTypes = DocumentType::query()->orderBy('name')->get();
            $unavailableDocumentTypes = Document::query()
                ->where('user_id', auth()->id())
                ->whereRaw("LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_')) <> ?", ['needs_correction'])
                ->distinct()
                ->pluck('document_type')
                ->all();
            $uploadSettings = UploadSetting::current();

            return view('student.upload', compact('documentTypes', 'unavailableDocumentTypes', 'uploadSettings'));
        })->name('upload');
        Route::post('/upload', [DocumentUploadController::class, 'store'])
            ->name('upload.store');

        Route::get('/notifications', [StudentNotificationController::class, 'index'])
            ->name('notifications');
        Route::post('/notifications/read-all', [StudentNotificationController::class, 'markAllRead'])
            ->name('notifications.read-all');
        Route::get('/notifications/{notificationId}', [StudentNotificationController::class, 'open'])
            ->name('notifications.open');

        Route::get('/account', [StudentProfileController::class, 'account'])->name('account');
    });

Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () use ($withStudentProfileUrls): void {
        Route::get('/dashboard', function () {
            $documents = Document::query();
            $documentCounts = [
                'total' => (clone $documents)->count(),
                'pending' => (clone $documents)->whereRaw('LOWER(status) = ?', ['pending'])->count(),
                'verified' => (clone $documents)->whereRaw('LOWER(status) = ?', ['verified'])->count(),
                'flagged' => (clone $documents)
                    ->whereRaw("LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_')) = ?", ['needs_correction'])
                    ->count(),
            ];
            $recentSubmissions = (clone $documents)->with('student')->latest()->limit(3)->get();

            return view('admin.dashboard', compact('documentCounts', 'recentSubmissions'));
        })->name('dashboard');

        Route::get('/submissions', function () {
            $submissions = Document::with('student')->latest()->get();

            return view('admin.submissions', compact('submissions'));
        })->name('submissions');

        Route::get('/verification', function () {
            $flaggedDocuments = Document::with('student')
                ->whereRaw("LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_')) = ?", ['needs_correction'])
                ->latest()
                ->get();

            return view('admin.verification', compact('flaggedDocuments'));
        })->name('verification');

        Route::get('/review', fn () => redirect()->route('admin.verification'))
            ->name('review');
        Route::get('/review/{document}', [DocumentStatusController::class, 'show'])
            ->name('review.document');
        Route::post('/documents/{document}/status', [DocumentStatusController::class, 'update'])
            ->name('documents.status.update');

        Route::get('/documents/{document}/preview', [DocumentFileController::class, 'preview'])
            ->name('documents.preview');
        Route::get('/documents/{document}/download', [DocumentFileController::class, 'download'])
            ->name('documents.download');

        Route::get('/student-documents', function () use ($withStudentProfileUrls) {
            $students = User::query()
                ->whereRaw('LOWER(account_role) = ?', ['student'])
                ->orderBy('name')
                ->get();
            $withStudentProfileUrls($students);

            return view('admin.student-documents', compact('students'));
        })->name('student-documents');

        Route::get('/student-docs/{student}', function (User $student) use ($withStudentProfileUrls) {
            abort_unless(strtolower((string) $student->account_role) === 'student', 404);
            $withStudentProfileUrls(collect([$student]));
            $documents = Document::where('user_id', $student->id)->latest()->get();

            return view('admin.student-docs', compact('student', 'documents'));
        })->name('student-docs');

        Route::get('/student-accounts', function () use ($withStudentProfileUrls) {
            $students = User::query()
                ->whereRaw('LOWER(account_role) = ?', ['student'])
                ->orderBy('name')
                ->get();
            $withStudentProfileUrls($students);

            return view('admin.student-accounts', compact('students'));
        })->name('student-accounts');

        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::post('/settings/document-types', [AdminSettingsController::class, 'storeDocumentType'])
            ->name('settings.document-types.store');
        Route::put('/settings/document-types/{documentType}', [AdminSettingsController::class, 'updateDocumentType'])
            ->name('settings.document-types.update');
        Route::delete('/settings/document-types/{documentType}', [AdminSettingsController::class, 'deleteDocumentType'])
            ->name('settings.document-types.delete');
        Route::put('/settings/upload-rules', [AdminSettingsController::class, 'saveFixedUploadRules'])
            ->name('settings.upload-rules.update');
    });
