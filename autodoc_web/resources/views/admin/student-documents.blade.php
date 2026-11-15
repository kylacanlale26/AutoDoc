
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
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input id="studentSearch" type="search" placeholder="Search student" aria-label="Search students">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            <section class="student-documents-list">

                @forelse ($students ?? [] as $student)

                    <div
                        class="student-document-item"
                        data-student-row
                        data-search="{{ trim(($student->name ?? '') . ' ' . ($student->first_name ?? '') . ' ' . ($student->last_name ?? '') . ' ' . ($student->student_number ?? '') . ' ' . ($student->email ?? '')) }}"
                    >

                        <div class="student-document-info">

                            <h2>
                                {{ $student->name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                            </h2>

                        </div>

                        <div class="student-document-action">
                            <a href="{{ route('admin.student-docs', $student) }}" aria-label="View documents for {{ $student->name }}">
                                <img src="{{ asset('icons/arrow-right-light.svg') }}" alt="View student documents">
                            </a>
                        </div>

                    </div>

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
