@extends('layouts.master')

@push('styles')
    @vite(['resources/css/welcome.css', 'resources/js/welcome.js'])
@endpush

@section('content')
<main class="welcome-page">

    <div class="welcome-container">

        <section class="welcome-intro">

            <div class="intro-content">

                <span class="portal-label">
                    Student Document Portal
                </span>

                <h1>
                    Your Documents,<br>
                    <span>Verified and Secured.</span>
                </h1>

                <p>
                    A convenient way to upload, verify, and store your required documents in one centralized platform.
                </p>

            </div>

        </section>

        <div class="welcome-divider"></div>

        <section class="welcome-login">

            <div class="login-content">

                <h2>Sign in to Your Account</h2>

                <p class="login-instruction">
                    Please use your official school email.
                </p>

                <a href="#"
                    class="microsoft-button">
                    Login with Microsoft
                </a>

                <div class="contact-section">

                    <div class="contact-divider">
                        <span>Don't have an account?</span>
                    </div>

                    <a href="#">
                        Contact MIS
                    </a>

                </div>

            </div>

        </section>

    </div>

</main>
@endsection