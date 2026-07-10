# ADA Co-OS — Deployment Kılavuzu

## Gereksinimler

| Bileşen | Minimum | Önerilen |
|---------|---------|----------|
| PHP | 8.2 | 8.3 |
| MySQL/MariaDB | 8.0 / 10.6 | 8.0+ / 10.11+ |
| Node.js | 18 | 20 LTS |
| Disk | 500 MB | 2 GB |

## Ortam Değişkenleri (.env)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nexus.adacreative.co

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<hostinger_db_name>
DB_USERNAME=<hostinger_db_user>
DB_PASSWORD=<güvenli_şifre>

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=<sendgrid_api_key>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@adacreative.co
MAIL_FROM_NAME="ADA Co-OS"
```

## İlk Kurulum

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan config:cache
php artisan view:cache
php artisan route:cache
npm ci && npm run build
```

## Deploy (Güncelleme)

```bash
# Tam deploy
python safe_deploy.py

# Belirli dosyalar
python safe_deploy.py app/Models/User.php resources/views/welcome.blade.php
```

**SSH şifresini ortam değişkeninde tutun:**
```bash
export NEXUS_SSH_PASS="şifreniz"
python safe_deploy.py
```

## Cron Kurulumu

Hostinger hPanel → Gelişmiş → Cron İşleri:

```
*/5 * * * * cd /home/u541429127/domains/nexus.adacreative.co/public_html && /opt/alt/php83/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

## Sorun Giderme

| Sorun | Çözüm |
|-------|-------|
| 500 hatası | `storage/logs/laravel.log` kontrol et |
| Migration hatası | `php artisan migrate:status` |
| E-posta gönderilmiyor | `php artisan mail:test admin@adacreative.co` |
| Health check | `curl https://nexus.adacreative.co/health` |
