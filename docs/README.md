# ADA Co-OS — Dökümanlar

Tüm dökümanları bu dizinde bulabilirsiniz.

---

## 📖 Döküman Listesi

| Döküman | Açıklama |
|:---|:---|
| [**README.md**](../README.md) | Proje özeti, kurulum, hızlı başlangıç |
| [**CHANGELOG.md**](../CHANGELOG.md) | Sürüm geçmişi ve değişiklik notları |
| [**ARCHITECTURE.md**](ARCHITECTURE.md) | Teknik mimari, multi-tenancy, route yapısı, servis katmanı |
| [**DATABASE.md**](DATABASE.md) | Veritabanı şeması, tablolar, ilişkiler (v2.0 dahil) |
| [**API_ROUTES.md**](API_ROUTES.md) | Tüm route'lar, middleware, named route'lar |
| [**DEPLOYMENT.md**](DEPLOYMENT.md) | Kurulum, production deploy, sorun giderme |
| [**DESIGN_SYSTEM.md**](DESIGN_SYSTEM.md) | UI token'ları, CSS sınıfları, renk paleti |
| [**competitor-feature-gap-analysis.md**](competitor-feature-gap-analysis.md) | Rakip analizi, eksik özellikler, tamamlanan fazlar |
| [**implementation-plan-v2.md**](implementation-plan-v2.md) | v2.0 implementasyon planı (10 faz — tamamlandı ✅) |

## 📁 Arşiv

Eski planlama dökümanları `docs/archive/` altında saklanmaktadır:

| Dosya | Açıklama |
|:---|:---|
| `NEXUS_ADMIN_PLAN.md` | Filament → Custom panel geçiş planı |
| `IMPLEMENTATION_PLAN.md` | Multi-tenant implementasyon planı |
| `TASK.md` | Webmaster panel görev takibi |

## 📊 v2.0 Özeti

v2.0 ile eklenen 10 ana özellik:

1. ✅ Zaman Takibi (Time Tracking + Timesheet)
2. ✅ Yorum & Aktivite Akışı (Threaded, @mention)
3. ✅ Gantt Şeması (Gün/Hafta/Ay)
4. ✅ Otomasyon Motoru (5 aksiyon tipi)
5. ✅ Müşteri Portalı (Ayrı auth guard)
6. ✅ DAM (Versiyon + Onay + Önizleme)
7. ✅ Tekrarlayan Görevler (Scheduled command)
8. ✅ Bütçe ve Kârlılık Analizi
9. ✅ Entegrasyonlar (Slack/Discord/Webhook)
10. ✅ Gelişmiş Dashboard & Widget'lar

## 🛡 v3.0 Özeti — Multi-Tenant Güvenlik

v3.0 ile eklenen güvenlik altyapısı:

1. ✅ 3 Katmanlı RBAC (24 rol, 54 permission, platform/tenant/workspace)
2. ✅ `EnsureTenantAccess` middleware (`/admin/*` koruma)
3. ✅ `BelongsToTenant` güvenlik scope'u (veri izolasyonu)
4. ✅ Session-based Impersonate (audit log + UI şerit)
5. ✅ Platform Ekip Yönetimi (`/platform/team`)
6. ✅ Role scope kolonu + hiyerarşi kontrolü
7. ✅ Akıllı login yönlendirme (platform vs tenant)

---

*Son güncelleme: 16 Nisan 2026 — v3.0*

