@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/student-sidebar.css',
        'resources/css/student/documents.css'
    ])
@endpush

@push('scripts')
    @vite('resources/js/students/documents.js')
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
                    <img src="{{ asset('icons/search-light.svg') }}" data-light-theme-icon="{{ asset('icons/search-light.svg') }}" data-dark-theme-icon="{{ asset('icons/search-dark.svg') }}" alt="Search">
                    <input id="documentSearch" type="text" placeholder="Search Document">
                </div>

                <button type="button" class="filter-button" data-filter-toggle aria-expanded="false" aria-controls="studentDocumentFilters">
                    <img src="{{ asset('icons/filter-light.svg') }}" data-light-theme-icon="{{ asset('icons/filter-light.svg') }}" data-dark-theme-icon="{{ asset('icons/filter-dark.svg') }}" alt="Filter">
                </button>

            </section>

            <section class="documents-filters" id="studentDocumentFilters" data-filter-panel hidden>

                <button type="button" class="filter-chip active" data-status-filter="all" aria-pressed="true">
                    All
                </button>

                <button type="button" class="filter-chip" data-status-filter="verified" aria-pressed="false">
                    Verified
                </button>

                <button type="button" class="filter-chip" data-status-filter="pending" aria-pressed="false">
                    Pending
                </button>

                <button type="button" class="filter-chip" data-status-filter="needs_correction" aria-pressed="false">
                    Needs Correction
                </button>

            </section>

            <section class="documents-list-section">

                <div class="documents-table">

                    @forelse(($documents ?? []) as $uploads)

                        <div class="document-item"
                             data-status="{{ strtolower(str_replace([' ', '-'], '_', $uploads->status)) }}"
                             data-document-url="{{ route('student.documents.show', $uploads) }}"
                             role="link"
                             tabindex="0"
                             aria-label="View {{ $uploads->document_name }} details">

                            <div class="document-info">
                                <h3>{{ $uploads->document_name }}</h3>
                                <small>Uploaded {{ $uploads->created_at->format('M d, Y') }}</small>
                                <span class="document-status {{ strtolower(str_replace([' ', '_'], '-', $uploads->status)) }}">{{ str_replace('_', ' ', ucfirst($uploads->status)) }}</span>
                            </div>

                            <div class="document-actions">
                                <button
                                    type="button"
                                    class="submission-more-button"
                                    aria-label="More actions for {{ $uploads->document_name }}"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-controls="document-actions-menu-{{ $uploads->id }}"
                                >
                                    <img
                                        src="{{ asset('icons/more-light.svg') }}"
                                        data-light-icon="{{ asset('icons/more-light.svg') }}"
                                        data-dark-icon="{{ asset('icons/more-dark.svg') }}"
                                        alt=""
                                    >
                                </button>

                                <div
                                    class="document-actions-menu"
                                    id="document-actions-menu-{{ $uploads->id }}"
                                    role="menu"
                                    hidden
                                >
                                    <a href="{{ route('student.documents.preview', $uploads) }}" target="_blank" rel="noopener noreferrer" role="menuitem">
                                        Preview File
                                    </a>
                                    <a href="{{ route('student.documents.download', $uploads) }}" role="menuitem">
                                        Export File
                                    </a>
                                </div>
                            </div>

                        </div>

                    @empty
                        <div class="empty-documents">
                            No Document Uploaded
                        </div>
                    @endforelse

                    <div class="empty-documents" id="filterEmpty" style="display: none;">
                        No documents match your search or filter.
                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

@endsection
