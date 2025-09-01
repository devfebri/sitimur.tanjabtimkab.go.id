# QUICK DEPLOYMENT CHECKLIST - SITIMUR

## ✅ PRE-UPLOAD CHECKLIST (LOKAL)

- ✅ Livewire assets published: `php artisan livewire:publish --assets`
- ✅ .env.production template sudah dibuat
- ✅ .htaccess dioptimasi untuk production  
- ✅ production-setup.sh & install-production.sh sudah dibuat
- ✅ Storage link sudah dibuat
- ✅ Semua cache dibersihkan (clean state)

## 📤 UPLOAD TO SERVER

### Files yang harus di-upload:
```
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/framework/ (kosong)
├── storage/app/ (kosong)
├── .env.production
├── .htaccess (di public/)
├── artisan
├── composer.json
├── composer.lock
├── install-production.sh
├── production-setup.sh
├── PRODUCTION_DEPLOYMENT_GUIDE.md
└── package.json
```

### Files yang TIDAK di-upload:
```
- .env (akan dibuat dari .env.production)
- vendor/ (akan diinstall via composer)
- node_modules/
- storage/logs/*
- .git/
- bootstrap/cache/*
```

## 🚀 SERVER SETUP COMMANDS

```bash
# 1. Set execute permission
chmod +x install-production.sh

# 2. Run installation script
./install-production.sh

# 3. Edit .env file
nano .env
# Edit:
# - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# - MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD
# - APP_URL=https://sitimur.tanjabtimkab.go.id

# 4. Test database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';"

# 5. Setup cron job
crontab -e
# Add: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## 🌐 WEB SERVER SETUP

### Apache (.htaccess already configured)
- Point DocumentRoot ke `/path/to/project/public`
- Enable mod_rewrite
- Setup SSL certificate

### Nginx
- Use config from PRODUCTION_DEPLOYMENT_GUIDE.md
- Point root ke `/path/to/project/public`
- Setup SSL certificate

## 🧪 POST-DEPLOYMENT TESTING

### Test URLs:
- ✅ https://sitimur.tanjabtimkab.go.id
- ✅ https://sitimur.tanjabtimkab.go.id/login
- ✅ https://sitimur.tanjabtimkab.go.id/vendor/livewire/livewire.js

### Test Functionality:
- ✅ Login system
- ✅ Chat system (Livewire working)
- ✅ File upload/download
- ✅ Auto-expire pengajuan command: `php artisan pengajuan:auto-expire`

## 🔧 TROUBLESHOOTING

### Livewire JS 404:
```bash
php artisan livewire:publish --assets
ls -la public/vendor/livewire/livewire.js
```

### Permission Issues:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/
```

### Cache Issues:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## 📊 MONITORING

### Check Logs:
```bash
tail -f storage/logs/laravel.log
tail -f /var/log/apache2/error.log
```

### Test Commands:
```bash
# Test database
php artisan tinker --execute="DB::connection()->getPdo();"

# Test auto-expire
php artisan pengajuan:auto-expire

# Check scheduler
php artisan schedule:list
```

## 🎯 FINAL CHECKLIST

- ✅ APP_ENV=production in .env
- ✅ APP_DEBUG=false in .env  
- ✅ Database connected and migrated
- ✅ Livewire JS accessible
- ✅ Storage symlink working
- ✅ HTTPS enabled and working
- ✅ Cron job configured
- ✅ All main features tested
- ✅ Error logs monitored

## 🆘 EMERGENCY ROLLBACK

If something goes wrong:
```bash
# 1. Restore database backup
mysql -u user -p database_name < backup.sql

# 2. Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 3. Check file permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# 4. Check logs for errors
tail -f storage/logs/laravel.log
```

---
**Deployment Date:** [Today's Date]  
**Version:** Production Ready  
**Contact:** [Your Contact Info]
