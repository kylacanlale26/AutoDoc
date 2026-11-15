
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/submissions.css'
    ])
@endpush

@push('scripts')
    @vite('resources/js/admin/submissions.js')
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="submissions-container">

            <section class="submissions-header">
                <h1>All Submissions</h1>
            </section>

            <section class="submission-search-section">

                <div class="submission-search">
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input id="submissionSearch" type="search" placeholder="Search student or file" aria-label="Search submissions">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            <section class="documents-filters">
                <button type="button" class="filter-chip active" data-status-filter="all" aria-pressed="true">All</button>
                <button type="button" class="filter-chip" data-status-filter="verified" aria-pressed="false">Verified</button>
                <button type="button" class="filter-chip" data-status-filter="pending" aria-pressed="false">Pending</button>
                <button type="button" class="filter-chip" data-status-filter="needs_correction" aria-pressed="false">Needs Correction</button>
            </section>

            <section class="student-submissions-list">

                @forelse (($submissions ?? []) as $submission)

                    <div
                        class="student-submission-item"
                        data-status="{{ strtolower(str_replace([' ', '-'], '_', $submission->status)) }}"
                        data-search="{{ $submission->student->name ?? '' }} {{ $submission->document_name }}"
                    >

                        <div class="submission-details">
                            <h2>
                                {{ $submission->student->name }}
                                <span> - {{ $submission->document_name }}</span>
                            </h2>
                            @php
                                $statusKey = strtolower(str_replace([' ', '-'], '_', $submission->status));
                                $statusClass = $statusKey === 'needs_correction' ? 'flagged' : $statusKey;
                            @endphp
                            <p class="submission-status {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($statusKey)) }}</p>
                            <p class="submission-date">{{ $submission->created_at->format('M d, Y') }}</p>
                        </div>

                        <a class="submission-more-button" href="{{ route('admin.review.document', $submission) }}" aria-label="Review {{ $submission->document_name }}">
                            Review
                        </a>

                    </div>

                @empty
                    <div class="empty-submissions">No Student Submission Available</div>
                @endforelse

                @if (count($submissions ?? []) > 0)
                    <div class="empty-submissions" id="submissionFilterEmpty" style="display: none;">
                        No submissions match your search or filter.
                    </div>
                @endif

            </section>

        </div>

    </main>

</div>

@endsection
