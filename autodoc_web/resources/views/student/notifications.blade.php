@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/sidebar/student-sidebar.css',
        'resources/css/student/notifications.css'
    ])
@endpush

@section('content')

<div class="student-layout">

    @include('sidebar.student-sidebar')
    
    <main class="student-main-content">

        <div class="notifications-container">

            <section class="notifications-header">
                <h1>Notifications</h1>
                @if ($notifications->contains(fn ($notification) => $notification->read_at === null))
                    <form method="POST" action="{{ route('student.notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="mark-all-read">Mark all as read</button>
                    </form>
                @endif
            </section>

            <section class="notifications-list">

                @forelse (($notifications ?? []) as $notification)

                    <div class="notification-item">

                        <div class="notification-content">
                            <h2>{{ $notification->data['document_name'] ?? 'Document status updated' }}</h2>
                            <p>{{ $notification->data['message'] ?? 'There is an update to your document.' }}</p>
                            @if (!empty($notification->data['status']))
                                <p>Status: {{ str_replace('_', ' ', ucfirst($notification->data['status'])) }}</p>
                            @endif
                            @if (!empty($notification->data['review_notes']))
                                <p>Admin note: {{ $notification->data['review_notes'] }}</p>
                            @endif
                            <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>

                        @if ($notification->read_at === null)
                            <img src="{{ asset('icons/unread.svg') }}" alt="Unread" class="unread-indicator">
                        @endif

                    </div>

                @empty
                    <div class="empty-notifications">No Notifications</div>
                    
                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection
