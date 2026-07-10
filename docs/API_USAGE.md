# ADA Co-OS API Kullanım Kılavuzu

ADA Co-OS, dış uygulamalar, mobil istemciler ve entegrasyonlar için güvenli bir REST API sağlar. Tüm istekler `Laravel Sanctum` ile korunmaktadır.

## 🔑 Kimlik Doğrulama (Sanctum)

API istekleri yapmadan önce bir Bearer token almanız gerekmektedir. Kullanıcı adı ve şifrenizle `/login` rotasından veya kullanıcı profilinizden token üretebilirsiniz.

```bash
curl -X POST https://nexus.adacreative.co/api/v1/tokens/create \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password", "device_name": "API Client"}'
```

Dönen token'ı sonraki isteklerinizde `Authorization` başlığı ile göndermelisiniz:
`Authorization: Bearer 1|TohXxxxxxxxxxxxxx...`

## 📚 Endpoint'ler (API v1)

Aşağıdaki endpoint'ler BTM Kesişim projesi kapsamında dışa açılmıştır:

### 1. Kimlik Bilgisi (`/api/v1/me`)
Geçerli token'a sahip kullanıcının detaylarını ve aktif olduğu Tenant (Çalışma Alanı) bilgisini döner.

### 2. Projeler (`/api/v1/projects`)
- `GET /projects` : Kullanıcının yetkisi dahilindeki tüm projeleri listeler.
- `POST /projects` : Yeni bir proje oluşturur.
- `GET /projects/{id}` : Proje detaylarını getirir.
- `PUT /projects/{id}` : Proje günceller.
- `DELETE /projects/{id}` : Projeyi siler.

### 3. Görevler (`/api/v1/tasks`)
Projelerle aynı CRUD yapısına sahiptir, spesifik görevlerin yönetilmesini sağlar.

## 📖 Swagger / OpenAPI Dokümantasyonu

Otomatik olarak üretilen detaylı ve etkileşimli API dokümantasyonu (Swagger UI) için sistemdeki şu adresi ziyaret edebilirsiniz:

👉 **[https://nexus.adacreative.co/docs/api](/docs/api)**

*(Not: Scramble paketi aracılığıyla kod tabanındaki değişiklikler bu sayfaya otomatik yansır.)*
