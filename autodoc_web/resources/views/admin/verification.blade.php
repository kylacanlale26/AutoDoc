
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
        'resources/css/admin/verification.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('components.admin-sidebar')

    <main class="admin-main-content">

        <div class="verification-container">

            <section class="verification-header">
                <h1>Review Submission</h1>
            </section>

            <section class="review-submissions-list">

                @forelse (($flaggedDocuments ?? []) as $document)

                    <div class="review-submission-item">

                        <div class="review-submission-details">
                            <h2>
                                {{ $document->student_name }}
                                <span>- {{ $document->document_name }}</span>
                            </h2>
                            <p class="review-submission-status">{{ $document->status }}</p>
                            <p class="review-submission-date">{{ $document->created_at->format('M d, Y') }}</p>
                        </div>

                        <div class="review-submission-action">
                            <a href="{{ route('admin.review') }}">
                                <img
                                    src="{{ asset('icons/arrow-right-light.svg') }}"
                                    alt="View submission"
                                >
                            </a>
                        </div>

                    </div>

                @empty
                    <div class="empty-review-submissions">No Flagged Documents Needing Review</div>
                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection
