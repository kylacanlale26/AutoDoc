
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/verification.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')

    <main class="admin-main-content">

        <div class="verification-container">

            <section class="verification-header">
                <h1>Review Submission</h1>
            </section>

            <section class="review-submissions-list">

                @forelse (($flaggedDocuments ?? []) as $document)

                    <a class="review-submission-item" href="{{ route('admin.review.document', $document) }}">

                        <div class="review-submission-details">
                            <h2>{{ $document->student->name ?? 'Unknown student' }}</h2>
                            <h3>{{ $document->document_name }}</h3>
                            <p class="review-submission-status">{{ $document->status }}</p>
                            <time class="review-submission-date" datetime="{{ $document->created_at->toIso8601String() }}">{{ $document->created_at->format('M d, Y · g:i A') }}</time>
                        </div>

                        <span class="review-submission-action" aria-hidden="true">Review&nbsp;›</span>
                    </a>

                @empty
                    <div class="empty-review-submissions">No Flagged Documents Needing Review</div>
                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection
