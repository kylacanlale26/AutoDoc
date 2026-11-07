
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
        'resources/css/admin/submissions.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('components.admin-sidebar')

    <main class="admin-main-content">

        <div class="submissions-container">

            <section class="submissions-header">
                <h1>All Submissions</h1>
            </section>

            <section class="submission-search-section">

                <div class="submission-search">
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input type="text" placeholder="Search student or file">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            <section class="documents-filters">
                <button type="button" class="filter-chip active">All</button>
                <button type="button" class="filter-chip">Verified</button>
                <button type="button" class="filter-chip">Pending</button>
                <button type="button" class="filter-chip">Needs Correction</button>
            </section>

            <section class="student-submissions-list">

                @forelse (($submissions ?? []) as $submission)

                    <div class="student-submission-item">

                        <div class="submission-details">
                            <h2>
                                <span> - {{ $submission->document_name }}</span>
                            </h2>
                            <p class="submission-status {{ strtolower($submission->status) }}">{{ $submission->status }}</p>
                            <p class="submission-date">{{ $submission->created_at->format('M d, Y') }}</p>
                        </div>

                        <button type="button" class="submission-more-button" aria-label="More actions">
                            <img src="{{ asset('icons/more-light.svg') }}" alt="">
                        </button>

                    </div>

                @empty
                    <div class="empty-submissions">No Student Submission Available</div>
                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection