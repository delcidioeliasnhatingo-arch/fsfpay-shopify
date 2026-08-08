#!/bin/bash
set -e

PORT="${PORT:-8080}"

echo "Starting FSFPAY Shopify Payment Module on port ${PORT}..."

if [ -f "composer.json" ]; then
    composer install --no-dev --optimize-autoloader 2>/dev/null || true
fi

exec php -S 0.0.0.0:${PORT} -t public public/index.php
