
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
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
            </section>

            <section class="review-content">

                <div class="document-review-panel">

                    <div class="document-review-header">

                        <div class="document-info">

                            <h2>Birth Certificate.pdf</h2>

                            <p>Student 003 · PDF · 2.1 MB</p>

                        </div>

                        <span class="document-status pending">
                            Pending
                        </span>

                    </div>

                    <div class="document-preview-placeholder">

                        <div class="preview-placeholder-content">

                            <div class="preview-file-icon">
                                <span>PDF</span>
                            </div>

                            <h3>Document Preview</h3>

                            <p>
                                Uploaded document will appear here.
                            </p>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="open-full-preview"
                    >
                        Open Full Preview
                    </button>

                </div>

                <div class="verification-review-panel">

                    <section class="verification-findings">

                        <h2>Verification Findings</h2>

                        <p class="findings-placeholder">
                            findings<br>
                            Review the highlighted section before deciding.
                        </p>

                    </section>

                    <section class="administrator-notes">

                        <label for="admin-notes">
                            Administrator Notes
                        </label>

                        <textarea
                            id="admin-notes"
                            name="admin_notes"
                            placeholder="Type the reason for your decision..."
                        ></textarea>

                    </section>

                    <div class="review-actions">

                        <button
                            type="button"
                            class="approve-document"
                        >
                            Approve Document
                        </button>

                        <button
                            type="button"
                            class="request-correction"
                        >
                            Request Correction
                        </button>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

@endsection
