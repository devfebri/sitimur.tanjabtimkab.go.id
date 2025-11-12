# 📚 CHAT HISTORY FEATURE - QUICK REFERENCE

## 🚀 Quick Start

### For Verifikators
```
1. Login dengan akun verifikator
2. Klik "Chat History" di sidebar
3. Lihat semua percakapan dengan PPK
4. Gunakan search/filter untuk mencari chat
5. Klik "Buka Chat" untuk membuka percakapan
```

### For Developers
```php
// Access the feature
GET /verifikator/chat-history → List all chats
GET /verifikator/chat-history/{id}/messages → Get messages for specific chat

// Key files
app/Http/Controllers/ChatsController.php → chatHistory() & chatHistoryMessages()
resources/views/chat-history.blade.php → Chat history UI
routes/web.php → Routes definition
database/migrations/2025_11_12_000001_*.php → Database changes
```

---

## 📂 File Structure

```
sitimur.tanjabtimkab.go.id/
├── app/Http/Controllers/
│   └── ChatsController.php (modified)
├── resources/views/
│   ├── chat-history.blade.php (NEW)
│   ├── chatsnew.blade.php (modified)
│   └── layouts/_include/sidebar.blade.php (modified)
├── routes/
│   └── web.php (modified)
├── database/migrations/
│   ├── 2025_08_03_000002_... (modified)
│   └── 2025_11_12_000001_... (NEW)
└── Documentation/
    ├── README_CHAT_HISTORY.md (summary)
    ├── CHAT_HISTORY_FEATURE.md (details)
    ├── CHAT_HISTORY_TESTING.php (tests)
    ├── CHAT_HISTORY_COMPLETE.md (comprehensive)
    └── FINAL_VERIFICATION_REPORT.md (verification)
```

---

## 🔧 Installation

### Step 1: Pull Latest Code
```bash
git pull origin main
```

### Step 2: Run Migration
```bash
php artisan migrate
```

### Step 3: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Step 4: Verify Installation
```bash
# Check routes
php artisan route:list | grep "chat-history"

# Check database
php artisan tinker
> DB::select('DESCRIBE chat_messages');
```

---

## 🎯 Features

| Feature | How to Use | Notes |
|---------|-----------|-------|
| **Search** | Type in search box | Real-time, searches paket name & PPK |
| **Filter Status** | Click status dropdown | Verifikasi / Pokja / Semua |
| **Sort** | Click sort dropdown | Latest / Oldest / Unread |
| **View Chat** | Click "Buka Chat" button | Opens full chat conversation |
| **Statistics** | See top 3 cards | Total pengajuan, pesan, unread |

---

## 🔐 Security

### Access Control
```php
// Only verifikators can access
VerifikatorMiddleware::class

// Verifikators only see assigned pengajuan
where('verifikator_id', Auth::id())

// API validates ownership
if ($pengajuan->verifikator_id !== $verifikator->id) {
    return 403;
}
```

### Protection
- ✅ CSRF tokens on forms
- ✅ XSS protection via Blade escaping
- ✅ SQL injection prevention via Eloquent
- ✅ Role-based access control

---

## 📊 Database

### Table: chat_messages
```sql
CREATE TABLE chat_messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    pengajuan_id INT NOT NULL,
    message TEXT,
    file_path TEXT,
    chat_type ENUM('verifikator','pokja') NOT NULL DEFAULT 'verifikator',
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    KEY chat_messages_user_id_index (user_id),
    KEY chat_messages_pengajuan_id_chat_type_created_at_index 
        (pengajuan_id, chat_type, created_at)
);
```

---

## 📱 Responsive Design

| Device | Breakpoint | Behavior |
|--------|-----------|----------|
| Desktop | 1200px+ | Full layout, all features visible |
| Tablet | 768-1199px | Adjusted spacing, stacked cards |
| Mobile | <768px | Single column, touch-friendly buttons |

---

## 🧪 Testing

### Manual Testing
```bash
# 1. Login as verifikator
# 2. Navigate to /verifikator/chat-history
# 3. Verify page loads with data
# 4. Test search functionality
# 5. Test filter dropdowns
# 6. Click "Buka Chat" button
# 7. Verify chat opens correctly
# 8. Click "Kembali" to return
# 9. Test on mobile device
```

### Automated Tests
See `CHAT_HISTORY_TESTING.php` for:
- 12 test categories
- Manual testing steps
- Verification checklist
- Edge cases to test

---

## 🐛 Troubleshooting

### Issue: Page doesn't load
```
Solution:
1. Check if logged in as verifikator
2. Verify migration ran: php artisan migrate:status
3. Check logs: tail storage/logs/laravel.log
4. Verify route: php artisan route:list | grep chat-history
```

### Issue: Filters not working
```
Solution:
1. Open browser console (F12)
2. Check for JavaScript errors
3. Try hard refresh (Ctrl+Shift+R)
4. Check if JavaScript is enabled
```

### Issue: Unread count incorrect
```
Solution:
1. Check read_at values in database
2. Verify chat_type is 'verifikator'
3. Ensure correct pengajuan status (< 20)
4. Clear browser cache
```

---

## 📈 Performance

### Optimization Tips
- Pagination: 20 items per page
- Indexes: Composite index on (pengajuan_id, chat_type, created_at)
- Eager Loading: User relationships loaded with chats
- Client-Side: Filtering done in browser (no server calls)

### Expected Performance
- Page load: < 2 seconds
- Search/filter: < 50ms
- API response: < 500ms
- Database query: < 200ms

---

## 🔄 API Endpoints

### Endpoints
```
GET /verifikator/chat-history
    Returns: View with all verifikator chats and statistics
    
GET /verifikator/chat-history/{id}/messages
    Returns: JSON with messages array
    Params: id (pengajuan_id)
    Auth: Verifikator role required
```

### Response Format
```json
{
    "messages": [
        {
            "id": 1,
            "user_id": 9,
            "user_name": "Nama User",
            "message": "Pesan teks",
            "file_path": "path/to/file",
            "file_name": "filename.pdf",
            "created_at": "Nov 12, 2025 10:30 AM",
            "chat_type": "verifikator"
        }
    ]
}
```

---

## 🎨 UI Components

### Statistics Cards
- Shows total pengajuan
- Shows total pesan
- Shows pesan belum dibaca

### Chat List Items
- Avatar dengan inisial user
- Nama paket dan status badge
- Last message preview
- Unread count badge
- "Buka Chat" button

### Filters Section
- Search input (real-time)
- Status dropdown (3 options)
- Sort dropdown (3 options)

---

## 📚 Documentation Files

### Must Read
1. **README_CHAT_HISTORY.md** - Start here for overview
2. **FINAL_VERIFICATION_REPORT.md** - For production deployment

### Detailed Info
3. **CHAT_HISTORY_FEATURE.md** - Technical documentation
4. **CHAT_HISTORY_COMPLETE.md** - Comprehensive guide

### Testing
5. **CHAT_HISTORY_TESTING.php** - Testing procedures

---

## 💻 Code Examples

### Get All Verifikator Chats
```php
use App\Models\ChatMessage;

$messages = ChatMessage::where('chat_type', 'verifikator')
    ->where('pengajuan_id', $pengajuanId)
    ->with('user')
    ->orderBy('created_at', 'asc')
    ->get();
```

### Get Unread Count
```php
$unreadCount = ChatMessage::where('pengajuan_id', $pengajuanId)
    ->where('chat_type', 'verifikator')
    ->whereNull('read_at')
    ->where('user_id', '!=', Auth::id())
    ->count();
```

### Check Verifikator Assignment
```php
if ($pengajuan->verifikator_id !== Auth::id()) {
    abort(403, 'Unauthorized');
}
```

---

## 🚀 Deployment

### Production Steps
```bash
# 1. Pull latest
git pull origin main

# 2. Migrate
php artisan migrate

# 3. Cache clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 4. Verify
php artisan route:list | grep chat-history

# 5. Monitor
tail -f storage/logs/laravel.log
```

### Rollback (if needed)
```bash
php artisan migrate:rollback --step=1
```

---

## 📞 Support

### Documentation
- See `CHAT_HISTORY_FEATURE.md` for architecture
- Check `FINAL_VERIFICATION_REPORT.md` for deployment
- Review `CHAT_HISTORY_TESTING.php` for testing

### Error Logs
```
Location: storage/logs/laravel.log
Check for: 403, 500, SQL errors, route errors
```

### Database Debug
```php
php artisan tinker
> DB::table('chat_messages')->latest('id')->limit(5)->get();
> DB::select('SHOW INDEX FROM chat_messages');
```

---

## ✅ Checklist Before Production

- [ ] Read FINAL_VERIFICATION_REPORT.md
- [ ] Test all 3 roles (ppk, verifikator, pokjapemilihan)
- [ ] Verify database migration
- [ ] Check page performance
- [ ] Test on mobile
- [ ] Verify security measures
- [ ] Check error logs are clean
- [ ] Get stakeholder approval
- [ ] Plan monitoring strategy
- [ ] Prepare rollback plan

---

**Status: ✅ READY FOR PRODUCTION**

*Last Updated: November 12, 2025*
*Version: 1.0*
