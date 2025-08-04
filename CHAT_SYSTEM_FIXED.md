# CHAT SYSTEM FIX - TESTING GUIDE

## ✅ PERBAIKAN YANG TELAH DILAKUKAN

### 1. Konfigurasi Echo dengan Vite
- Mengembalikan `@vite(['resources/js/app.js'])` di master.blade.php
- Memperbaiki konfigurasi Echo di `resources/js/echo.js`
- Menambahkan error handling dan debug logging

### 2. Livewire Integration
- Memperbaiki method `sendMessage()` dengan emit event
- Menambahkan method `messageReceived()` untuk real-time updates
- Memperbaiki setup Echo listener dengan error handling

### 3. Form Validation
- Menambahkan validation di form input
- Disable tombol send jika pesan kosong dan tidak ada file
- Loading state saat upload file

### 4. Batch Scripts
- `start_chat_system.bat` - Jalankan semua services sekaligus
- `start_vite.bat` - Jalankan Vite development server
- `start_reverb.bat` - Jalankan Laravel Reverb (sudah ada)
- `start_queue.bat` - Jalankan Queue Worker (sudah ada)

## 🚀 CARA TESTING

### 1. Persiapan
```bash
# Install dependencies jika belum
npm install
composer install

# Pastikan database sudah ada data user
php artisan migrate --seed
```

### 2. Jalankan Services
**Opsi A: Jalankan semua sekaligus**
```bash
start_chat_system.bat
```

**Opsi B: Jalankan satu-satu**
```bash
# Terminal 1
php artisan reverb:start --host=0.0.0.0 --port=8080

# Terminal 2  
php artisan queue:work --tries=3 --timeout=60

# Terminal 3
npm run dev
```

### 3. Testing Chat
1. Login sebagai user dengan role PPK atau Pokja Pemilihan
2. Buka halaman Chat atau Detail Pengajuan
3. Pilih user untuk chat
4. Kirim pesan text atau file
5. Buka browser/tab lain dengan user berbeda
6. Cek apakah pesan muncul real-time

### 4. Debug
- Buka `/test-chat-debug` untuk halaman debug
- Cek Console Browser untuk log Echo
- Cek Network tab untuk WebSocket connection

## 🔧 TROUBLESHOOTING

### Error: "Echo is not defined"
- Pastikan Vite dev server berjalan (`npm run dev`)
- Cek Console untuk error Vite build

### Error: WebSocket connection failed
- Pastikan Laravel Reverb berjalan di port 8080
- Cek konfigurasi .env untuk REVERB_* variables

### Error: Broadcasting authorization failed
- Pastikan route `/broadcasting/auth` ada dan benar
- Cek apakah user sudah login

### Form tidak bisa submit
- Cek apakah tombol disabled karena pesan kosong
- Cek Console untuk error JavaScript/Livewire

## ✨ FITUR YANG SUDAH FIXED

- ✅ Real-time messaging dengan Laravel Reverb
- ✅ File sharing dengan preview dan download
- ✅ Responsive design untuk mobile
- ✅ Unread message indicators
- ✅ Auto-scroll to bottom
- ✅ Search conversations
- ✅ Integration dengan detail pengajuan
- ✅ Role-based access control
- ✅ Error handling dan validation

## 📍 NEXT STEPS

Jika masih ada error, cek:
1. Console Browser untuk error JavaScript
2. Laravel Log untuk error backend
3. Network tab untuk failed requests
4. Ensure all services running dengan benar
