#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

PORT=8080

echo "=========================================================="
echo "  d.r.f (de.rec.fotografie) - Nativer macOS PHP Server"
echo "  ⚡ 100% ohne Docker • 0% CPU Leerlauf • Keine Lüfterhitze"
echo "=========================================================="

# Check if already running
if lsof -i :$PORT >/dev/null 2>&1; then
    echo "ℹ️  Server läuft bereits auf Port ${PORT}!"
else
    echo "Starte nativen Server auf Port ${PORT}..."
    nohup /usr/local/bin/php -d memory_limit=512M -S "0.0.0.0:${PORT}" scripts/router.php > server.log 2>&1 &
    echo $! > server.pid
    sleep 1
    echo "Server erfolgreich gestartet! (PID: $(cat server.pid))"
fi

echo ""
echo "=========================================================="
echo "  PLATTFORM ERFOLGREICH BEREIT: 🚀"
echo "=========================================================="
echo "  🌐 Hauptseite (WordPress):  http://localhost:${PORT}/"
echo "  💬 Forum (Flarum):           http://localhost:${PORT}/forum/"
echo "  📁 Dateiablage (FileGator):  http://localhost:${PORT}/dateien/"
echo "     Benutzer: admin oder orga / Passwort: password123"
echo "  📝 Anmeldung 2026 / 2027:    http://localhost:${PORT}/tagungsbuero/"
echo "  ⚙️  WordPress Admin:        http://localhost:${PORT}/wp-admin/"
echo "     Benutzer: admin / Passwort: password123"
echo "  🔧 Flarum Admin:            http://localhost:${PORT}/forum/admin"
echo "     Benutzer: admin / Passwort: password123"
echo "=========================================================="
echo "  Logs einsehen: tail -f server.log"
echo "  Beenden mit:   ./stop.sh"
echo "=========================================================="
