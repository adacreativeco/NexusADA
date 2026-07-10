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
        table.data tr:hover td { background: #f9fafb; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .money { font-family: DejaVu Sans Mono, monospace; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #999; text-align: center; }
        .metric-box { display: inline-block; text-align: center; padding: 12px 20px; border: 1px solid #e5e7eb; border-radius: 8px; margin-right: 8px; margin-bottom: 8px; }
        .metric-value { font-size: 20px; font-weight: 800; color: #10b981; }
        .metric-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 2px; }
    </style>
</head>
<body>
    @include('reports._header', ['reportTitle' => 'Proje Raporu', 'generatedAt' => $generatedAt])

    {{-- Proje Bilgileri --}}
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Müşteri</div>
            <div class="info-value">{{ $project->client->name ?? '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kullanım Alanı</div>
            <div class="info-value">{{ ucfirst($project->usage_area ?? '—') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tamamlanma Tarihi</div>
            <div class="info-value">{{ $project->completion_date ? \Carbon\Carbon::parse($project->completion_date)->format('d.m.Y') : '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Oluşturulma</div>
            <div class="info-value">{{ $project->created_at?->format('d.m.Y') ?? '—' }}</div>
        </div>
    </div>

    {{-- Değer Metrikleri --}}
    <div class="section-title">Değer Analizi</div>
    <div style="margin-bottom: 20px;">
        <div class="metric-box">
            <div class="metric-value money">₺{{ number_format($project->budget ?? 0, 0, ',', '.') }}</div>
            <div class="metric-label">Planlanan Bütçe</div>
        </div>
        <div class="metric-box">
            <div class="metric-value money">₺{{ number_format($project->actual_revenue ?? 0, 0, ',', '.') }}</div>
            <div class="metric-label">Gerçekleşen Gelir</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">{{ $project->profitability_score ?? 0 }}%</div>
            <div class="metric-label">Kârlılık</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">{{ $project->team_hours ?? 0 }}h</div>
            <div class="metric-label">İş Gücü</div>
        </div>
    </div>

    {{-- Proje Özeti --}}
    @if($project->summary)
    <div class="section-title">Proje Özeti</div>
    <p style="margin-bottom: 16px; line-height: 1.8;">{{ $project->summary }}</p>
    @endif

    @if($project->strategic_value_notes)
    <div class="section-title">Stratejik Değer Notları</div>
    <p style="margin-bottom: 16px; line-height: 1.8;">{{ $project->strategic_value_notes }}</p>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Bu rapor ADA Co-OS tarafından {{ $generatedAt }} tarihinde {{ $generatedBy }} kullanıcısı için üretilmiştir.
        <br>© {{ date('Y') }} Ada Creative Co. — Tüm hakları saklıdır.
    </div>
</body>
</html>
