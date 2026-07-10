<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teklif Önizleme - {{ $proposal->proposal_number }}</title>
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <style>
        :root {
            --primary: {{ $tenant->primary_color ?? '#10b981' }};
            --primary-rgb: 16, 185, 129;
            --bg-page: #f3f4f6;
            --bg-card: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --text-tertiary: #9ca3af;
            --border: #e5e7eb;
            --action-bar-bg: rgba(15, 23, 42, 0.9);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 14px;
            padding-top: 80px;
            padding-bottom: 60px;
        }

        /* Top Action Bar */
        .action-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background-color: var(--action-bar-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .bar-title {
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            height: 38px;
            padding: 0 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .btn-outline {
            background-color: transparent;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-outline:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #ef4444;
            color: #ffffff;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        /* Proposal Sheet Layout */
        .proposal-container {
            max-width: 850px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .proposal-sheet {
            background-color: var(--bg-card);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            padding: 50px;
            position: relative;
            overflow: hidden;
        }

        .brand-strip {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background-color: var(--primary);
        }

        /* Header block */
        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--border);
            padding-bottom: 30px;
            margin-bottom: 40px;
        }

        .logo-container {
            max-width: 300px;
        }

        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .logo-subtitle {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header-meta {
            text-align: right;
        }

        .doc-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .doc-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--primary);
            font-weight: 600;
            margin-top: 4px;
        }

        /* Meta details grid */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .meta-card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-tertiary);
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .meta-item {
            display: flex;
            margin-bottom: 8px;
        }

        .meta-label {
            width: 120px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .meta-val {
            color: var(--text-primary);
            flex: 1;
        }

        /* Items Table */
        .items-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 16px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f9fafb;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-secondary);
            padding: 12px 16px;
            text-align: left;
            border-bottom: 2px solid var(--border);
        }

        .items-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
            vertical-align: top;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
        }

        /* Financial summary block */
        .summary-block {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .summary-table {
            width: 320px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 8px 16px;
            font-size: 13px;
        }

        .summary-table tr.total-row {
            border-top: 2px solid var(--border);
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .summary-table tr.total-row td {
            padding-top: 16px;
            color: var(--primary);
        }

        /* Notes and terms */
        .notes-section {
            margin-bottom: 30px;
        }

        .notes-title {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .notes-content {
            font-size: 13px;
            color: var(--text-secondary);
            white-space: pre-wrap;
            background-color: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        /* Footer */
        .sheet-footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            text-align: center;
            font-size: 11px;
            color: var(--text-tertiary);
        }

        /* Print styles optimization */
        @media print {
            body {
                padding-top: 0;
                padding-bottom: 0;
                background-color: #ffffff;
            }

            .action-bar {
                display: none !important;
            }

            .proposal-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar -->
    <div class="action-bar">
        <div class="bar-title">
            <span class="material-symbols-outlined" style="color: var(--primary);">description</span>
            Teklif Önizleme Ekranı
        </div>
        <div class="action-buttons">
            <a href="/admin/proposal?proposal_id={{ $proposal->id }}" class="btn btn-outline">
                <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                Düzenle
            </a>
            <a href="/admin/reports/proposal/{{ $proposal->id }}?t={{ time() }}" class="btn btn-outline">
                <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
                PDF İndir
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <span class="material-symbols-outlined" style="font-size: 18px;">print</span>
                Yazdır / Farklı Kaydet
            </button>
            <a href="/admin/proposals" class="btn btn-danger">
                <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
                Kapat
            </a>
        </div>
    </div>

    <!-- Proposal Container -->
    <div class="proposal-container">
        <div class="proposal-sheet">
            <div class="brand-strip"></div>

            <!-- Header -->
            <div class="sheet-header">
                <div class="logo-container">
                    @if($logoRaw)
                        <div style="height: 48px; max-width: 250px; display: inline-block;">
                            {!! $logoRaw !!}
                        </div>
                    @elseif($logoUrl)
                        <img src="{{ $logoUrl }}" style="height: 48px; max-width: 250px; object-fit: contain;" alt="Logo">
                    @else
                        <div class="logo-text">{{ $tenant->name ?? 'ADA Co-OS' }}</div>
                        <div class="logo-subtitle">{{ $tenant ? $tenant->name . ' Kurumsal Yönetim Platformu' : 'Kurumsal Yönetim Platformu' }}</div>
                    @endif
                </div>
                <div class="header-meta">
                    <div class="doc-title">Hizmet Fiyat Teklifi</div>
                    <div class="doc-number">{{ $proposal->proposal_number }}</div>
                </div>
            </div>

            <!-- Meta Details -->
            <div class="details-grid">
                <div>
                    <div class="meta-card-title">Müşteri Bilgileri</div>
                    <div class="meta-item">
                        <div class="meta-label">Müşteri / Kurum</div>
                        <div class="meta-val"><strong>{{ $proposal->client->name ?? '—' }}</strong></div>
                    </div>
                    @if($proposal->client->email)
                    <div class="meta-item">
                        <div class="meta-label">E-Posta</div>
                        <div class="meta-val">{{ $proposal->client->email }}</div>
                    </div>
                    @endif
                    @if($proposal->client->phone)
                    <div class="meta-item">
                        <div class="meta-label">Telefon</div>
                        <div class="meta-val">{{ $proposal->client->phone }}</div>
                    </div>
                    @endif
                </div>
                <div>
                    <div class="meta-card-title">Teklif Detayları</div>
                    <div class="meta-item">
                        <div class="meta-label">Teklif Başlığı</div>
                        <div class="meta-val">{{ $proposal->title }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Hazırlayan</div>
                        <div class="meta-val">{{ $proposal->creator->name ?? 'Sistem' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Geçerlilik Tarihi</div>
                        <div class="meta-val">{{ $proposal->valid_until ? $proposal->valid_until->format('d.m.Y') : '—' }}</div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="items-section-title">Hizmet ve Ürün Kalemleri</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Açıklama</th>
                        <th style="width: 80px; text-align: center;">Miktar</th>
                        <th style="width: 140px; text-align: right;">Birim Fiyat</th>
                        <th style="width: 90px; text-align: center;">KDV</th>
                        <th style="width: 160px; text-align: right;">Toplam (KDV Dahil)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposal->items ?? [] as $item)
                        @php
                            $qty = (float)($item['quantity'] ?? 0);
                            $price = (float)($item['unit_price'] ?? 0);
                            $vat = (float)($item['vat_rate'] ?? 0);
                            $total = $qty * $price * (1 + ($vat/100));
                        @endphp
                        <tr>
                            <td>{{ $item['description'] }}</td>
                            <td style="text-align: center;" class="font-mono">{{ $qty }}</td>
                            <td style="text-align: right;" class="font-mono">{{ number_format($price, 2, ',', '.') }} {{ $proposal->currency }}</td>
                            <td style="text-align: center;" class="font-mono">%{{ $vat }}</td>
                            <td style="text-align: right; font-weight: 600;" class="font-mono">{{ number_format($total, 2, ',', '.') }} {{ $proposal->currency }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-tertiary); padding: 30px;">Hizmet kalemi bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Financial Summary -->
            <div class="summary-block">
                <table class="summary-table">
                    <tr>
                        <td style="color: var(--text-secondary);">Ara Toplam</td>
                        <td style="text-align: right;" class="font-mono">{{ number_format($proposal->subtotal, 2, ',', '.') }} {{ $proposal->currency }}</td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-secondary);">KDV Toplamı</td>
                        <td style="text-align: right;" class="font-mono">{{ number_format($proposal->tax_total, 2, ',', '.') }} {{ $proposal->currency }}</td>
                    </tr>
                    @if($proposal->discount_total > 0)
                    <tr>
                        <td style="color: #ef4444;">İndirim</td>
                        <td style="text-align: right; color: #ef4444;" class="font-mono">-{{ number_format($proposal->discount_total, 2, ',', '.') }} {{ $proposal->currency }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Genel Toplam</td>
                        <td style="text-align: right;" class="font-mono">
                            @php
                                $symbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
                                $sym = $symbols[$proposal->currency] ?? $proposal->currency;
                            @endphp
                            {{ $sym }}{{ number_format($proposal->grand_total, 2, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Notes & Terms -->
            @if($proposal->notes)
                <div class="notes-section">
                    <div class="notes-title">Teklif Notları</div>
                    <div class="notes-content">{{ $proposal->notes }}</div>
                </div>
            @endif

            @if($proposal->terms)
                <div class="notes-section">
                    <div class="notes-title">Şartlar & Koşullar</div>
                    <div class="notes-content" style="font-size: 12px; color: var(--text-secondary);">{{ $proposal->terms }}</div>
                </div>
            @endif

            <!-- Footer -->
            <div class="sheet-footer">
                Bu teklif {{ $proposal->tenant->name ?? 'ADA Co-OS' }} sistemi üzerinden dijital olarak oluşturulmuştur.
                <br>© {{ date('Y') }} {{ $proposal->tenant->name ?? 'Ada Creative Co.' }} — Tüm hakları saklıdır.
            </div>

        </div>
    </div>

</body>
</html>
