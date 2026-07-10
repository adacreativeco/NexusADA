{{-- ADA Co-OS — Ortak Rapor Header --}}
@php
    $tenant = $tenant ?? null;
    $logoUrl = $logoUrl ?? null;
    $logoRaw = $logoRaw ?? null;
    $primaryColor = $tenant->primary_color ?? '#10b981';
@endphp
<div style="border-bottom: 3px solid {{ $primaryColor }}; padding-bottom: 16px; margin-bottom: 24px; width: 100%; min-height: 50px;">
    <div style="float: left; width: 60%;">
        @if($logoRaw)
            <div style="height: 40px; display: inline-block;">
                {!! $logoRaw !!}
            </div>
        @elseif($logoUrl)
            <img src="{{ $logoUrl }}" style="height: 40px; max-width: 250px; object-fit: contain;">
        @else
            <div style="font-size: 20px; font-weight: 800; color: #111; letter-spacing: -0.02em;">
                {{ $tenant->name ?? 'ADA Co-OS' }}
            </div>
            <div style="font-size: 9px; color: #666; margin-top: 2px;">
                {{ $tenant ? $tenant->name . ' Kurumsal Yönetim Platformu' : 'Kurumsal Dijital Zeka Platformu' }}
            </div>
        @endif
    </div>
    <div style="float: right; width: 38%; text-align: right;">
        <div style="font-size: 14px; font-weight: 700; color: #333;">{{ $reportTitle }}</div>
        <div style="font-size: 9px; color: #888; margin-top: 2px;">{{ $generatedAt }}</div>
    </div>
    <div style="clear: both;"></div>
</div>
