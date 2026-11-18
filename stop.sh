#!/usr/bin/env bash
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

echo "Stoppe lokale Server..."

if command -v docker >/dev/null 2>&1 && docker info >/dev/null 2>&1; then
    docker compose down 2>/dev/null || true
fi

# Kill any running standalone router
pkill -f "scripts/router.php" 2>/dev/null || true

echo "Alle Server gestoppt."
