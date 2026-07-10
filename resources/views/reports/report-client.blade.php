<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; line-height: 1.6; padding: 40px; }
        .info-grid { display: table; width: 100%; margin-bottom: 24px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; padding: 6px 12px 6px 0; font-weight: 700; color: #555; width: 160px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.03em; }
        .info-value { display: table-cell; padding: 6px 0; color: #111; }
        .section-title { font-size: 14px; font-weight: 700; color: #111; margin: 28px 0 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.data th { background: #f9fafb; font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; font-weight: 700; color: #555; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e5e7eb; }
        table.data td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .money { font-family: DejaVu Sans Mono, monospace; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #999; text-align: center; }
        .metric-box { display: inline-block; text-align: center; padding: 12px 20px; border: 1px solid #e5e7eb; border-radius: 8px; margin-right: 8px; margin-bottom: 8px; }
        .metric-value { font-size: 20px; font-weight: 800; color: #10b981; }
        .metric-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 2px; }
        .importance-stars { color: #f59e0b; letter-spacing: 2px; }
    </style>
</head>
<body>
    @include('reports._header', ['reportTitle' => 'Müşteri Özet Raporu', 'generatedAt' => $generatedAt])

    {{-- Müşteri Profili --}}
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Firma Adı</div>
            <div class="info-value" style="font-weight: 700; font-size: 14px;">{{ $client->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Sektör</div>
            <div class="info-value">{{ $client->industry ?? '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Karar Tarzı</div>
            <div class="info-value">{{ ucfirst($client->decision_style ?? '—') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Risk Seviyesi</div>
            <div class="info-value">
                @if($client->risk_level === 'dusuk') <span class="badge badge-success">Düşük</span>
                @elseif($client->risk_level === 'orta') <span class="badge badge-warning">Orta</span>
                @elseif($client->risk_level === 'yuksek') <span class="badge badge-danger">Yüksek</span>
                @else — @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Stratejik Önem</div>
            <div class="info-value">
                <span class="importance-stars">{{ str_repeat('★', $client->strategic_importance ?? 0) }}{{ str_repeat('☆', 5 - ($client->strategic_importance ?? 0)) }}</span>
                ({{ $client->strategic_importance ?? 0 }}/5)
            </div>
        </div>
    </div>

    {{-- Özet Metrikleri --}}
    <div style="margin-bottom: 20px;">
        <div class="metric-box">
            <div class="metric-value money">₺{{ number_format($totalBudget, 0, ',', '.') }}</div>
            <div class="metric-label">Toplam Bütçe</div>
        </div>
        <div class="metric-box">
            <div class="metric-value money">₺{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="metric-label">Toplam Gelir</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">{{ $avgProfitability }}%</div>
            <div class="metric-label">Ort. Kârlılık</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">{{ $client->projects->count() }}</div>
            <div class="metric-label">Proje Sayısı</div>
        </div>
    </div>

    {{-- Proje Portföyü --}}
    <div class="section-title">Proje Portföyü</div>
    @if($client->projects->count() > 0)
    <table class="data">
        <thead>
            <tr>
                <th>Proje</th>
                <th>Alan</th>
                <th>Bütçe</th>
                <th>Gelir</th>
                <th>Kârlılık</th>
                <th>Tarih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($client->projects as $project)
            <tr>
                <td style="font-weight: 600;">{{ $project->client_name }}</td>
                <td>{{ ucfirst($project->usage_area ?? '—') }}</td>
                <td class="money">₺{{ number_format($project->budget ?? 0, 0, ',', '.') }}</td>
                <td class="money">₺{{ number_format($project->actual_revenue ?? 0, 0, ',', '.') }}</td>
                <td>{{ $project->profitability_score ?? '—' }}%</td>
                <td>{{ $project->created_at?->format('d.m.Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: #888; font-style: italic;">Bu müşteriye ait proje bulunmamaktadır.</p>
    @endif

    {{-- Davranışsal Notlar --}}
    @if($client->behavioral_notes)
    <div class="section-title">Davranışsal Notlar</div>
    <p style="line-height: 1.8;">{{ $client->behavioral_notes }}</p>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Bu rapor ADA Co-OS tarafından {{ $generatedAt }} tarihinde {{ $generatedBy }} kullanıcısı için üretilmiştir.
        <br>© {{ date('Y') }} Ada Creative Co. — Tüm hakları saklıdır.
    </div>
</body>
</html>
