# Auto-Change Pengajuan Status Implementation

## Overview
Implementasi otomatis untuk mengubah status pengajuan dari 14/34 menjadi 88 setelah 3 hari tidak ada aktivitas.

## Components Implemented

### 1. Controller Method
**File**: `app/Http/Controllers/PengajuanOpenController.php`

#### Method: `autoChangeExpiredStatus()`
- Mencari pengajuan dengan status 14 atau 34
- Mengecek yang sudah lewat 3 hari dari `updated_at`
- Mengupdate status menjadi 88 dengan pesan otomatis

#### Method: `getPengajuanWithStatus14And34()`
- Menampilkan semua pengajuan dengan status 14/34
- Menghitung umur setiap pengajuan
- Menentukan kapan akan expired

### 2. Laravel Command
**File**: `app/Console/Commands/AutoChangeExpiredPengajuanStatus.php`

**Command**: `pengajuan:auto-expire`
- Menjalankan logic auto-change via command line
- Dapat dijalankan manual atau via scheduler

### 3. Scheduler Configuration
**File**: `app/Console/Kernel.php`

**Schedule**: Setiap hari jam 08:00
- Command: `pengajuan:auto-expire`
- Log output ke: `storage/logs/auto-expire-pengajuan.log`

### 4. Test Routes
**File**: `routes/web.php`

#### Route: `/test/auto-expire-pengajuan`
- Method: GET
- Purpose: Test manual auto-expire function
- Response: JSON dengan hasil eksekusi

#### Route: `/test/pengajuan-status-14-34`
- Method: GET
- Purpose: Melihat semua pengajuan status 14/34 dan info expired
- Response: JSON dengan detail pengajuan

## Database Schema
**Table**: `pengajuans`

Kolom yang digunakan:
- `status`: Status pengajuan (14, 34, 88)
- `updated_at`: Timestamp terakhir update (untuk hitung umur)
- `status_updated`: Timestamp khusus untuk perubahan status
- `pesan_akhir`: Pesan untuk keterangan perubahan status

## Logic Flow

### Auto-Change Process:
1. Cari pengajuan dengan status = 14 OR status = 34
2. Filter yang `updated_at` <= 3 hari yang lalu
3. Update masing-masing pengajuan:
   - `status` = 88
   - `status_updated` = now()
   - `pesan_akhir` = pesan otomatis

### Status Explanation:
- **Status 14**: Dalam proses review pokja
- **Status 34**: Dalam proses review lanjutan
- **Status 88**: Expired/timeout setelah 3 hari

## Usage Instructions

### Manual Testing:
1. **Check current status 14/34:**
   ```
   GET /test/pengajuan-status-14-34
   ```

2. **Run auto-expire manually:**
   ```
   GET /test/auto-expire-pengajuan
   ```

3. **Command line testing:**
   ```bash
   php artisan pengajuan:auto-expire
   ```

### Production Setup:

1. **Setup Scheduler** (Linux cron):
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **Setup Scheduler** (Windows Task Scheduler):
   - Action: `php artisan schedule:run`
   - Trigger: Every minute
   - Start in: Project directory

3. **Check scheduled commands:**
   ```bash
   php artisan schedule:list
   ```

4. **Run scheduler manually:**
   ```bash
   php artisan schedule:run
   ```

### Log Monitoring:
- Log file: `storage/logs/auto-expire-pengajuan.log`
- Monitor for successful executions and errors

## Security Considerations
- Auto-change hanya berlaku untuk status 14 dan 34
- Pesan otomatis ditambahkan untuk audit trail
- Timestamp `status_updated` dicatat untuk tracking

## Configuration Options

### Modify Expiry Days:
Ubah di method `autoChangeExpiredStatus()`:
```php
$threeDaysAgo = now()->subDays(3); // Ganti 3 dengan jumlah hari yang diinginkan
```

### Modify Schedule Time:
Ubah di `app/Console/Kernel.php`:
```php
$schedule->command('pengajuan:auto-expire')
         ->dailyAt('08:00'); // Ganti waktu sesuai kebutuhan
```

### Add Additional Status:
Modify query di `autoChangeExpiredStatus()`:
```php
->where(function($query) {
    $query->where('status', 14)
          ->orWhere('status', 34)
          ->orWhere('status', 'NEW_STATUS'); // Tambah status baru
})
```

## Error Handling
- Try-catch blocks untuk menangani errors
- Return structured response dengan success/error status
- Log errors untuk debugging

## Testing Recommendations

1. **Unit Testing**: Buat test untuk method `autoChangeExpiredStatus()`
2. **Integration Testing**: Test scheduler execution
3. **Data Validation**: Pastikan hanya status 14/34 yang berubah
4. **Performance Testing**: Monitor dengan data volume besar

## Maintenance

### Regular Checks:
1. Monitor log file untuk errors
2. Verify scheduler berjalan correctly
3. Check database untuk konsistensi data
4. Review dan adjust expiry time jika perlu

### Backup Considerations:
- Backup database sebelum production deployment
- Test di staging environment terlebih dahulu
- Monitor impact terhadap existing workflows
