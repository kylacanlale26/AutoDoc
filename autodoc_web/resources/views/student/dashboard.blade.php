@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/student-sidebar.css',
        'resources/css/student/dashboard.css'
    ])
@endpush

@section('content')

<div class="student-layout">

    @include('components.student-sidebar')

    <main class="student-main-content">

        <div class="dashboard-container">

            <section class="dashboard-header">
                <h1>Hello, Student!</h1>
                <p>Manage your documents in one place.</p>
            </section>

            <section class="document-summary">

                <div class="summary-card">
                    <img src="{{ asset('icons/document-light.svg') }}" alt="Documents">
                    <h2>0</h2>
                    <span>Total Documents</span>
                </div>

                <div class="summary-card">
                    <img src="{{ asset('icons/verified-light.svg') }}" alt="Verified">
                    <h2>0</h2>
                    <span>Verified</span>
                </div>

                <div class="summary-card">
                    <img src="{{ asset('icons/pending-light.svg') }}" alt="Pending">
                    <h2>0</h2>
                    <span>Pending</span>
                </div>

                <div class="summary-card">
                    <img src="{{ asset('icons/correction-light.svg') }}" alt="Needs Correction">
                    <h2>0</h2>
                    <span>Needs Correction</span>
                </div>

            </section>

            <section class="dashboard-upload">

                <a href="#" class="upload-button">

                    <div class="upload-button-content">
                        <img src="{{ asset('icons/upload.svg') }}" alt="">
                        <span>Upload a Document</span>
                    </div>

                    <img src="{{ asset('icons/upload-arrow.svg') }}" alt="" class="upload-arrow">
                
                </a>

            </section>

            <section class="recent-documents">

                <h2>Recent Documents</h2>

                <div class="recent-documents-table">

                    @forelse(($documents ?? []) as $recent)

                        <div class="submission-item">
                            <div>
                                <strong>{{ $recent->student_name }}</strong>
                                <br>
                                <span>{{ $recent->document_name }}</span>
                            </div>

                            <span class="document-status {{ strtolower($recent->status) }}">
                                {{ $recent->status }}
                            </span>
                        </div>

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