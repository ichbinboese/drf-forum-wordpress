#!/usr/bin/env bash
set -e

echo "=== [DRF] Initialisiere WordPress & Theme drf-augenblicke ==="

# Wait for WordPress to be accessible
until docker compose exec -T wordpress php -r "echo 'WP container ready';" >/dev/null 2>&1; do
    echo "Warte auf WordPress Container..."
    sleep 2
done

# Run WP-CLI to install core if not installed
echo "Prüfe WordPress Installation..."
if ! docker compose run --rm wpcli core is-installed 2>/dev/null; then
    echo "Installiere WordPress Core..."
    docker compose run --rm wpcli core install \
        --url="http://localhost:8080" \
        --title="d.r.f Tagungsbüro »Augenblicke«" \
        --admin_user="admin" \
        --admin_password="password123" \
        --admin_email="tagungsbuero2026@d-r-f.de" \
        --skip-email
fi

echo "Aktiviere Theme drf-augenblicke..."
docker compose run --rm wpcli theme activate drf-augenblicke

echo "Aktiviere Plugin drf-flarum-bridge..."
docker compose run --rm wpcli plugin activate drf-flarum-bridge

echo "Konfiguriere Permalinks..."
docker compose run --rm wpcli rewrite structure '/%postname%/' --hard

echo "Erstelle Standard-Seiten falls nötig..."
# Pages list
PAGES=("verein:Verein" "augenblicke:Augenblicke" "tagungsbuero:Tagungsbüro" "sozialgremium:Sozialgremium" "shuttleservice:Shuttleservice" "workshops:Workshops" "impressum:Impressum" "datenschutz:Datenschutz")

for item in "${PAGES[@]}"; do
    slug="${item%%:*}"
    title="${item##*:}"
    if ! docker compose run --rm wpcli post list --name="$slug" --post_type=page --format=ids | grep -q '[0-9]'; then
        echo "Erstelle Seite: $title ($slug)..."
        docker compose run --rm wpcli post create --post_type=page --post_title="$title" --post_name="$slug" --post_status=publish
    fi
done

echo "Erstelle und verknüpfe Hauptmenü..."
if ! docker compose run --rm wpcli menu list --format=csv | grep -q "Hauptnavigation"; then
    MENU_ID=$(docker compose run --rm wpcli menu create "Hauptnavigation" --porcelain)
    docker compose run --rm wpcli menu location assign "$MENU_ID" primary-menu
    
    # Add items to menu
    for item in "${PAGES[@]}"; do
        slug="${item%%:*}"
        PAGE_ID=$(docker compose run --rm wpcli post list --name="$slug" --post_type=page --field=ID)
        if [ -n "$PAGE_ID" ]; then
            docker compose run --rm wpcli menu item add-post "$MENU_ID" "$PAGE_ID"
        fi
    done
fi

echo "=== [DRF] WordPress & Theme erfolgreich eingerichtet! ==="
echo "URL: http://localhost:8080/"
echo "Admin: http://localhost:8080/wp-admin/ (admin / password123)"
