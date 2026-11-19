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

                    <a class="notification-item {{ $notification->read_at === null ? 'is-unread' : '' }}"
                       href="{{ route('student.notifications.open', $notification->id) }}">

                        <div class="notification-content">
                            <h2 class="notification-status-heading">
                                {{ isset($notification->data['status']) ? str_replace('_', ' ', ucfirst($notification->data['status'])) : 'Document update' }}
                            </h2>
                            <p>
                                {{ $notification->data['document_type'] ?? 'Document' }}
                                @if (!empty($notification->data['document_name']))
                                    · {{ $notification->data['document_name'] }}
                                @endif
                            </p>
                            <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>

                        @if ($notification->read_at === null)
                            <img src="{{ asset('icons/unread.svg') }}" alt="Unread" class="unread-indicator">
                        @endif

                    </a>

                @empty
                    <div class="empty-notifications">No Notifications</div>
                    
                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection
