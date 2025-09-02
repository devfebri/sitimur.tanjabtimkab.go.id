#!/bin/bash

# Server Troubleshooting Script untuk SITIMUR
# File: server_debug.sh
# Usage: bash server_debug.sh

echo "======================================"
echo "SITIMUR SERVER DEBUGGING SCRIPT"
echo "======================================"
echo ""

# 1. Cek File Permissions
echo "1. CHECKING FILE PERMISSIONS"
echo "------------------------------"
echo "Storage directory permissions:"
ls -la storage/
echo ""
echo "Bootstrap cache permissions:"
ls -la bootstrap/cache/
echo ""
echo "Public directory permissions:"
ls -la public/ | head -10
echo ""

# 2. Cek Database Connection
echo "2. CHECKING DATABASE CONNECTION"
echo "------------------------------"
php -r "
try {
    \$pdo = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
    echo '✅ Database connection: SUCCESS\n';
    \$stmt = \$pdo->query('SELECT COUNT(*) as total FROM metode_pengadaan_berkass');
    \$result = \$stmt->fetch();
    echo '✅ metode_pengadaan_berkass table: ' . \$result['total'] . ' records\n';
} catch(Exception \$e) {
    echo '❌ Database connection: FAILED - ' . \$e->getMessage() . '\n';
}
"
echo ""

# 3. Cek Laravel Configuration
echo "3. CHECKING LARAVEL CONFIGURATION"
echo "------------------------------"
echo "Current environment:"
php artisan env
echo ""
echo "Config cache status:"
if [ -f "bootstrap/cache/config.php" ]; then
    echo "✅ Config is cached"
else
    echo "⚠️ Config is not cached"
fi
echo ""
echo "Route cache status:"
if [ -f "bootstrap/cache/routes-v7.php" ]; then
    echo "✅ Routes are cached"
else
    echo "⚠️ Routes are not cached"
fi
echo ""

# 4. Cek Logs
echo "4. CHECKING ERROR LOGS"
echo "------------------------------"
echo "Laravel log (last 20 lines):"
if [ -f "storage/logs/laravel.log" ]; then
    tail -20 storage/logs/laravel.log
else
    echo "❌ Laravel log file not found"
fi
echo ""

# 5. Test Database Query
echo "5. TESTING DATABASE QUERIES"
echo "------------------------------"
echo "Testing metodePengadaanBerkas query:"
php artisan tinker --execute="
\$data = DB::select('SELECT COUNT(*) as total FROM metode_pengadaan_berkass WHERE metode_pengadaan_id = 1');
echo 'Berkas for metode_pengadaan_id=1: ' . \$data[0]->total . PHP_EOL;

\$pengajuan = DB::table('pengajuans')->orderBy('id', 'desc')->first();
if(\$pengajuan) {
    echo 'Latest pengajuan ID: ' . \$pengajuan->id . ' (metode: ' . \$pengajuan->metode_pengadaan_id . ')' . PHP_EOL;
} else {
    echo 'No pengajuan found' . PHP_EOL;
}
"
echo ""

# 6. Cek Web Server Access
echo "6. CHECKING WEB SERVER ACCESS"
echo "------------------------------"
echo "Testing internal URL access:"
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" "http://localhost/debug-berkas/1/1" 2>/dev/null || echo "❌ Cannot test localhost access"
echo ""

# 7. Cek PHP Version & Extensions
echo "7. CHECKING PHP CONFIGURATION"
echo "------------------------------"
echo "PHP Version:"
php -v | head -1
echo ""
echo "Required PHP extensions:"
php -m | grep -E "(pdo|mysql|json|curl|mbstring|openssl)" | while read ext; do
    echo "✅ $ext"
done
echo ""

# 8. Cek .env File
echo "8. CHECKING ENVIRONMENT FILE"
echo "------------------------------"
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    echo "APP_ENV: $(grep APP_ENV .env)"
    echo "APP_DEBUG: $(grep APP_DEBUG .env)"
    echo "DB_HOST: $(grep DB_HOST .env)"
    echo "DB_DATABASE: $(grep DB_DATABASE .env)"
else
    echo "❌ .env file not found"
fi
echo ""

# 9. Recommended Actions
echo "9. RECOMMENDED ACTIONS"
echo "------------------------------"
echo "If data berkas is not showing:"
echo "1. Run: php artisan config:clear"
echo "2. Run: php artisan route:clear"  
echo "3. Run: php artisan view:clear"
echo "4. Run: php artisan cache:clear"
echo "5. Check: chmod -R 775 storage/"
echo "6. Check: chmod -R 775 bootstrap/cache/"
echo "7. Test: Access /debug-berkas/{pengajuan_id}/{metode_id}"
echo "8. Check Laravel logs in storage/logs/"
echo ""

echo "======================================"
echo "DEBUGGING SCRIPT COMPLETED"
echo "======================================"
