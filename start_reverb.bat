@echo off
echo Starting Laravel Reverb WebSocket Server...
echo Make sure to open another terminal to run queue worker if needed
echo.
php artisan reverb:start --debug
pause
