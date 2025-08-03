# 🔧 PERBAIKAN ROUTE API UNREAD COUNT

## ❌ MASALAH YANG DITEMUKAN

**Error**: `Route [api.unread.count] not defined`

## ✅ PENYEBAB & SOLUSI

### Penyebab:
Route API berada dalam **route group dengan prefix** (`ppk/` dan `pokjapemilihan/`), sehingga nama route menjadi:
- PPK: `ppk_api.unread.count` 
- Pokja: `pokjapemilihan_api.unread.count`

Bukan `api.unread.count` seperti yang digunakan di JavaScript.

### Solusi:
Memperbaiki JavaScript di `master.blade.php` untuk menggunakan nama route yang benar berdasarkan role user:

```javascript
const role = '{{ auth()->user()->role }}';
let apiUrl = '';

if (role === 'ppk') {
    apiUrl = '{{ route("ppk_api.unread.count") }}';
} else if (role === 'pokjapemilihan') {
    apiUrl = '{{ route("pokjapemilihan_api.unread.count") }}';
}
```

## 🔍 VERIFIKASI ROUTE

Route yang terdaftar sekarang:
```
GET|HEAD  ppk/api/unread-count ..................... ppk_api.unread.count › ChatsController@getUnreadCount
GET|HEAD  pokjapemilihan/api/unread-count ........... pokjapemilihan_api.unread.count › ChatsController@getUnreadCount
```

## 📁 FILE YANG DIPERBAIKI

**1. `resources/views/layouts/master.blade.php`**
- Perbaikan JavaScript untuk deteksi role user
- Gunakan nama route yang sesuai dengan prefix group
- Dynamic route selection berdasarkan role

## 🧪 TESTING

### Test Route PPK:
```
URL: /ppk/api/unread-count
Route Name: ppk_api.unread.count
Method: GET
Response: {'count': number}
```

### Test Route Pokja:
```
URL: /pokjapemilihan/api/unread-count  
Route Name: pokjapemilihan_api.unread.count
Method: GET
Response: {'count': number}
```

## ✅ STATUS PERBAIKAN

- [x] Route name diperbaiki di JavaScript
- [x] Dynamic route selection berdasarkan user role
- [x] Cache cleared untuk memastikan perubahan terbaca
- [x] Route list verified - kedua route terdaftar dengan benar

## 🎯 HASIL AKHIR

Sekarang badge unread count akan berfungsi dengan:
- **PPK users**: API call ke `ppk/api/unread-count`
- **Pokja users**: API call ke `pokjapemilihan/api/unread-count`
- **Auto-update**: Setiap 30 detik dan saat ada activity
- **Real-time**: Badge update tanpa refresh halaman

Badge di sidebar menu Chat seharusnya sekarang berfungsi dengan benar! 🚀

---

**Status: ✅ DIPERBAIKI**  
**Last Updated: {{ date('Y-m-d H:i:s') }}**
