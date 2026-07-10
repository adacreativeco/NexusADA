<!DOCTYPE html>
<html lang="tr" class="nx-admin">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/ada-co-os-logo-transparent.svg') }}?v=2">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#10b981">
    <title>@yield('title', 'Dashboard') — ADA Co-OS</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Geist:wght@300;400;500;600;700;800;900&family=Geist+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <script>
        // Initialize theme based on localStorage, default to dark
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.remove('light');
        }
    </script>

    {{-- Heroicons (inline SVG sprite — önemli ikonlar) --}}
    @stack('head')

    @vite(['resources/css/nexus-admin.css', 'resources/js/nexus-admin.js', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="nx-admin {{ session('impersonating_tenant_id') ? 'impersonating' : '' }}">

{{-- ═══════════ IMPERSONATE BAR ═══════════ --}}
@if(session('impersonating_tenant_id'))
<div id="impersonate-bar" style="
    position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white; padding: 8px 24px;
    display: flex; align-items: center; justify-content: space-between;
    font-size: 13px; font-weight: 600; font-family: var(--nx-font-ui, 'Inter', sans-serif);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
">
    <span>
        🔍 <strong>{{ session('impersonating_tenant_name', 'Tenant #' . session('impersonating_tenant_id')) }}</strong> olarak görüntülüyorsunuz
    </span>
    <form method="POST" action="{{ route('platform.impersonate.stop') }}" style="margin:0;">
        @csrf
        <button type="submit" style="
            background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);
            color: white; padding: 4px 14px; border-radius: 6px;
            font-size: 12px; font-weight: 600; cursor: pointer;
            transition: background 0.2s;
        " onmouseover="this.style.background='rgba(255,255,255,0.35)'"
           onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            ✕ Çık
        </button>
    </form>
</div>
<style>
    body.impersonating .nx-sidebar { top: 40px !important; height: calc(100vh - 40px) !important; }
    body.impersonating .nx-main-content { padding-top: 40px !important; }
    body.impersonating .nx-topbar { top: 40px !important; }
</style>
@endif

<div class="nx-layout" id="nx-app">
    <script>(function(){if(localStorage.getItem('nx-sidebar-collapsed')==='true'){document.getElementById('nx-app').classList.add('sidebar-collapsed');}})();</script>

    {{-- ═══════════ SIDEBAR ═══════════ --}}
    <aside class="nx-sidebar" id="nx-sidebar">
        <script>(function(){if(localStorage.getItem('nx-sidebar-collapsed')==='true'){document.getElementById('nx-sidebar').classList.add('collapsed');}})();</script>
        {{-- Brand --}}
        <div class="nx-sidebar-brand" style="display: flex; align-items: center; justify-content: flex-start; padding: 0 16px; gap: 10px; height: 64px;">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                <img src="{{ asset('images/ada-co-os-logo-transparent.svg') }}" alt="ADA Co-OS" style="width: 36px; height: 36px; flex-shrink: 0;">
                <span class="nx-brand-text" style="font-family: var(--nx-font-heading); font-weight: 800; font-size: 18px; background: linear-gradient(135deg, #10b981, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; white-space: nowrap;">Co-OS</span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="nx-sidebar-nav" style="flex: 1; overflow-y: auto; padding: 12px 0; display: flex; flex-direction: column; gap: 4px;">
            {{-- Home --}}
            <a href="{{ route('admin.dashboard') }}" wire:navigate
               class="nx-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined nx-nav-icon">home</span>
                <span class="nx-nav-label">{{ __('Ana Sayfa') }}</span>
                <span class="nx-sidebar-tooltip">{{ __('Ana Sayfa') }}</span>
            </a>

            {{-- Category: CRM & Müşteri --}}
            @if(\App\Models\PlatformSetting::get('cat_settings', true))
                <div class="nx-nav-group-label">{{ __('Müşteri & Kurum') }}</div>
                @if(\App\Models\PlatformSetting::get('mod_clients', true))
                <a href="{{ route('admin.resource.index', 'clients') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/clients*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">group</span>
                    <span class="nx-nav-label">{{ __('Müşteriler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Müşteriler') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_departments', true))
                <a href="{{ route('admin.resource.index', 'departments') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/departments*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">corporate_fare</span>
                    <span class="nx-nav-label">{{ __('Departmanlar') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Departmanlar') }}</span>
                </a>
                @endif
            @endif

            {{-- Category: Operasyonlar --}}
            @if(\App\Models\PlatformSetting::get('cat_project', true))
                <div class="nx-nav-group-label">{{ __('Operasyonlar') }}</div>
                @php
                    $sidebarTasks = \App\Models\Task::where('status', '!=', 'done')->where('due_date', '<', now())->count();
                    $sidebarProposals = \App\Models\Proposal::where('status', 'pending_approval')->count();
                    $sidebarContracts = \App\Models\Contract::where('status', 'pending_approval')->count();
                    $sidebarExpenses = \App\Models\Expense::where('status', 'pending_approval')->count();
                @endphp
                <a href="{{ route('admin.resource.index', 'works') }}" wire:navigate
                   class="nx-nav-item {{ (request()->is('admin/works') || request()->is('admin/works/*/timeline')) ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">work</span>
                    <span class="nx-nav-label">{{ __('İşler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('İşler') }}</span>
                </a>
                <a href="{{ route('admin.works.pipeline') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.works.pipeline') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">splitscreen</span>
                    <span class="nx-nav-label">{{ __('İş Pipeline') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('İş Pipeline') }}</span>
                </a>
                <a href="{{ route('admin.resource.index', 'proposals') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/proposals*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">description</span>
                    @if($sidebarProposals > 0)
                        <span style="position: absolute; top: 2px; right: 2px; background: var(--nx-warning); color: #000; border-radius: 50%; width: 14px; height: 14px; font-size: 9px; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 6px rgba(245, 158, 11, 0.4);">
                            {{ $sidebarProposals }}
                        </span>
                    @endif
                    <span class="nx-nav-label">{{ __('Teklifler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Teklifler') }}</span>
                </a>
                <a href="{{ route('admin.resource.index', 'contracts') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/contracts*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">handshake</span>
                    @if($sidebarContracts > 0)
                        <span style="position: absolute; top: 2px; right: 2px; background: var(--nx-warning); color: #000; border-radius: 50%; width: 14px; height: 14px; font-size: 9px; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 6px rgba(245, 158, 11, 0.4);">
                            {{ $sidebarContracts }}
                        </span>
                    @endif
                    <span class="nx-nav-label">{{ __('Sözleşmeler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Sözleşmeler') }}</span>
                </a>
                @if(\App\Models\PlatformSetting::get('mod_projects', true))
                <a href="{{ route('admin.resource.index', 'projects') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/projects*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">folder</span>
                    <span class="nx-nav-label">{{ __('Projeler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Projeler') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_tasks', true))
                <a href="{{ route('admin.resource.index', 'tasks') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/tasks') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">task_alt</span>
                    @if($sidebarTasks > 0)
                        <span style="position: absolute; top: 2px; right: 2px; background: var(--nx-danger); color: #fff; border-radius: 50%; width: 14px; height: 14px; font-size: 9px; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 6px rgba(239, 68, 68, 0.4);">
                            {{ $sidebarTasks }}
                        </span>
                    @endif
                    <span class="nx-nav-label">{{ __('Görevler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Görevler') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_kanban', true))
                <a href="{{ route('admin.tasks.board') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.tasks.board') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">view_week</span>
                    <span class="nx-nav-label">{{ __('Kanban') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Kanban') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_timesheet', true))
                <a href="{{ route('admin.timesheet') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.timesheet') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">timelapse</span>
                    <span class="nx-nav-label">{{ __('Timesheet') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Timesheet') }}</span>
                </a>
                @endif
                <a href="{{ route('admin.finance') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.finance') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">payments</span>
                    @if($sidebarExpenses > 0)
                        <span style="position: absolute; top: 2px; right: 2px; background: var(--nx-warning); color: #000; border-radius: 50%; width: 14px; height: 14px; font-size: 9px; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 6px rgba(245, 158, 11, 0.4);">
                            {{ $sidebarExpenses }}
                        </span>
                    @endif
                    <span class="nx-nav-label">{{ __('Finans') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Finans') }}</span>
                </a>
            @endif

            {{-- Category: Pazarlama --}}
            @if(\App\Models\PlatformSetting::get('cat_marketing', true))
                <div class="nx-nav-group-label">{{ __('Pazarlama') }}</div>
                @if(\App\Models\PlatformSetting::get('mod_campaigns', true))
                <a href="{{ route('admin.resource.index', 'campaigns') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/campaigns*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">campaign</span>
                    <span class="nx-nav-label">{{ __('Kampanyalar') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Kampanyalar') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_content', true))
                <a href="{{ route('admin.resource.index', 'content-items') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/content-items*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">article</span>
                    <span class="nx-nav-label">{{ __('İçerikler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('İçerikler') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_social', true))
                <a href="{{ route('admin.resource.index', 'social-posts') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/social-posts*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">forum</span>
                    <span class="nx-nav-label">{{ __('Sosyal Medya') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Sosyal Medya') }}</span>
                </a>
                @endif
            @endif

            {{-- Category: İletişim & Takvim --}}
            @if(\App\Models\PlatformSetting::get('cat_media', true))
                <div class="nx-nav-group-label">{{ __('İletişim & Takvim') }}</div>
                @if(\App\Models\PlatformSetting::get('mod_events', true))
                <a href="{{ route('admin.resource.index', 'events') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/events*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">event</span>
                    <span class="nx-nav-label">{{ __('Etkinlikler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Etkinlikler') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_press', true))
                <a href="{{ route('admin.resource.index', 'press-contacts') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/press-contacts*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">contact_page</span>
                    <span class="nx-nav-label">{{ __('Basın') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Basın') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_calendar', true))
                <a href="{{ route('admin.calendar') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">calendar_month</span>
                    <span class="nx-nav-label">{{ __('Takvim') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Takvim') }}</span>
                </a>
                @endif
            @endif

            {{-- Category: İç Araçlar --}}
            @if(\App\Models\PlatformSetting::get('cat_internal', true))
                <div class="nx-nav-group-label">{{ __('İç Araçlar') }}</div>
                @if(\App\Models\PlatformSetting::get('mod_inbox', true))
                <a href="{{ route('admin.resource.index', 'emails') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/emails*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">inbox</span>
                    <span class="nx-nav-label">{{ __('Gelen Kutusu') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Gelen Kutusu') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_tools', true))
                <a href="{{ route('admin.resource.index', 'tools') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/tools*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">build</span>
                    <span class="nx-nav-label">{{ __('Araçlar') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Araçlar') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_brand', true))
                <a href="{{ route('admin.resource.index', 'brand-assets') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/brand-assets*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">palette</span>
                    <span class="nx-nav-label">{{ __('Marka Varlıkları') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Marka Varlıkları') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_emails', true))
                <a href="{{ route('admin.resource.index', 'email-templates') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/email-templates*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">mail_outline</span>
                    <span class="nx-nav-label">{{ __('E-posta Şablonları') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('E-posta Şablonları') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_email_acc', true))
                <a href="{{ route('admin.email-accounts') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.email-accounts') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">alternate_email</span>
                    <span class="nx-nav-label">{{ __('E-posta Hesapları') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('E-posta Hesapları') }}</span>
                </a>
                @endif
                <a href="{{ route('admin.resource.index', 'assets') }}" wire:navigate class="nx-nav-item {{ request()->is('admin/assets*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">folder_special</span>
                    <span class="nx-nav-label">{{ __('Assets') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Assets') }}</span>
                </a>
                <a href="{{ route('admin.resource.index', 'knowledge-articles') }}" wire:navigate class="nx-nav-item {{ request()->is('admin/knowledge-articles*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">menu_book</span>
                    <span class="nx-nav-label">{{ __('Bilgi Bankası') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Bilgi Bankası') }}</span>
                </a>
                <a href="{{ route('admin.resource.index', 'workflows') }}" wire:navigate class="nx-nav-item {{ request()->is('admin/workflows*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">route</span>
                    <span class="nx-nav-label">{{ __('İş Akışları') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('İş Akışları') }}</span>
                </a>
            @endif

            {{-- Category: Sistem --}}
            @if(\App\Models\PlatformSetting::get('cat_system', true))
                <div class="nx-nav-group-label">{{ __('Sistem') }}</div>
                @if(\App\Models\PlatformSetting::get('mod_automations', true))
                <a href="{{ route('admin.resource.index', 'automations') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/automations*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">bolt</span>
                    <span class="nx-nav-label">{{ __('Otomasyonlar') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Otomasyonlar') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_integrations', true))
                <a href="{{ route('admin.resource.index', 'integrations') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/integrations*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">link</span>
                    <span class="nx-nav-label">{{ __('Entegrasyonlar') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Entegrasyonlar') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_proposal', true))
                <a href="{{ route('admin.proposal') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.proposal') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">request_quote</span>
                    <span class="nx-nav-label">{{ __('Teklif Motoru') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Teklif Motoru') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_team', true))
                <a href="{{ route('admin.team') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.team') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">groups</span>
                    <span class="nx-nav-label">{{ __('Ekip') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Ekip') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_2fa', true))
                <a href="{{ route('admin.two-factor') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.two-factor') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">security</span>
                    <span class="nx-nav-label">{{ __('2FA Güvenlik') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('2FA Güvenlik') }}</span>
                </a>
                @endif
                @if(\App\Models\PlatformSetting::get('mod_audit', true))
                <a href="{{ route('admin.audit-log') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
                     <span class="material-symbols-outlined nx-nav-icon">history</span>
                     <span class="nx-nav-label">{{ __('Denetim Kaydı') }}</span>
                     <span class="nx-sidebar-tooltip">{{ __('Denetim Kaydı') }}</span>
                </a>
                @endif
                @php 
                    $sidebarUnread = \App\Models\AppNotification::where('user_id', auth()->id())->unread()->count(); 
                @endphp
                <a href="{{ route('admin.notifications') }}" wire:navigate
                   class="nx-nav-item {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon" style="position: relative;">
                        notifications
                        @if($sidebarUnread > 0)
                            <span style="position: absolute; top: -1px; right: -1px; width: 6px; height: 6px; border-radius: 50%; background: var(--nx-danger);"></span>
                        @endif
                    </span>
                    <span class="nx-nav-label">{{ __('Bildirimler') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Bildirimler') }}</span>
                </a>
                <a href="{{ route('admin.resource.index', 'ai-memories') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/ai-memories*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">psychology</span>
                    <span class="nx-nav-label">{{ __('AI Actions') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('AI Actions') }}</span>
                </a>
                <a href="{{ route('admin.resource.index', 'workflows') }}" wire:navigate
                   class="nx-nav-item {{ request()->is('admin/workflows*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined nx-nav-icon">account_tree</span>
                    <span class="nx-nav-label">{{ __('Workflow') }}</span>
                    <span class="nx-sidebar-tooltip">{{ __('Workflow') }}</span>
                </a>
                @if(auth()->user()->isWebmaster())
                <a href="{{ route('platform.dashboard') }}" wire:navigate
                   class="nx-nav-item" style="border-top: 1px solid var(--nx-border); margin-top: 4px; padding-top: 12px;">
                    <span class="material-symbols-outlined nx-nav-icon" style="color: #8b5cf6;">admin_panel_settings</span>
                    <span class="nx-nav-label" style="color: #8b5cf6;">{{ __('Platform Paneli') }}</span>
                    <span class="nx-sidebar-tooltip" style="color: #8b5cf6;">{{ __('Platform Paneli') }}</span>
                </a>
                @endif
            @endif
        </nav>

        {{-- Sidebar Footer --}}
        <div class="nx-sidebar-footer" style="padding: 8px; border-top: 1px solid var(--nx-border); width: 100%; flex-shrink: 0;">
            <button id="nx-sidebar-toggle" class="nx-nav-item" style="border: none; background: none; width: 100%; cursor: pointer;" title="Menüyü Daralt">
                <span class="material-symbols-outlined nx-nav-icon" style="transform: rotate(0deg); transition: transform 0.3s;">chevron_left</span>
                <span class="nx-nav-label" style="font-size: 13px; font-weight: 500;">{{ __('Menüyü Daralt') }}</span>
            </button>
        </div>
    </aside>

    {{-- ═══════════ MAIN CONTENT ═══════════ --}}
    <main class="nx-main">
        {{-- Topbar --}}
        <header class="nx-topbar">
            <div class="nx-breadcrumb">
                <button class="nx-mobile-toggle" onclick="document.getElementById('nx-sidebar').classList.toggle('active')" style="display: none; background: none; border: none; padding: 4px; color: var(--nx-text-primary); cursor: pointer; margin-right: 8px; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="font-size: 22px;">menu</span>
                </button>
                <a href="{{ route('admin.dashboard') }}">Nexus</a>
                <span class="separator">/</span>
                <span class="current">@yield('breadcrumb', 'Dashboard')</span>
            </div>

            <div class="nx-topbar-actions">
                {{-- Search Trigger --}}
                <button class="nx-search-trigger" onclick="window.dispatchEvent(new CustomEvent('open-command-palette'))">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                    Ara...
                    <kbd>Ctrl+K</kbd>
                </button>

                {{-- Theme Toggle --}}
                <button class="nx-theme-toggle" onclick="toggleTheme()" title="Temayı Değiştir">
                    <span class="material-symbols-outlined theme-icon-dark" style="font-size: 20px;">dark_mode</span>
                    <span class="material-symbols-outlined theme-icon-light" style="font-size: 20px; display: none;">light_mode</span>
                </button>

                <script>
                    function updateThemeIcons() {
                        const isLight = document.documentElement.classList.contains('light');
                        const darkIcon = document.querySelector('.theme-icon-dark');
                        const lightIcon = document.querySelector('.theme-icon-light');
                        if (darkIcon && lightIcon) {
                            darkIcon.style.display = isLight ? 'block' : 'none';
                            lightIcon.style.display = isLight ? 'none' : 'block';
                        }
                    }

                    function toggleTheme() {
                        if (document.documentElement.classList.contains('light')) {
                            document.documentElement.classList.remove('light');
                            localStorage.setItem('theme', 'dark');
                        } else {
                            document.documentElement.classList.add('light');
                            localStorage.setItem('theme', 'light');
                        }
                        updateThemeIcons();
                    }

                    // Run on initial load & layout initialization
                    document.addEventListener('DOMContentLoaded', () => {
                        updateThemeIcons();
                    });
                    // Alpine/Livewire load check
                    if (window.Livewire) {
                        Livewire.hook('element.initialized', () => updateThemeIcons());
                    }
                </script>

                {{-- Notification Bell --}}
                @livewire('admin.notification-bell')

                {{-- User Menu --}}
                <div class="nx-user-menu" x-data="{ open: false }" @click="open = !open" style="position: relative;">
                    <div class="nx-user-avatar">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div x-show="open" @click.away="open = false"
                         style="position: absolute; top: 100%; right: 0; margin-top: 8px; width: 180px; background: var(--nx-bg-card); border: 1px solid var(--nx-border); border-radius: var(--nx-radius-md); padding: 4px; z-index: 50;">
                        <div style="padding: 8px 12px; font-size: 13px; color: var(--nx-text-secondary); border-bottom: 1px solid var(--nx-border); margin-bottom: 4px;">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="nx-nav-item" style="width: 100%; border: none; background: none; font-family: var(--nx-font-ui);">
                                <svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                                </svg>
                                <span>Çıkış Yap</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="nx-content">
            {{-- 2FA Recovery Codes Warning --}}
            @if(auth()->user()->two_factor_enabled && count(auth()->user()->two_factor_recovery_codes ?? []) <= 2)
                <div style="margin-bottom: 24px; padding: 12px 20px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; color: white; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(245,158,11,0.2);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        </div>
                        <div>
                            <span style="font-size: 13px; font-weight: 600;">Güvenlik Uyarısı:</span>
                            <span style="font-size: 13px; opacity: 0.95; margin-left: 4px;">
                                @if(count(auth()->user()->two_factor_recovery_codes ?? []) == 0)
                                    Hiç kurtarma kodunuz kalmadı! Olası bir durumda hesabınıza erişemeyebilirsiniz.
                                @else
                                    Sadece {{ count(auth()->user()->two_factor_recovery_codes ?? []) }} adet kurtarma kodunuz kaldı.
                                @endif
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('admin.two-factor') }}" style="background: white; color: #d97706; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                        Yeni Kodlar Üret
                    </a>
                </div>
            @endif

            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>
</div>

{{-- Command Palette (Ctrl+K) --}}
<div id="nx-command-palette" style="display: none;">
    <div class="nx-command-backdrop" onclick="window.closeCommandPalette()">
        <div class="nx-command-palette" onclick="event.stopPropagation()">
            <input type="text" class="nx-command-input" placeholder="Ara... (müşteri, proje, kampanya)" id="nx-command-search" autofocus>
            <div class="nx-command-results" id="nx-command-results">
                <div class="nx-command-empty">Aramak için yazmaya başlayın</div>
            </div>
        </div>
    </div>
</div>

@livewireScripts
@stack('scripts')

<script>
// ── Global Toast Notification System ──────────────────────────────
(function() {
    // Create toast container
    const toastContainer = document.createElement('div');
    toastContainer.id = 'nx-toast-container';
    toastContainer.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
    document.body.appendChild(toastContainer);

    function showToast(message, type) {
        const toast = document.createElement('div');
        const colors = {
            success: 'background:linear-gradient(135deg,#059669,#10b981);',
            error: 'background:linear-gradient(135deg,#dc2626,#ef4444);',
            info: 'background:linear-gradient(135deg,#2563eb,#3b82f6);'
        };
        toast.style.cssText = (colors[type] || colors.info) + 'color:white;padding:14px 20px;border-radius:10px;font-size:13px;font-weight:500;display:flex;align-items:center;gap:10px;box-shadow:0 8px 30px rgba(0,0,0,0.3);max-width:400px;pointer-events:auto;animation:nxToastIn .3s ease forwards;font-family:Inter,sans-serif;';
        
        const icons = {
            success: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
            error: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>',
            info: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>'
        };
        toast.innerHTML = (icons[type] || icons.info) + '<span>' + message + '</span>';
        toastContainer.appendChild(toast);
        setTimeout(() => { toast.style.animation = 'nxToastOut .3s ease forwards'; setTimeout(() => toast.remove(), 300); }, 4000);
    }

    // Listen for Livewire dispatch('notify') events
    window.addEventListener('notify', e => showToast(e.detail.message, e.detail.type || 'success'));
    // Also listen for Livewire 3 format
    document.addEventListener('livewire:init', () => {
        if (window.Livewire) {
            Livewire.on('notify', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                showToast(d.message, d.type || 'success');
            });
        }
    });

    // Toast animations
    const style = document.createElement('style');
    style.textContent = '@keyframes nxToastIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}} @keyframes nxToastOut{from{opacity:1;transform:translateY(0)}to{opacity:0;transform:translateY(-10px)}} [x-cloak]{display:none!important}';
    document.head.appendChild(style);

    // ── Global wire:confirm Interceptor ────────────────────────────
    if (!window.hasInitializedConfirmInterceptor) {
        window.hasInitializedConfirmInterceptor = true;
        window.isBypassingConfirmInterceptor = false;

        document.addEventListener('click', function(e) {
            if (window.isBypassingConfirmInterceptor) return;

            const btn = e.target.closest('[wire\\:confirm]');
            if (!btn) return;

            const msg = btn.getAttribute('wire:confirm');
            if (!msg) return;

            // Prevent Livewire from processing the click
            e.stopPropagation();
            e.preventDefault();

            // Create modal
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;z-index:99998;display:flex;align-items:center;justify-content:center;animation:nxFadeIn .2s ease;';
            overlay.innerHTML = `
                <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);" onclick="this.parentElement.remove()"></div>
                <div style="position:relative;background:var(--nx-bg-card,#1e1e2e);border:1px solid var(--nx-border,#2e2e3e);border-radius:14px;padding:28px 32px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.5);animation:nxScaleIn .2s ease;">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                        <div style="width:42px;height:42px;border-radius:10px;background:rgba(239,68,68,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        </div>
                        <div>
                            <h3 style="margin:0;font-size:16px;font-weight:600;color:var(--nx-text,#e2e8f0);font-family:Inter,sans-serif;">Onay Gerekli</h3>
                            <p style="margin:4px 0 0;font-size:13px;color:var(--nx-text-muted,#94a3b8);font-family:Inter,sans-serif;">${msg}</p>
                        </div>
                    </div>
                    <p style="font-size:12px;color:var(--nx-text-muted,#94a3b8);margin-bottom:20px;font-family:Inter,sans-serif;">Bu işlem geri alınamaz.</p>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button class="nx-btn nx-btn-secondary" style="font-size:13px;padding:8px 16px;" onclick="this.closest('[style*=fixed]').remove()">Vazgeç</button>
                        <button id="nx-confirm-yes" class="nx-btn" style="font-size:13px;padding:8px 16px;background:linear-gradient(135deg,#dc2626,#ef4444);color:white;border:none;border-radius:8px;cursor:pointer;">Evet, Devam Et</button>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);

            // On confirm, re-trigger the original click with bypassed window.confirm and bypassed interceptor
            overlay.querySelector('#nx-confirm-yes').addEventListener('click', function() {
                overlay.remove();
                
                window.isBypassingConfirmInterceptor = true;
                const originalConfirm = window.confirm;
                window.confirm = () => true;
                
                btn.click();
                
                // Allow event loop to process Livewire/Alpine click handlers before restoring
                setTimeout(() => {
                    window.confirm = originalConfirm;
                    window.isBypassingConfirmInterceptor = false;
                }, 100);
            });
        }, true); // Use capture phase to intercept before Livewire
    }

    // Modal animations
    const style2 = document.createElement('style');
    style2.textContent = '@keyframes nxFadeIn{from{opacity:0}to{opacity:1}} @keyframes nxScaleIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}';
    document.head.appendChild(style2);

    // ── Desktop App Auto-Updater Logic ──────────────────────────────
    if (navigator.userAgent.toLowerCase().indexOf('electron') > -1) {
        const currentVersion = '{{ config('nativephp.version', '1.0.0') }}';
        
        fetch('https://nexus.adacreative.co/api/desktop/version')
            .then(res => res.json())
            .then(data => {
                // Determine if a newer version exists
                const vRemote = data.version.split('.').map(Number);
                const vLocal = currentVersion.split('.').map(Number);
                let hasUpdate = false;
                
                for(let i=0; i<3; i++) {
                    const r = vRemote[i] || 0;
                    const l = vLocal[i] || 0;
                    if (r > l) { hasUpdate = true; break; }
                    if (l > r) { break; }
                }

                if (hasUpdate && data.download_url) {
                    const banner = document.createElement('div');
                    banner.id = 'desktop-update-banner';
                    banner.style.cssText = `
                        position: fixed; top: 0; left: 0; right: 0; z-index: 100000;
                        background: linear-gradient(135deg, #3b82f6, #2563eb);
                        color: white; padding: 10px 24px;
                        display: flex; align-items: center; justify-content: space-between;
                        font-size: 13px; font-weight: 500; font-family: var(--nx-font-ui, 'Inter', sans-serif);
                        box-shadow: 0 4px 12px rgba(37,99,235,0.3);
                        animation: nxFadeIn 0.3s ease;
                    `;
                    
                    let closeBtn = data.is_mandatory ? '' : `
                        <button onclick="document.getElementById('desktop-update-banner').remove()" style="
                            background: rgba(255,255,255,0.1); border: none;
                            color: white; padding: 4px 10px; border-radius: 4px;
                            font-size: 12px; cursor: pointer; transition: all 0.2s; margin-left: 8px;
                        " onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">Sonra</button>
                    `;

                    banner.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            <span>Masaüstü uygulamasının yeni sürümü mevcut <strong>(v${data.version})</strong>. ${data.is_mandatory ? 'Devam etmek için güncellemeniz gerekiyor.' : 'Lütfen güncelleyin.'}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <a href="${data.download_url}" target="_blank" style="
                                background: white; color: #2563eb; border: none; text-decoration: none;
                                padding: 6px 14px; border-radius: 6px; font-weight: 600; font-size: 12px;
                                cursor: pointer; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            " onclick="${data.is_mandatory ? '' : 'document.getElementById("desktop-update-banner").remove()'}">Önceki Sürümü Silip Yenisini İndir</a>
                            ${closeBtn}
                        </div>
                    `;
                    document.body.appendChild(banner);
                    
                    // Add top padding to sidebar and main content if necessary
                    const existingOffset = document.body.classList.contains('impersonating') ? 40 : 0;
                    const style = document.createElement('style');
                    style.innerHTML = `
                        .nx-sidebar { top: ${existingOffset + 44}px !important; height: calc(100vh - ${existingOffset + 44}px) !important; }
                        .nx-main-content { padding-top: ${existingOffset + 44}px !important; }
                        .nx-topbar { top: ${existingOffset + 44}px !important; }
                    `;
                    document.head.appendChild(style);
                }
            })
            .catch(err => console.error('Nexus Update Check Error:', err));
    }
})();
</script>
    <x-cookie-banner />
    <script>
    (function() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('ADA PWA Service Worker Registered', reg))
                    .catch(err => console.error('PWA Registration Error', err));
            });
        }

        window.deferredPrompt = window.deferredPrompt || null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPrompt = e;
            const banner = document.createElement('div');
            banner.id = 'pwa-install-banner';
            banner.style.cssText = 'position: fixed; bottom: 20px; left: 20px; right: 20px; background: rgba(20,20,20,0.85); backdrop-filter: blur(10px); border: 1px solid var(--nx-border, #1e1e1e); padding: 12px 18px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; z-index: 99999; box-shadow: 0 4px 20px rgba(0,0,0,0.4);';
            banner.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">📱</span>
                    <div style="text-align: left;">
                        <div style="font-size: 13px; font-weight: 600; color: #fff;">ADA Co-OS Ana Ekrana Ekle</div>
                        <div style="font-size: 11px; color: #a1a1aa; margin-top: 2px;">Daha hızlı ve pratik erişim için uygulamayı kurun.</div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button id="pwa-install-btn" style="background: #10b981; color: white; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">Kur</button>
                    <button onclick="document.getElementById('pwa-install-banner').remove()" style="background: none; border: none; color: #a1a1aa; cursor: pointer; font-size: 14px; padding: 4px 8px;">✕</button>
                </div>
            `;
            document.body.appendChild(banner);
            
            document.getElementById('pwa-install-btn').addEventListener('click', () => {
                banner.remove();
                if (window.deferredPrompt) {
                    window.deferredPrompt.prompt();
                    window.deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        }
                        window.deferredPrompt = null;
                    });
                }
            });
        });
    })();
    </script>

</body>
</html>
