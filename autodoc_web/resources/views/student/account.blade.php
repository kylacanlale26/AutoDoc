@extends('layouts.master')

@push('styles')
    @vite([
        'resources/css/components/student-sidebar.css',
        'resources/css/student/account.css'
    ])
@endpush

@section('content')

<div class="student-layout">

    @include('components.student-sidebar')

    <main class="student-main-content">

        <div class="account-container">

            <section class="account-header">
                <h1>My Account</h1>
            </section>

            <section class="account-profile">

                <div class="account-profile-picture">
                    <img src="{{ asset('icons/profile_placeholder.svg') }}" alt="Student Profile">
                </div>

                <div class="account-profile-info">
                    <h2>Student Name</h2>
                    <p>BS Information Technology, 3rd Year, Section A</p>
                </div>

                <div class="account-details">

                    <div class="account-detail-item">
                        <h3>Student ID</h3>
                        <p>2026-00000</p>
                    </div>

                    <div class="account-detail-item">
                        <h3>MCC Email</h3>
                        <p>student@mcc.edu.ph</p>
                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

@endsection