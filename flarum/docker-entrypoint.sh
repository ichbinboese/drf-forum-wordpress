#!/usr/bin/env bash
set -e

echo "[Flarum Entrypoint] Initialisiere Flarum Community Forum..."

if [ ! -f /var/www/html/composer.json ]; then
    echo "[Flarum] Installiere Flarum via Composer..."
    rm -rf /var/www/html/* /var/www/html/.[!.]*
    composer create-project flarum/flarum /tmp/flarum --stability=stable --prefer-dist --no-interaction
    cp -r /tmp/flarum/. /var/www/html/
    rm -rf /tmp/flarum
fi

# Ensure document root public exists
mkdir -p /var/www/html/public

# Ensure permissions
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/public/assets 2>/dev/null || true

# Copy custom assets if present
if [ -d /custom-assets ]; then
    mkdir -p /var/www/html/public/assets/custom
    cp -r /custom-assets/* /var/www/html/public/assets/custom/ || true
fi

echo "[Flarum Entrypoint] Flarum ist einsatzbereit."
exec "$@"
