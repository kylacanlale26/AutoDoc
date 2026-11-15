
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/student-docs.css'
    ])
@endpush

@push('scripts')
    @vite('resources/js/admin/student-docs.js')
@endpush

@section('content')

<div class="admin-layout">
    
    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="student-docs-container">

            <section class="student-docs-header">
                <h1>Student Documents</h1>
                <p>Student: <span>{{ $student->name ?: trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}</span></p>
            </section>

            <section class="submission-search-section">

                <div class="submission-search">
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input id="adminStudentDocumentSearch" type="text" placeholder="Search Document" aria-label="Search documents">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            </section>

            <section class="documents-filters">
                <button type="button" class="filter-chip active" data-status-filter="all" aria-pressed="true">All</button>
                <button type="button" class="filter-chip" data-status-filter="verified" aria-pressed="false">Verified</button>
                <button type="button" class="filter-chip" data-status-filter="pending" aria-pressed="false">Pending</button>
                <button type="button" class="filter-chip" data-status-filter="needs_correction" aria-pressed="false">Needs Correction</button>
            </section>

            <section class="student-documents-list">

                @forelse (($documents ?? []) as $document)

                    <div class="student-document-item" data-status="{{ strtolower(str_replace([' ', '-'], '_', $document->status)) }}">

                        <div class="student-document-details">
                            <h2>{{ $document->document_name }}</h2>
                            <p class="student-document-date">{{ $document->created_at->format('M d, Y') }}</p>
                            <p class="student-document-status {{ strtolower(str_replace([' ', '_'], '-', $document->status)) }}">{{ str_replace('_', ' ', ucfirst($document->status)) }}</p>
                        </div>

                        <div class="document-actions">
                            <button
                                type="button"
                                class="document-more-button"
                                aria-label="More actions for {{ $document->document_name }}"
                                aria-haspopup="true"
                                aria-expanded="false"
                                aria-controls="admin-document-actions-{{ $document->id }}"
                            >
                                <img src="{{ asset('icons/more-light.svg') }}" alt="">
                            </button>

                            <div class="document-actions-menu" id="admin-document-actions-{{ $document->id }}" role="menu" hidden>
                                <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" rel="noopener noreferrer" role="menuitem">
                                    Preview File
                                </a>
                                <a href="{{ route('admin.documents.download', $document) }}" role="menuitem">
                                    Download/Export File
                                </a>
                            </div>
                        </div>

                    </div>

                @empty
                    <div class="empty-student-documents">No Documents Uploaded</div>

                @endforelse

                @if (count($documents ?? []) > 0)
                    <div class="empty-student-documents" id="adminStudentDocumentFilterEmpty" style="display: none;">
                        No documents match your search or filter.
                    </div>
                @endif

            </section>

        </div>

    </main>

</div>

@endsection
