# Unread Notifications - Simple jQuery Implementation

## Perubahan yang Dilakukan

### 1. Hapus API Endpoints
- ❌ Dihapus: `/api/unread-count/{id}` dan `/api/mark-as-read/{id}`
- ❌ Tidak perlu API layer terpisah

### 2. Tambah Routes Sederhana di Setiap Role Group
Setiap role (ppk, verifikator, pokjapemilihan) memiliki:
```php
Route::get('/pengajuan/{id}/unread-count', [ChatsController::class, 'getUnreadCount'])->name('unread.count');
Route::post('/pengajuan/{id}/mark-as-read', [ChatsController::class, 'markAsRead'])->name('mark.read');
```

**Routes yang terdaftar:**
- `ppk_unread.count` → `/ppk/pengajuan/{id}/unread-count`
- `verifikator_unread.count` → `/verifikator/pengajuan/{id}/unread-count`
- `pokjapemilihan_unread.count` → `/pokjapemilihan/pengajuan/{id}/unread-count`

### 3. Update jQuery Implementation
**Dari:**
```javascript
var url = '/' + userRole + '/api/unread-count/' + pengajuanId;
```

**Ke:**
```javascript
var baseUrl = "{{ route('ppk_unread.count', ['id' => 'PLACEHOLDER']) }}";
var url = baseUrl.replace('ppk_', userRole + '_').replace('PLACEHOLDER', pengajuanId);
```

**Keuntungan:**
- ✅ Tidak perlu string manipulation
- ✅ Gunakan Laravel route helper
- ✅ Lebih clean dan maintainable
- ✅ Tidak ada API layer yang tidak perlu

## Cara Kerja

### Frontend (jQuery di open.blade.php)
1. Saat halaman load, `loadUnreadCounts()` dipanggil
2. Setiap `.chat-button` diloop
3. AJAX GET ke `/role/pengajuan/{id}/unread-count`
4. Response berisi `{"unread_count": N}`
5. Badge diupdate dengan jumlah unread message
6. Dijalankan setiap 5 detik

```javascript
function loadUnreadCounts() {
    var userRole = '{{ auth()->user()->role }}';
    var baseUrl = "{{ route('ppk_unread.count', ['id' => 'PLACEHOLDER']) }}";
    
    $('.chat-button').each(function() {
        var pengajuanId = $(this).data('pengajuan-id');
        var $badge = $(this).find('.chat-badge');
        var url = baseUrl.replace('ppk_', userRole + '_').replace('PLACEHOLDER', pengajuanId);
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var unreadCount = response.unread_count;
                if (unreadCount > 0) {
                    $badge.text(unreadCount).removeClass('d-none');
                } else {
                    $badge.addClass('d-none');
                }
            }
        });
    });
}
```

### Backend (ChatsController)
```php
public function getUnreadCount($pengajuanId)
{
    $user = Auth::user();
    $pengajuan = Pengajuan::findOrFail($pengajuanId);
    
    // Tentukan chat_type (verifikator atau pokja)
    $chatType = 'verifikator';
    if ($user->role === 'pokjapemilihan') {
        $chatType = 'pokja';
    } elseif ($user->role === 'ppk') {
        $chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';
    }
    
    // Hitung unread message
    $unreadCount = ChatMessage::where('pengajuan_id', $pengajuanId)
        ->where('chat_type', $chatType)
        ->where('user_id', '!=', $user->id)
        ->whereNull('read_at')
        ->count();
    
    return response()->json(['unread_count' => $unreadCount]);
}
```

## Testing

### Test URL
- Buka: `http://localhost/test-unread-simple.html`
- Akan test GET request ke `/ppk/pengajuan/1/unread-count`

### Di Browser Console
1. Buka pengajuan detail
2. Tekan F12 (Developer Tools)
3. Buka Console tab
4. Lihat AJAX request ke `/ppk/pengajuan/{id}/unread-count`
5. Response harus: `{"unread_count": N}`
6. Badge harus muncul dengan angka di tombol Chat

### Verifikasi di Network Tab
1. Buka Network tab di F12
2. Filter: `unread-count`
3. Lihat request:
   - Status: **200**
   - Response: `{"unread_count": 6}`

## Troubleshooting

### Badge tidak muncul
1. Check browser console (F12)
2. Pastikan ada error? 
3. Verifikasi ada pesan unread di database: 
   ```php
   ChatMessage::where('pengajuan_id', 1)
       ->where('read_at', null)
       ->count();
   ```

### 404 Not Found
- Pastikan routes sudah terdaftar: `php artisan route:list | grep unread-count`
- Pastikan middleware auth bekerja dengan baik

### CSRF Token Error
- Check: `<meta name="csrf-token">` di head HTML
- Pastikan sudah set di AJAX header (sudah ada di code)

## Commits
- `1d331db` - refactor: Remove API endpoints and use simple jQuery routes instead

## File yang Diubah
- `routes/web.php` - Routes update
- `resources/views/dashboard/open.blade.php` - jQuery update
- `public/test-unread-simple.html` - Test file baru
