
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/student-accounts.css'
    ])
@endpush

@push('scripts')
    @vite('resources/js/admin/student-accounts.js')
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="student-accounts-container">

            <section class="student-accounts-header">
                <h1>Student Accounts</h1>
            </section>

            <section class="student-search-section">

                <div class="student-search">
                    <img src="{{ asset('icons/search-light.svg') }}" data-light-theme-icon="{{ asset('icons/search-light.svg') }}" data-dark-theme-icon="{{ asset('icons/search-dark.svg') }}" alt="Search">
                    <input id="studentAccountSearch" type="search" placeholder="Search student" aria-label="Search student name, number, or email">
                </div>

                <button type="button" class="filter-button" data-filter-toggle aria-expanded="false" aria-controls="accountCourseFilterPanel">
                    <img src="{{ asset('icons/filter-light.svg') }}" data-light-theme-icon="{{ asset('icons/filter-light.svg') }}" data-dark-theme-icon="{{ asset('icons/filter-dark.svg') }}" alt="Filter">
                </button>

            </section>

            <div class="account-course-filter" id="accountCourseFilterPanel" data-filter-panel hidden>
                <label for="accountCourseFilter">Filter by program</label>
                <select id="accountCourseFilter">
                    <option value="">All programs</option>
                    @foreach (($students ?? collect())->pluck('course')->filter()->unique()->sort() as $course)
                        <option value="{{ $course }}">{{ $course }}</option>
                    @endforeach
                </select>
            </div>

            <section class="student-accounts-list">

                @forelse (($students ?? []) as $student)

                    @php
                        $studentName = $student->name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                    @endphp
                    <div
                        class="student-account-item"
                        data-course="{{ $student->course ?? '' }}"
                        data-student-search="{{ $studentName }} {{ $student->student_number ?? '' }} {{ $student->email }}"
                    >

                        <div class="account-student-name">
                            <img class="student-account-avatar" src="{{ $student->profile_picture_url ?? asset('icons/profile_placeholder.svg') }}" alt="">
                            <h2>{{ $studentName !== '' ? $studentName : '—' }}</h2>
                        </div>

                        <div class="account-student-number">{{ $student->student_number ?: '—' }}</div>

                        <div class="account-student-email">{{ $student->email ?: '—' }}</div>

                    </div>

                @empty
                    <div class="empty-student-accounts">No Student Added</div>

                @endforelse

                @if (count($students ?? []) > 0)
                    <div class="empty-student-accounts" id="studentAccountSearchEmpty" style="display: none;">
                        No student accounts match your search.
                    </div>
                @endif

            </section>

        </div>

    </main>

</div>

@endsection
