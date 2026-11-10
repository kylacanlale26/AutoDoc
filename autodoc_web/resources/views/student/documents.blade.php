@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/student-sidebar.css',
        'resources/css/student/documents.css'
    ])
@endpush

@section('content')

<div class="student-layout">

    @include('sidebar.student-sidebar')
    
    <main class="student-main-content">

        <div class="documents-container">

            <section class="documents-header">
                <h1>My Documents</h1>
            </section>

            <section class="documents-search-section">

                <div class="documents-search">
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input type="text" placeholder="Search Document">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            <section class="documents-filters">

                <button type="button" class="filter-chip active">
                    All
                </button>

                <button type="button" class="filter-chip">
                    Verified
                </button>

                <button type="button" class="filter-chip">
                    Pending
                </button>

                <button type="button" class="filter-chip">
                    Needs Correction
                </button>

            </section>

            <section class="documents-list-section">

                <div class="documents-table">

                    @forelse(($documents ?? []) as $uploads)

                        <div class="document-item">

                            <div class="document-info">
                                <span>{{ $uploads->student_name }}</span>
                                <span>{{ $uploads->document_name }}</span>
                                <span>{{ $uploads->status }}</span>
                            </div>

                            <button type="button" class="submission-more-button" aria-label="More actions">
                                <img src="{{ asset('icons/more-light.svg') }}" alt="">
                            </button>

                        </div>

                    @empty
                        <div class="empty-documents">
                            No Document Uploaded
                        </div>

                    @endforelse

                </div>

            </section>

        </div>

    </main>

</div>

@endsection