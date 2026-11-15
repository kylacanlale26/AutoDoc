@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/admin-sidebar.css',
        'resources/css/admin/dashboard.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="admin-dashboard-container">

            <section class="admin-dashboard-header">

                <h1>Administrator Overview</h1>

                <p>Monitor document submissions and verification activity.</p>
            
            </section>

            <section class="admin-summary">

                <div class="admin-summary-card">

                    <span>Total Submissions</span>

                    <h2>{{ $documentCounts['total'] ?? 0 }}</h2>

                </div>

                <div class="admin-summary-card">

                    <span>Awaiting Review</span>

                    <h2>{{ $documentCounts['pending'] ?? 0 }}</h2>

                </div>

                <div class="admin-summary-card">

                    <span>Verified</span>

                    <h2>{{ $documentCounts['verified'] ?? 0 }}</h2>

                </div>

                <div class="admin-summary-card">

                    <span>Flagged</span>

                    <h2>{{ $documentCounts['flagged'] ?? 0 }}</h2>

                </div>

            </section>

            <section class="admin-dashboard-bottom">

                <div class="recent-submissions">

                    <h2>Recent Submissions</h2>

                    <div class="recent-submissions-list">

                        @forelse (($recentSubmissions ?? []) as $submission)

                            <div class="submission-item">

                                <div class="submission-info">
                                    
                                    <h3>{{ $submission->student->name ?? 'Unknown student' }}</h3>
                                    
                                    <p>{{ $submission->document_name }}</p>
                                
                                </div>

                                @php
                                    $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $submission->status));
                                    $statusClass = $normalizedStatus === 'needs_correction' ? 'flagged' : $normalizedStatus;
                                @endphp
                                <span class="submission-status {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($normalizedStatus)) }}</span>

                            </div>

                        @empty
                            <div class="empty-submissions">No Recent Submission</div>
                        
                        @endforelse

                    </div>

                </div>


                {{-- Quick Actions --}}
                <div class="admin-quick-actions">

                    <h2>Quick Actions</h2>

                    <div class="quick-actions-list">
                        <a href="{{ route('admin.verification') }}" class="quick-action-link">Review Pending Documents</a>

                        <a href="{{ route('admin.submissions') }}" class="quick-action-link">Search Documents</a>

                        <a href="{{ route('admin.settings') }}" class="quick-action-link">Manage Requirements</a>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

@endsection
