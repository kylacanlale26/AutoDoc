@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/student-sidebar.css',
        'resources/css/student/document-detail.css'
    ])
@endpush

@section('title', $document->document_name . ' | AutoDoc')

@section('content')
@php
    $adminRecommendation = session('notificationReviewNotes')
        ?? ($document->reviewed_by ? $document->review_notes : null);
    $analysisNote = data_get($document->analysis?->result, 'verification.verdictSummary')
        ?: data_get($document->analysis?->result, 'error')
        ?: $document->review_notes;
@endphp

<div class="student-layout">
    @include('sidebar.student-sidebar')

    <main class="student-main-content">
        <div class="document-detail-container">
            <a class="document-detail-back" href="{{ route('student.documents') }}">← My Documents</a>
            <header class="document-detail-header">
                <div>
                    <p>{{ $document->document_type }}</p>
                    <h1>{{ $document->document_name }}</h1>
                    <span class="document-status {{ strtolower(str_replace([' ', '_'], '-', $document->status)) }}">
                        {{ str_replace('_', ' ', ucfirst($document->status)) }}
                    </span>
                </div>
                <a class="document-detail-download" href="{{ route('student.documents.download', $document) }}">Download document</a>
            </header>

            @if (session('notificationMessage'))
                <section class="document-detail-message" role="status">
                    {{ session('notificationMessage') }}
                </section>
            @endif

            <section class="document-detail-recommendation">
                <h2>Admin recommendation</h2>
                @if ($adminRecommendation)
                    <p>{{ $adminRecommendation }}</p>
                @else
                    <p>No admin recommendation has been added for this document.</p>
                @endif
                @if ($analysisNote && !$adminRecommendation)
                    <h2>Automated analysis note</h2>
                    <p>{{ $analysisNote }}</p>
                @endif
            </section>

            <section class="document-detail-preview">
                <h2>Document preview</h2>
                @if (str_starts_with((string) $document->mime_type, 'image/'))
                    <img
                        class="document-detail-image"
                        src="{{ route('student.documents.preview', $document) }}"
                        alt="Preview of {{ $document->document_name }}"
                        loading="lazy"
                    >
                @else
                    <iframe
                        title="Preview of {{ $document->document_name }}"
                        src="{{ route('student.documents.preview', $document) }}#view=Fit"
                        loading="lazy"
                    ></iframe>
                @endif
            </section>
        </div>
    </main>
</div>
@endsection
