@extends('layouts.public')

@section('title', 'Gizlilik Politikası — Nexus ADA')

@section('content')
<article style="max-width:720px;margin:80px auto;padding:24px;color:#ccc;line-height:1.8;font-size:15px;">
    <h1 style="color:#f0f0f5;font-size:1.8rem;margin-bottom:24px;">Gizlilik Politikası</h1>

    <p><strong>Yürürlük Tarihi:</strong> 24 Nisan 2026</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">1. Genel</h2>
    <p>ADA Creative Co. ("biz") olarak gizliliğinize önem veriyoruz. Bu politika, Nexus ADA platformunu kullanırken kişisel verilerinizin nasıl toplandığını, kullanıldığını ve korunduğunu açıklar.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">2. Toplanan Bilgiler</h2>
    <ul>
        <li><strong>Hesap bilgileri:</strong> Ad, soyad, e-posta, şirket adı</li>
        <li><strong>Kullanım verileri:</strong> IP adresi, tarayıcı türü, erişim zamanları</li>
        <li><strong>İçerik verileri:</strong> Platform üzerinde oluşturduğunuz projeler, görevler, kampanyalar</li>
    </ul>

    <h2 style="color:#f0f0f5;margin-top:32px;">3. Veri Güvenliği</h2>
    <p>Verileriniz HTTPS ile iletilir, veritabanında korunur. IMAP şifreleri AES-256-CBC ile şifrelenir. Erişim RBAC ile kontrol edilir ve tüm işlemler audit trail ile kaydedilir.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">4. Çerezler</h2>
    <p>Platform, oturum yönetimi için gerekli çerezleri kullanır. Üçüncü taraf analitik çerezi kullanılmaz.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">5. Veri Paylaşımı</h2>
    <p>Kişisel verileriniz üçüncü taraflarla paylaşılmaz. İstisna: yasal zorunluluklar ve e-posta gönderim hizmeti (SendGrid — sadece gönderim amaçlı).</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">6. İletişim</h2>
    <p>Sorularınız için: <a href="mailto:hello@adacreative.co" style="color:#10b981;">hello@adacreative.co</a></p>

    <p style="margin-top:32px;color:#666;font-size:13px;">Son güncelleme: {{ date('d.m.Y') }}</p>
</article>
@endsection
