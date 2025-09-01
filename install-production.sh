#!/bin/bash

# Production Installation Script for SITIMUR
# Jalankan script ini di server production setelah upload files

echo "🚀 Starting SITIMUR Production Installation..."
echo "=================================================="

# 1. Install Composer Dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Setup Environment
echo "⚙️  Setting up environment..."
if [ ! -f .env ]; then
    cp .env.production .env
    echo "✅ .env file created from template"
    echo "⚠️  IMPORTANT: Edit .env file to configure database and mail settings"
else
    echo "⚠️  .env file already exists, skipping..."
fi

# 3. Generate Application Key
echo "🔑 Generating application key..."
php artisan key:generate --force

# 4. Create Storage Directories
echo "📁 Creating storage directories..."
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# 5. Set Permissions
echo "🔒 Setting permissions..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod +x artisan

# 6. Create Storage Link
echo "🔗 Creating storage symlink..."
php artisan storage:link

# 7. Publish Livewire Assets
echo "⚡ Publishing Livewire assets..."
php artisan livewire:publish --assets

# 8. Run Database Migrations (akan diminta konfirmasi)
echo "🗄️  Running database migrations..."
echo "⚠️  Make sure database credentials in .env are correct before continuing!"
read -p "Continue with database migration? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    echo "✅ Database migrations completed"
else
    echo "⏭️  Skipping database migrations"
fi

# 9. Seed Database (optional)
read -p "Run database seeding? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
    echo "✅ Database seeding completed"
else
    echo "⏭️  Skipping database seeding"
fi

# 10. Cache Configuration for Performance
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 11. Test Critical Components
echo "🧪 Testing critical components..."

# Test database connection
echo "Testing database connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database: Connected ✅'; } catch(Exception \$e) { echo 'Database: Failed ❌ - ' . \$e->getMessage(); }"

# Test Livewire assets
if [ -f "public/vendor/livewire/livewire.js" ]; then
    echo "Livewire Assets: Found ✅"
else
    echo "Livewire Assets: Missing ❌"
fi

# Test storage link
if [ -L "public/storage" ]; then
    echo "Storage Link: Created ✅"
else
    echo "Storage Link: Missing ❌"
fi

echo ""
echo "🎉 Production installation completed!"
echo "=================================================="
echo ""
echo "📋 Next Steps:"
echo "1. ⚙️  Edit .env file with your production settings:"
echo "   - Database credentials"
echo "   - Mail server settings"
echo "   - APP_URL=https://sitimur.tanjabtimkab.go.id"
echo ""
echo "2. 🌐 Configure your web server (Apache/Nginx) to point to 'public' folder"
echo ""
echo "3. 🔒 Setup SSL certificate for HTTPS"
echo ""
echo "4. ⏰ Setup cron job for Laravel scheduler:"
echo "   * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "5. 🧪 Test the application:"
echo "   - Homepage: https://sitimur.tanjabtimkab.go.id"
echo "   - Login page: https://sitimur.tanjabtimkab.go.id/login"
echo "   - Livewire JS: https://sitimur.tanjabtimkab.go.id/vendor/livewire/livewire.js"
echo ""
echo "6. 📊 Monitor logs:"
echo "   - Application: tail -f storage/logs/laravel.log"
echo "   - Web server: tail -f /var/log/apache2/error.log"
echo ""
echo "🆘 If you encounter issues, check PRODUCTION_DEPLOYMENT_GUIDE.md"
echo "=================================================="
