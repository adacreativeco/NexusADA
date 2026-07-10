@extends('layouts.public')

@section('title', 'KVKK Aydınlatma Metni — Nexus ADA')

@section('content')
<article style="max-width:720px;margin:80px auto;padding:24px;color:#ccc;line-height:1.8;font-size:15px;">
    <h1 style="color:#f0f0f5;font-size:1.8rem;margin-bottom:24px;">Kişisel Verilerin Korunması Hakkında Aydınlatma Metni</h1>

    <p><strong>Veri Sorumlusu:</strong> ADA Creative Co. ("Şirket")<br>
    <strong>İletişim:</strong> hello@adacreative.co</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">1. Toplanan Kişisel Veriler</h2>
    <p>Nexus ADA platformu aşağıdaki kişisel verileri işlemektedir:</p>
    <ul>
        <li>Ad Soyad, e-posta adresi, telefon numarası</li>
        <li>Şirket/ajans adı ve iş unvanı</li>
        <li>IP adresi, tarayıcı bilgileri (oturum yönetimi ve güvenlik)</li>
        <li>Platform üzerinde oluşturulan proje, görev ve içerik verileri</li>
    </ul>

    <h2 style="color:#f0f0f5;margin-top:32px;">2. İşleme Amacı</h2>
    <ul>
        <li>Hizmet sunumu ve kullanıcı hesabı yönetimi</li>
        <li>Güvenlik ve denetim (audit trail)</li>
        <li>Yasal yükümlülüklerin yerine getirilmesi</li>
    </ul>

    <h2 style="color:#f0f0f5;margin-top:32px;">3. Hukuki Dayanak</h2>
    <p>6698 sayılı KVKK'nın 5. maddesinin 2. fıkrası kapsamında: sözleşmenin ifası, meşru menfaat ve açık rıza.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">4. Haklarınız</h2>
    <p>KVKK'nın 11. maddesi kapsamında aşağıdaki haklara sahipsiniz:</p>
    <ul>
        <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
        <li>Kişisel verileriniz işlenmişse buna ilişkin bilgi talep etme</li>
        <li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme</li>
        <li>Eksik veya yanlış işlenmiş olması hâlinde düzeltilmesini isteme</li>
        <li>KVKK'nın 7. maddesi kapsamında silinmesini veya yok edilmesini isteme</li>
    </ul>

    <h2 style="color:#f0f0f5;margin-top:32px;">5. Verilerinizi İndirme ve Silme</h2>
    <p>Platform içerisindeki <strong>Ayarlar</strong> sayfasından "Verilerimi İndir" butonuyla tüm verilerinizi JSON formatında indirebilirsiniz. "Hesabımı Sil" butonuyla verilerinizin anonimleştirilmesini talep edebilirsiniz.</p>

    <h2 style="color:#f0f0f5;margin-top:32px;">6. İletişim</h2>
    <p>Kişisel verilerinize ilişkin talepleriniz için: <a href="mailto:hello@adacreative.co" style="color:#10b981;">hello@adacreative.co</a></p>

    <p style="margin-top:32px;color:#666;font-size:13px;">Son güncelleme: {{ date('d.m.Y') }}</p>
</article>
@endsection
