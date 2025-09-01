# PANDUAN DEPLOYMENT PRODUCTION SITIMUR

## Persiapan File untuk Production

### 1. File yang Sudah Disiapkan:
- ✅ Livewire assets sudah dipublish ke `public/vendor/livewire/`
- ✅ .env.production template sudah dibuat
- ✅ .htaccess sudah dioptimasi untuk production
- ✅ production-setup.sh script sudah dibuat
- ✅ Cache sudah dioptimasi

### 2. Langkah Deploy ke Server:

#### A. Upload Files
```bash
# Upload semua file kecuali:
# - .env (akan dibuat dari .env.production)
# - node_modules/
# - .git/
# - storage/logs/* (kosongkan)
# - vendor/ (akan diinstall via composer)
```

#### B. Setup di Server
```bash
# 1. Set permissions (Linux)
chmod +x production-setup.sh
./production-setup.sh

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Setup environment
cp .env.production .env
nano .env  # Edit database dan mail credentials

# 4. Generate application key
php artisan key:generate --force

# 5. Run migrations
php artisan migrate --force

# 6. Seed database (jika diperlukan)
php artisan db:seed --force

# 7. Cache untuk performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Setup storage link
php artisan storage:link

# 9. Setup scheduler (crontab -e)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

#### C. Web Server Configuration

**Apache Virtual Host:**
```apache
<VirtualHost *:80>
    ServerName sitimur.tanjabtimkab.go.id
    DocumentRoot /path/to/project/public
    
    <Directory /path/to/project/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sitimur_error.log
    CustomLog ${APACHE_LOG_DIR}/sitimur_access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName sitimur.tanjabtimkab.go.id
    DocumentRoot /path/to/project/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/private.key
    
    <Directory /path/to/project/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sitimur_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/sitimur_ssl_access.log combined
</VirtualHost>
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name sitimur.tanjabtimkab.go.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name sitimur.tanjabtimkab.go.id;
    root /path/to/project/public;
    index index.php;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/private.key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 3. Post-Deployment Checklist:

#### A. Testing URLs:
- ✅ https://sitimur.tanjabtimkab.go.id (homepage)
- ✅ https://sitimur.tanjabtimkab.go.id/login
- ✅ https://sitimur.tanjabtimkab.go.id/vendor/livewire/livewire.js
- ✅ https://sitimur.tanjabtimkab.go.id/storage (symlink)

#### B. Functionality Testing:
- ✅ Login system
- ✅ Chat system (Livewire)
- ✅ File upload/download
- ✅ Auto-expire pengajuan command
- ✅ Database connections
- ✅ Email notifications

#### C. Performance Optimization:
```bash
# Enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1

# Monitor logs
tail -f storage/logs/laravel.log
tail -f /var/log/apache2/sitimur_error.log
```

### 4. Database Configuration:

#### A. Edit .env untuk Production:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sitimur_production
DB_USERNAME=sitimur_user
DB_PASSWORD=strong_password_here
```

#### B. Create Database:
```sql
CREATE DATABASE sitimur_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sitimur_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON sitimur_production.* TO 'sitimur_user'@'localhost';
FLUSH PRIVILEGES;
```

### 5. Security Considerations:

#### A. File Permissions:
```bash
# Laravel folders should be 755
find . -type d -exec chmod 755 {} \;

# Laravel files should be 644
find . -type f -exec chmod 644 {} \;

# Storage and cache need write permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Artisan needs execute permission
chmod +x artisan
```

#### B. Hide Sensitive Files:
```apache
# .htaccess in root (not public)
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

### 6. Monitoring & Maintenance:

#### A. Log Rotation:
```bash
# Setup logrotate for Laravel logs
sudo nano /etc/logrotate.d/laravel

/path/to/project/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    notifempty
    create 644 www-data www-data
}
```

#### B. Backup Strategy:
```bash
#!/bin/bash
# Daily backup script
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u sitimur_user -p sitimur_production > /backups/sitimur_$DATE.sql
tar -czf /backups/sitimur_files_$DATE.tar.gz /path/to/project --exclude=vendor --exclude=node_modules
```

### 7. Troubleshooting Common Issues:

#### A. Livewire JS 404 Error:
```bash
php artisan livewire:publish --assets
php artisan view:clear
```

#### B. Storage Permission Issues:
```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

#### C. Cache Issues:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### D. Database Connection Issues:
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

### 8. Final Production Checklist:

- ✅ APP_ENV=production
- ✅ APP_DEBUG=false
- ✅ APP_URL=https://sitimur.tanjabtimkab.go.id
- ✅ Database credentials configured
- ✅ Mail configuration set
- ✅ HTTPS certificate installed
- ✅ Livewire assets published
- ✅ Storage symlink created
- ✅ Cron job for scheduler configured
- ✅ File permissions set correctly
- ✅ Error logs monitoring setup
- ✅ Backup strategy implemented

## Support & Maintenance

Untuk bantuan teknis dan maintenance aplikasi, hubungi:
- Developer: [Your Contact Info]
- System Admin: [Admin Contact Info]

Semua konfigurasi di atas sudah dioptimasi untuk production environment dengan fokus pada security, performance, dan reliability.
