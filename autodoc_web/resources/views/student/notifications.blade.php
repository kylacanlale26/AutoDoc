@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/student-sidebar.css',
        'resources/css/student/notifications.css'
    ])
@endpush

@section('content')

<div class="student-layout">

    @include('components.student-sidebar')

    <main class="student-main-content">

        <div class="notifications-container">

            <section class="notifications-header">
                <h1>Notifications</h1>
                <a href="#" class="mark-all-read">Mark all as read</a>
            </section>

            <section class="notifications-list">

                @forelse (($notifications ?? []) as $notification)

                    <div class="notification-item">

                        <div class="notification-content">
                            <h2>{{ $notification->title }}</h2>
                            <p>{{ $notification->message }}</p>
                            <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>

                        @if (!$notification->is_read)
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