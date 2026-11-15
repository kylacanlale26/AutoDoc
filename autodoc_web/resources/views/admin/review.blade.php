
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/review.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="review-container">

            <section class="review-header">
                <h1>Review Submission</h1>
                @if (session('success'))
                    <p role="status">{{ session('success') }}</p>
                @endif
                @if ($errors->any())
                    <div role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="review-content">

                <div class="document-review-panel">

                    <div class="document-review-header">

                        <div class="document-info">

                            <h2>{{ $document->document_name }}</h2>

                            <p>{{ $document->student->name }} · {{ number_format((int) $document->file_size_bytes / 1048576, 2) }} MB</p>

                        </div>

                        <span class="document-status {{ strtolower(str_replace(' ', '-', $document->status)) }}">
                            {{ str_replace('_', ' ', ucfirst($document->status)) }}
                        </span>

                    </div>

                    <div class="document-preview-placeholder">
                        <div class="preview-placeholder-content">
                            <h3>Document Preview</h3>
                            <p>Uploaded document will appear here.</p>
                        </div>
                    </div>

                    <a class="open-full-preview" href="{{ route('admin.documents.preview', $document) }}" target="_blank" rel="noopener">
                        Open Full Preview
                    </a>

                </div>

                <div class="verification-review-panel">

                    <form method="POST" action="{{ route('admin.documents.status.update', $document) }}">
                        @csrf
                    <section class="administrator-notes">

                        <label for="admin-notes">
                            Administrator Notes
                        </label>

                        <textarea
                            id="admin-notes"
                            name="review_notes"
                            placeholder="Type the reason for your decision..."
                        >{{ old('review_notes', $document->review_notes) }}</textarea>

                    </section>

                    <div class="review-actions">

                        <button
                            type="submit"
                            name="status"
                            value="verified"
                            class="approve-document"
                        >
                            Approve Document
                        </button>

                        <button
                            type="submit"
                            name="status"
                            value="needs_correction"
                            class="request-correction"
                        >
                            Request Correction
                        </button>

                    </div>
                    </form>

                </div>

            </section>

        </div>

    </main>

</div>

@endsection
