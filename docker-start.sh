#!/usr/bin/env bash
set -e

echo "========================================================"
echo "  Personal Money Tracker - Docker Server Launcher"
echo "========================================================"
echo ""

if ! command -v docker &> /dev/null; then
    echo "[ERROR] Docker is not installed or not in PATH."
    echo "Please install Docker and Docker Compose before running this script."
    exit 1
fi

if [ ! -f .env.docker ]; then
    echo "[INFO] Creating .env.docker from template..."
    cp .env.docker.example .env.docker
fi

echo "[1/3] Building and starting containerized services..."
docker compose up -d --build

echo ""
echo "[2/3] Checking container status..."
docker compose ps

echo ""
echo "[3/3] Application is ready!"
echo "Open your browser at: http://localhost:8000"
echo ""
echo "Useful commands:"
echo "  View logs:        docker compose logs -f"
echo "  Stop containers:  docker compose down"
echo "  Artisan console:  docker compose exec app php artisan [command]"
