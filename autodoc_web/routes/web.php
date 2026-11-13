
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MicrosoftAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {

    Route::get('/auth/microsoft',
        [MicrosoftAuthController::class, 'redirect']
    )->name('microsoft.login');

    Route::get('/auth/microsoft/callback',
        [MicrosoftAuthController::class, 'callback']
    )->name('microsoft.callback');

});

Route::middleware('auth')->group(function () {

    Route::post('/logout',
        [AuthController::class, 'logout']
    )->name('logout');

    Route::prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', function () {
            return view('student.dashboard');
        })->name('dashboard');

        Route::get('/documents', function () {
            return view('student.documents');
        })->name('documents');

        Route::get('/upload', function () {
            return view('student.upload');
        })->name('upload');

        Route::get('/notifications', function () {
            return view('student.notifications');
        })->name('notifications');

        Route::get('/account', function () {
            return view('student.account');
        })->name('account');

    });

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/submissions', function () {
            return view('admin.submissions');
        })->name('submissions');

        Route::get('/verification', function () {
            return view('admin.verification');
        })->name('verification');

        Route::get('/review', function () {
            return view('admin.review');
        })->name('review');

        Route::get('/student-documents', function () {
            return view('admin.student-documents');
        })->name('student-documents');

        Route::get('/student-docs', function () {
            return view('admin.student-docs');
        })->name('student-docs');

        Route::get('/student-accounts', function () {
            return view('admin.student-accounts');
        })->name('student-accounts');

        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');

    });

});