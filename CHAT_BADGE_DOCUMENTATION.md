# 📊 Chat Unread Count Badge - Dokumentasi

## 🎯 FITUR BARU: BADGE PESAN BELUM DIBACA

Sistem chat sekarang menampilkan **count pesan baru yang belum dibaca** di menu Chat di sidebar untuk role PPK dan Pokja Pemilihan.

## ✨ FITUR YANG DITAMBAHKAN

### 1. Badge Visual
- ✅ **Badge merah** dengan angka jumlah pesan belum dibaca
- ✅ **Animasi pulse** untuk menarik perhatian  
- ✅ **Auto-hide** ketika tidak ada pesan baru
- ✅ **Responsive design** untuk mobile dan desktop

### 2. Real-time Update
- ✅ **Auto-refresh** badge setiap 30 detik
- ✅ **Instant update** ketika ada pesan baru (via Livewire events)
- ✅ **Update immediately** ketika user membuka percakapan
- ✅ **API endpoint** untuk AJAX requests

### 3. Tracking System
- ✅ **Read status** tracking per pesan
- ✅ **Auto-mark as read** ketika user membuka conversation
- ✅ **Role-based counting** (hanya untuk PPK & Pokja)
- ✅ **Exclude own messages** dari count

## 🔧 IMPLEMENTASI TEKNIS

### Database Schema
```php
// Field di table chat_messages
'read_at' => 'datetime' // NULL = belum dibaca, filled = sudah dibaca
```

### Backend Components

**1. View Composer** - `app/View/Composers/SidebarComposer.php`
```php
public function compose(View $view)
{
    // Hitung unread messages untuk user yang login
    // Hanya untuk role PPK dan pokjapemilihan
}
```

**2. API Controller** - `app/Http/Controllers/ChatsController.php`
```php
public function getUnreadCount()
{
    // API endpoint untuk AJAX update badge
    // Return JSON: {'count': number}
}
```

**3. Livewire Component** - `app/Livewire/CustomChat.php`
```php
public function markMessagesAsRead()
{
    // Mark pesan sebagai read ketika conversation dibuka
}
```

### Frontend Components

**1. Sidebar Badge** - `resources/views/layouts/_include/sidebar.blade.php`
```blade
<span class="badge badge-pill badge-danger float-right chat-badge">
    {{ $unreadChatCount }}
</span>
```

**2. JavaScript Update** - `resources/views/layouts/master.blade.php`
```javascript
// Auto-refresh badge setiap 30 detik
// Listen for Livewire events
// Update via API call
```

**3. CSS Animation** - `resources/views/layouts/_include/sidebar.blade.php`
```css
.badge-danger {
    animation: pulse-badge 2s infinite;
}
```

## 🎨 STYLING & DESIGN

### Visual Design
- **Background**: `#dc3545` (Bootstrap danger red)
- **Color**: White text
- **Size**: `0.6rem` font, `18px` min-width
- **Position**: Float right di menu Chat
- **Animation**: Subtle pulse effect

### Responsive Behavior
- **Desktop**: Normal badge size dengan full animation
- **Mobile**: Smaller badge (`0.55rem`, `16px` min-width)
- **Tablet**: Adaptive sizing

## 🔄 WORKFLOW LOGIC

### Count Calculation
1. **Get User Conversations**: Ambil semua conversation yang user ikuti
2. **Filter Messages**: Pesan dari user lain (bukan diri sendiri)
3. **Check Read Status**: Hitung yang `read_at` masih NULL
4. **Return Count**: Total unread messages

### Mark as Read Process
1. **User Opens Conversation**: Pilih conversation di chat
2. **Auto-mark Read**: Semua pesan dari user lain di-mark sebagai read
3. **Update Badge**: Count berkurang real-time
4. **Broadcast Update**: Badge di sidebar langsung update

### Real-time Updates
1. **Page Load**: Badge shows initial count via view composer
2. **Periodic Refresh**: JavaScript poll API setiap 30 detik
3. **Event-driven**: Update via Livewire events (message-sent, conversation-selected)
4. **API Response**: AJAX call ke `/api/unread-count`

## 📱 USER EXPERIENCE

### For PPK Users
- Melihat berapa pesan baru dari Pokja Pemilihan
- Badge muncul di menu "Chat" dengan angka merah
- Otomatis hilang ketika semua pesan sudah dibaca

### For Pokja Pemilihan Users  
- Melihat berapa pesan baru dari PPK
- Badge muncul di menu "Chat" dengan angka merah
- Otomatis hilang ketika semua pesan sudah dibaca

### Interactive Behavior
- **Hover Effect**: Badge slightly scales on hover
- **Click Action**: Klik menu Chat buka halaman chat
- **Auto Update**: Badge berubah tanpa refresh halaman
- **Visual Feedback**: Pulse animation menarik perhatian

## 🧪 TESTING SCENARIOS

### Test 1: Badge Display
- [ ] Login sebagai PPK, ada pesan baru dari Pokja → badge muncul
- [ ] Login sebagai Pokja, ada pesan baru dari PPK → badge muncul  
- [ ] Tidak ada pesan baru → badge tidak muncul

### Test 2: Count Accuracy
- [ ] 1 pesan baru → badge show "1"
- [ ] 5 pesan baru → badge show "5"
- [ ] Mixed conversations → count semua unread messages

### Test 3: Mark as Read
- [ ] Buka conversation → badge count berkurang
- [ ] Baca semua pesan → badge hilang
- [ ] Switch conversation → badge update untuk setiap conversation

### Test 4: Real-time Update
- [ ] Terima pesan baru → badge langsung update (dalam 30 detik)
- [ ] Send pesan → badge tidak count pesan sendiri
- [ ] Multiple users → badge accurate per user

### Test 5: Responsive Design
- [ ] Desktop → badge normal size dan position
- [ ] Mobile → badge smaller tapi tetap visible
- [ ] Different screen sizes → consistent behavior

## 🔧 TROUBLESHOOTING

### Badge Tidak Muncul
1. Check role user (harus PPK atau pokjapemilihan)
2. Verify ada pesan belum dibaca dari user lain
3. Clear cache: `php artisan view:clear`
4. Check view composer terdaftar di AppServiceProvider

### Count Tidak Akurat
1. Check database: field `read_at` di table `chat_messages`
2. Verify conversation participants JSON format
3. Test API endpoint: `/api/unread-count`
4. Check browser console untuk JavaScript errors

### Badge Tidak Update Real-time
1. Check JavaScript console untuk errors
2. Verify CSRF token di AJAX request
3. Test API response: should return `{'count': number}`
4. Check Livewire events firing properly

## 📈 PERFORMANCE NOTES

- **Database Queries**: Optimized dengan index pada conversation_id dan user_id
- **API Calls**: Lightweight JSON response, cached untuk 30 seconds
- **JavaScript**: Minimal DOM manipulation, efficient event listeners
- **Memory Usage**: View composer runs only on sidebar pages

## 🎉 HASIL AKHIR

Sekarang menu Chat di sidebar menampilkan:
```
Chat (3)  ← Badge merah dengan count unread messages
```

Badge ini:
- ✅ Update otomatis setiap 30 detik
- ✅ Update instant ketika ada activity 
- ✅ Hilang ketika semua pesan sudah dibaca
- ✅ Hanya muncul untuk PPK & Pokja Pemilihan
- ✅ Responsive dan accessible design
- ✅ Professional government-style appearance

---

**Status: ✅ FEATURE COMPLETE**  
**Last Updated: {{ date('Y-m-d H:i:s') }}**  
**Version: 2.1 - With Unread Count Badge**
