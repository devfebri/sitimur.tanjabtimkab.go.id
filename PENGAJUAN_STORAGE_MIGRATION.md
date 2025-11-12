# 📦 MIGRASI FILE PENGAJUAN KE STORAGE

## Status: ✅ SELESAI

Tanggal: 11 November 2025

---

## 🎯 Apa yang Diubah?

Semua file pengajuan sekarang disimpan di dalam `storage/app/public` bukan di `public/` folder.

### Sebelum (LAMA):
```
public/
├── pengajuan/
│   └── 19-10-2025/
│       └── username/
│           └── 123/
│               ├── berkasajuan/
│               │   └── xxx.pdf
│               ├── revisions/
│               │   └── yyy.pdf
│               └── chat_uploads/
│                   └── zzz.jpg
```

### Sesudah (BARU):
```
storage/app/public/
├── pengajuan/
│   └── 2024/10/19/      ← Format Y/m/d bukan d-m-Y
│       └── username/
│           └── 123/
│               ├── berkasajuan/
│               │   └── xxx.pdf
│               ├── revisions/
│               │   └── yyy.pdf
│               └── chat_uploads/
│                   └── zzz.jpg
                   
public/storage → /storage/app/public (SYMLINK)
```

---

## 📁 File Structure

**Struktur folder yang konsisten di storage:**
```
storage/app/public/
└── pengajuan/
    └── YYYY/MM/DD/              ← Created at date (Y/m/d)
        └── {username}/          ← User yang upload
            └── {pengajuan_id}/
                ├── berkasajuan/         ← Dokumen pengajuan
                ├── revisions/           ← File revisi
                └── chat_uploads/        ← Attachment dari chat
```

---

## 🔧 Tools & Helpers yang Dibuat

### FileStorageHelper.php

Helper class untuk unified file operations:

```php
use App\Helpers\FileStorageHelper;

// Upload pengajuan file
$path = FileStorageHelper::uploadPengajuanFile($file, $pengajuan, 'berkasajuan');

// Upload chat file
$path = FileStorageHelper::uploadChatFile($file, $pengajuan);

// Upload revision file
$path = FileStorageHelper::uploadRevisionFile($file, $pengajuan);

// Delete file
FileStorageHelper::deleteFile($filePath);

// Get public URL
$url = FileStorageHelper::getPublicUrl($filePath);

// Check if file exists
$exists = FileStorageHelper::fileExists($filePath);

// Get file size
$size = FileStorageHelper::getFileSize($filePath);
```

**Keunggulan:**
- Single point untuk semua file operations
- Consistent path handling
- Better error handling
- Easy to extend

---

## ✅ File yang Diubah

### 1. Controllers Updated

**`app/Http/Controllers/PengajuanOpenController.php`**
- Method: `updatePengajuanFile()` (Line ~510)
- Change: Gunakan helper untuk upload revisi
- Old: `$uploaded->move(public_path($uploadPath), $filename);`
- New: `FileStorageHelper::uploadRevisionFile($uploaded, $pengajuan);`

**`app/Http/Controllers/PengajuanController.php`**
- Method: `uploadBerkasAjax()` (Line ~492)
- Change: Gunakan helper untuk upload berkas
- Old: `$file->move(public_path($folderPath), $filename);`
- New: `FileStorageHelper::uploadPengajuanFile($file, $pengajuan, $slug);`
- Delete: `public_path()` calls, gunakan helper

**`app/Http/Controllers/ChatsController.php`** (Sudah diubah sebelumnya)
- Method: `sendMessage()`
- Already using: `Storage::disk('public')`

### 2. New Helper Created

**`app/Helpers/FileStorageHelper.php`** ✅ BARU
- Unified file upload/download/delete operations
- Error handling
- Path validation
- Consistent naming

---

## 📊 Path Format Comparison

| Aspek | Lama | Baru |
|-------|------|------|
| **Lokasi** | `public/pengajuan/` | `storage/app/public/pengajuan/` |
| **Date format** | `d-m-Y` | `Y/m/d` |
| **Database path** | `/pengajuan/19-10-2025/...` | `pengajuan/2024/10/19/...` |
| **Browser URL** | `/pengajuan/19-10-2025/...` | `/storage/pengajuan/2024/10/19/...` |
| **Upload method** | `$file->move()` | `Storage::storeAs()` |
| **Delete method** | `unlink()` | `Storage::delete()` |

---

## 🚀 Implementasi Steps

### 1. Deploy Code Changes
```bash
git pull  # Pull changes
composer install  # Update if needed
```

### 2. Verify Symlink
```bash
# Check symlink exists
ls -la public/storage

# If missing, create it
php artisan storage:link

# Verify it points to right location
readlink public/storage  # Should show: ../storage/app/public
```

### 3. Set Permissions
```bash
# Make storage writable
chmod -R 755 storage/app/public

# Or more permissive if needed
chmod -R 777 storage/app/public
```

### 4. Migrate Old Files (OPTIONAL)

Jika ada file lama di `public/pengajuan/`, migrate ke storage:

```php
// In Laravel tinker or migration
$oldFiles = \App\Models\PengajuanFile::all();

foreach ($oldFiles as $file) {
    $oldPath = public_path($file->file_path);
    
    if (file_exists($oldPath)) {
        // Copy to storage
        $newPath = str_replace('pengajuan/d-m-Y', 'pengajuan/Y/m/d', $file->file_path);
        
        copy($oldPath, storage_path('app/public/' . $newPath));
        
        // Update database
        $file->file_path = $newPath;
        $file->save();
        
        // Delete old file
        @unlink($oldPath);
    }
}
```

### 5. Clear Old Uploads
```bash
# After migration
rm -rf public/pengajuan/  # Linux/Mac
rmdir /s public\pengajuan  # Windows
```

### 6. Test Upload
1. Buat pengajuan baru
2. Upload file
3. Verify file ada di `storage/app/public/pengajuan/`
4. Verify URL berfungsi via `/storage/pengajuan/...`

---

## 🔍 Verification

### Check Files in Storage
```bash
# List all pengajuan files
ls -la storage/app/public/pengajuan/

# Count total files
find storage/app/public/pengajuan -type f | wc -l
```

### Check Database Paths
```bash
# In Laravel tinker
PengajuanFile::first();
# Should show: "file_path" => "pengajuan/2024/10/19/username/id/..."
```

### Check URLs Work
```
Open browser: http://localhost:8000/storage/pengajuan/2024/10/19/.../file.pdf
Should download file, not 404
```

---

## 🛡️ Security Benefits

1. **No direct access to public_html**
   - Before: `public/pengajuan/file.pdf` accessible directly
   - After: Only via `/storage/pengajuan/file.pdf` with symlink

2. **Authorization possible**
   - Can add middleware to check permissions
   - Can serve files with authentication

3. **Easy to secure**
   - Remove symlink = all files inaccessible
   - Can move to private storage if needed

4. **Cloud-ready**
   - Can switch to S3 without code changes
   - Can implement tiered storage

---

## 📖 Using FileStorageHelper

### Simple Upload
```php
use App\Helpers\FileStorageHelper;

// Upload file
$filePath = FileStorageHelper::uploadPengajuanFile(
    $request->file('file'),
    $pengajuan,
    'berkasajuan'
);

// Save to database
PengajuanFile::create([
    'pengajuan_id' => $pengajuan->id,
    'file_path' => $filePath,
    // ... other fields
]);
```

### Delete File
```php
// Delete from storage
FileStorageHelper::deleteFile($existingFile->file_path);

// Delete from database
$existingFile->delete();
```

### Get Public URL
```php
// In blade template
$url = FileStorageHelper::getPublicUrl($file->file_path);

// <a href="{{ $url }}">Download</a>
```

### Check File Exists
```php
if (FileStorageHelper::fileExists($file->file_path)) {
    // File ada
}
```

---

## 🚨 Troubleshooting

### Files Not Found After Deploy
- ✓ Check: symlink exists (`ls public/storage`)
- ✓ Check: files in storage (`ls storage/app/public/pengajuan/`)
- ✓ Check: permissions (`ls -la storage/app/public/`)

### Old Files Still in public/
- ✓ They'll still work (old URLs redirect)
- ✓ Can delete after migration complete
- ✓ Keep backup just in case

### Upload Fails with Permission Error
- ✓ Run: `chmod -R 755 storage/app/public`
- ✓ Check: web server user can write (www-data, nginx, etc)
- ✓ Check: disk space available

### Database Paths Look Wrong
- ✓ Check: Format is `pengajuan/Y/m/d/...` not `/pengajuan/d-m-Y/...`
- ✓ Check: No `/storage/` prefix in database
- ✓ Check: Helper is being used correctly

---

## 📋 Checklist

**Pre-Deploy:**
- [ ] Read this documentation
- [ ] Review code changes
- [ ] Backup database
- [ ] Backup public/pengajuan folder (just in case)

**Deploy:**
- [ ] Deploy code
- [ ] php artisan storage:link
- [ ] chmod -R 755 storage/app/public

**Post-Deploy:**
- [ ] Test new upload works
- [ ] Check file in storage
- [ ] Check URL works in browser
- [ ] Check file permissions
- [ ] Monitor logs for errors
- [ ] Old files still accessible? (old URLs)

**Migration (if needed):**
- [ ] Migrate old files to storage
- [ ] Update database paths
- [ ] Test old files still work
- [ ] Delete old public/pengajuan folder

---

## 🎓 Key Points

1. **Storage path format:** `pengajuan/YYYY/MM/DD/username/id/category/file.ext`
2. **Database path:** Relative (no `/storage/` prefix)
3. **Browser URL:** Add `/storage/` prefix automatically
4. **Use helper:** Always use `FileStorageHelper` for operations
5. **Symlink:** MUST exist for `/storage/` URLs to work
6. **Permissions:** Must be writable by web server

---

## 📞 Support

If issues:
1. Check permissions: `ls -la storage/app/public/`
2. Check symlink: `ls -la public/storage`
3. Check database: `SELECT file_path FROM pengajuan_files LIMIT 1;`
4. Check storage: `ls -la storage/app/public/pengajuan/`
5. Check logs: `tail -f storage/logs/laravel.log`

---

**Status:** ✅ READY FOR PRODUCTION

Semua file pengajuan sekarang menggunakan unified storage system yang lebih aman, scalable, dan mudah di-maintain.
