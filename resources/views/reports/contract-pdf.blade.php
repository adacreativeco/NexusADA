<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sözleşme - {{ $contract->contract_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.6; padding: 40px; }
        .info-grid { display: table; width: 100%; margin-bottom: 24px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; padding: 6px 12px 6px 0; font-weight: 700; color: #555; width: 140px; font-size: 10px; text-transform: uppercase; }
        .info-value { display: table-cell; padding: 6px 0; color: #111; }
        .section-title { font-size: 13px; font-weight: 700; color: #111; margin: 24px 0 10px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; }
        .money { font-family: DejaVu Sans Mono, monospace; }
        .footer { margin-top: 50px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #999; text-align: center; }
    </style>
</head>
<body>
    @include('reports._header', ['reportTitle' => 'Hizmet ve Ortaklık Sözleşmesi', 'generatedAt' => $generatedAt])

    {{-- Contract Metadata --}}
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Sözleşme No</div>
            <div class="info-value"><strong>{{ $contract->contract_number }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Sözleşme Başlığı</div>
            <div class="info-value">{{ $contract->title }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Müşteri / Kurum</div>
            <div class="info-value">{{ $contract->client->name ?? '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Başlangıç Tarihi</div>
            <div class="info-value">{{ $contract->start_date ? $contract->start_date->format('d.m.Y') : '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Bitiş Tarihi</div>
            <div class="info-value">{{ $contract->end_date ? $contract->end_date->format('d.m.Y') : 'Yenilenen / Süresiz' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Sözleşme Bedeli</div>
            <div class="info-value money">
                @php
                    $symbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
                    $sym = $symbols[$contract->currency] ?? $contract->currency;
                @endphp
                <strong>{{ $sym }}{{ number_format($contract->value, 2, ',', '.') }}</strong>
            </div>
        </div>
        @if($contract->work)
        <div class="info-row">
            <div class="info-label">İş / Süreç Referansı</div>
            <div class="info-value">{{ $contract->work->title }}</div>
        </div>
        @endif
    </div>

    {{-- Terms --}}
    <div class="section-title">Sözleşme Şartları ve Maddeleri</div>
    <div style="white-space: pre-wrap; line-height: 1.7; font-size: 11px; color: #444; margin-bottom: 30px;">
        {{ $contract->terms ?? 'Sözleşme maddeleri detaylandırılmamıştır.' }}
    </div>

    {{-- Signature Blocks --}}
    <div style="margin-top: 60px; width: 100%; display: table;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 50%; padding-right: 20px;">
                <div style="border-top: 1px solid #ccc; padding-top: 8px; text-align: center;">
                    <strong>Müşteri Yetkilisi</strong>
                    <div style="margin-top: 40px; font-size: 10px; color: #888;">İmza / Kaşe</div>
                </div>
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 20px;">
                <div style="border-top: 1px solid #ccc; padding-top: 8px; text-align: center;">
                    <strong>Ada Creative Co. Yetkilisi</strong>
                    <div style="margin-top: 40px; font-size: 10px; color: #888;">İmza / Kaşe</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Bu sözleşme ADA Co-OS sistemi üzerinden dijital olarak oluşturulmuştur.
        <br>© {{ date('Y') }} Ada Creative Co. — Tüm hakları saklıdır.
    </div>
</body>
</html>
