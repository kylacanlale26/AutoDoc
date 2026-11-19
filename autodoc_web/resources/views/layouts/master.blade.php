<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="{{ asset('images/autodoc-logo.png') }}">
    <title>@yield('title', 'AutoDoc')</title>
    <script>
        try {
            document.documentElement.dataset.theme = localStorage.getItem('autodoc-theme') === 'dark' ? 'dark' : 'light';
        } catch (_) {
            document.documentElement.dataset.theme = 'light';
        }
    </script>

    @vite([
    'resources/css/app.css',
    'resources/css/layouts/master.css',
    'resources/js/app.js',
    ])

    @stack('styles')
    @vite('resources/css/dark-theme.css')

</head>
<body>

<div class="autodoc-loading-screen" id="autodocLoadingScreen" role="status" aria-live="polite" aria-label="Loading AutoDoc">
    <div class="autodoc-loading-mark" aria-hidden="true">
        <img
            id="loadingLogo"
            src="{{ asset('icons/autodoc-logo.svg') }}"
            data-light-logo="{{ asset('icons/autodoc-logo.svg') }}"
            data-dark-logo="{{ asset('icons/autodoc-logo-darkmode.svg') }}"
            alt=""
        >
    </div>
    <span class="autodoc-loading-label">Loading AutoDoc</span>
</div>
    
<!-- header -->
<header class="top-header">
    <a class="app" href="{{ auth()->check() ? (strtolower((string) auth()->user()->account_role) === 'admin' ? route('admin.dashboard') : route('student.dashboard')) : route('welcome') }}" aria-label="AutoDoc dashboard">
        <img
            id="appLogo"
            src="{{ asset('icons/autodoc-logo.svg') }}"
            data-light-logo="{{ asset('icons/autodoc-logo.svg') }}"
            data-dark-logo="{{ asset('icons/autodoc-logo-darkmode.svg') }}"
            alt="AUTODOC Logo"
        >
        <span class="auto">AUTO</span><span class="doc">DOC</span>
    </a>

    <div class="theme">
        <button id="themeToggle" class="theme-icon" type="button" aria-label="Switch to dark mode" aria-pressed="false"
                data-light-icon="{{ asset('icons/dark-theme-toggle.svg') }}"
                data-dark-icon="{{ asset('icons/light-theme-toggle.svg') }}">
            <img id="themeIcon" src="{{ asset('icons/dark-theme-toggle.svg') }}" alt="Toggle Theme">
        </button>
    </div>
</header>

<div>

    @yield('sidebar')

    @yield('content')


</div>

@stack('scripts')

</body>
</html>
