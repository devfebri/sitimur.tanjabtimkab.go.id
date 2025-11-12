# Debugging Unread Chat Notifications

## Error: Missing required parameter for [Route: ppk_api.unread.count]

### Root Cause
Error ini muncul karena ada pemanggilan `route()` helper di Blade template yang tidak memiliki parameter yang diperlukan.

### Solution Implemented

Kami telah mengubah pendekatan dari menggunakan Laravel `route()` helper ke string concatenation langsung di JavaScript.

**Before:**
```blade
url: "{{ route(auth()->user()->role.'_api.unread.count', ['id' => ':id']) }}".replace(':id', pengajuanId)
```

**After:**
```javascript
var userRole = '{{ auth()->user()->role }}';
var url = '/' + userRole + '/api/unread-count/' + pengajuanId;
```

### How to Debug

1. **Open Browser Developer Tools** (F12)
2. **Go to Console tab**
3. **Look for lines like:**
   ```
   Loading unread count from: /ppk/api/unread-count/1
   ```

4. **If you see errors**, check:
   - Network tab → see if requests are being made
   - Check if HTTP 404 or 500 errors appear
   - Look for CORS issues

### Testing the API Endpoint Directly

#### Via Browser URL:
```
http://localhost/ppk/api/unread-count/1
http://localhost/verifikator/api/unread-count/1
http://localhost/pokjapemilihan/api/unread-count/1
```

Should return JSON:
```json
{
  "unread_count": 6
}
```

#### Via Terminal:
```bash
curl "http://localhost/ppk/api/unread-count/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Verification Checklist

- [x] Routes are registered correctly (verified in `test_routes_api.php`)
- [x] Routes have proper parameter names `{id}`
- [x] Controller methods exist and return JSON
- [x] Database columns exist: `read_at`, `chat_type`
- [x] JavaScript uses correct URL format

### Expected Console Output

When page loads, you should see:
```
Loading unread count from: /ppk/api/unread-count/1
Unread count response: {unread_count: 6}
Loading unread count from: /verifikator/api/unread-count/2
Unread count response: {unread_count: 0}
```

### If Badge Still Doesn't Appear

1. **Check JavaScript is running:**
   - Open console, type: `typeof loadUnreadCounts`
   - Should show: `function`

2. **Check chat buttons are found:**
   - Open console, type: `$('.chat-button').length`
   - Should show a number > 0

3. **Manually call function:**
   - Open console, type: `loadUnreadCounts()`
   - Check for any errors

4. **Check badge HTML:**
   - Right-click button → Inspect
   - Look for: `<span class="... chat-badge ...">0</span>`

### File Changes Made

- `resources/views/dashboard/open.blade.php` - Updated JavaScript
- `resources/views/chatsnew.blade.php` - Added mark as read functionality
- `app/Http/Controllers/ChatsController.php` - Added API methods
- `routes/web.php` - Added API routes for all 3 roles

### Related Test Files

- `test_unread_functionality.php` - Tests unread counting logic
- `test_routes_api.php` - Verifies routes and URL generation
- `check_db_structure.php` - Checks database columns

