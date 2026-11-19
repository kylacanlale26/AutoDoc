@extends('layouts.master')

@push('styles')
    @vite('resources/css/student/complete-profile.css')
@endpush

@push('scripts')
    @vite('resources/js/students/complete-profile.js')
@endpush

@section('title', 'Complete Student Profile | AutoDoc')

@section('content')
<main class="student-onboarding-page">
    <section class="student-onboarding-card">
        <p class="student-onboarding-eyebrow">Student account setup</p>
        <h1>Complete your student profile</h1>
        <p class="student-onboarding-intro">
            Enter your current Mabalacat City College details to continue to AutoDoc.
        </p>

        @if ($errors->any())
            <div class="student-onboarding-errors" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('student.profile.complete.store') }}" class="student-onboarding-form" enctype="multipart/form-data">
            @csrf

            <label for="student_number">Student number</label>
            <input id="student_number" name="student_number" value="{{ old('student_number', auth()->user()->student_number) }}" required maxlength="50" autocomplete="off">

            <label for="course">Course / program</label>
            <select id="course" name="course" required>
                <option value="" disabled @selected(!old('course', auth()->user()->course))>Choose your course / program</option>
                @foreach ([
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
                ] as $course)
                    <option value="{{ $course }}" @selected(old('course', auth()->user()->course) === $course)>{{ $course }}</option>
                @endforeach
            </select>

            <label for="year_level">Year level</label>
            <select id="year_level" name="year_level" required>
                <option value="" disabled @selected(!old('year_level', auth()->user()->year_level))>Choose your year level</option>
                @foreach (['1', '2', '3', '4'] as $year)
                    <option value="{{ $year }}" @selected(old('year_level', auth()->user()->year_level) === $year)>{{ $year }}</option>
                @endforeach
            </select>

            <label for="section">Section</label>
            <select id="section" name="section" required>
                <option value="" disabled @selected(!old('section', auth()->user()->section))>Choose your section</option>
                @foreach (['A', 'B', 'C'] as $section)
                    <option value="{{ $section }}" @selected(old('section', auth()->user()->section) === $section)>{{ $section }}</option>
                @endforeach
            </select>

            <label for="profile_picture">Profile picture <span>(JPG or PNG, up to 5 MB)</span></label>
            <div class="student-onboarding-photo-picker">
                <img id="profilePicturePreview" src="{{ asset('icons/profile_placeholder.svg') }}" alt="Profile picture preview">
                <input id="profile_picture" name="profile_picture" type="file" accept="image/png,image/jpeg" required>
            </div>

            <div class="student-onboarding-terms">
                <h2>Terms and conditions</h2>
                <ul>
                    <li>AutoDoc student services are only for currently enrolled students of Mabalacat City College.</li>
                    <li>You confirm that the student details and documents you submit are accurate and belong to you.</li>
                    <li>Your profile and uploaded documents may be reviewed and processed by authorized college administrators for document verification.</li>
                </ul>
                <label class="student-onboarding-consent" for="terms_accepted">
                    <input id="terms_accepted" name="terms_accepted" type="checkbox" value="1" required @checked(old('terms_accepted'))>
                    <span>I confirm that I am a student and agree to the terms above.</span>
                </label>
            </div>

            <button type="submit">Save details and continue</button>
        </form>
    </section>
</main>
@endsection
