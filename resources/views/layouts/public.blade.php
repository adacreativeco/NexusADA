<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ADA Co-OS')</title>
    <meta name="description" content="@yield('meta_description', 'ADA Co-OS — Digital Intelligence Platform')">

    {{-- PRE-LAUNCH: Lansman gününde bu satırı kaldır --}}
    <meta name="robots" content="noindex, nofollow">

    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'ADA Co-OS')">
    <meta property="og:description" content="@yield('meta_description', 'ADA Co-OS — Digital Intelligence Platform')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="tr_TR">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/ada-co-os-logo-transparent.svg') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #07070e;
            color: #ccc;
            min-height: 100vh;
        }
        a { color: #10b981; text-decoration: none; }
        a:hover { text-decoration: underline; }
        h1, h2, h3 { color: #f0f0f5; }
        ul { margin-left: 20px; margin-bottom: 12px; }
        li { margin-bottom: 6px; }

        /* ── Simple top bar ────── */
        .public-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 32px;
            background: rgba(7,7,14,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .public-nav a.brand { color: #f0f0f5; font-weight: 700; font-size: 15px; }
        .public-nav a.back { color: #888; font-size: 13px; }
        .public-nav a.back:hover { color: #f0f0f5; }
    </style>
</head>
<body>
    <nav class="public-nav">
        <a href="/" class="brand">ADA Co-OS</a>
        <a href="/" class="back">← Ana Sayfa</a>
    </nav>

    @yield('content')
    <x-cookie-banner />
</body>
</html>
