<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Müşteri Portalı' }} — ADA Co-OS</title>
    {{-- Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Geist:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #34d399;
            --primary-glow: rgba(16, 185, 129, 0.2);
            --bg: #070b19;
            --card: rgba(20, 27, 46, 0.7);
            --border: rgba(42, 53, 80, 0.6);
            --text: #e8eaf6;
            --text-secondary: #9ca3af;
            --radius: 16px;
            --transition: all 0.3s ease;
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at center, #0e172a 0%, var(--bg) 100%); 
            color: var(--text); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .login-card { 
            background: var(--card); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border); 
            border-radius: var(--radius); 
            padding: 40px; 
            width: 100%; 
            max-width: 420px; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            animation: cardEntrance 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-align: center;
        }

        .login-card-brand {
            margin-bottom: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 14px;
            color: var(--primary);
            font-size: 28px;
            font-weight: 700;
        }

        .login-card h1 { 
            font-family: 'Geist', sans-serif;
            font-size: 24px; 
            font-weight: 800; 
            margin-bottom: 6px; 
            letter-spacing: -0.02em;
            color: #fff;
        }

        .login-card p { 
            font-size: 13px; 
            color: var(--text-secondary); 
            margin-bottom: 30px; 
            line-height: 1.4;
        }

        .form-group { 
            margin-bottom: 20px; 
            text-align: left;
        }

        .form-group label { 
            display: block; 
            font-size: 11px; 
            font-weight: 600; 
            color: var(--text-secondary); 
            margin-bottom: 8px; 
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-group input { 
            width: 100%; 
            padding: 12px 16px; 
            background: rgba(10, 15, 30, 0.6); 
            border: 1px solid var(--border); 
            border-radius: 10px; 
            color: #fff; 
            font-size: 14px; 
            font-family: 'Inter', sans-serif; 
            transition: var(--transition);
        }

        .form-group input:focus { 
            outline: none; 
            border-color: var(--primary); 
            box-shadow: 0 0 0 3px var(--primary-glow);
            background: rgba(10, 15, 30, 0.8); 
        }

        .btn-primary { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #10b981, #059669); 
            color: #fff; 
            border: none; 
            border-radius: 10px; 
            font-size: 14px; 
            font-weight: 700; 
            cursor: pointer; 
            font-family: 'Inter', sans-serif; 
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover { 
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .error { 
            color: #ef4444; 
            font-size: 12px; 
            margin-top: 6px; 
            display: block;
            font-weight: 500;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(20px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body>
    {{ $slot }}
</body>
</html>
