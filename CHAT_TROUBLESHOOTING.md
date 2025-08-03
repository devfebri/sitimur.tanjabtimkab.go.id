# 🔧 Chat System - Troubleshooting Guide

## Kemungkinan Masalah dan Solusi

### 1. Error "Class not found" atau "Route not found"
```bash
# Regenerate autoload dan cache
composer dump-autoload
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. File Upload Error
```bash
# Pastikan storage link aktif
php artisan storage:link

# Cek permission folder storage
chmod -R 775 storage/
chmod -R 775 public/storage/
```

### 3. Migration Error
```bash
# Rollback dan re-run migration
php artisan migrate:rollback --step=2
php artisan migrate
```

### 4. Livewire Component Error
```bash
# Clear Livewire cache
php artisan livewire:publish --config
php artisan config:clear
```

### 5. Chat Not Loading
- Pastikan user login dengan role 'ppk' atau 'pokjapemilihan'
- Cek ada user lain dengan role berbeda untuk chat
- Buka browser console untuk error JavaScript

### 6. File Download Error
- Cek file ada di `storage/app/public/chat-files/`
- Pastikan storage link aktif: `ls -la public/storage`
- Cek permission file storage

### 7. Real-time Not Working
Broadcasting sudah ready, tapi perlu:
```bash
# Install Laravel Echo (opsional)
npm install laravel-echo pusher-js
npm run dev
```

## Quick Test Commands

```bash
# Test database connection
php artisan tinker
>>> App\Models\User::count()
>>> App\Models\ChatConversation::count()

# Test routes
php artisan route:list --name=chats

# Test storage
php artisan storage:link
ls -la public/storage

# Test permissions  
php artisan tinker
>>> Storage::disk('public')->put('test.txt', 'test content')
>>> Storage::disk('public')->exists('test.txt')
```

## Manual Fixes

### Jika CustomChat.php error:
```php
// Pastikan import lengkap di app/Livewire/CustomChat.php
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Livewire\WithFileUploads;
```

### Jika routes tidak berfungsi:
```php
// Check di routes/web.php ada:
Route::get('/chats', [ChatsController::class, 'index'])->name('chats');
```

### Jika view error:
```blade
{{-- Pastikan di resources/views/chats.blade.php ada: --}}
@livewire('custom-chat', ['pengajuanId' => request()->query('pengajuan')])
```

## Verifikasi Sistem Bekerja

1. **Database**: Chat tables ada dan migrate sukses
2. **Storage**: Folder `public/storage/chat-files` ada
3. **Routes**: `/ppk/chats` dan `/pokjapemilihan/chats` accessible  
4. **Livewire**: Component load tanpa error
5. **File Upload**: Bisa upload dan download file
6. **Permissions**: Hanya PPK/Pokja yang bisa akses

---
**💡 Tip**: Jika masih ada masalah, cek Laravel log di `storage/logs/laravel.log`
