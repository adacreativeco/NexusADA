@extends('layouts.public')

@section('title', 'Kullanım Koşulları — Nexus ADA')

@section('content')
<article style="max-width:720px;margin:80px auto;padding:24px;color:#ccc;line-height:1.8;font-size:15px;">
    <h1 style="color:#f0f0f5;font-size:1.8rem;margin-bottom:24px;">Kullanım Koşulları</h1>

    <p><strong>Yürürlük Tarihi:</strong> 24 Nisan 2026</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">1. Kapsam</h2>
    <p>Bu koşullar, Nexus ADA platformunun ("Platform") kullanımına ilişkin kuralları belirler. Platformu kullanarak bu koşulları kabul etmiş sayılırsınız.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">2. Hesap Sorumluluğu</h2>
    <ul>
        <li>Hesap bilgilerinizin gizliliğinden siz sorumlusunuz.</li>
        <li>Hesabınız üzerinden gerçekleştirilen tüm işlemlerden siz sorumlusunuz.</li>
        <li>Yetkisiz erişim tespit ettiğinizde derhal bizi bilgilendirmeniz gerekmektedir.</li>
    </ul>

    <h2 style="color:#f0f0f5;margin-top:32px;">3. Kabul Edilebilir Kullanım</h2>
    <p>Platformu yalnızca yasal iş amaçları için kullanabilirsiniz. Aşağıdaki davranışlar yasaktır:</p>
    <ul>
        <li>Platformun güvenlik mekanizmalarını atlatmaya çalışmak</li>
        <li>Diğer kullanıcıların verilerine yetkisiz erişim</li>
        <li>Platformun performansını olumsuz etkileyecek aşırı yüklenme</li>
        <li>Yasa dışı içerik barındırma veya paylaşma</li>
    </ul>

    <h2 style="color:#f0f0f5;margin-top:32px;">4. Hizmet Seviyesi</h2>
    <p>Platform "olduğu gibi" sunulur. %99.9 uptime garantisi verilmez; bakım süreleri önceden duyurulur.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">5. Fikri Mülkiyet</h2>
    <p>Platform üzerinde oluşturduğunuz içerikler size aittir. Platform yazılımı ve tasarımı ADA Creative Co.'ya aittir.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">6. Fesih</h2>
    <p>Hesabınızı istediğiniz zaman kapatabilirsiniz. Kişisel verileriniz KVKK kapsamında anonimleştirilir.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">7. Değişiklikler</h2>
    <p>Bu koşullar güncellenebilir. Önemli değişiklikler platform içi bildirim ile duyurulur.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">8. İletişim</h2>
    <p>Sorularınız için: <a href="mailto:hello@adacreative.co" style="color:#10b981;">hello@adacreative.co</a></p>

    <p style="margin-top:32px;color:#666;font-size:13px;">Son güncelleme: {{ date('d.m.Y') }}</p>
</article>
@endsection
