
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/student-documents.css'
    ])
@endpush

@push('scripts')
    @vite('resources/js/admin/student-documents.js')
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="student-documents-container">

            <section class="student-documents-header">
                <h1>Student Documents</h1>
            </section>

            <section class="student-search-section">

                <div class="student-search">
                    <img src="{{ asset('icons/search-light.svg') }}" data-light-theme-icon="{{ asset('icons/search-light.svg') }}" data-dark-theme-icon="{{ asset('icons/search-dark.svg') }}" alt="Search">
                    <input id="studentSearch" type="search" placeholder="Search student" aria-label="Search students">
                </div>

                <button type="button" class="filter-button" data-filter-toggle aria-expanded="false" aria-controls="studentCourseFilterPanel">
                    <img src="{{ asset('icons/filter-light.svg') }}" data-light-theme-icon="{{ asset('icons/filter-light.svg') }}" data-dark-theme-icon="{{ asset('icons/filter-dark.svg') }}" alt="Filter">
                </button>

            </section>

            <div class="student-course-filter" id="studentCourseFilterPanel" data-filter-panel hidden>
                <label for="studentCourseFilter">Filter by program</label>
                <select id="studentCourseFilter">
                    <option value="">All programs</option>
                    @foreach (($students ?? collect())->pluck('course')->filter()->unique()->sort() as $course)
                        <option value="{{ $course }}">{{ $course }}</option>
                    @endforeach
                </select>
            </div>

            <section class="student-documents-list">

                @forelse ($students ?? [] as $student)

                    <a
                        class="student-document-item"
                        href="{{ route('admin.student-docs', $student) }}"
                        data-student-row
                        data-course="{{ $student->course ?? '' }}"
                        data-search="{{ trim(($student->name ?? '') . ' ' . ($student->first_name ?? '') . ' ' . ($student->last_name ?? '') . ' ' . ($student->student_number ?? '') . ' ' . ($student->email ?? '')) }}"
                    >

                        <img class="student-list-avatar" src="{{ $student->profile_picture_url ?? asset('icons/profile_placeholder.svg') }}" alt="">
                        <div class="student-document-info">

                            <h2>{{ $student->name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}</h2>
                            <p>{{ $student->student_number ?: 'Student number not provided' }}</p>
                            <p>{{ $student->course ?: 'Program not provided' }}</p>

                        </div>

                        <span class="student-document-action" aria-hidden="true">View&nbsp;›</span>
                    </a>

                @empty
                    <div class="empty-student-documents">
                        No Student
                    </div>

                @endforelse

                @if (count($students ?? []) > 0)
                    <div class="empty-student-documents" id="studentSearchEmpty" style="display: none;">
                        No students match your search.
                    </div>
                @endif

            </section>

        </div>

    </main>

</div>

@endsection
