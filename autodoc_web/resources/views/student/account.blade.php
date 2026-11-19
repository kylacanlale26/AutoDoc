@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/student-sidebar.css',
        'resources/css/student/account.css'
    ])
@endpush

@section('content')

<div class="student-layout">

    @include('sidebar.student-sidebar')
    
    <main class="student-main-content">

        <div class="account-container">

            <section class="account-header">
                <h1>My Account</h1>
            </section>

            <section class="account-profile">

                <div class="account-profile-picture">
                    <img src="{{ $profilePictureUrl }}" alt="Student Profile">
                </div>

                <div class="account-profile-info">
                    <h2>{{ trim((Auth::user()->first_name ?? '') . ' ' . (Auth::user()->last_name ?? '')) ?: Auth::user()->name }}</h2>
                    <p>{{ Auth::user()->student_number ?: 'Student number not provided' }}</p>
                </div>

                <div class="account-details">

                    <div class="account-detail-item">
                        <h3>Program</h3>
                        <p>{{ Auth::user()->course ?: 'Not provided' }}</p>
                    </div>

                    <div class="account-detail-item">
                        <h3>Year Level and Section</h3>
                        <p>{{ Auth::user()->year_level ?: 'Year not provided' }} - {{ Auth::user()->section ?: 'Section not provided' }}</p>
                    </div>

                    <div class="account-detail-item">
                        <h3>MCC Email</h3>
                        <p>{{ Auth::user()->email }}</p>
                    </div>

                </div>

            </section>

            <section class="account-document-lists">
                <div class="account-document-list">
                    <h2>Verified Documents</h2>
                    @forelse ($verifiedDocuments as $document)
                        <div class="account-document-row">
                            <strong>{{ $document->document_type }}</strong>
                            <span>{{ $document->document_name }}</span>
                        </div>
                    @empty
                        <p class="account-document-empty">No verified documents yet.</p>
                    @endforelse
                </div>

                <div class="account-document-list">
                    <h2>Unsubmitted Documents</h2>
                    @forelse ($unsubmittedDocumentTypes as $documentType)
                        <div class="account-document-row">
                            <strong>{{ $documentType }}</strong>
                            <span>Not submitted</span>
                        </div>
                    @empty
                        <p class="account-document-empty">All available document types have been submitted.</p>
                    @endforelse
                </div>

                @if ($needsCorrectionDocuments->isNotEmpty())
                    <div class="account-document-list account-document-list-correction">
                        <h2>Needs Correction</h2>
                        @foreach ($needsCorrectionDocuments as $document)
                            <a class="account-document-row" href="{{ route('student.documents.show', $document) }}">
                                <strong>{{ $document->document_type }}</strong>
                                <span>{{ $document->document_name }} — Upload a corrected file</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>

    </main>

</div>

@endsection
