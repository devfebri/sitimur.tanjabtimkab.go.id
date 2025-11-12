# 📦 File Storage Quick Reference

## Menggunakan FileStorageHelper

### Setup
```php
use App\Helpers\FileStorageHelper;
```

### Upload Pengajuan File
```php
// Upload dokumen pengajuan (berkasajuan, revisions, dll)
$filePath = FileStorageHelper::uploadPengajuanFile(
    $request->file('file'),
    $pengajuan,
    'berkasajuan'  // atau 'revisions', atau slug lainnya
);

// Simpan ke database
PengajuanFile::create([
    'pengajuan_id' => $pengajuan->id,
    'file_path' => $filePath,
    'nama_file' => $request->file('file')->getClientOriginalName(),
]);
```

### Upload Chat File
```php
// Upload attachment di chat
$filePath = FileStorageHelper::uploadChatFile(
    $request->file('attachment'),
    $pengajuan
);

// Simpan ke database
ChatMessage::create([
    'pengajuan_id' => $pengajuan->id,
    'file_path' => $filePath,
]);
```

### Upload Revision File
```php
// Upload file revisi (otomatis namanya -revisi)
$filePath = FileStorageHelper::uploadRevisionFile(
    $request->file('file'),
    $pengajuan
);

// Simpan ke database
PengajuanFile::create([
    'pengajuan_id' => $pengajuan->id,
    'file_path' => $filePath,
    'revisi_ke' => $pengajuanFile->revisi_ke + 1,
]);
```

### Delete File
```php
// Hapus dari storage
FileStorageHelper::deleteFile($file->file_path);

// Hapus dari database
$file->delete();
```

### Get Public URL
```php
// Di controller
$url = FileStorageHelper::getPublicUrl($file->file_path);
return response()->json(['url' => $url]);

// Di blade template
<a href="{{ FileStorageHelper::getPublicUrl($file->file_path) }}" download>
    Download
</a>

// Untuk image
<img src="{{ FileStorageHelper::getPublicUrl($file->file_path) }}" />
```

### Check If File Exists
```php
if (FileStorageHelper::fileExists($file->file_path)) {
    // File ada, bisa diakses
}
```

### Get File Info
```php
// File size (bytes)
$size = FileStorageHelper::getFileSize($file->file_path);
echo "Size: " . formatBytes($size);

// Last modified (timestamp)
$lastMod = FileStorageHelper::getLastModified($file->file_path);
echo "Modified: " . date('Y-m-d H:i:s', $lastMod);
```

### Move/Copy File
```php
// Pindah file dalam storage
FileStorageHelper::moveFile(
    'pengajuan/2024/10/19/user/123/old_file.pdf',
    'pengajuan/2024/10/19/user/123/new_file.pdf'
);

// Copy file
FileStorageHelper::copyFile(
    'pengajuan/2024/10/19/user/123/file.pdf',
    'pengajuan/2024/10/19/user/123/file_backup.pdf'
);
```

---

## Path Format

**Database:**
```
pengajuan/YYYY/MM/DD/username/pengajuan_id/category/filename.ext
pengajuan/2024/10/19/ppk_budi/123/berkasajuan/1729363200-abc123.pdf
```

**Browser URL:**
```
/storage/pengajuan/YYYY/MM/DD/username/pengajuan_id/category/filename.ext
/storage/pengajuan/2024/10/19/ppk_budi/123/berkasajuan/1729363200-abc123.pdf
```

**Filesystem:**
```
storage/app/public/pengajuan/YYYY/MM/DD/username/pengajuan_id/category/filename.ext
storage/app/public/pengajuan/2024/10/19/ppk_budi/123/berkasajuan/1729363200-abc123.pdf
```

---

## Common Patterns

### Upload & Save
```php
// Full workflow
$file = $request->file('dokumen');
$pengajuan = Pengajuan::findOrFail($id);

$filePath = FileStorageHelper::uploadPengajuanFile(
    $file,
    $pengajuan,
    'berkasajuan'
);

PengajuanFile::create([
    'pengajuan_id' => $pengajuan->id,
    'nama_file' => $file->getClientOriginalName(),
    'file_path' => $filePath,
    'slug' => 'proposal',
]);

return response()->json([
    'success' => true,
    'url' => FileStorageHelper::getPublicUrl($filePath),
]);
```

### Replace File
```php
// Delete old
$oldFile = PengajuanFile::where('slug', 'proposal')->first();
if ($oldFile) {
    FileStorageHelper::deleteFile($oldFile->file_path);
    $oldFile->delete();
}

// Upload new
$filePath = FileStorageHelper::uploadPengajuanFile(
    $request->file('dokumen'),
    $pengajuan,
    'berkasajuan'
);

PengajuanFile::create([...]);
```

### Display File
```blade
@if ($file->file_path)
    @php
        $url = \App\Helpers\FileStorageHelper::getPublicUrl($file->file_path);
        $isImage = preg_match('/\.(jpg|jpeg|png|gif)$/i', $file->file_path);
    @endphp
    
    @if ($isImage)
        <img src="{{ $url }}" alt="Lampiran" class="img-thumbnail" />
    @else
        <a href="{{ $url }}" class="btn btn-sm btn-primary" download>
            <i class="mdi mdi-download"></i> Download
        </a>
    @endif
@endif
```

---

## Error Handling

```php
try {
    $filePath = FileStorageHelper::uploadPengajuanFile(
        $file,
        $pengajuan,
        'berkasajuan'
    );
} catch (\Exception $e) {
    return response()->json([
        'error' => 'Upload gagal: ' . $e->getMessage()
    ], 400);
}
```

---

## Important Notes

1. **Path dalam database: RELATIVE (tanpa `/storage/`)**
   - Benar: `pengajuan/2024/10/19/user/123/file.pdf`
   - Salah: `/storage/pengajuan/2024/10/19/user/123/file.pdf`

2. **Helper otomatis handle:**
   - Unique filename (timestamp + uniqid)
   - Directory creation
   - Error handling
   - Path formatting

3. **Symlink HARUS ada:**
   - `php artisan storage:link`
   - `public/storage` → `storage/app/public`

4. **Permissions harus writable:**
   - `chmod -R 755 storage/app/public`

5. **URL di browser:**
   - Helper automatiknya tambah `/storage/` prefix
   - Symlink resolve ke actual file

---

## Migration dari Public

Jika ada file lama di `public/pengajuan/`:

```bash
# Migrate all files
php artisan db:seed --class=MigrateFileToStorageSeeder

# Monitor progress di logs
tail -f storage/logs/laravel.log
```

---

**Happy Coding! 🚀**
