<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoDoc</title>

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>

<!-- header -->
<header>
    <div class="nav-wrap">
        <div class="webname">
            <img id="brandLogo" src="{{ asset('icons/autodoc-logo.svg') }} "alt="AUTODOC Logo" class="logo">
            <span class="auto">AUTO</span><span class="doc">DOC</span>
        </div>
        <nav class="links">
            <a href="#">Announcement</a>
            <a href="#">Contact</a>
            <a href="#">About Us</a>
            <a href="#">Sign In</a>
        </nav>
        <button id="dark-mode">
            <img id="themeIcon" src="{{ asset('icons/dark-mode.svg') }}" alt="Toggle Theme">
        </button>
    </div>
</header>

<!-- landing page banner -->
<section class="banner">
    <div class="banner-content">
        <p class="banner1">Education and Formation</p>
        <h1>Learn, Grow, and Serve</h1>
        <p class="banner2">Start Here, Be Successful Anywhere. Mabalacat City College believes education should shape the whole person equipping every Mabalaqueño with the knowledge, skills, and values to build a career, realize their full potential, and serve their community as a responsible, engaged citizen.</p>
    </div>
</section>

<script src="{{ asset('js/welcome.js') }}"></script>

</body>
</html>