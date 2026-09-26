@echo off
echo ========================================================
echo   Personal Money Tracker - Docker Server Launcher
echo ========================================================
echo.

where docker >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Docker is not installed or not in your system PATH.
    echo Please install Docker Desktop from https://www.docker.com/products/docker-desktop/
    echo and ensure Docker is running before executing this script.
    echo.
    echo Alternatively, you can run the app locally without Docker:
    echo   php artisan serve --port=8000
    echo   npm run dev
    echo.
    pause
    exit /b 1
)

if not exist .env.docker (
    echo [INFO] Creating .env.docker from template...
    copy .env.docker.example .env.docker
)

echo [1/3] Building and starting containerized services...
docker compose up -d --build

echo.
echo [2/3] Checking container status...
docker compose ps

echo.
echo [3/3] Application is ready!
echo Open your browser at: http://localhost:8000
echo.
echo Useful commands:
echo   View logs:        docker compose logs -f
echo   Stop containers:  docker compose down
echo   Artisan console:  docker compose exec app php artisan [command]
echo.
pause
