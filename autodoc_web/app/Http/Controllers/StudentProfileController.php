<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class StudentProfileController extends Controller
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

    public function show(): View
    {
        return view('student.complete-profile');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => [
                'required',
                'string',
                'max:50',
                'unique:users,student_number,' . $request->user()->id,
            ],
            'course' => ['required', 'string', Rule::in(self::COURSES)],
            'year_level' => ['required', 'string', Rule::in(['1', '2', '3', '4'])],
            'section' => ['required', 'string', Rule::in(['A', 'B', 'C'])],
            'profile_picture' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'terms_accepted' => ['accepted'],
        ], [
            'terms_accepted.accepted' => 'You must confirm that you are a student and accept these terms to continue.',
        ]);

        $user = $request->user();
        try {
            $profilePicturePath = $request->file('profile_picture')->store("students/{$user->id}/profile", 's3');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors([
                'profile_picture' => 'The profile picture could not be uploaded. Check storage settings and try again.',
            ]);
        }
        if (! $profilePicturePath) {
            return back()->withInput()->withErrors([
                'profile_picture' => 'The profile picture could not be saved. Please try again.',
            ]);
        }

        $user->student_number = trim($validated['student_number']);
        $user->course = trim($validated['course']);
        $user->year_level = trim($validated['year_level']);
        $user->section = trim($validated['section']);
        $user->profile_picture_path = $profilePicturePath;
        $user->terms_accepted_at = now();
        $user->profile_completed_at = now();
        $user->save();

        return redirect()->route('student.dashboard');
    }

    public function account(): View
    {
        $user = Auth::user();
        $profilePictureUrl = $user->profile_picture_path
            ? Storage::disk('s3')->temporaryUrl($user->profile_picture_path, now()->addMinutes(30))
            : asset('icons/profile_placeholder.svg');

        $currentDocuments = Document::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->unique('document_type')
            ->values();

        $verifiedDocuments = $currentDocuments
            ->filter(fn (Document $document): bool => strtolower($document->status) === 'verified')
            ->values();
        $needsCorrectionDocuments = $currentDocuments
            ->filter(fn (Document $document): bool => str_replace([' ', '-'], '_', strtolower($document->status)) === 'needs_correction')
            ->values();

        $submittedTypes = $currentDocuments->pluck('document_type');
        $unsubmittedDocumentTypes = DocumentType::query()
            ->orderBy('name')
            ->pluck('name')
            ->diff($submittedTypes)
            ->values();

        return view('student.account', compact(
            'profilePictureUrl',
            'verifiedDocuments',
            'needsCorrectionDocuments',
            'unsubmittedDocumentTypes',
        ));
    }
}
