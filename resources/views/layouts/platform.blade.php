<!DOCTYPE html>
<html lang="tr" class="nx-admin">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/ada-co-os-logo-transparent.svg') }}?v=2">
    <title>@yield('title', 'Platform') — Nexus Webmaster</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('head')
    @vite(['resources/css/nexus-admin.css', 'resources/js/nexus-admin.js'])

    {{-- Platform badge stili (sadece bu) --}}
    <style>
        .platform-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 6px;
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            background: var(--nx-badge-success-bg);
            color: var(--nx-badge-success-text);
        }
    </style>
    @livewireStyles
</head>
<body class="nx-admin">
<div class="nx-layout" id="nx-app">
    <script>(function(){if(localStorage.getItem('nx-sidebar-collapsed')==='true'){document.getElementById('nx-app').classList.add('sidebar-collapsed');}})();</script>

    {{-- ═══════════ SIDEBAR ═══════════ --}}
    <aside class="nx-sidebar" id="nx-sidebar">
        <script>(function(){if(localStorage.getItem('nx-sidebar-collapsed')==='true'){document.getElementById('nx-sidebar').classList.add('collapsed');}})();</script>
        {{-- Brand --}}
        <div class="nx-sidebar-brand" style="display: flex; align-items: center; justify-content: flex-start; padding: 0 16px; gap: 10px; height: 64px; border-bottom: 1px solid var(--nx-border);">
            <a href="{{ route('platform.dashboard') }}" wire:navigate class="flex items-center" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                <img src="{{ asset('images/ada-co-os-logo-transparent.svg') }}" alt="ADA Co-OS" style="width: 36px; height: 36px; flex-shrink: 0;">
                <span class="nx-brand-text" style="font-family: var(--nx-font-heading); font-weight: 800; font-size: 18px; background: linear-gradient(135deg, #10b981, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; white-space: nowrap;">Co-OS</span>
            </a>
            <span class="platform-badge" style="font-size: 8px; padding: 2px 6px; margin-left: auto;">Webmaster</span>
        </div>

        {{-- Navigation --}}
        <nav class="nx-sidebar-nav" style="flex: 1; overflow-y: auto; padding: 12px 0; display: flex; flex-direction: column; gap: 4px;">
            {{-- Dashboard --}}
            <div class="nx-nav-group">
                <a href="{{ route('platform.dashboard') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.dashboard') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>

            {{-- Tenant Management --}}
            <div class="nx-nav-group">
                <div class="nx-nav-group-label">Kiracı Yönetimi</div>
                <a href="{{ route('platform.tenants') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.tenants') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                    <span>Kiracılar</span>
                </a>
                <a href="{{ route('platform.plans') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.plans') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                    </svg>
                    <span>Planlar</span>
                </a>
                <a href="{{ route('platform.invoices') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.invoices') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                    </svg>
                    <span>Faturalar</span>
                </a>
                <a href="{{ route('platform.access-requests') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.access-requests') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                    <span>Erken Erişim</span>
                </a>
            </div>

            {{-- System --}}
            <div class="nx-nav-group">
                <div class="nx-nav-group-label">Sistem</div>
                <a href="{{ route('platform.team') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.team') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                    </svg>
                    <span>Ekip Yönetimi</span>
                </a>
                <a href="{{ route('platform.announcements') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.announcements') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46"/>
                    </svg>
                    <span>Duyurular</span>
                </a>
                <a href="{{ route('platform.system-log') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.system-log') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z"/>
                    </svg>
                    <span>Sistem Logları</span>
                </a>
                <a href="{{ route('platform.usage-stats') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.usage-stats') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                    <span>Kullanım Metrikleri</span>
                </a>
                <a href="/telescope" target="_blank" class="nx-nav-item">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0 1 12 12.75ZM12 12.75c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 0 1-1.152 6.135M12 12.75c-2.883 0-5.647.508-8.207 1.44a23.91 23.91 0 0 0 1.152 6.135m13.111-7.575c.132-.484.24-.978.325-1.48a23.93 23.93 0 0 0-13.361 0c.085.502.193.996.325 1.48M12 2.25c.966 0 1.75.784 1.75 1.75S12.966 5.75 12 5.75 10.25 4.966 10.25 4s.784-1.75 1.75-1.75Z"/>
                    </svg>
                    <span>Telescope</span>
                </a>
                <a href="{{ route('platform.settings') }}"
                   class="nx-nav-item {{ request()->routeIs('platform.settings') ? 'active' : '' }}">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                    <span>Ayarlar</span>
                </a>
            </div>

            {{-- Quick Link to Admin --}}
            <div class="nx-nav-group">
                <div class="nx-nav-group-label">Hızlı Geçiş</div>
                <a href="{{ route('admin.dashboard') }}" class="nx-nav-item">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    <span>Admin Paneli →</span>
                </a>
            </div>
        </nav>

        {{-- Sidebar Footer --}}
        <div class="nx-sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}" style="width: 100%;">
                @csrf
                <button type="submit" class="nx-nav-item" style="width: 100%; border: none; background: none; font-family: var(--nx-font-ui);">
                    <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                    </svg>
                    <span>Çıkış Yap</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════ MAIN CONTENT ═══════════ --}}
    <main class="nx-main">
        {{-- Topbar --}}
        <header class="nx-topbar">
            <div class="nx-breadcrumb">
                <a href="{{ route('platform.dashboard') }}">Platform</a>
                <span class="separator">/</span>
                <span class="current">@yield('breadcrumb', 'Dashboard')</span>
            </div>

            <div class="nx-topbar-actions">
                <div class="platform-badge" style="margin-right: 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                    </svg>
                    WEBMASTER
                </div>
                <div class="nx-user-menu" x-data="{ open: false }" @click="open = !open" style="position: relative;">
                    <div class="nx-user-avatar">
                        {{ substr(auth()->user()->name ?? 'W', 0, 1) }}
                    </div>
                    <div x-show="open" @click.away="open = false"
                         style="position: absolute; top: 100%; right: 0; margin-top: 8px; width: 180px; background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-md); padding: 4px; z-index: 50;">
                        <div style="padding: 8px 12px; font-size: 13px; color: var(--nx-text-secondary); border-bottom: 1px solid var(--nx-border); margin-bottom: 4px;">
                            {{ auth()->user()->name ?? 'Webmaster' }}
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="nx-nav-item" style="text-decoration: none;">
                            <span>Admin Paneli</span>
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="nx-nav-item" style="width: 100%; border: none; background: none; font-family: var(--nx-font-ui);">
                                <span>Çıkış Yap</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="nx-content">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
