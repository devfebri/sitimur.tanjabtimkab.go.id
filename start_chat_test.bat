@echo off
echo ========================================
echo       STARTING CHAT SYSTEM TEST
echo ========================================
echo.
echo 1. Clearing cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Checking Livewire...
php artisan tinker --execute="echo 'Livewire: ' . (class_exists('Livewire\\Livewire') ? 'OK' : 'FAILED'); echo PHP_EOL; echo 'CustomChat: ' . (class_exists('App\\Livewire\\CustomChat') ? 'OK' : 'FAILED');"

echo.
echo 3. Starting Laravel server...
echo    Access: http://localhost:8000/test-chat
echo    PPK Chat: http://localhost:8000/ppk/chats  
echo    Pokja Chat: http://localhost:8000/pokjapemilihan/chats
echo.
echo Starting server in 3 seconds...
timeout /t 3 /nobreak > nul

php artisan serve --port=8000
