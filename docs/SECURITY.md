# ADA Co-OS — Güvenlik Raporu

**Tarih:** 5 Mayıs 2026
**Hazırlayan:** Teknik Lead
**Durum:** Production (v3.1) — nexus.adacreative.co

---

## Mevcut Güvenlik Önlemleri

### 1. Kimlik Doğrulama & Erişim Kontrolü
- **RBAC:** 24 rol, 54+ permission — `spatie/laravel-permission`
- **3 katman:** Platform → Tenant → Workspace izolasyonu
- **Password Policy:** Min 10 karakter, 1 büyük harf, 1 rakam, 1 özel karakter
- **Rate Limiting:** Login: 5/dakika, Kayıt: 5/dakika, Erken erişim formu: 3/10 dakika
- **2FA:** Opsiyonel (Google Authenticator). Platform adminleri (webmaster, super_admin) için zorunlu.
- **E-posta doğrulama:** Kayıt sonrası `MustVerifyEmail` + `verified` middleware
- **Session:** Database-backed, 120 dk timeout, `session()->regenerate()` login sonrası

### 2. Veri İzolasyonu
- **BelongsToTenant trait:** Tüm tenant-scoped tablolarda global scope
- **tenant_id güncelleme engeli:** `abort(403)` ile korunuyor
- **EnsureTenantAccess middleware:** `/admin/*` rotalarında aktif
- **Impersonate:** Platform admin → tenant geçici erişim, session-based, geri dönüş mevcut

### 3. Veri Güvenliği
- **Audit Trail:** `owen-it/laravel-auditing` — tüm CRUD olayları kaydediliyor (IP, user agent, eski/yeni değerler)
- **IMAP şifreleri:** `encrypted` cast ile DB'de şifreleniyor (Laravel APP_KEY ile AES-256-CBC)
- **HTTPS:** Hosting sağlayıcı SSL sertifikası (Let's Encrypt)
- **CSRF:** Livewire otomatik, form route'larda `@csrf` token
- **XSS:** Blade `{{ }}` auto-escaping, e-posta HTML body sunucu tarafı sanitize

### 4. Uygulama Güvenliği
- **Mass Assignment:** `$guarded` ile korunuyor, `NexusTable` bulk update kontrollü
- **SQL Injection:** Eloquent ORM, parametreli sorgular
- **Rate Limiting:** Laravel throttle middleware
- **Robots.txt:** Tüm crawling engellenmiş (CRM gizliliği)
- **noindex meta tag:** Tüm admin layout'larında

## Bilinen Sınırlar

### ⚠️ Henüz Yapılmamış
1. **Penetration test:** Yapılmadı. Üçüncü parti güvenlik denetimi yok.
2. **Veri şifreleme (at rest):** MySQL veritabanı dosya düzeyinde şifrelenmemiş. Hosting sağlayıcı altyapısına güveniliyor.
3. **WAF (Web Application Firewall):** Yok. Hosting düzeyinde temel DDoS koruması var.
4. **IP whitelisting:** Admin paneline IP bazlı erişim kısıtlaması yok.
5. **SSO/SAML/OAuth:** Desteklenmiyor. Session-based auth kullanılıyor.
6. **Security headers:** CSP nonce-based (AppServiceProvider'da), HSTS hosting düzeyinde.

### 🔄 Planlanan İyileştirmeler
1. Üçüncü parti pen-test (Q3 2026)
2. SSO/SAML entegrasyonu (kurumsal pilot sonrası)
3. IP whitelisting (tenant ayarlarında opsiyonel)
4. Otomatik session invalidation (şüpheli aktivite tespiti)

## KVKK Uyumu

| Gereksinim | Durum |
|------------|-------|
| Veri toplama onayı | ✅ Kayıt formunda KVKK checkbox |
| Veri erişim hakkı | ✅ "Verilerimi İndir" butonu (JSON export) |
| Unutulma hakkı | ✅ "Hesabımı Sil" butonu (anonimleştirme) |
| Veri minimizasyonu | ✅ Sadece gerekli alanlar toplanıyor |
| Gizlilik politikası | ✅ /gizlilik-politikasi rotası |
| KVKK metni | ✅ /kvkk rotası |
| Kullanım koşulları | ✅ /kullanim-kosullari rotası |

## İletişim

Güvenlik açığı bildirimi: security@adacreative.co
