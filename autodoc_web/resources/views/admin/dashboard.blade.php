@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
        'resources/css/admin/dashboard.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('components.admin-sidebar')

    <main class="admin-main-content">

        <div class="admin-dashboard-container">

            <section class="admin-dashboard-header">

                <h1>Administrator Overview</h1>

                <p>Monitor document submissions and verification activity.</p>
            
            </section>

            <section class="admin-summary">

                <div class="admin-summary-card">

                    <span>Total Submissions</span>

                    <h2>0</h2>

                </div>

                <div class="admin-summary-card">

                    <span>Awaiting Review</span>

                    <h2>0</h2>

                </div>

                <div class="admin-summary-card">

                    <span>Verified</span>

                    <h2>0</h2>

                </div>

                <div class="admin-summary-card">

                    <span>Flagged</span>

                    <h2>0</h2>

                </div>

            </section>

            <section class="admin-dashboard-bottom">

                <div class="recent-submissions">

                    <h2>Recent Submissions</h2>

                    <div class="recent-submissions-list">

                        @forelse (($recentSubmissions ?? []) as $submission)

                            <div class="submission-item">

                                <div class="submission-info">
                                    
                                    <h3>{{ $submission->student_name }}</h3>
                                    
                                    <p>{{ $submission->document_name }}</p>
                                
                                </div>

                                <span class="submission-status {{ strtolower($submission->status) }}">{{ $submission->status }}</span>

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