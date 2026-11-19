@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/student-sidebar.css',
        'resources/css/student/dashboard.css'
    ])
@endpush

@section('content')

<div class="student-layout">

    @include('sidebar.student-sidebar')
    
    <main class="student-main-content">

        <div class="dashboard-container">

            <section class="dashboard-header">
                <h1>Hello, {{ auth()->user()->first_name }}!</h1>
                <p>Manage your documents in one place.</p>
            </section>

            <section class="document-summary">

                <div class="summary-card">
                    <img src="{{ asset('icons/document-light.svg') }}" alt="Documents">
                    <h2>{{ $documentCounts['total'] ?? 0 }}</h2>
                    <span>Total Documents</span>
                </div>

                <div class="summary-card">
                    <img src="{{ asset('icons/verified-light.svg') }}" alt="Verified">
                    <h2>{{ $documentCounts['verified'] ?? 0 }}</h2>
                    <span>Verified</span>
                </div>

                <div class="summary-card">
                    <img src="{{ asset('icons/pending-light.svg') }}" alt="Pending">
                    <h2>{{ $documentCounts['pending'] ?? 0 }}</h2>
                    <span>Pending</span>
                </div>

                <div class="summary-card">
                    <img src="{{ asset('icons/correction-light.svg') }}" alt="Needs Correction">
                    <h2>{{ $documentCounts['needs_correction'] ?? 0 }}</h2>
                    <span>Needs Correction</span>
                </div>

            </section>

            <section class="dashboard-upload">

                <a href="{{ route('student.upload') }}" class="upload-button">

                    <div class="upload-button-content">
                        <img src="{{ asset('icons/upload.svg') }}" alt="">
                        <span>Upload a Document</span>
                    </div>

                    <img src="{{ asset('icons/upload-arrow.svg') }}" alt="" class="upload-arrow">
                
                </a>

            </section>

            <section class="recent-documents">

                <h2>Recent Documents</h2>

                <div class="recent-documents-table {{ count($documents ?? []) === 0 ? 'is-empty' : '' }}">

                    @forelse(($documents ?? []) as $recent)

                        <a class="submission-item" href="{{ route('student.documents.show', $recent) }}">
                            <h3>{{ $recent->document_name }}</h3>
                            <span class="document-status {{ strtolower(str_replace([' ', '_'], '-', $recent->status)) }}">
                                {{ str_replace('_', ' ', ucfirst($recent->status)) }}
                            </span>
                        </a>

                    @empty
                        <div class="empty-documents">
                            No Recent Submissions
                        </div>
                    @endforelse

                </div>
                
            </section>

        </div>

    </main>
    
</div>

@endsection
