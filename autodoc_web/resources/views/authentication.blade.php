<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Authentication - AutoDoc</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
</head>

<body>

<main class="authentication-page">

    <div class="authentication-card">
        <h1>Authentication</h1>
        <p class="authentication-subtitle">Enter the code that was sent to your email.</p>

        @if ($errors->any())
            <div class="verification-error">{{ $errors->first() }}</div>
        @endif

        <form id="verificationForm" method="POST" action="{{ route('authentication.verify') }}">
            @csrf
            <div class="code-inputs">
                <input type="text" name="code[]" maxlength="1" inputmode="numeric" autocomplete="one-time-code" class="code-input" autofocus>
                <input type="text" name="code[]" maxlength="1" inputmode="numeric" class="code-input">
                <input type="text" name="code[]" maxlength="1" inputmode="numeric" class="code-input">
                <input type="text" name="code[]" maxlength="1" inputmode="numeric" class="code-input">
            </div>
            <div class="request-code">
                <span>Can't see code?</span>
                <button type="button" id="requestCode">Request new code.</button>
            </div>
            <button type="submit" class="verify-button">Verify Code</button>
        </form>
    </div>
</main>

<script src="{{ asset('js/authentication.js') }}"></script>

</body>
</html>