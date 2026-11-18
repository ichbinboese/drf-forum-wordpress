#!/usr/bin/env bash
set -e

echo "[Flarum Entrypoint] Initialisiere Flarum Community Forum..."

# Clean up any stale temp dir
rm -rf /tmp/flarum

if [ ! -f /var/www/html/composer.json ]; then
    echo "[Flarum] Installiere Flarum via Composer..."
    rm -rf /var/www/html/* /var/www/html/.[!.]* 2>/dev/null || true
    composer create-project flarum/flarum:^2.0 /tmp/flarum --prefer-dist --no-interaction --no-audit
    cp -r /tmp/flarum/. /var/www/html/
    rm -rf /tmp/flarum
fi

# Ensure document root public exists
mkdir -p /var/www/html/public

# Ensure permissions
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/public/assets 2>/dev/null || true

# Wait for database
echo "[Flarum] Warte auf MariaDB Verbindung..."
until mariadb --skip-ssl -h db -u drf_user -pdrf_pass_secret -e "SELECT 1;" >/dev/null 2>&1; do
    sleep 2
done

# If config.php doesn't exist, run automated flarum install CLI
if [ ! -f /var/www/html/config.php ]; then
    echo "[Flarum] Führe automatische Flarum-Installation via CLI aus..."
    cat << "EOF" > /tmp/flarum-install-config.json
{
  "baseUrl": "http://localhost:8080/forum",
  "databaseConfiguration": {
    "driver": "mysql",
    "host": "db",
    "database": "flarum",
    "username": "drf_user",
    "password": "drf_pass_secret",
    "prefix": "",
    "port": 3306
  },
  "adminUser": {
    "username": "admin",
    "password": "password123",
    "email": "tagungsbuero2026@d-r-f.de"
  },
  "settings": {
    "forum_title": "de.rec.fotografie Community Forum"
  }
}
EOF
    php /var/www/html/flarum install -f /tmp/flarum-install-config.json || true
    rm -f /tmp/flarum-install-config.json
fi

# Copy custom assets if present
if [ -d /custom-assets ]; then
    mkdir -p /var/www/html/public/assets/custom
    cp -r /custom-assets/* /var/www/html/public/assets/custom/ 2>/dev/null || true
fi

chown -R www-data:www-data /var/www/html

echo "[Flarum Entrypoint] Flarum ist einsatzbereit."
exec "$@"
