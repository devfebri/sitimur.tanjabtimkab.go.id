# 🎯 Testing Guide - Unread Notifications dengan Simple jQuery

## Step 1: Buka Pengajuan Detail
- Klik salah satu pengajuan dari dashboard
- Perhatikan tombol **Chat** di bawah sebelah kanan

## Step 2: Buka Developer Console
**Windows/Linux:** Tekan `F12`
**Mac:** Press `Cmd + Option + I`

Atau: Right-click → Inspect → Console tab

```
┌─────────────────────────────────────────────────────────┐
│ Chrome/Firefox Developer Tools                          │
├─────────┬──────────┬──────────┬───────────────────────┤
│ Elements│ Console  │ Network  │ Application           │
│         │ █ HERE   │          │                       │
└─────────┴──────────┴──────────┴───────────────────────┘
```

## Step 3: Buka Tab Network
Klik tab **Network** di Developer Tools

```
┌─────────────────────────────────────────────────────────┐
│ Network Tab                                             │
├─────────┬──────────┬──────────┬───────────────────────┤
│ Elements│ Console  │ Network  │ Application           │
│         │          │ █ HERE   │                       │
└─────────┴──────────┴──────────┴───────────────────────┘
```

## Step 4: Refresh Halaman
- Tekan `F5` atau `Ctrl+R`
- Lihat request list muncul di Network tab

## Step 5: Cari Request "unread-count"
Ketik di search box: `unread-count`

```
┌─────────────────────────────────────────────────────────┐
│ Network Search: "unread-count"                          │
├─────────────────────────────────────────────────────────┤
│ ✓ GET /ppk/pengajuan/1/unread-count      200   1.2 KB  │
│ ✓ GET /ppk/pengajuan/1/unread-count      200   1.2 KB  │
│ ✓ GET /ppk/pengajuan/1/unread-count      200   1.2 KB  │
└─────────────────────────────────────────────────────────┘
```

## Step 6: Cek Response
Klik salah satu request dari list
Buka tab **Response**

```
┌─────────────────────────────────────────────────────────┐
│ unread-count Request                                    │
├────────┬──────────┬──────────┬────────┬────────────────┤
│ Headers│ Preview  │ Response │ Timing │ Cookies        │
│        │          │ █ HERE   │        │                │
├────────┴──────────┴──────────┴────────┴────────────────┤
│ {"unread_count": 6}                                     │
└─────────────────────────────────────────────────────────┘
```

**Status Code harus:** `200` ✓

**Response Content:**
```json
{
  "unread_count": 6
}
```

## Step 7: Verifikasi Badge

### Di Halaman
Lihat tombol **Chat** di halaman

```
┌─────────────────────────────────────────┐
│ Status Terakhir                         │
│ ┌─────────────────────────────────────┐ │
│ │ ○ Chat Verifikator                  │ │
│ │   dengan badge: (3)                 │ │
│ └─────────────────────────────────────┘ │
│ atau                                    │
│ ┌─────────────────────────────────────┐ │
│ │ ○ Chat Pokja        (6)              │ │
│ │   dengan badge                      │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

### Badge Element
Klik Chat button, lalu inspect
Cari element `<span class="chat-badge">`

```html
<!-- HTML Structure -->
<a href="..." class="btn btn-success position-relative chat-button">
  <i class="mdi mdi-chat-multiple"></i>Chat
  <span class="position-absolute top-0 start-100 translate-middle 
        badge rounded-pill bg-danger chat-badge">
    6  ← Badge angka unread message
  </span>
</a>
```

## Step 8: Cek Console Logs

Klik tab **Console**

```
┌─────────────────────────────────────────────────────────┐
│ Console Logs                                            │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Jika berhasil harus ada:                               │
│ ✓ Unread count response: {unread_count: 6}            │
│                                                         │
│ Jika error akan muncul:                                │
│ ✗ Error loading unread count: ...                      │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## ✅ Checklist Verifikasi

- [ ] Request `unread-count` ada di Network tab
- [ ] Status code: **200** (bukan 404 atau 500)
- [ ] Response: `{"unread_count": N}`
- [ ] Badge muncul di tombol Chat (jika N > 0)
- [ ] Console tidak ada error (atau hanya warning biasa)
- [ ] Badge refresh setiap 5 detik

---

## 🐛 Troubleshooting

### ❌ Masalah: Status 404 Not Found
```
GET /ppk/pengajuan/1/unread-count    404
```

**Penyebab:** Route tidak terdaftar

**Solusi:**
```bash
# Jalankan di terminal
php artisan route:list | Select-String "unread-count"

# Harus muncul 3 routes:
# GET|HEAD   ppk/pengajuan/{id}/unread-count
# GET|HEAD   verifikator/pengajuan/{id}/unread-count
# GET|HEAD   pokjapemilihan/pengajuan/{id}/unread-count
```

### ❌ Masalah: Status 500 Internal Server Error
```
GET /ppk/pengajuan/1/unread-count    500
```

**Penyebab:** Error di controller

**Solusi:**
1. Check file `storage/logs/laravel.log` untuk error detail
2. Pastikan pengajuan ID 1 ada di database
3. Pastikan `chat_messages` table ada dengan columns: `id`, `pengajuan_id`, `chat_type`, `read_at`, `user_id`

### ❌ Masalah: Badge tidak muncul
```
Response: {"unread_count": 6}
Tapi badge tidak terlihat
```

**Penyebab:** Kemungkinan CSS atau JavaScript issue

**Solusi:**
1. Inspect badge element: Right-click → Inspect
2. Cek apakah class `d-none` ada?
3. Jika ada class `d-none`, bagus! (berarti jQuery sedang hide)
4. Setelah AJAX selesai, class `d-none` seharusnya dihapus
5. Verifikasi jQuery `$badge.removeClass('d-none')` berjalan

### ❌ Masalah: Error "Undefined variable"
```
Error: Undefined variable 'csrf'
atau
Error: CSRF token mismatch
```

**Penyebab:** CSRF token tidak terbaca

**Solusi:**
1. Check HTML `<head>` ada: `<meta name="csrf-token">`
2. Check JavaScript AJAX setup:
```javascript
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```
3. Pastikan sudah ada di file

---

## 📱 Alternative Testing (PHP CLI)

Jika ingin test dari terminal tanpa browser:

```bash
# Login dahulu ke Laravel Tinker
php artisan tinker

# Cek apakah endpoint bisa diakses
$response = Route::dispatch(
    Request::create('/ppk/pengajuan/1/unread-count', 'GET')
);

# Print response
echo $response->getContent();

# Keluar dari tinker
exit
```

---

## 🎓 Penjelasan Flow

```
1. User buka halaman pengajuan detail
   ↓
2. jQuery loadUnreadCounts() dipanggil saat $(document).ready()
   ↓
3. Loop semua .chat-button element
   ↓
4. Setiap button buat AJAX GET request ke:
   /ppk/pengajuan/{id}/unread-count
   ↓
5. ChatsController@getUnreadCount() dijalankan
   - Check user role & pengajuan status
   - Tentukan chat_type (verifikator atau pokja)
   - Query ChatMessage table: count WHERE read_at IS NULL
   ↓
6. Return JSON: {"unread_count": 6}
   ↓
7. jQuery update badge:
   - Jika count > 0: show badge dengan number
   - Jika count = 0: hide badge dengan class "d-none"
   ↓
8. setInterval(loadUnreadCounts, 5000) = refresh setiap 5 detik
```

---

## 💡 Tips

1. **Check console** - Jika ada error akan muncul di console
2. **Network tab important** - Lihat actual request & response
3. **Clear cache** - Jika tidak melihat perubahan, tekan `Ctrl+Shift+Delete`
4. **F5 refresh** - Jangan Ctrl+F5 (hard refresh) karena perlu CSRF token baru

---

**Status: ✅ SIAP UNTUK TESTING**

Ikuti langkah-langkah di atas untuk memverifikasi unread notification berfungsi!
