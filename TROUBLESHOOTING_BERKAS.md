# TROUBLESHOOTING: Data Berkas Tidak Muncul di Step 2

## 🔍 Analisis Masalah

Berdasarkan kode yang ada, data berkas pada step 2 dimuat melalui AJAX call ke endpoint:
```
/{role}_metode_pengadaan_berkas/{pengajuan_id}?metode={metode_pengadaan_id}
```

Yang menjalankan method `metodePengadaanBerkas()` di `PengajuanController.php`.

## 🧪 Langkah Debugging di Server

### 1. Upload & Jalankan Script Debug

Upload file `debug_berkas.php` ke folder `public/` di server, lalu akses:
```
https://sitimur.tanjabtimkab.go.id/debug_berkas.php
```

### 2. Test dengan Route Debug

Setelah file terupload, test endpoint debug:
```
https://sitimur.tanjabtimkab.go.id/debug-berkas/1/1
```
*(ganti 1/1 dengan pengajuan_id dan metode_pengadaan_id yang sesuai)*

### 3. Jalankan Server Debug Script

Di terminal server:
```bash
chmod +x server_debug.sh
./server_debug.sh
```

## 🔧 Kemungkinan Penyebab & Solusi

### A. Database Issues

**Penyebab:**
- Tabel `metode_pengadaan_berkass` kosong di server
- Data tidak ter-sync antara local dan server
- Koneksi database salah

**Solusi:**
```sql
-- Cek apakah tabel ada dan berisi data
SELECT COUNT(*) FROM metode_pengadaan_berkass;
SELECT * FROM metode_pengadaan_berkass LIMIT 5;

-- Cek data pengajuan terbaru
SELECT id, nama_paket, metode_pengadaan_id FROM pengajuans ORDER BY created_at DESC LIMIT 5;
```

### B. Environment Configuration

**Penyebab:**
- File `.env` tidak sesuai dengan database server
- Cache configuration bermasalah

**Solusi:**
```bash
# Clear semua cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
```

### C. File Permissions

**Penyebab:**
- Permission storage/ atau bootstrap/cache/ salah
- Web server tidak bisa write logs

**Solusi:**
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### D. JavaScript/AJAX Issues

**Penyebab:**
- URL route tidak sesuai di server
- JavaScript error di browser
- CSRF token issue

**Debugging:**
1. Buka Developer Tools (F12) di browser
2. Check Console tab untuk errors
3. Check Network tab saat klik "Lanjut ke Upload Dokumen"
4. Lihat request URL dan response

### E. Laravel Route Issues

**Penyebab:**
- Route cache tidak sesuai
- Middleware blocking request

**Solusi:**
```bash
# Check routes
php artisan route:list | grep metode_pengadaan_berkas

# Clear route cache
php artisan route:clear
```

## 📋 Checklist Debugging

### ✅ Step 1: Verifikasi Database
- [ ] Tabel `metode_pengadaan_berkass` ada dan berisi data
- [ ] Tabel `pengajuans` ada dan berisi data
- [ ] Koneksi database berfungsi (test dengan `php artisan tinker`)

### ✅ Step 2: Verifikasi Environment
- [ ] File `.env` sesuai dengan server database
- [ ] `APP_ENV=production` dan `APP_DEBUG=false`
- [ ] URL aplikasi sesuai (`APP_URL`)

### ✅ Step 3: Verifikasi Cache
- [ ] Clear semua cache Laravel
- [ ] Test akses route debug
- [ ] Check permission storage dan bootstrap/cache

### ✅ Step 4: Verifikasi JavaScript
- [ ] Buka Developer Tools browser
- [ ] Check console errors
- [ ] Check network requests saat AJAX call

### ✅ Step 5: Verifikasi Response
- [ ] Test endpoint manual: `/debug-berkas/{id}/{metode}`
- [ ] Check Laravel logs di `storage/logs/laravel.log`
- [ ] Verify response format JSON

## 🚨 Quick Fix Commands

Jalankan di server untuk quick fix:

```bash
# 1. Clear semua cache
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear

# 2. Fix permissions
chmod -R 775 storage/ bootstrap/cache/

# 3. Test database
php artisan tinker --execute="echo 'DB Test: '; var_dump(DB::connection()->getPdo());"

# 4. Check logs
tail -50 storage/logs/laravel.log

# 5. Test route
curl -s "http://localhost/debug-berkas/1/1" | jq .
```

## 📞 Debug Information Needed

Untuk troubleshooting lebih lanjut, kirim informasi berikut:

1. **Output dari `debug_berkas.php`**
2. **Response dari `/debug-berkas/{id}/{metode}`**
3. **Laravel logs** (`storage/logs/laravel.log`)
4. **Browser console errors** (F12 → Console)
5. **Network requests** (F12 → Network → saat klik tombol)

## 🔗 Test URLs

Setelah upload ke server, test URL berikut:

1. **Manual Debug:** `https://sitimur.tanjabtimkab.go.id/debug_berkas.php`
2. **Route Debug:** `https://sitimur.tanjabtimkab.go.id/debug-berkas/1/1`
3. **Actual Endpoint:** `https://sitimur.tanjabtimkab.go.id/{role}_metode_pengadaan_berkas/1?metode=1`

Ganti `{role}` dengan role user yang login (misal: `ppk`, `pokja`, dll.)

---

**📝 Catatan:** File debugging (`debug_berkas.php`) sebaiknya dihapus setelah troubleshooting selesai untuk keamanan production.
