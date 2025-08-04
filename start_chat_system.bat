@echo off
title CHAT SYSTEM - All Services
echo ================================
echo    CHAT SYSTEM STARTER
echo ================================
echo.
echo This will start all required services:
echo - Laravel Reverb (WebSocket)
echo - Laravel Queue Worker
echo - Vite Development Server
echo.
echo Make sure you have:
echo 1. Composer dependencies installed
echo 2. NPM dependencies installed (npm install)
echo 3. Database migrated and seeded
echo.

pause
echo.

echo Starting services in new windows...

echo [1/3] Starting Laravel Reverb...
start "Laravel Reverb" cmd /k "php artisan reverb:start --host=0.0.0.0 --port=8080"
timeout /t 3

echo [2/3] Starting Laravel Queue Worker...
start "Laravel Queue" cmd /k "php artisan queue:work --tries=3 --timeout=60"
timeout /t 3

echo [3/3] Starting Vite Development Server... 
start "Vite Dev Server" cmd /k "npm run dev"
timeout /t 3

echo.
echo ================================
echo    ALL SERVICES STARTED!
echo ================================
echo.
echo Services running:
echo - Reverb WebSocket: ws://localhost:8080
echo - Queue Worker: Running in background
echo - Vite Dev Server: http://localhost:5173
echo.
echo Now you can test the chat system!
echo Navigate to your Laravel app and try sending messages.
echo.
echo To stop all services, close the opened terminal windows.
echo.

pause
