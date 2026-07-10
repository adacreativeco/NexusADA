<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/ada-co-os-logo-transparent.svg') }}">
    <title>ADA Co-OS — Digital Intelligence Platform</title>
    <meta name="description" content="Pazarlama, Kurumsal İletişim, Proje Yönetimi ve Medya operasyonlarınızı güçlü bir dijital zeka platformuyla birleştirin.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ================================================================
           ADA CO-OS — Shared Design Tokens (from nexus-admin.css)
           Same DNA as admin panel: same colors, fonts, spacing
           ================================================================ */
        :root {
            /* Backgrounds — identical to --nx-bg-* */
            --bg-base: #0a0a0f;
            --bg-card: #111118;
            --bg-elevated: #1a1a24;
            --bg-hover: #1e1e2a;

            /* Borders — identical to --nx-border-* */
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.15);
            --border-active: rgba(16, 185, 129, 0.5);

            /* Text — identical to --nx-text-* */
            --text-primary: #f0f0f5;
            --text-secondary: #8888a0;
            --text-muted: #555568;

            /* Accent — identical to --nx-accent */
            --accent: #10b981;
            --accent-hover: #34d399;
            --accent-glow: rgba(16, 185, 129, 0.15);
            --accent-glow-strong: rgba(16, 185, 129, 0.25);

            /* Semantic — identical to --nx-* */
            --info: #3b82f6;
            --warning: #f59e0b;
            --danger: #ef4444;

            /* Typography — identical to --nx-font-* */
            --font-ui: 'Inter', system-ui, -apple-system, sans-serif;

            /* Radius — identical to --nx-radius-* */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;

            /* Shadows — identical to --nx-shadow-* */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.4);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.5);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.6);

            /* Transitions — identical to --nx-transition-* */
            --transition-fast: 100ms ease;
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
            position: absolute; left: 50%; transform: translateX(-50%);
        }
        .nav-pill-slider {
            position: absolute; top: 3px; bottom: 3px;
            border-radius: 100px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.06);
            transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 0;
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
        .nav-pill a:hover {
            color: var(--text-primary);
        }
        .nav-pill a.active {
            color: var(--text-primary);
        }

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
            position: relative;
            overflow: hidden;
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
            text-align: center;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 900px; height: 500px;
            background: radial-gradient(ellipse at center, var(--accent-glow) 0%, transparent 70%);
            pointer-events: none;
        }

        .version-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 6px 16px; border-radius: 100px;
            background: var(--bg-card); border: 1px solid var(--border);
            font-size: 12px; font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 32px;
        }
        .version-badge .dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent);
            animation: pulse-dot 2s ease infinite;
        }
        .version-badge .ver { color: var(--accent); font-weight: 600; }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .hero h1 {
            font-size: clamp(2.8rem, 5.5vw, 4.2rem);
            font-weight: 800; line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 24px;
        }
        .hero h1 .highlight {
            color: var(--accent);
        }
        .hero-desc {
            font-size: 1.05rem; line-height: 1.7;
            color: var(--text-secondary);
            max-width: 600px; margin: 0 auto 40px;
        }

        .hero-ctas {
            display: flex; align-items: center;
            justify-content: center; gap: 12px;
            margin-bottom: 80px;
        }
        .btn-lg {
            display: inline-flex; align-items: center;
            padding: 12px 28px; border-radius: var(--radius-md);
            font-size: 14px; font-weight: 600;
            transition: all var(--transition-slow);
            font-family: var(--font-ui);
            cursor: pointer;
        }
        .btn-lg.primary {
            background: var(--accent); color: #fff; border: none;
        }
        .btn-lg.primary:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 24px var(--accent-glow-strong);
            transform: translateY(-1px);
        }
        .btn-lg.secondary {
            background: transparent; color: var(--text-primary);
            border: 1px solid var(--border);
        }
        .btn-lg.secondary:hover {
            border-color: var(--border-hover);
            background: var(--bg-hover);
        }

        /* ── STATS BAR ───────────────────────────────────── */
        .stats-bar {
            max-width: 860px; margin: 0 auto 100px;
            display: grid; grid-template-columns: repeat(4, 1fr);
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        .stat-item {
            padding: 28px 20px; text-align: center;
            border-right: 1px solid var(--border);
            transition: background var(--transition);
        }
        .stat-item:last-child { border-right: none; }
        .stat-item:hover { background: var(--bg-hover); }
        .stat-value {
            font-size: 1.8rem; font-weight: 800;
            color: var(--accent); letter-spacing: -0.02em;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 12px; font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* ── FEATURES ────────────────────────────────────── */
        .features-section {
            max-width: 1100px; margin: 0 auto;
            padding: 0 40px 120px;
        }
        .section-header {
            text-align: center; margin-bottom: 56px;
        }
        .section-tag {
            font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: var(--accent); margin-bottom: 12px;
        }
        .section-header h2 {
            font-size: 2.2rem; font-weight: 800;
            letter-spacing: -0.02em; margin-bottom: 16px;
        }
        .section-header p {
            font-size: 1rem; color: var(--text-secondary);
            max-width: 520px; margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px; text-align: center;
            transition: all var(--transition-slow);
            position: relative; overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            opacity: 0; transition: opacity var(--transition-slow);
        }
        .feature-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 56px; height: 56px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            border-radius: var(--radius-lg);
            background: var(--bg-elevated);
            border: 1px solid var(--border);
        }
        .feature-icon svg {
            width: 26px; height: 26px;
            color: var(--accent);
        }
        .feature-card h3 {
            font-size: 1.05rem; font-weight: 700;
            margin-bottom: 8px;
        }
        .feature-card p {
            font-size: 13.5px; line-height: 1.65;
            color: var(--text-secondary);
        }

        /* ── CTA ─────────────────────────────────────────── */
        .cta-section {
            max-width: 800px; margin: 0 auto;
            padding: 0 40px 120px; text-align: center;
        }
        .cta-box {
            padding: 64px 48px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            position: relative; overflow: hidden;
        }
        .cta-box::before {
            content: '';
            position: absolute; top: -1px; left: 20%; right: 20%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
        }
        .cta-box::after {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 400px; height: 200px;
            background: radial-gradient(ellipse, var(--accent-glow) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-box h2 {
            font-size: 2rem; font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 16px; position: relative;
        }
        .cta-box p {
            font-size: 15px; color: var(--text-secondary);
            max-width: 480px; margin: 0 auto 32px;
            position: relative;
        }
        .cta-buttons {
            display: flex; align-items: center;
            justify-content: center; gap: 12px;
            position: relative;
        }

        /* ── COMPARISON (Biz vs Diğerleri) ──────────────── */
        .compare-section {
            max-width: 900px; margin: 0 auto;
            padding: 0 40px 120px;
        }
        .compare-table {
            width: 100%; border-collapse: separate; border-spacing: 0;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        .compare-table th, .compare-table td {
            padding: 16px 24px;
            text-align: center; font-size: 13.5px;
            border-bottom: 1px solid var(--border);
        }
        .compare-table th {
            background: var(--bg-elevated);
            font-weight: 700; font-size: 11px;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--text-secondary);
        }
        .compare-table th:first-child, .compare-table td:first-child {
            text-align: left; font-weight: 500;
        }
        .compare-table th.us-col {
            color: var(--accent); font-size: 12px;
        }
        .compare-table tr:last-child td { border-bottom: none; }
        .compare-table tr:hover td { background: rgba(255,255,255,0.02); }
        .cmp-yes { color: var(--accent); font-weight: 600; }
        .cmp-no { color: var(--text-muted); }
        .cmp-partial { color: #f59e0b; }
        .cmp-best {
            color: var(--accent); font-weight: 700;
            position: relative;
        }
        .cmp-best::after {
            content: '★';
            position: absolute; top: -2px; right: -14px;
            font-size: 9px;
        }

        /* ── PRICING ────────────────────────────────────── */
        .pricing-section {
            max-width: 1100px; margin: 0 auto;
            padding: 0 40px 120px;
        }
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px; align-items: start;
        }
        .pricing-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 36px 28px;
            transition: all var(--transition-slow);
            position: relative;
        }
        .pricing-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        .pricing-card.featured {
            border-color: var(--accent);
            box-shadow: 0 0 40px var(--accent-glow), 0 8px 32px rgba(0,0,0,0.4);
        }
        .pricing-card.featured::before {
            content: 'Önerilen';
            position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            padding: 4px 16px; border-radius: 100px;
            background: var(--accent); color: #fff;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .pricing-label {
            font-size: 13px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--text-secondary); margin-bottom: 8px;
        }
        .pricing-price {
            font-size: 2.4rem; font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 4px;
        }
        .pricing-price span {
            font-size: 14px; font-weight: 500;
            color: var(--text-secondary);
        }
        .pricing-desc {
            font-size: 13.5px; color: var(--text-secondary);
            margin-bottom: 24px; line-height: 1.5;
        }
        .pricing-features {
            list-style: none; margin-bottom: 28px;
        }
        .pricing-features li {
            font-size: 13px; color: var(--text-primary);
            padding: 6px 0;
            display: flex; align-items: center; gap: 8px;
        }
        .pricing-features li::before {
            content: '✓'; color: var(--accent);
            font-weight: 700; font-size: 14px;
            flex-shrink: 0;
        }
        .pricing-btn {
            display: block; text-align: center;
            padding: 12px; border-radius: var(--radius-md);
            font-size: 14px; font-weight: 600;
            transition: all 0.3s ease;
            font-family: var(--font-ui);
        }
        .pricing-btn.outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-primary);
        }
        .pricing-btn.outline:hover {
            border-color: var(--border-hover);
            background: var(--bg-hover);
        }
        .pricing-btn.solid {
            background: var(--accent); color: #fff;
            border: none;
        }
        .pricing-btn.solid:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 20px var(--accent-glow-strong);
        }

        /* ── FAQ ────────────────────────────────────────── */
        .faq-section {
            max-width: 760px; margin: 0 auto;
            padding: 0 40px 120px;
        }
        .faq-item {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            margin-bottom: 8px;
            overflow: hidden;
            transition: border-color var(--transition);
        }
        .faq-item:hover { border-color: var(--border-hover); }
        .faq-item.open { border-color: rgba(16,185,129,0.3); }
        .faq-question {
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            background: var(--bg-card);
            cursor: pointer; user-select: none;
            transition: background var(--transition);
        }
        .faq-question:hover { background: var(--bg-hover); }
        .faq-question h3 {
            font-size: 14.5px; font-weight: 600;
            color: var(--text-primary);
        }
        .faq-question .faq-icon {
            width: 20px; height: 20px; flex-shrink: 0;
            color: var(--text-muted);
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), color 0.2s;
        }
        .faq-item.open .faq-icon {
            transform: rotate(45deg);
            color: var(--accent);
        }
        .faq-answer {
            max-height: 0; overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        .faq-answer-inner {
            padding: 0 24px 20px;
            font-size: 13.5px; line-height: 1.7;
            color: var(--text-secondary);
        }

        /* ── FOOTER ──────────────────────────────────────── */
        .footer {
            padding: 0;
            border-top: 1px solid var(--border);
            background: rgba(8, 8, 14, 0.6);
        }
        .footer-main {
            max-width: 1200px; margin: 0 auto;
            padding: 64px 40px 40px;
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 48px;
        }
        .footer-brand p {
            font-size: 13px; color: var(--text-muted);
            line-height: 1.7; margin: 16px 0 24px;
            max-width: 280px;
        }
        .footer-social {
            display: flex; gap: 12px;
        }
        .footer-social a {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            transition: all 0.2s;
        }
        .footer-social a:hover {
            background: var(--accent-bg);
            border-color: var(--accent);
            color: var(--accent);
            transform: translateY(-2px);
        }
        .footer-col h4 {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--text-secondary); margin-bottom: 20px;
        }
        .footer-col a {
            display: block; font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            padding: 5px 0;
            transition: color 0.15s;
        }
        .footer-col a:hover {
            color: var(--accent);
        }
        .footer-bottom {
            max-width: 1200px; margin: 0 auto;
            padding: 24px 40px;
            border-top: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
        }
        .footer-copy {
            font-size: 12px; color: var(--text-muted);
        }
        .footer-copy strong {
            color: var(--accent); font-weight: 600;
        }
        .footer-legal {
            display: flex; gap: 20px;
        }
        .footer-legal a {
            font-size: 12px; color: var(--text-muted);
            text-decoration: none; transition: color 0.15s;
        }
        .footer-legal a:hover { color: var(--text-secondary); }

        @media (max-width: 768px) {
            .footer-main { grid-template-columns: 1fr 1fr; gap: 32px; padding: 40px 24px 32px; }
            .footer-bottom { padding: 20px 24px; flex-direction: column; text-align: center; }
            .footer-legal { justify-content: center; }
        }
        @media (max-width: 480px) {
            .footer-main { grid-template-columns: 1fr; }
        }

        /* ── RESPONSIVE ──────────────────────────────────── */
        @media (max-width: 900px) {
            .features-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; }
            .nav-pill { display: none; }
            .hero { padding: 120px 20px 60px; }
            .hero-ctas { flex-direction: column; }
            .stats-bar { grid-template-columns: repeat(2, 1fr); }
            .stat-item:nth-child(2) { border-right: none; }
            .features-section { padding: 0 20px 80px; }
            .features-grid { grid-template-columns: 1fr; }
            .compare-section { padding: 0 20px 80px; }
            .compare-table th, .compare-table td { padding: 10px 12px; font-size: 12px; }
            .pricing-section { padding: 0 20px 80px; }
            .pricing-grid { grid-template-columns: 1fr; }
            .faq-section { padding: 0 20px 80px; }
            .cta-section { padding: 0 20px 80px; }
            .cta-box { padding: 40px 24px; }
        }

        /* ── SCROLL REVEAL ───────────────────────────────── */
        .reveal {
            opacity: 0; transform: translateY(16px);
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.visible {
            opacity: 1; transform: translateY(0);
        }
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
            <a href="/" class="active" data-nav>Ana Sayfa</a>
            <a href="#features" data-nav>Özellikler</a>
            <a href="#pricing" data-nav>Fiyatlandırma</a>
            <a href="/platforms" data-nav>Platformlar</a>
        </div>
        <div class="nav-right">
            <a href="{{ url('/admin/login') }}" class="btn-ghost">Giriş Yap</a>
            <a href="{{ url('/admin') }}" class="btn-accent">Panele Git →</a>
        </div>
    </nav>

    <!-- ═══ HERO ═══ -->
    <section class="hero">
        <div class="version-badge">
            <span class="dot"></span>
            <span class="ver">v{{ config('app.version') }}</span> — {{ config('app.version_note') }}
        </div>
        <h1>
            Kurumsal Zekânızı<br>
            <span class="highlight">Tek Noktadan</span> Yönetin.
        </h1>
        <p class="hero-desc">
            Pazarlama, Kurumsal İletişim, Proje Yönetimi ve Medya operasyonlarınızı güçlü bir dijital zeka platformuyla birleştirin. Web, Masaüstü ve Mobil — her yerden erişin.
        </p>
        <div class="hero-ctas">
            <a href="{{ url('/admin/login') }}" class="btn-lg primary">Paneli Keşfet →</a>
            <a href="/platforms" class="btn-lg secondary">Platformları İncele</a>
        </div>
    </section>

    <!-- ═══ STATS BAR ═══ -->
    <div class="stats-bar reveal">
        <div class="stat-item">
            <div class="stat-value">6+</div>
            <div class="stat-label">Entegre Modül</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">3</div>
            <div class="stat-label">Platform (Web · Desktop · Mobile)</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">∞</div>
            <div class="stat-label">Proje & Kampanya</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">PDF</div>
            <div class="stat-label">Tek Tıkla Rapor</div>
        </div>
    </div>

    <!-- ═══ FEATURES ═══ -->
    <section class="features-section" id="features">
        <div class="section-header reveal">
            <div class="section-tag">Modüller</div>
            <h2>Her Departmana Özel Güç</h2>
            <p>ADA Co-OS, kurumsal operasyonlarınızı birbirine bağlayan modüler bir yapıya sahiptir.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                </div>
                <h3>Gelişmiş Görselleştirme</h3>
                <p>Proje gelirleri, kampanya bütçeleri ve medya duyarlılığını interaktif grafiklerle takip edin.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </div>
                <h3>E-posta Senkronizasyonu</h3>
                <p>IMAP üzerinden Gmail ve Outlook hesaplarınızı bağlayın, tüm iletişimi merkeze taşıyın.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                </div>
                <h3>İçerik Takvimi</h3>
                <p>Kampanyalarınızı ve etkinliklerinizi sürükle-bırak destekli tam ekran takvim ile planlayın.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                </div>
                <h3>PDF Raporlama</h3>
                <p>Projelerin detaylı ve markalı raporlarını tek tıkla indirin, paydaşlarla paylaşın.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
                </div>
                <h3>Masaüstü Uygulama</h3>
                <p>Windows ve Linux için özel masaüstü uygulaması. Tarayıcıdan bağımsız, yerel bildirimlerle.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <h3>Kurumsal Güvenlik</h3>
                <p>RBAC yetkilendirme, Audit Trail denetim kaydı ve tam erişim kontrolü.</p>
            </div>
        </div>
    </section>

    <!-- ═══ COMPARISON: BIZ vs DİĞERLERİ ═══ -->
    <section class="compare-section" id="compare">
        <div class="section-header reveal">
            <div class="section-tag">Karşılaştırma</div>
            <h2>Neden ADA Co-OS?</h2>
            <p>Gerçek bir kurumsal zeka platformu ile dağınık araçlar arasındaki fark.</p>
        </div>
        <table class="compare-table reveal">
            <thead>
                <tr>
                    <th>Özellik</th>
                    <th>Dağınık Araçlar</th>
                    <th>Rakip Paneller</th>
                    <th class="us-col">ADA Co-OS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Tüm departmanlar tek panelde</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-partial">Kısmi</td>
                    <td class="cmp-best">✓</td>
                </tr>
                <tr>
                    <td>E-posta senkronizasyonu (IMAP)</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-best">✓</td>
                </tr>
                <tr>
                    <td>PDF rapor motoru</td>
                    <td class="cmp-partial">Eklenti</td>
                    <td class="cmp-partial">Limitli</td>
                    <td class="cmp-best">✓ Tek tık</td>
                </tr>
                <tr>
                    <td>Masaüstü uygulaması</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-best">✓ Windows + Linux</td>
                </tr>
                <tr>
                    <td>Medya analizi & duyarlılık</td>
                    <td class="cmp-partial">Ayrı araç</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-best">✓ Entegre</td>
                </tr>
                <tr>
                    <td>Audit trail & RBAC</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-partial">Temel</td>
                    <td class="cmp-best">✓ Tam yetki</td>
                </tr>
                <tr>
                    <td>Kendi sunucunuzda barındırma</td>
                    <td class="cmp-no">✗</td>
                    <td class="cmp-partial">Bazıları</td>
                    <td class="cmp-best">✓ Self-hosted</td>
                </tr>
                <tr>
                    <td>Fiyat</td>
                    <td class="cmp-no">$50–200/ay per tool</td>
                    <td class="cmp-partial">$99–499/ay</td>
                    <td class="cmp-best">Sabit lisans</td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- ═══ PRICING ═══ -->
    <section class="pricing-section" id="pricing">
        <div class="section-header reveal">
            <div class="section-tag">Fiyatlandırma</div>
            <h2>İhtiyacınıza Göre Seçin</h2>
            <p>Her ölçekte kurumsal güç. Gizli maliyet yok, sürpriz yok.</p>
        </div>
        <div class="pricing-grid">
            <!-- Starter -->
            <div class="pricing-card reveal">
                <div class="pricing-label">Başlangıç</div>
                <div class="pricing-price">Ücretsiz</div>
                <div class="pricing-desc">Tek kullanıcı için temel modüller. Hemen başlayın.</div>
                <ul class="pricing-features">
                    <li>1 kullanıcı</li>
                    <li>3 proje limiti</li>
                    <li>Temel raporlama</li>
                    <li>Web erişimi</li>
                    <li>Topluluk desteği</li>
                </ul>
                <a href="{{ url('/admin/login') }}" class="pricing-btn outline">Hemen Başla</a>
            </div>
            <!-- Pro -->
            <div class="pricing-card featured reveal">
                <div class="pricing-label">Profesyonel</div>
                <div class="pricing-price">₺2.490 <span>/ ay</span></div>
                <div class="pricing-desc">Büyüyen ekipler için tüm modüller ve öncelikli destek.</div>
                <ul class="pricing-features">
                    <li>10 kullanıcıya kadar</li>
                    <li>Sınırsız proje & kampanya</li>
                    <li>PDF rapor motoru</li>
                    <li>E-posta senkronizasyonu</li>
                    <li>Masaüstü uygulaması</li>
                    <li>Öncelikli e-posta desteği</li>
                </ul>
                <a href="{{ url('/admin/login') }}" class="pricing-btn solid">14 Gün Ücretsiz Dene</a>
            </div>
            <!-- Enterprise -->
            <div class="pricing-card reveal">
                <div class="pricing-label">Kurumsal</div>
                <div class="pricing-price">Özel <span>teklif</span></div>
                <div class="pricing-desc">Büyük organizasyonlar için özel kurulum ve SLA garantisi.</div>
                <ul class="pricing-features">
                    <li>Sınırsız kullanıcı</li>
                    <li>Self-hosted kurulum</li>
                    <li>Audit trail & RBAC</li>
                    <li>API erişimi</li>
                    <li>Özel entegrasyonlar</li>
                    <li>Tahsisli hesap yöneticisi</li>
                </ul>
                <a href="mailto:hello@adacreative.co" class="pricing-btn outline">İletişime Geç</a>
            </div>
        </div>
    </section>

    <!-- ═══ FAQ ═══ -->
    <section class="faq-section" id="faq">
        <div class="section-header reveal">
            <div class="section-tag">Sık Sorulan Sorular</div>
            <h2>Merak Edilenler</h2>
        </div>
        <div class="faq-list">
            <div class="faq-item reveal">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open'); const a=this.nextElementSibling; a.style.maxHeight=a.style.maxHeight?null:a.scrollHeight+'px'">
                    <h3>ADA Co-OS nedir?</h3>
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div class="faq-answer"><div class="faq-answer-inner">
                    ADA Co-OS, pazarlama, kurumsal iletişim, proje yönetimi ve medya operasyonlarını tek bir panelde birleştiren kurumsal dijital zeka platformudur. Tüm departmanlarınızı bağlar, verilerinizi analiz eder ve raporlarınızı otomatikleştirir.
                </div></div>
            </div>
            <div class="faq-item reveal">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open'); const a=this.nextElementSibling; a.style.maxHeight=a.style.maxHeight?null:a.scrollHeight+'px'">
                    <h3>Mevcut e-posta hesaplarımı bağlayabilir miyim?</h3>
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div class="faq-answer"><div class="faq-answer-inner">
                    Evet! Gmail ve Outlook hesaplarınızı IMAP üzerinden bağlayabilirsiniz. Tüm gelen postalarınız otomatik olarak ADA Co-OS'ya senkronize edilir ve merkezi gelen kutunuzdan yönetebilirsiniz.
                </div></div>
            </div>
            <div class="faq-item reveal">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open'); const a=this.nextElementSibling; a.style.maxHeight=a.style.maxHeight?null:a.scrollHeight+'px'">
                    <h3>Verilerim güvende mi?</h3>
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div class="faq-answer"><div class="faq-answer-inner">
                    Kesinlikle. ADA Co-OS self-hosted olarak çalışır — verileriniz kendi sunucunuzda kalır. RBAC (rol tabanlı erişim kontrolü) ve Audit Trail ile her işlem kaydedilir ve denetlenebilir.
                </div></div>
            </div>
            <div class="faq-item reveal">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open'); const a=this.nextElementSibling; a.style.maxHeight=a.style.maxHeight?null:a.scrollHeight+'px'">
                    <h3>Masaüstü uygulaması neler sunuyor?</h3>
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div class="faq-answer"><div class="faq-answer-inner">
                    Masaüstü uygulaması Windows ve Linux'ta çalışır. Tarayıcıdan bağımsız çalışır, yerel sistem bildirimleri gönderir ve tam ekran bağımsız pencere deneyimi sunar.
                </div></div>
            </div>
            <div class="faq-item reveal">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open'); const a=this.nextElementSibling; a.style.maxHeight=a.style.maxHeight?null:a.scrollHeight+'px'">
                    <h3>Deneme süreci var mı?</h3>
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div class="faq-answer"><div class="faq-answer-inner">
                    Evet, Profesyonel plan 14 gün ücretsiz deneme içerir. Kredi kartı gerekmez. Başlangıç planı ise her zaman ücretsizdir.
                </div></div>
            </div>
            <div class="faq-item reveal">
                <div class="faq-question" onclick="this.parentElement.classList.toggle('open'); const a=this.nextElementSibling; a.style.maxHeight=a.style.maxHeight?null:a.scrollHeight+'px'">
                    <h3>Hangi tarayıcılar destekleniyor?</h3>
                    <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div class="faq-answer"><div class="faq-answer-inner">
                    Chrome, Firefox, Safari ve Edge'in güncel sürümleri tam olarak desteklenir. Mobil tarayıcılarda da PWA olarak çalışır.
                </div></div>
            </div>
        </div>
    </section>

    <!-- ═══ CTA ═══ -->
    <section class="cta-section">
        <div class="cta-box reveal">
            <h2>Dijital Zekânızı Yükseltin</h2>
            <p>ADA Co-OS ile kurumsal hafızanızı kalıcı hale getirin. Hemen keşfedin veya masaüstü uygulamasını indirin.</p>
            <div class="cta-buttons">
                <a href="{{ url('/admin/login') }}" class="btn-lg primary">Hemen Başla →</a>
                <a href="/platforms" class="btn-lg secondary">Uygulamayı İndir</a>
            </div>
        </div>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer class="footer">
        <div class="footer-main">
            <!-- Brand Column -->
            <div class="footer-brand">
                <img src="{{ asset('images/nexus-ada-logo.svg') }}" alt="ADA Co-OS" style="height: 28px; width: auto;">
                <p>Pazarlama, kurumsal iletişim, proje yönetimi ve medya operasyonlarınızı tek bir dijital zeka platformunda birleştirin.</p>
                <div class="footer-social">
                    <a href="https://linkedin.com/company/adacreativeco" target="_blank" rel="noopener" aria-label="LinkedIn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="https://x.com/adacreativeco" target="_blank" rel="noopener" aria-label="X (Twitter)">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://github.com/adacreativeco" target="_blank" rel="noopener" aria-label="GitHub">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                    </a>
                    <a href="https://instagram.com/adacreativeco" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Platform Column -->
            <div class="footer-col">
                <h4>Platform</h4>
                <a href="/#features">Özellikler</a>
                <a href="/platforms">Platformlar</a>
                <a href="/#pricing">Fiyatlandırma</a>
                <a href="/#faq">Sıkça Sorulanlar</a>
            </div>

            <!-- Product Column -->
            <div class="footer-col">
                <h4>Ürün</h4>
                <a href="/admin">Yönetim Paneli</a>
                <a href="/#comparison">Karşılaştırma</a>
                <a href="#">API Dökümanları</a>
                <a href="#">Güncellemeler</a>
            </div>

            <!-- Company Column -->
            <div class="footer-col">
                <h4>Şirket</h4>
                <a href="https://adacreative.co" target="_blank">ADA Creative Co.</a>
                <a href="https://adacreative.co/about" target="_blank">Hakkımızda</a>
                <a href="https://adacreative.co/contact" target="_blank">İletişim</a>
                <a href="https://adacreative.co/blog" target="_blank">Blog</a>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <div class="footer-copy">© {{ date('Y') }} ADA Co-OS — crafted by <strong>ADA Creative Co.</strong></div>
            <div class="footer-legal">
                <a href="#">Gizlilik Politikası</a>
                <a href="#">Kullanım Koşulları</a>
                <a href="#">KVKK</a>
            </div>
        </div>
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

        /* ── Smooth Scroll for Anchors ─────────────── */
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const t = document.querySelector(a.getAttribute('href'));
                if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

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

            // Initial position
            if (activeLink) {
                requestAnimationFrame(() => {
                    slider.style.transition = 'none';
                    moveSlider(activeLink);
                    requestAnimationFrame(() => {
                        slider.style.transition = '';
                    });
                });
            }

            // Hover: slide to hovered item
            links.forEach(link => {
                link.addEventListener('mouseenter', () => moveSlider(link));
            });

            // Mouse leave: slide back to active
            pill.addEventListener('mouseleave', () => {
                if (activeLink) moveSlider(activeLink);
            });

            // Click: set new active
            links.forEach(link => {
                link.addEventListener('click', () => {
                    links.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    activeLink = link;
                    moveSlider(link);
                });
            });

            // Auto-highlight based on scroll position
            const sections = [
                { id: 'features', linkHref: '#features' },
                { id: 'pricing', linkHref: '#pricing' }
            ];
            const homeLink = pill.querySelector('[href="/"]');

            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const sectionId = entry.target.id;
                        const match = sections.find(s => s.id === sectionId);
                        if (match) {
                            const link = pill.querySelector(`[href="${match.linkHref}"]`);
                            if (link) {
                                links.forEach(l => l.classList.remove('active'));
                                link.classList.add('active');
                                activeLink = link;
                                moveSlider(link);
                            }
                        }
                    }
                });
            }, { threshold: 0.3 });

            sections.forEach(s => {
                const el = document.getElementById(s.id);
                if (el) sectionObserver.observe(el);
            });

            // Scroll to top = Ana Sayfa
            window.addEventListener('scroll', () => {
                if (window.scrollY < 200 && homeLink) {
                    links.forEach(l => l.classList.remove('active'));
                    homeLink.classList.add('active');
                    activeLink = homeLink;
                    moveSlider(homeLink);
                }
            }, { passive: true });

            // Re-calc on resize
            window.addEventListener('resize', () => {
                if (activeLink) moveSlider(activeLink);
            });
        })();
    </script>
</body>
</html>
