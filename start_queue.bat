@echo off
echo Starting Laravel Queue Worker...
echo This will process queued jobs including broadcasting events
echo.
php artisan queue:work --verbose
pause
