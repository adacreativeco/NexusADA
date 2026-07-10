# ADA Co-OS — API & Route Referansı

Bu döküman tüm route tanımlarını, middleware yapılandırmalarını ve erişim kurallarını içerir.

---

## 1. Public Route'lar (`routes/web.php`)

| Method | URI | Açıklama |
|:---|:---|:---|
| `GET` | `/` | Landing page (welcome.blade.php) |
| `GET` | `/platforms` | Platformlar sayfası |

**Middleware:** Yok (public)

---

## 2. Admin Panel Route'ları (`routes/admin.php`)

**Prefix:** `/admin`  
**Middleware:** `auth`, `ensure_tenant`

### Kimlik Doğrulama

| Method | URI | Livewire | Açıklama |
|:---|:---|:---|:---|
| `GET` | `/admin/login` | `Admin\Auth\Login` | Giriş sayfası |
| `POST` | `/admin/logout` | — | Çıkış |

### Dashboard & Core

| Method | URI | Livewire | Açıklama |
|:---|:---|:---|:---|
| `GET` | `/admin` | `Admin\Dashboard` | Ana dashboard |
| `GET` | `/admin/calendar` | `Admin\Calendar` | Takvim görünümü |
| `GET` | `/admin/kanban` | `Admin\TaskBoard` | Kanban board |
| `GET` | `/admin/team` | `Admin\TeamManager` | Ekip yönetimi |
| `GET` | `/admin/proposals` | `Admin\ProposalBuilder` | Teklif motoru |
| `GET` | `/admin/audit-log` | `Admin\AuditLog` | Denetim kaydı |

### CRUD Kaynakları (NexusTable)

Her kaynak için otomatik olarak 3 route oluşturulur:

| Method | URI Pattern | Açıklama |
|:---|:---|:---|
| `GET` | `/admin/{resource}` | Liste |
| `GET` | `/admin/{resource}/create` | Oluşturma formu |
| `GET` | `/admin/{resource}/{id}/edit` | Düzenleme formu |

**Mevcut Kaynaklar:**

| Resource Slug | Model | Modül |
|:---|:---|:---|
| `clients` | Client | Settings & Core |
| `departments` | Department | Settings & Core |
| `projects` | Project | Project Management |
| `tasks` | Task | Project Management |
| `campaigns` | Campaign | Marketing & Content |
| `content-items` | ContentItem | Marketing & Content |
| `social-posts` | SocialPost | Marketing & Content |
| `media-insights` | MediaInsight | Media & Insights |
| `events` | Event | Media & Insights |
| `press-contacts` | PressContact | Media & Insights |
| `incoming-emails` | IncomingEmail | Internal Tools |
| `tools` | Tool | Internal Tools |
| `brand-assets` | BrandAsset | Internal Tools |
| `email-templates` | EmailTemplate | Internal Tools |

---

## 3. Platform Panel Route'ları (`routes/platform.php`)

**Prefix:** `/platform`  
**Middleware:** `auth`, `role:webmaster|super_admin`

### Yönetim

| Method | URI | Livewire | Açıklama |
|:---|:---|:---|:---|
| `GET` | `/platform` | `Platform\Dashboard` | Platform dashboard |
| `GET` | `/platform/tenants` | `Platform\TenantManager` | Kiracı yönetimi |
| `GET` | `/platform/plans` | `Platform\PlanManager` | Plan yönetimi |
| `GET` | `/platform/invoices` | `Platform\InvoiceManager` | Fatura yönetimi |
| `GET` | `/platform/team` | `Admin\TeamManager` | Platform ekip yönetimi |
| `GET` | `/platform/announcements` | `Platform\AnnouncementManager` | Duyurular |
| `GET` | `/platform/system-log` | `Platform\SystemLog` | Sistem logları |
| `GET` | `/platform/settings` | `Platform\Settings` | Platform ayarları |

### Impersonate

| Method | URI | Açıklama |
|:---|:---|:---|
| `POST` | `/platform/tenants/{tenant}/impersonate` | Kiracı impersonate başlat |
| `POST` | `/platform/impersonate/stop` | Impersonate durdur |

---

## 4. Middleware Referansı

### Global Middleware

| Middleware | Açıklama |
|:---|:---|
| `SetTenantContext` | Her istekte auth user'ın `tenant_id`'sini `config('tenant.id')`'ye yazar |

### Route Middleware Alias'ları

| Alias | Sınıf | Açıklama |
|:---|:---|:---|
| `role` | `Spatie\Permission\Middleware\RoleMiddleware` | Rol kontrolü |
| `permission` | `Spatie\Permission\Middleware\PermissionMiddleware` | İzin kontrolü |
| `tenant` | `App\Http\Middleware\SetTenantContext` | Tenant bağlamı |
| `ensure_tenant` | `App\Http\Middleware\EnsureTenantAccess` | `/admin/*` erişim kontrolü |

### Middleware Sırası

```
Admin:    auth → ensure_tenant → [controller]
Platform: auth → role:webmaster|super_admin → [controller]
Client:   auth:client → [controller]
```

---

## 5. Named Route'lar

### Admin
```php
route('admin.dashboard')     // /admin
route('admin.login')         // /admin/login
route('admin.logout')        // /admin/logout (POST)
route('admin.calendar')      // /admin/calendar
route('admin.kanban')        // /admin/kanban
route('admin.team')          // /admin/team
route('admin.proposals')     // /admin/proposals
route('admin.audit-log')     // /admin/audit-log
```

### Platform
```php
route('platform.dashboard')      // /platform
route('platform.tenants')        // /platform/tenants
route('platform.plans')          // /platform/plans
route('platform.invoices')       // /platform/invoices
route('platform.team')           // /platform/team
route('platform.announcements')  // /platform/announcements
route('platform.system-log')     // /platform/system-log
route('platform.settings')       // /platform/settings
```

---

*Son güncelleme: 16 Nisan 2026 — v3.0*
