#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

PORT=8080
if lsof -i :$PORT >/dev/null 2>&1; then
    PORT=8000
fi

echo "=========================================================="
echo "  d.r.f Augenblicke - Standalone PHP Development Server"
echo "=========================================================="
echo "  🌐 URL: http://localhost:${PORT}/"
echo "  💬 Forum Preview: http://localhost:${PORT}/forum/"
echo "  Beenden mit: Ctrl + C oder ./stop.sh"
echo "=========================================================="

php -S "0.0.0.0:${PORT}" -t . scripts/router.php
