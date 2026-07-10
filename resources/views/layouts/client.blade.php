<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Portal' }} — ADA Co-OS</title>
    {{-- Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Geist:wght@300;400;500;600;700;800;900&family=Geist+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#10b981">
    
    <style>
        /* Modern Glassmorphic Dark Design System */
        :root {
            --primary: #10b981;
            --primary-hover: #34d399;
            --primary-glow: rgba(16, 185, 129, 0.15);
            --bg: #070b19;
            --card: rgba(20, 27, 46, 0.65);
            --card-hover: rgba(26, 37, 64, 0.8);
            --border: rgba(42, 53, 80, 0.5);
            --border-hover: rgba(60, 75, 112, 0.8);
            --text: #e8eaf6;
            --text-secondary: #9ca3af;
            --text-muted: #6b7280;
            --radius-lg: 16px;
            --radius-md: 10px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body { 
            font-family: 'Inter', var(--nx-font-ui), sans-serif; 
            background: radial-gradient(circle at top left, #0e172a, var(--bg)); 
            color: var(--text); 
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Topbar styling */
        .client-topbar { 
            background: rgba(19, 27, 46, 0.8); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border); 
            padding: 0 32px; 
            height: 64px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            position: sticky; 
            top: 0; 
            z-index: 50; 
            transition: var(--transition);
        }

        .client-topbar-brand { 
            font-family: 'Geist', sans-serif;
            font-size: 18px; 
            font-weight: 800; 
            background: linear-gradient(135deg, #10b981, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.03em;
        }

        .client-topbar-nav { 
            display: flex; 
            gap: 8px; 
        }

        .client-topbar-nav a { 
            padding: 8px 18px; 
            font-size: 13px; 
            font-weight: 600; 
            color: var(--text-secondary); 
            text-decoration: none; 
            border-radius: var(--radius-md); 
            transition: var(--transition); 
        }

        .client-topbar-nav a:hover { 
            color: var(--text); 
            background: rgba(255, 255, 255, 0.03); 
        }

        .client-topbar-nav a.active { 
            color: #fff; 
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.25);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.05);
        }

        .client-topbar-user { 
            display: flex; 
            align-items: center; 
            gap: 16px; 
        }

        .client-topbar-user span { 
            font-size: 13px; 
            font-weight: 500;
            color: var(--text-secondary); 
        }

        .client-content { 
            max-width: 1240px; 
            margin: 0 auto; 
            padding: 40px 32px; 
        }

        /* Glassmorphic Cards */
        .client-card { 
            background: var(--card); 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border); 
            border-radius: var(--radius-lg); 
            padding: 24px; 
            transition: var(--transition);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }

        .client-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-hover);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
            background: var(--card-hover);
        }

        /* Premium Buttons */
        .nx-btn { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            gap: 6px; 
            font-weight: 600; 
            font-size: 12px;
            padding: 8px 16px;
            border-radius: var(--radius-md); 
            cursor: pointer; 
            transition: var(--transition); 
            font-family: 'Inter', sans-serif; 
            border: 1px solid transparent; 
        }

        .nx-btn-primary { 
            background: linear-gradient(135deg, #10b981, #059669); 
            color: #fff; 
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
        }

        .nx-btn-primary:hover { 
            opacity: 0.95; 
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        .nx-btn-secondary {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            color: var(--text);
        }

        .nx-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--border-hover);
        }

        .btn-logout { 
            background: transparent; 
            border: 1px solid var(--border); 
            color: var(--text-secondary); 
            padding: 8px 16px; 
            border-radius: var(--radius-md); 
            font-size: 12px; 
            font-weight: 600;
            cursor: pointer; 
            transition: var(--transition);
        }

        .btn-logout:hover { 
            border-color: var(--nx-danger, #ef4444); 
            color: var(--nx-danger, #ef4444); 
            background: rgba(239, 68, 68, 0.05);
        }

        /* Input Styles */
        .nx-input {
            background: rgba(10, 15, 30, 0.6);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 10px 14px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            transition: var(--transition);
            width: 100%;
        }

        .nx-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-glow);
        }
        
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @livewireStyles
</head>
<body>
    <div class="client-topbar">
        <span class="client-topbar-brand">ADA Co-OS Client Portal</span>
        <nav class="client-topbar-nav">
            <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">Panel</a>
            <a href="{{ route('client.projects') }}" class="{{ request()->routeIs('client.projects') ? 'active' : '' }}">Projeler</a>
            <a href="{{ route('client.invoices') }}" class="{{ request()->routeIs('client.invoices') ? 'active' : '' }}">Faturalar</a>
        </nav>
        <div class="client-topbar-user">
            <span>{{ auth('client')->user()->name ?? '' }}</span>
            <form action="{{ route('client.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Çıkış</button>
            </form>
        </div>
    </div>
    <div class="client-content" style="animation: fadeSlideIn 0.4s ease-out;">
        {{ $slot }}
    </div>
    @livewireScripts
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('ADA Client PWA Service Worker Registered', reg))
                    .catch(err => console.error('PWA Registration Error', err));
            });
        }

        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const banner = document.createElement('div');
            banner.id = 'pwa-install-banner';
            banner.style.cssText = 'position: fixed; bottom: 24px; right: 24px; background: rgba(20, 27, 46, 0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid var(--border); padding: 16px 20px; border-radius: 16px; display: flex; align-items: center; gap: 20px; z-index: 99999; box-shadow: 0 10px 40px rgba(0,0,0,0.5); width: 380px; transition: all 0.3s ease;';
            banner.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                    <div style="font-size: 24px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: rgba(16, 185, 129, 0.1); border-radius: 10px; color: #10b981;">📲</div>
                    <div style="text-align: left;">
                        <div style="font-size: 13px; font-weight: 700; color: #fff; font-family: 'Geist', sans-serif;">ADA Co-OS Kur</div>
                        <div style="font-size: 11px; color: #9ca3af; margin-top: 2px; font-family: 'Inter', sans-serif;">Ana ekrana ekleyip anında erişin.</div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button id="pwa-install-btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.2s;">Kur</button>
                    <button onclick="document.getElementById('pwa-install-banner').remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 14px; padding: 4px 8px;">✕</button>
                </div>
            `;
            document.body.appendChild(banner);
            
            document.getElementById('pwa-install-btn').addEventListener('click', () => {
                banner.remove();
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                });
            });
        });
    </script>
</body>
</html>
