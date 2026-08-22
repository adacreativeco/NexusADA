#!/bin/bash
set -e

echo "🚀 Deploying ADA Co-OS (NexusADA) Production Release..."

# Put app in maintenance mode
php artisan down || true

# Pull latest commits
git pull origin main

# Install composer dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Build assets
npm ci || npm install
npm run build

# Run database migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart queues and reverb
php artisan queue:restart
supervisorctl restart all || true

# Bring app back up
php artisan up

echo "✅ Deployment completed successfully!"
