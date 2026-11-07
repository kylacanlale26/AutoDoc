
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
        'resources/css/admin/student-docs.css'
    ])
@endpush

@section('content')

<div class="admin-layout">
    
    @include('components.admin-sidebar')

    <main class="admin-main-content">

        <div class="student-docs-container">

            <section class="student-docs-header">
                <h1>Student Documents</h1>
                <p>Student: <span>{{ $student->name ?? '<student name>' }}</span></p>
            </section>

            <section class="submission-search-section">

                <div class="submission-search">
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input type="text" placeholder="Search Document">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            </section>

            <section class="documents-filters">
                <button type="button" class="filter-chip active">All</button>
                <button type="button" class="filter-chip">Verified</button>
                <button type="button" class="filter-chip">Pending</button>
                <button type="button" class="filter-chip">Needs Correction</button>
            </section>

            <section class="student-documents-list">

                @forelse (($documents ?? []) as $document)

                    <div class="student-document-item">

                        <div class="student-document-details">
                            <h2>{{ $document->document_name }}</h2>
                            <p class="student-document-date">{{ $document->created_at->format('M d, Y') }}</p>
                            <p class="student-document-status {{ strtolower(str_replace(' ', '-', $document->status)) }}">{{ $document->status }}</p>
                        </div>

                        <button type="button" class="document-more-button" aria-label="More document actions">
                            <img src="{{ asset('icons/more-light.svg') }}" alt="">
                        </button>

                    </div>

                @empty
                    <div class="empty-student-documents">No Documents Uploaded</div>

                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection
