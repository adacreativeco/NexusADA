# ADA Co-OS — Mimari Dökümanı

Bu döküman, ADA Co-OS platformunun teknik mimarisini detaylı olarak açıklar.

---

## 1. Genel Bakış

ADA Co-OS, **paylaşımlı veritabanı** (shared database) modelli çok kiracılı (multi-tenant) bir SaaS platformudur. Tek bir Laravel kurulumu ile birden çok organizasyona hizmet verir.

```
┌─────────────────────────────────────────────────┐
│                   İSTEMCİLER                     │
│  Web Tarayıcı  │  Electron App  │  Mobil (PWA)  │
└────────┬────────────────┬───────────────┬────────┘
         │                │               │
    ┌────▼────────────────▼───────────────▼────┐
    │              NGINX / Apache               │
    └────────────────┬─────────────────────────┘
                     │
    ┌────────────────▼─────────────────────────┐
    │           Laravel 12 (PHP 8.4)           │
    │                                          │
    │  ┌──────────┐  ┌───────────┐  ┌────────┐│
    │  │ Livewire │  │ Alpine.js │  │ Vite   ││
    │  └──────────┘  └───────────┘  └────────┘│
    │                                          │
    │  ┌──────────────────────────────────────┐│
    │  │        Route Grupları                ││
    │  │  /          → web.php (landing)      ││
    │  │  /admin/*   → admin.php (panel)      ││
    │  │  /platform/*→ platform.php (webmaster)│
    │  │  /client/*  → client.php (portal)    ││
    │  └──────────────────────────────────────┘│
    │                                          │
    │  ┌──────────────────────────────────────┐│
    │  │        Middleware Stack               ││
    │  │  auth → role → tenant                ││
    │  │  auth:client (müşteri portalı)       ││
    │  └──────────────────────────────────────┘│
    │                                          │
    │  ┌──────────────────────────────────────┐│
    │  │        Servis Katmanı (v2.0)         ││
    │  │  AutomationEngine                    ││
    │  │  WebhookDispatcher                   ││
    │  └──────────────────────────────────────┘│
    └────────────────┬─────────────────────────┘
                     │
    ┌────────────────▼─────────────────────────┐
    │        SQLite (WAL Modu)                  │
    │  tenant_id scoped tables (18+ tablo)     │
    └───────────────────────────────────────────┘
```

## 2. Multi-Tenancy Modeli

### 2.1 Strateji: Shared Database with Column Scoping

Her tablo bir `tenant_id` nullable FK içerir. Veriler fiziksel olarak tek veritabanında tutulur ama uygulama katmanında global scope ile filtrelenir.

### 2.2 BelongsToTenant Trait

`app/Models/Traits/BelongsToTenant.php`

```php
// Otomatik olarak:
// 1. Global scope ekler → tenant_id = current_tenant_id
// 2. Model oluşturulurken tenant_id otomatik atanır
// 3. Console ortamında bypass eder
// 4. Webmaster (tenant_id = null) tüm veriyi görür
```

### 2.3 SetTenantContext Middleware

`app/Http/Middleware/SetTenantContext.php`

Her HTTP isteğinde `config('tenant.id')` değerini auth user'ın `tenant_id`'sine eşitler. Böylece global scope bu değeri okuyarak filtreleme yapar.

### 2.4 Tenant-Scoped Tablolar (18+ Adet)

| # | Tablo | Açıklama |
|:---|:---|:---|
| 1 | `users` | Kullanıcılar |
| 2 | `clients` | Müşteriler |
| 3 | `projects` | Projeler |
| 4 | `tasks` | Görevler |
| 5 | `campaigns` | Kampanyalar |
| 6 | `content_items` | İçerikler |
| 7 | `social_posts` | Sosyal medya postları |
| 8 | `media_insights` | Medya yansımaları |
| 9 | `press_contacts` | Basın iletişim rehberi |
| 10 | `events` | Etkinlikler |
| 11 | `notes` | Notlar |
| 12 | `incoming_emails` | Gelen e-postalar |
| 13 | `email_templates` | E-posta şablonları |
| 14 | `brand_assets` | Marka varlıkları |
| 15 | `documents` | Dokümanlar |
| 16 | `interactions` | Etkileşimler |
| 17 | `tools` | Araçlar |
| 18 | `departments` | Departmanlar |

## 3. Yetkilendirme (RBAC) — 3 Katmanlı Rol Sistemi

Spatie Laravel Permission kullanılır. `roles.scope` kolonu ile roller katmanlara ayrılır.

### 3.1 Platform Rolleri (13)

| Rol | Perm | Açıklama |
|:---|:---|:---|
| `super_admin` | 54 | God mode — tüm sistem erişimi |
| `admin` | 50 | Organizasyon yönetimi (sistem ayarları hariç) |
| `webmaster` | 41 | Platform sahibi (legacy), tenant/plan yönetimi |
| `moderator` | 29 | İçerik + kullanıcı yönetimi |
| `editor` | 24 | İçerik oluştur & yayınla |
| `contributor` | 18 | İçerik oluştur (yayınlayamaz) |
| `developer` | 15 | API/webhook/log erişimi |
| `auditor` | 12 | Salt görüntüleme + export |
| `api_user` | 11 | Makine entegrasyonu |
| `viewer` | 11 | Salt okunur |
| `support_agent` | 10 | Destek — müşteri/görev düzenleme |
| `billing_admin` | 5 | Sadece fatura yönetimi |
| `guest` | 3 | Çok kısıtlı geçici erişim |

### 3.2 Tenant Rolleri (8)

| Rol | Perm | Açıklama |
|:---|:---|:---|
| `tenant_owner` | 46 | Ajansı kuran kişi, tam tenant erişimi |
| `tenant_admin` | 45 | Kullanıcı/ayar yönetimi (tenant silemez) |
| `tenant_manager` | 36 | Proje/ekip yönetimi |
| `tenant_editor` | 24 | İçerik oluştur & yayınla |
| `tenant_member` | 24 | Standart çalışan |
| `tenant_viewer` | 11 | Salt okunur |
| `tenant_billing` | 6 | Ajans fatura yönetimi |
| `tenant_guest` | 3 | Davet ile sınırlı erişim |

### 3.3 Workspace Rolleri (3)

| Rol | Perm | Açıklama |
|:---|:---|:---|
| `workspace_admin` | 23 | Workspace yönetimi |
| `workspace_member` | 17 | Workspace içi çalışma |
| `workspace_viewer` | 5 | Workspace görüntüleme |

### 3.4 İzolasyon Kuralları

- **Tenant A admini** → Tenant B verisini **asla** göremez (`BelongsToTenant` global scope)
- **Platform sahibi** → tüm veriyi görür (scope uygulanmaz)
- **Platform + impersonate** → sadece hedef tenant verisini görür (session-based)
- **Tenant owner** → platform ayarlarına **erişemez** (`EnsureTenantAccess` middleware)
- **Hiyerarşi** → kullanıcı kendi seviyesinden üst rol **atayamaz** (`TeamManager`)

### 3.5 Middleware Stack

| Middleware | Dosya | Görev |
|:---|:---|:---|
| `tenant` | `SetTenantContext.php` | `config('app.tenant_id')` atar |
| `ensure_tenant` | `EnsureTenantAccess.php` | `/admin/*` erişim kontrolü |
| `role` | Spatie | Rol bazlı yetkilendirme |

### 3.6 Impersonate Mekanizması

Platform sahibi (`webmaster`/`super_admin`), bir kiracıyı "impersonate" ederek o kiracının gözünden admin panelini kullanabilir.

- **Başlat:** `POST /platform/tenants/{tenant}/impersonate`
- **Durdur:** `POST /platform/impersonate/stop`
- Session: `impersonating_tenant_id`, `impersonating_tenant_name`
- UI: Turuncu üst şerit — kiracı adı + "Çık" butonu
- Audit: `logger()` ile kim, hangi tenant, ne zaman loglanır

## 4. Route Yapısı

### `routes/web.php`
- `GET /` → Landing page
- `GET /platforms` → Platformlar sayfası

### `routes/admin.php`
- Prefix: `/admin`
- Middleware: `auth`, `ensure_tenant`
- Tüm admin panel sayfaları (NexusTable, Dashboard, Gantt, Timesheet, Kanban, Takvim, Teklif, Ekip...)

### `routes/platform.php`
- Prefix: `/platform`
- Middleware: `auth`, `role:webmaster|super_admin`
- Webmaster paneli: Dashboard, Kiracılar, Planlar, Faturalar, Ekip, Duyurular, Sistem Log, Ayarlar
- Impersonate start/stop endpoints

### `routes/client.php` *(v2.0)*
- Prefix: `/client`
- Auth Guard: `client` (ayrı guard, `client_users` tablosu)
- Müşteri portalı (dashboard, projeler, faturalar — read-only)

## 5. Veritabanı

- **Motor:** SQLite (WAL modu)
- **ORM:** Eloquent
- **Audit:** `owen-it/laravel-auditing` paketi
- **Migrations:** `database/migrations/` altında kronolojik sırada

### Özel Tablolar (Platform)

| Tablo | Amaç |
|:---|:---|
| `tenants` | Kiracı kayıtları (isim, e-posta, durum, plan, tarihler) |
| `plans` | Abonelik planları (fiyat, limitler, özellikler) |
| `invoices` | Faturalar (tutar, durum, dönem) |
| `platform_announcements` | Webmaster duyuruları |

### v2.0 Tabloları

| Tablo | Amaç |
|:---|:---|
| `time_logs` | Zaman takibi kayıtları |
| `comments` | Polymorphic threaded yorumlar |
| `automation_rules` | İş akışı otomasyon kuralları |
| `automation_logs` | Otomasyon çalışma geçmişi |
| `integration_settings` | Webhook entegrasyon ayarları |
| `client_users` | Müşteri portalı kullanıcıları |

## 6. Servis Katmanı *(v2.0)*

### `AutomationEngine` (`app/Services/AutomationEngine.php`)
- Trigger-based otomasyon motoru
- 5 aksiyon tipi: `notify`, `change_status`, `send_email`, `assign_user`, `send_webhook`
- Koşullu değerlendirme (field + operator + value)
- `AutomationLog` ile loglama

### `WebhookDispatcher` (`app/Services/WebhookDispatcher.php`)
- Slack, Discord, Generic Webhook formatlarında payload gönderimi
- `IntegrationSetting` model'den konfigürasyon okur

### Observers
- `TaskObserver` → Task create/update/status_changed → AutomationEngine
- `ProjectObserver` → Project create/update/status_changed → AutomationEngine

### Scheduled Commands
- `nexus:process-recurring` → Tekrar eden görev şablonlarını işle (daily)
- `nexus:process-deadlines` → Gecikmiş görev otomasyonlarını tetikle (daily 08:00)

## 7. Frontend Mimarisi

### CSS Design System (`nexus-admin.css`)
- 1200+ satır özel CSS
- CSS custom properties ile design token'lar
- Dark theme (Linear/Stripe estetiği)
- `--nx-` prefix ile tüm değişkenler
- Responsive breakpoint'ler

### JavaScript (`nexus-admin.js`)
- Chart.js entegrasyonu (dashboard grafikleri)
- Sidebar collapse/expand
- Command palette (Ctrl+K)
- Keyboard shortcuts

### Livewire Bileşenleri

| Bileşen | Açıklama |
|:---|:---|
| `NexusTable` | Yeniden kullanılabilir tablo base class (16 resource config) |
| `Dashboard` | KPI kartları, grafikler, aktif zamanlayıcılar, bütçe sağlığı |
| `TaskBoard` | Kanban drag & drop |
| `Gantt` | Zaman çizelgesi görünümü (gün/hafta/ay zoom) |
| `Timesheet` | Haftalık/aylık zaman raporu grid'i |
| `Calendar` | Etkinlik takvimi |
| `ProposalBuilder` | Teklif oluşturucu |

### NexusTable Resource Configs (16 adet)

| Config | Model |
|:---|:---|
| `ClientConfig` | Client |
| `ProjectConfig` | Project |
| `TaskConfig` | Task |
| `CampaignConfig` | Campaign |
| `ContentItemConfig` | ContentItem |
| `MediaInsightConfig` | MediaInsight |
| `BrandAssetConfig` | BrandAsset |
| `DepartmentConfig` | Department |
| `EventConfig` | Event |
| `IncomingEmailConfig` | IncomingEmail |
| `PressContactConfig` | PressContact |
| `ToolConfig` | Tool |
| `SocialPostConfig` | SocialPost |
| `EmailTemplateConfig` | EmailTemplate |
| `AutomationRuleConfig` | AutomationRule *(v2.0)* |
| `IntegrationSettingConfig` | IntegrationSetting *(v2.0)* |

## 8. Dış Entegrasyonlar

| Servis | Amaç | Protokol |
|:---|:---|:---|
| Gmail / Outlook | E-posta senkronizasyonu | IMAP / OAuth2 |
| Telescope | Debug & monitoring | Laravel paket |
| Electron | Masaüstü uygulaması | Node.js bridge |
| Slack | Bildirim webhook | HTTP POST *(v2.0)* |
| Discord | Bildirim webhook | HTTP POST *(v2.0)* |
| Generic Webhook | Özel endpoint | HTTP POST *(v2.0)* |

---

## 9. v3.1 — Yeni Özellikler (Mayıs 2026)

### 9.1 İki Faktörlü Doğrulama (2FA)
- `google2fa-laravel` paketi ile TOTP tabanlı 2FA
- Platform adminleri (`webmaster`, `super_admin`) için **zorunlu**
- Diğer roller için opsiyonel
- Recovery code mekanizması (10 tek kullanımlık kod, şifreli saklanır)
- Download-before-activate güvenlik adımı (lockout önleme)
- UI: QR kod, kod doğrulama ekranı, recovery code yönetimi

### 9.2 REST API — Sanctum (`routes/api.php`)
- `GET /api/v1/projects` — tenant-scoped proje listesi
- `GET /api/v1/tasks` — tenant-scoped görev listesi
- `GET /api/v1/dashboard` — KPI özet verileri
- Tüm endpointler Sanctum `auth:sanctum` middleware koruması altında
- Detay: [`API_ROUTES.md`](./API_ROUTES.md)

### 9.3 Modül & Kategori Toggle Sistemi
- `platform_settings` tablosunda JSON-based konfigürasyon
- Platform ayarlarından modüller açılıp kapatılabilir
- Admin navigasyonu DB'den okunan ayarlara göre dinamik render
- Livewire: `Platform\Settings` bileşeni
- Toggle'lar: Marketing, Media, Internal Tools, System ve alt kategorileri

### 9.4 IMAP E-posta Senkronizasyonu
- `webklex/laravel-imap` ile Gmail/Outlook bağlantısı
- `ImapSyncService` — her 5 dakikada bir `imap:sync` artisan komutu
- `message_id` bazlı duplicate koruması
- `email_accounts` tablosu: şifreler `encrypted` cast ile AES-256-CBC
- `BelongsToTenant` scope ile hesap izolasyonu
- UI: `EmailAccountManager` Livewire bileşeni

### 9.5 Dashboard Görselleştirme
- Chart.js npm paketi entegre (CDN kaldırıldı, Vite ile bundle)
- 8 KPI widget: gelir, görev, kampanya, bütçe, zamanlayıcı, vb.
- FullCalendar entegrasyonu: drag-drop + Türkçe dil desteği

### 9.6 Güvenlik Sertleştirmesi (Müdür W18)
- Security headers middleware (X-Frame-Options, X-Content-Type-Options, Referrer-Policy)
- CSP nonce-based (AppServiceProvider paylaşımlı)
- Canonical URL meta tag ve SEO hardening
- CORS: API rotaları için `config/cors.php` özelleştirilmiş
- `/sitemap.xml`, `/robots.txt` ve legal route'lar (`/kvkk`, `/gizlilik-politikasi`)

---

*Son güncelleme: 5 Mayıs 2026 — v3.1*
