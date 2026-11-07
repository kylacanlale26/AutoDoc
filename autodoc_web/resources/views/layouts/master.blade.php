<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/png" href="{{ asset('images/autodoc-logo.png') }}">
    <title>@yield('title', 'AutoDoc')</title>

    @vite([
    'resources/css/app.css',
    'resources/css/layouts/master.css',
    'resources/css/layouts/darkmode.css',
    'resources/js/app.js',
    'resources/js/layouts/master.js'
    ])

    @stack('styles')

</head>
<body>
    
<!-- header -->
<header class="top-header">
    <div class="app">
        <img id="appLogo" src="{{ asset('icons/autodoc-logo.svg') }}" alt="AUTODOC Logo">
        <span class="auto">AUTO</span><span class="doc">DOC</span>
    </div>

    <div class="theme">
        <button id="themeToggle" class="theme-icon" aria-label="Toggle theme">
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