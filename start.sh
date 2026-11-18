#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

echo "=========================================================="
echo "  d.r.f (de.rec.fotografie) - WordPress 6.7 & Flarum Stack"
echo "=========================================================="

if command -v docker >/dev/null 2>&1 && docker info >/dev/null 2>&1; then
    echo "1. Starte Docker Compose Services..."
    docker compose up -d

    echo "2. Initialisiere WordPress & Theme drf-augenblicke..."
    bash ./scripts/init-wordpress.sh || true

    echo ""
    echo "=========================================================="
    echo "  LOKALER SERVER ERFOLGREICH GESTARTET! 🚀"
    echo "=========================================================="
    echo "  🌐 Hauptseite (WordPress):  http://localhost:8080/"
    echo "  💬 Forum (Flarum):           http://localhost:8080/forum/"
    echo "  ⚙️  WordPress Admin:        http://localhost:8080/wp-admin/"
    echo "     Benutzer: admin / Passwort: password123"
    echo "=========================================================="
else
    echo "⚠️ Docker Daemon nicht verfügbar. Starte Standalone PHP Server..."
    exec bash ./start-standalone.sh
fi
