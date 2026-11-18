#!/usr/bin/env bash
set -e

echo "[Flarum Entrypoint] Initialisiere Flarum Community Forum..."

# Ensure document root public exists
mkdir -p /var/www/html/public

if [ ! -f /var/www/html/composer.json ]; then
    echo "[Flarum] Installiere Flarum via Composer..."
    composer create-project flarum/flarum . --stability=stable --prefer-dist --no-interaction
fi

# Ensure permissions
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/public/assets

# Copy custom assets if present
if [ -d /custom-assets ]; then
    mkdir -p /var/www/html/public/assets/custom
    cp -r /custom-assets/* /var/www/html/public/assets/custom/ || true
fi

echo "[Flarum Entrypoint] Flarum ist einsatzbereit."
exec "$@"
