<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/ada-co-os-logo-transparent.svg') }}">
    <title>ADA Co-OS — Platformlar | Web · Masaüstü · Mobil</title>
    <meta name="description" content="ADA Co-OS'ya Web, Masaüstü ve Mobil'den erişin. İhtiyacınıza en uygun platformu seçin.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ================================================================
           ADA CO-OS — Shared Design Tokens (from nexus-admin.css)
           Same DNA as admin panel: same colors, fonts, spacing
           ================================================================ */
        :root {
            --bg-base: #0a0a0f;
            --bg-card: #111118;
            --bg-elevated: #1a1a24;
            --bg-hover: #1e1e2a;
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.15);
            --border-active: rgba(16, 185, 129, 0.5);
            --text-primary: #f0f0f5;
            --text-secondary: #8888a0;
            --text-muted: #555568;
            --accent: #10b981;
            --accent-hover: #34d399;
            --accent-glow: rgba(16, 185, 129, 0.15);
            --accent-glow-strong: rgba(16, 185, 129, 0.25);
            --info: #3b82f6;
            --warning: #f59e0b;
            --danger: #ef4444;
            --font-ui: 'Inter', system-ui, -apple-system, sans-serif;
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.6);
            --transition: 150ms ease;
            --transition-slow: 300ms ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-ui);
            background: var(--bg-base);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a { text-decoration: none; color: inherit; }

        /* ── NAVBAR ──────────────────────────────────────── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 14px 32px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .navbar::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent 10%, var(--accent) 50%, transparent 90%);
            opacity: 0.4;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            position: relative; z-index: 2;
        }
        .nav-brand img {
            height: 28px; width: auto;
        }
        .nav-brand-text {
            font-weight: 700; font-size: 16px;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }
        .nav-brand-text span {
            color: var(--accent); font-weight: 800;
        }
        .nav-pill {
            position: absolute; left: 50%; transform: translateX(-50%);
            display: flex; align-items: center; gap: 0;
            padding: 3px;
            background: rgba(17, 17, 24, 0.9);
            backdrop-filter: blur(24px) saturate(1.4);
            -webkit-backdrop-filter: blur(24px) saturate(1.4);
            border: 1px solid var(--border);
            border-radius: 100px;
        }
        .nav-pill-slider {
            position: absolute; top: 3px; bottom: 3px;
            border-radius: 100px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.06);
            transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none; z-index: 0;
        }
        .nav-pill a {
            padding: 6px 18px; border-radius: 100px;
            font-size: 13px; font-weight: 500;
            color: var(--text-secondary);
            transition: color 0.25s ease;
            white-space: nowrap;
            position: relative; z-index: 1;
            user-select: none;
        }
        .nav-pill a:hover { color: var(--text-primary); }
        .nav-pill a.active { color: var(--text-primary); }
        .nav-right {
            display: flex; align-items: center; gap: 10px;
            position: relative; z-index: 2;
        }
        .btn-ghost {
            padding: 8px 18px; border-radius: 100px;
            font-size: 13px; font-weight: 500;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            background: rgba(17, 17, 24, 0.6);
            backdrop-filter: blur(12px);
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: var(--font-ui);
        }
        .btn-ghost:hover {
            color: var(--text-primary);
            border-color: var(--border-hover);
            background: rgba(255,255,255,0.06);
        }
        .btn-accent {
            padding: 8px 20px; border-radius: 100px;
            font-size: 13px; font-weight: 600;
            background: var(--accent); color: #fff;
            border: none; cursor: pointer;
            transition: all 0.3s ease;
            font-family: var(--font-ui);
            position: relative; overflow: hidden;
        }
        .btn-accent::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 50%);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-accent:hover {
            background: var(--accent-hover);
            box-shadow: 0 0 24px var(--accent-glow-strong), 0 2px 8px rgba(0,0,0,0.3);
            transform: translateY(-1px);
        }
        .btn-accent:hover::before { opacity: 1; }

        /* ── HERO ────────────────────────────────────────── */
        .hero {
            padding: 160px 40px 80px;
            text-align: center; position: relative;
        }
        .hero::before {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 900px; height: 500px;
            background: radial-gradient(ellipse at center, var(--accent-glow) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 3.8rem);
            font-weight: 800; line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 24px;
        }
        .hero h1 .highlight { color: var(--accent); }
        .hero-desc {
            font-size: 1.05rem; color: var(--text-secondary);
            max-width: 620px; margin: 0 auto 40px;
            line-height: 1.7;
        }

        /* ── PLATFORM CARDS ─────────────────────────────── */
        .platforms-grid {
            max-width: 1100px; margin: 0 auto;
            padding: 0 40px 100px;
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .platform-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px 32px;
            text-align: center;
            transition: all var(--transition-slow);
            display: flex; flex-direction: column;
        }
        .platform-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .platform-icon {
            width: 64px; height: 64px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            border-radius: var(--radius-lg);
            background: var(--bg-elevated);
            border: 1px solid var(--border);
        }
        .platform-icon svg {
            width: 30px; height: 30px;
            color: var(--accent);
        }

        .platform-tag {
            display: inline-block;
            padding: 4px 14px; border-radius: 100px;
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            background: var(--accent-glow);
            color: var(--accent);
            margin-bottom: 16px;
        }
        .platform-card h3 {
            font-size: 1.3rem; font-weight: 700;
            margin-bottom: 10px;
        }
        .platform-card .desc {
            font-size: 14px; color: var(--text-secondary);
            margin-bottom: 24px; flex-grow: 1;
            line-height: 1.6;
        }

        .feature-list {
            list-style: none; margin-bottom: 28px;
            text-align: left;
        }
        .feature-list li {
            font-size: 13.5px; color: var(--text-primary);
            padding: 8px 0;
            display: flex; align-items: center; gap: 10px;
        }
        .feature-list li + li {
            border-top: 1px solid var(--border);
        }
        .feature-list .check {
            width: 18px; height: 18px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            background: var(--accent-glow);
        }
        .feature-list .check svg {
            width: 12px; height: 12px; color: var(--accent);
        }

        .platform-btn {
            display: block; text-align: center;
            padding: 12px; border-radius: var(--radius-md);
            font-size: 14px; font-weight: 600;
            background: var(--accent); color: #fff;
            border: none; transition: all var(--transition-slow);
            font-family: var(--font-ui);
        }
        .platform-btn:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 20px var(--accent-glow-strong);
        }

        /* ── HOW IT WORKS ───────────────────────────────── */
        .how-section {
            max-width: 800px; margin: 0 auto;
            padding: 80px 40px;
        }
        .section-tag {
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: var(--accent); margin-bottom: 12px;
        }
        .how-section h2 {
            font-size: 2rem; font-weight: 800;
            letter-spacing: -0.02em; margin-bottom: 16px;
        }
        .how-section > p {
            font-size: 1rem; color: var(--text-secondary);
            margin-bottom: 40px;
        }
        .steps {
            display: flex; flex-direction: column; gap: 12px;
        }
        .step {
            display: flex; align-items: center; gap: 20px;
            padding: 24px; border-radius: var(--radius-lg);
            background: var(--bg-card);
            border: 1px solid var(--border);
            transition: border-color var(--transition);
        }
        .step:hover { border-color: var(--border-hover); }
        .step-num {
            width: 40px; height: 40px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--radius-md);
            background: var(--accent-glow);
            font-size: 16px; font-weight: 800;
            color: var(--accent);
        }
        .step h3 { font-size: 15px; font-weight: 600; margin-bottom: 2px; }
        .step p { font-size: 13px; color: var(--text-secondary); }

        /* ── COMPARISON TABLE ───────────────────────────── */
        .comparison-section {
            max-width: 800px; margin: 0 auto;
            padding: 0 40px 100px;
        }
        .comparison-section h2 {
            font-size: 2rem; font-weight: 800;
            letter-spacing: -0.02em; margin-bottom: 32px;
        }
        .comp-table {
            width: 100%; border-collapse: separate; border-spacing: 0;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        .comp-table th, .comp-table td {
            padding: 14px 20px;
            text-align: center; font-size: 13px;
            border-bottom: 1px solid var(--border);
        }
        .comp-table th {
            background: var(--bg-elevated);
            font-weight: 700; color: var(--text-secondary);
            text-transform: uppercase; letter-spacing: 0.05em;
            font-size: 11px;
        }
        .comp-table th:first-child, .comp-table td:first-child {
            text-align: left; font-weight: 500;
        }
        .comp-table tr:last-child td { border-bottom: none; }
        .comp-yes { color: var(--accent); }
        .comp-no { color: var(--text-muted); }
        .comp-partial { color: var(--warning); }

        /* ── FOOTER ──────────────────────────────────────── */
        .footer {
            padding: 40px; text-align: center;
            border-top: 1px solid var(--border);
        }
        .footer-tagline {
            font-size: 14px; font-weight: 600;
            color: var(--accent); margin-bottom: 8px;
            font-style: italic;
        }
        .footer-copy {
            font-size: 12px; color: var(--text-muted);
        }
        .footer-copy strong {
            color: var(--text-secondary); font-weight: 600;
        }

        /* ── RESPONSIVE ──────────────────────────────────── */
        @media (max-width: 768px) {
            .navbar { padding: 10px 16px; }
            .nav-pill { display: none; }
            .hero { padding: 120px 20px 60px; }
            .platforms-grid { grid-template-columns: 1fr; padding: 0 20px 60px; }
            .how-section, .comparison-section { padding-left: 20px; padding-right: 20px; }
            .comp-table th, .comp-table td { padding: 10px 12px; font-size: 12px; }
        }

        .reveal {
            opacity: 0; transform: translateY(16px);
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

    <!-- ═══ NAVBAR ═══ -->
    <nav class="navbar">
        <a href="/" class="nav-brand">
            <img src="{{ asset('images/nexus-ada-logo.svg') }}" alt="ADA Co-OS" style="height: 32px; width: auto;">
        </a>
        <div class="nav-pill" id="navPill">
            <div class="nav-pill-slider" id="navSlider"></div>
            <a href="/" data-nav>Ana Sayfa</a>
            <a href="/#features" data-nav>Özellikler</a>
            <a href="/platforms" class="active" data-nav>Platformlar</a>
        </div>
        <div class="nav-right">
            <a href="{{ url('/admin/login') }}" class="btn-ghost">Giriş Yap</a>
            <a href="{{ url('/admin') }}" class="btn-accent">Panele Git →</a>
        </div>
    </nav>

    <!-- ═══ HERO ═══ -->
    <section class="hero">
        <h1>Her Yerde, <span class="highlight">Her Zaman</span> Erişin.</h1>
        <p class="hero-desc">
            ADA Co-OS'ya web tarayıcınızdan, masaüstü uygulamasından veya cebinizdeki akıllı telefondan ulaşın. Aynı güçlü deneyim, istediğiniz platformda.
        </p>
    </section>

    <!-- ═══ PLATFORM CARDS ═══ -->
    <section class="platforms-grid" id="platforms">
        <!-- WEB -->
        <div class="platform-card reveal">
            <div class="platform-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
            </div>
            <span class="platform-tag">Web Platform</span>
            <h3>Web Tarayıcı</h3>
            <p class="desc">Herhangi bir modern tarayıcıdan doğrudan erişin. Kurulum gerektirmez.</p>
            <ul class="feature-list">
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Chrome, Firefox, Safari, Edge desteği</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Anlık erişim — kurulum yok</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Her zaman en güncel sürüm</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Responsive tasarım</li>
            </ul>
            <a href="{{ url('/admin/login') }}" class="platform-btn">Tarayıcıda Aç →</a>
        </div>

        <!-- DESKTOP -->
        <div class="platform-card reveal">
            <div class="platform-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
            </div>
            <span class="platform-tag">Masaüstü Uygulama</span>
            <h3>Masaüstü (Desktop)</h3>
            <p class="desc">Windows ve Linux için özel masaüstü uygulaması. Tarayıcıdan bağımsız çalışır.</p>
            <ul class="feature-list">
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Windows (.exe) desteği</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Linux (.AppImage) desteği</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Yerel sistem bildirimleri</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Bağımsız pencere deneyimi</li>
            </ul>
            <a href="/downloads/Nexus-ADA-1.0.0-setup.exe" class="platform-btn">Windows İçin İndir ↓</a>
        </div>

        <!-- MOBILE -->
        <div class="platform-card reveal">
            <div class="platform-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>
            </div>
            <span class="platform-tag">Mobil Uygulama</span>
            <h3>Mobil (PWA)</h3>
            <p class="desc">Telefonunuzun ana ekranına ekleyin. Uygulama mağazası gerektirmez.</p>
            <ul class="feature-list">
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> iOS & Android desteği</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Ana ekrana ekle özelliği</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Offline erişim altyapısı</li>
                <li><span class="check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span> Uygulama hissi (tam ekran)</li>
            </ul>
            <a href="{{ url('/admin') }}" class="platform-btn">Mobilde Aç →</a>
        </div>
    </section>

    <!-- ═══ HOW IT WORKS ═══ -->
    <section class="how-section">
        <div class="section-tag reveal">Nasıl Çalışır?</div>
        <h2 class="reveal">3 Adımda Başlayın</h2>
        <p class="reveal">Hangi platformu seçerseniz seçin, dakikalar içinde operasyonlarınızı yönetmeye başlayın.</p>
        <div class="steps">
            <div class="step reveal">
                <div class="step-num">1</div>
                <div>
                    <h3>Platformunuzu Seçin</h3>
                    <p>Web tarayıcınızı açın, masaüstü uygulamasını indirin veya telefonunuzun ana ekranına ekleyin.</p>
                </div>
            </div>
            <div class="step reveal">
                <div class="step-num">2</div>
                <div>
                    <h3>Giriş Yapın</h3>
                    <p>Yöneticinizin size verdiği hesap bilgileriyle panele giriş yapın. Tüm modüller sizi bekliyor.</p>
                </div>
            </div>
            <div class="step reveal">
                <div class="step-num">3</div>
                <div>
                    <h3>Operasyonlarınızı Yönetin</h3>
                    <p>Projelerinizi takip edin, kampanyalarınızı planlayın, raporlarınızı indirin. ADA Co-OS her şeyi bağlar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ COMPARISON TABLE ═══ -->
    <section class="comparison-section" id="comparison">
        <div class="section-tag reveal">Karşılaştırma</div>
        <h2 class="reveal">Platform Özellikleri</h2>
        <table class="comp-table reveal">
            <thead>
                <tr>
                    <th>Özellik</th>
                    <th>Web</th>
                    <th>Masaüstü</th>
                    <th>Mobil</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Tam Panel Erişimi</td><td class="comp-yes">✓</td><td class="comp-yes">✓</td><td class="comp-yes">✓</td></tr>
                <tr><td>Kurulum Gerektirmez</td><td class="comp-yes">✓</td><td class="comp-no">✗</td><td class="comp-yes">✓</td></tr>
                <tr><td>Sistem Bildirimleri</td><td class="comp-no">✗</td><td class="comp-yes">✓</td><td class="comp-partial">Kısmi</td></tr>
                <tr><td>Offline Erişim</td><td class="comp-no">✗</td><td class="comp-partial">Kısmi</td><td class="comp-yes">✓</td></tr>
                <tr><td>Otomatik Güncelleme</td><td class="comp-yes">✓</td><td class="comp-partial">Kısmi</td><td class="comp-yes">✓</td></tr>
                <tr><td>PDF Raporlama</td><td class="comp-yes">✓</td><td class="comp-yes">✓</td><td class="comp-yes">✓</td></tr>
                <tr><td>E-posta Senkronizasyonu</td><td class="comp-yes">✓</td><td class="comp-yes">✓</td><td class="comp-yes">✓</td></tr>
            </tbody>
        </table>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer class="footer">
        <div class="footer-tagline">"Bağlar. Hatırlatır. Analiz Eder."</div>
        <div class="footer-copy">© {{ date('Y') }} ADA Co-OS — made by <strong>ADA Creative Co.</strong> #elevatewithADA</div>
    </footer>

    <script>
        /* ── Scroll Reveal ─────────────────────────── */
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        /* ── Nav Pill Sliding Toggle ───────────────── */
        (function() {
            const pill = document.getElementById('navPill');
            const slider = document.getElementById('navSlider');
            const links = pill.querySelectorAll('[data-nav]');
            let activeLink = pill.querySelector('.active');

            function moveSlider(target) {
                const pillRect = pill.getBoundingClientRect();
                const targetRect = target.getBoundingClientRect();
                slider.style.left = (targetRect.left - pillRect.left) + 'px';
                slider.style.width = targetRect.width + 'px';
            }

            if (activeLink) {
                requestAnimationFrame(() => {
                    slider.style.transition = 'none';
                    moveSlider(activeLink);
                    requestAnimationFrame(() => { slider.style.transition = ''; });
                });
            }

            links.forEach(link => {
                link.addEventListener('mouseenter', () => moveSlider(link));
            });

            pill.addEventListener('mouseleave', () => {
                if (activeLink) moveSlider(activeLink);
            });

            links.forEach(link => {
                link.addEventListener('click', () => {
                    links.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    activeLink = link;
                    moveSlider(link);
                });
            });

            window.addEventListener('resize', () => {
                if (activeLink) moveSlider(activeLink);
            });
        })();
    </script>
</body>
</html>
