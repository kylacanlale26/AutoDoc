
@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/admin-sidebar.css',
        'resources/css/admin/student-accounts.css'
    ])
@endpush

@section('content')

<div class="admin-layout">

    @include('sidebar.admin-sidebar')
    
    <main class="admin-main-content">

        <div class="student-accounts-container">

            <section class="student-accounts-header">
                <h1>Student Accounts</h1>
                <a href="#" class="add-account-button">+ Add Account</a>
            </section>

            <section class="student-search-section">

                <div class="student-search">
                    <img src="{{ asset('icons/search-light.svg') }}" alt="Search">
                    <input type="text" placeholder="Search student">
                </div>

                <button type="button" class="filter-button">
                    <img src="{{ asset('icons/filter-light.svg') }}" alt="Filter">
                </button>

            </section>

            <section class="student-accounts-list">

                @forelse (($students ?? []) as $student)

                    <div class="student-account-item">

                        <div class="account-student-name">
                            <h2>{{ $student->name }}</h2>
                        </div>

                        <div class="account-student-number">{{ $student->student_number }}</div>

                        <div class="account-student-email">{{ $student->email }}</div>

                    </div>

                @empty
                    <div class="empty-student-accounts">No Student Added</div>

                @endforelse

            </section>

        </div>

    </main>

</div>

@endsection
