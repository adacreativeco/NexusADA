# ADA Co-OS — Masaüstü Kurulum Kılavuzu (NativePHP / Electron)

ADA Co-OS, sadece bir web uygulaması değil, aynı zamanda Windows ve macOS için optimize edilmiş bir masaüstü uygulamasıdır. Bu doküman, NativePHP kullanılarak masaüstü paketinin nasıl derleneceğini açıklar.

## 🛠️ Önkoşullar

- PHP 8.2 veya üzeri
- Node.js (v18+) ve npm
- SQLite eklentisi aktif edilmiş PHP kurulumu (Masaüstü yerel depolama için)

## 🚀 Derleme (Build) İşlemi

Masaüstü uygulamasını derlemek için aşağıdaki adımları izleyin:

1. **Paketlerin Yüklenmesi:**
```bash
composer install
npm install
```

2. **Asset'lerin Paketlenmesi:**
```bash
npm run build
```

3. **Veritabanı Hazırlığı:**
Masaüstü uygulaması varsayılan olarak yerel bir SQLite kopyası kullanır.
```bash
php artisan migrate --force
```

4. **NativePHP Build Komutu:**
```bash
php artisan native:build
```
Bu komut çalıştığında Electron paketleyicisi devreye girer. `dist/` klasörü altında Windows için `.exe`, macOS için `.dmg` dosyası oluşturulacaktır.

## ⚠️ Bilinen Hatalar ve Çözümleri

- **Telescope Çakışması:** NativePHP derlenirken Laravel Telescope bazen boot aşamasında takılabilir. `composer.json` içerisinden `laravel/telescope` paketinin dev ortamında kaldığından veya `AppServiceProvider` içerisinde devre dışı bırakıldığından emin olun.
- **EBUSY (File Lock) Hatası:** Windows ortamında build alırken veritabanı dosyaları kilitlenebilir. Build öncesinde `.sqlite` dosyalarının kapalı olduğundan emin olun.

## 🔐 Güvenlik Notu
Derlenen paketlerin kodları görünür olacağından `.env` içerisinde prodüksiyon API veya SMTP şifrelerinin gömülü **olmamasına** dikkat edilmelidir. Masaüstü istemcisi API üzerinden bulut sunucuya haberleşmelidir.
