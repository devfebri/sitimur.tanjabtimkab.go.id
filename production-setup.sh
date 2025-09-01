#!/bin/bash

# Script untuk setup permissions Laravel di server Linux
# Jalankan script ini setelah upload ke server

echo "Setting up Laravel permissions for production..."

# Set ownership ke web server user (biasanya www-data atau nginx)
# sudo chown -R www-data:www-data /path/to/your/laravel/project

# Set permissions untuk folders
find . -type d -exec chmod 755 {} \;

# Set permissions untuk files
find . -type f -exec chmod 644 {} \;

# Set permissions khusus untuk storage dan bootstrap/cache
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Set permissions untuk artisan
chmod +x artisan

echo "Permissions set successfully!"
echo ""
echo "Next steps:"
echo "1. Copy .env.production to .env and edit database credentials"
echo "2. Run: php artisan key:generate"
echo "3. Run: php artisan migrate --force"
echo "4. Run: php artisan db:seed (if needed)"
echo "5. Run: php artisan config:cache"
echo "6. Run: php artisan route:cache"
echo "7. Run: php artisan view:cache"
echo "8. Run: php artisan storage:link"
echo "9. Setup cron job for scheduler: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1"
