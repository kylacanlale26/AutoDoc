<?php

use App\Http\Controllers\DocumentAnalysisCallbackController;
use App\Http\Controllers\DocumentUploadController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\MobileStudentController;
use Illuminate\Support\Facades\Route;

Route::post('/document-analysis/callback', DocumentAnalysisCallbackController::class)
    ->name('api.document-analysis.callback');

Route::prefix('mobile')->name('mobile.')->group(function (): void {
    Route::get('/auth/config', [MobileAuthController::class, 'config'])->name('auth.config');
    Route::post('/auth/microsoft', [MobileAuthController::class, 'microsoft'])->middleware('throttle:10,1')->name('auth.microsoft');

    Route::middleware('mobile.token')->group(function (): void {
        Route::post('/auth/logout', [MobileAuthController::class, 'logout'])->name('auth.logout');
        Route::get('/me', [MobileStudentController::class, 'me'])->name('me');
        Route::post('/profile', [MobileStudentController::class, 'completeProfile'])->name('profile.complete');
        Route::get('/dashboard', [MobileStudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/documents', [MobileStudentController::class, 'documents'])->name('documents');
        Route::get('/documents/{document}', [MobileStudentController::class, 'showDocument'])->name('documents.show');
        Route::post('/documents', [DocumentUploadController::class, 'store'])->name('documents.upload');
        Route::get('/documents/{document}/file', [MobileStudentController::class, 'file'])->name('documents.file');
        Route::get('/document-types', [MobileStudentController::class, 'documentTypes'])->name('document-types');
        Route::get('/notifications', [MobileStudentController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/read-all', [MobileStudentController::class, 'markNotificationsRead'])->name('notifications.read-all');
        Route::post('/notifications/{notificationId}/read', [MobileStudentController::class, 'markNotificationRead'])->name('notifications.read');
    });
});
