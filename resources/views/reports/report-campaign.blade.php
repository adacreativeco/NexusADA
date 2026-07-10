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
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #f3f4f6; color: #374151; }
        .money { font-family: DejaVu Sans Mono, monospace; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #999; text-align: center; }
        .metric-box { display: inline-block; text-align: center; padding: 12px 20px; border: 1px solid #e5e7eb; border-radius: 8px; margin-right: 8px; margin-bottom: 8px; }
        .metric-value { font-size: 20px; font-weight: 800; color: #10b981; }
        .metric-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 2px; }
    </style>
</head>
<body>
    @include('reports._header', ['reportTitle' => 'Kampanya Raporu', 'generatedAt' => $generatedAt])

    {{-- Kampanya Bilgileri --}}
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Kampanya Adı</div>
            <div class="info-value" style="font-weight: 700; font-size: 14px;">{{ $campaign->title }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Departman</div>
            <div class="info-value">{{ $campaign->department->name ?? '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Hedef</div>
            <div class="info-value">{{ $campaign->goal ?? '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Durum</div>
            <div class="info-value">
                @php
                    $statusMap = ['active' => ['Aktif', 'success'], 'draft' => ['Taslak', 'gray'], 'completed' => ['Tamamlandı', 'info'], 'cancelled' => ['İptal', 'danger']];
                    $s = $statusMap[$campaign->status] ?? ['—', 'gray'];
                @endphp
                <span class="badge badge-{{ $s[1] }}">{{ $s[0] }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Tarih Aralığı</div>
            <div class="info-value">
                {{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('d.m.Y') : '—' }}
                →
                {{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('d.m.Y') : '—' }}
            </div>
        </div>
    </div>

    {{-- Bütçe --}}
    <div style="margin-bottom: 20px;">
        <div class="metric-box">
            <div class="metric-value money">₺{{ number_format($campaign->budget ?? 0, 0, ',', '.') }}</div>
            <div class="metric-label">Kampanya Bütçesi</div>
        </div>
        <div class="metric-box">
            <div class="metric-value">{{ $campaign->contentItems->count() }}</div>
            <div class="metric-label">İçerik Sayısı</div>
        </div>
    </div>

    {{-- Açıklama --}}
    @if($campaign->description)
    <div class="section-title">Kampanya Açıklaması</div>
    <p style="margin-bottom: 16px; line-height: 1.8;">{{ $campaign->description }}</p>
    @endif

    {{-- İçerik Öğeleri --}}
    <div class="section-title">İlişkili İçerikler</div>
    @if($campaign->contentItems->count() > 0)
    <table class="data">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>Tip</th>
                <th>Durum</th>
                <th>Oluşturulma</th>
            </tr>
        </thead>
        <tbody>
            @foreach($campaign->contentItems as $item)
            <tr>
                <td style="font-weight: 600;">{{ $item->title }}</td>
                <td>{{ ucfirst($item->type ?? '—') }}</td>
                <td>
                    @if($item->status === 'approved') <span class="badge badge-success">Onaylı</span>
                    @elseif($item->status === 'draft') <span class="badge badge-gray">Taslak</span>
                    @else <span class="badge badge-warning">{{ ucfirst($item->status ?? '—') }}</span>
                    @endif
                </td>
                <td>{{ $item->created_at?->format('d.m.Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: #888; font-style: italic;">Bu kampanyaya bağlı içerik öğesi bulunmamaktadır.</p>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Bu rapor ADA Co-OS tarafından {{ $generatedAt }} tarihinde {{ $generatedBy }} kullanıcısı için üretilmiştir.
        <br>© {{ date('Y') }} Ada Creative Co. — Tüm hakları saklıdır.
    </div>
</body>
</html>
