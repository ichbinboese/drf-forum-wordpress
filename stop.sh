#!/usr/bin/env bash
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

echo "Stoppe lokale Server..."

if command -v docker >/dev/null 2>&1 && docker info >/dev/null 2>&1; then
    docker compose down 2>/dev/null || true
fi

# Kill any running standalone router
if [ -f server.pid ]; then
    PID=$(cat server.pid 2>/dev/null || true)
    if [ -n "$PID" ] && kill -0 "$PID" 2>/dev/null; then
        kill "$PID" 2>/dev/null || true
    fi
    rm -f server.pid
fi
pkill -f "scripts/router.php" 2>/dev/null || true

echo "Alle Server gestoppt."
