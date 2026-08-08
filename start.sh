#!/usr/bin/env bash
set -e

PORT="${PORT:-8080}"

echo "Starting FSFPAY Shopify Payment Module on port ${PORT}..."

if [ -f "artisan" ] && [ -f "vendor/autoload.php" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
fi

exec php -S 0.0.0.0:"${PORT}" -t public public/index.php
