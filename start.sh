#!/bin/bash
set -e
if [ -f composer.json ]; then
    composer install --no-dev --optimize-autoloader
fi
php -S 0.0.0.0:${PORT:-8080} -t public/
