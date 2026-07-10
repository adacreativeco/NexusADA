<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Teklif - {{ $proposal->proposal_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #374151; line-height: 1.5; padding: 30px; }
        
        .brand-strip { height: 6px; background-color: {{ $tenant->primary_color ?? '#10b981' }}; margin-bottom: 20px; }
        
        .sheet-header { border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 30px; width: 100%; }
        .logo-col { float: left; width: 60%; }
        .meta-col { float: right; width: 38%; text-align: right; }
        
        .logo-text { font-size: 20px; font-weight: bold; color: #111827; }
        .logo-subtitle { font-size: 9px; color: #4b5563; margin-top: 2px; text-transform: uppercase; }
        
        .doc-title { font-size: 18px; font-weight: 700; color: #111827; text-transform: uppercase; }
        .doc-number { font-size: 12px; color: {{ $tenant->primary_color ?? '#10b981' }}; font-weight: bold; margin-top: 2px; }
        
        .clear { clear: both; }
        
        /* Two Column Grid using Table */
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details-table td { width: 50%; vertical-align: top; }
        .details-card { padding-right: 20px; }
        .details-card-right { padding-left: 20px; }
        
        .meta-card-title { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 10px; }
        
        .meta-item { margin-bottom: 6px; font-size: 11px; }
        .meta-label { display: inline-block; width: 110px; font-weight: bold; color: #4b5563; }
        .meta-val { display: inline-block; color: #1f2937; }
        
        /* Items Table */
        .items-section-title { font-size: 12px; font-weight: 700; color: #111827; margin-bottom: 12px; text-transform: uppercase; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #f9fafb; font-size: 9px; font-weight: 700; text-transform: uppercase; color: #4b5563; padding: 10px 12px; text-align: left; border-bottom: 2px solid #e5e7eb; }
        .items-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-size: 11px; }
        
        /* Summary Block */
        .summary-wrapper { width: 100%; margin-bottom: 30px; }
        .summary-table { width: 280px; float: right; border-collapse: collapse; }
        .summary-table td { padding: 6px 12px; font-size: 11px; color: #4b5563; }
        .summary-table tr.total-row { border-top: 2px solid #e5e7eb; font-weight: bold; }
        .summary-table tr.total-row td { padding-top: 10px; color: {{ $tenant->primary_color ?? '#10b981' }}; font-size: 13px; }
        
        /* Notes */
        .notes-section { margin-bottom: 20px; }
        .notes-title { font-size: 10px; font-weight: 700; color: #111827; margin-bottom: 6px; text-transform: uppercase; }
        .notes-content { font-size: 11px; color: #4b5563; white-space: pre-wrap; background-color: #f9fafb; padding: 12px; border-radius: 6px; border: 1px solid #e5e7eb; }
        
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="brand-strip"></div>

    <!-- Header -->
    <div class="sheet-header">
        <div class="logo-col">
            @if($logoRaw)
                <div style="height: 40px;">
                    {!! $logoRaw !!}
                </div>
            @elseif($logoUrl)
                <img src="{{ $logoUrl }}" style="height: 40px; max-width: 200px; object-fit: contain;">
            @else
                <div class="logo-text">{{ $tenant->name ?? 'ADA Co-OS' }}</div>
                <div class="logo-subtitle">{{ $tenant ? $tenant->name . ' Kurumsal Yönetim Platformu' : 'Kurumsal Yönetim Platformu' }}</div>
            @endif
        </div>
        <div class="meta-col">
            <div class="doc-title">Hizmet Fiyat Teklifi</div>
            <div class="doc-number">{{ $proposal->proposal_number }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Meta Details Grid -->
    <table class="details-table">
        <tr>
            <td>
                <div class="details-card">
                    <div class="meta-card-title">Müşteri Bilgileri</div>
                    <div class="meta-item">
                        <span class="meta-label">Müşteri / Kurum</span>
                        <span class="meta-val"><strong>{{ $proposal->client->name ?? '—' }}</strong></span>
                    </div>
                    @if($proposal->client->email)
                    <div class="meta-item">
                        <span class="meta-label">E-Posta</span>
                        <span class="meta-val">{{ $proposal->client->email }}</span>
                    </div>
                    @endif
                    @if($proposal->client->phone)
                    <div class="meta-item">
                        <span class="meta-label">Telefon</span>
                        <span class="meta-val">{{ $proposal->client->phone }}</span>
                    </div>
                    @endif
                </div>
            </td>
            <td>
                <div class="details-card-right">
                    <div class="meta-card-title">Teklif Detayları</div>
                    <div class="meta-item">
                        <span class="meta-label">Teklif Başlığı</span>
                        <span class="meta-val">{{ $proposal->title }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Hazırlayan</span>
                        <span class="meta-val">{{ $proposal->creator->name ?? 'Sistem' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Geçerlilik Tarihi</span>
                        <span class="meta-val">{{ $proposal->valid_until ? $proposal->valid_until->format('d.m.Y') : '—' }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items -->
    <div class="items-section-title">Hizmet ve Ürün Kalemleri</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Açıklama</th>
                <th style="width: 60px; text-align: center;">Miktar</th>
                <th style="width: 110px; text-align: right;">Birim Fiyat</th>
                <th style="width: 70px; text-align: center;">KDV</th>
                <th style="width: 130px; text-align: right;">Toplam (KDV Dahil)</th>
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
                    <td style="text-align: center;">{{ $qty }}</td>
                    <td style="text-align: right;">{{ number_format($price, 2, ',', '.') }} {{ $proposal->currency }}</td>
                    <td style="text-align: center;">%{{ $vat }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($total, 2, ',', '.') }} {{ $proposal->currency }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #9ca3af;">Hizmet kalemi bulunmamaktadır.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Financial Summary -->
    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td>Ara Toplam</td>
                <td style="text-align: right;">{{ number_format($proposal->subtotal, 2, ',', '.') }} {{ $proposal->currency }}</td>
            </tr>
            <tr>
                <td>KDV Toplamı</td>
                <td style="text-align: right;">{{ number_format($proposal->tax_total, 2, ',', '.') }} {{ $proposal->currency }}</td>
            </tr>
            @if($proposal->discount_total > 0)
            <tr>
                <td style="color: #ef4444;">İndirim</td>
                <td style="text-align: right; color: #ef4444;">-{{ number_format($proposal->discount_total, 2, ',', '.') }} {{ $proposal->currency }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Genel Toplam</td>
                <td style="text-align: right;">
                    @php
                        $symbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
                        $sym = $symbols[$proposal->currency] ?? $proposal->currency;
                    @endphp
                    {{ $sym }}{{ number_format($proposal->grand_total, 2, ',', '.') }}
                </td>
            </tr>
        </table>
        <div class="clear"></div>
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
            <div class="notes-content" style="font-size: 10px;">{{ $proposal->terms }}</div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Bu teklif {{ $proposal->tenant->name ?? 'ADA Co-OS' }} sistemi üzerinden dijital olarak oluşturulmuştur.
        <br>© {{ date('Y') }} {{ $proposal->tenant->name ?? 'Ada Creative Co.' }} — Tüm hakları saklıdır.
    </div>
</body>
</html>
