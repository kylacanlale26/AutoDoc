<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" type="image/png" href="{{ asset('images/autodoc-logo.png') }}">
    <title>AutoDoc</title>

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <main class="login-page">
        <div class="login-card">
            <div class="brand-logo">
                <span class="auto">AUTO</span><span class="doc">DOC</span>
            </div>
            <p class="login-subtitle">Sign in to your MCC Account</p>


            <form id="loginForm" method="POST" action="{{ route('login') }}">

                @csrf
                <!-- login error -->
                @if ($errors->any())
                    <div class="login-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" autocomplete="email" required>
                </div>

                <!-- password -->
                <div class="form-group password-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                        <button type="button" id="togglePassword" class="password-toggle" aria-label="Show password">
                            <img id="visibilityIcon" src="{{ asset('icons/visibility-light.svg') }}" alt="Show password">
                        </button>
                    </div>
                </div>

                <!-- forgot password -->
                <div class="forgot-wrapper">
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" class="sign-in-button">Sign In</button>
            </form>

            <!-- lines -->
            <div class="lines">
                <span></span>
                <p>Don't have an account?</p>
                <span></span>
            </div>

            <!-- message -->
            <p class="contact-mis">Contact MIS</p>
        </div>
    </main>

    <script src="{{ asset('js/login.js') }}"></script>

</body>
</html>