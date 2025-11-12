# Unread Chat Notifications Feature

## Overview
Added unread message badge notifications on chat buttons to help users quickly identify if they have unread messages in the chat system.

## Features Implemented

### 1. Unread Message Counting
- Count unread messages for each chat type (verifikator/pokja)
- Unread = messages sent by others and not yet read by current user
- Counts only messages from the current pengajuan

### 2. Badge Display on Chat Buttons
- Red badge with count number appears on chat button when there are unread messages
- Badge automatically hides when all messages are read
- Positioned at top-right of button using Bootstrap positioning classes

### 3. Automatic Message Marking
- Messages are automatically marked as read when user opens the chat
- Happens via AJAX call when chat page loads
- `read_at` timestamp is set to current time

### 4. Real-time Updates
- Unread count updates every 5 seconds via polling
- No need to refresh page to see notification updates
- Works in background without blocking UI

## Files Modified

### Backend - Controller
**File:** `app/Http/Controllers/ChatsController.php`

#### New Methods:
```php
/**
 * Get unread messages count for a specific pengajuan
 * Route: GET /api/unread-count/{id}
 * Returns: JSON with unread_count
 */
public function getUnreadCount($pengajuanId)

/**
 * Mark messages as read
 * Route: POST /api/mark-as-read/{id}
 * Returns: JSON with success flag
 */
public function markAsRead($pengajuanId)
```

**Logic:**
- Determines `chat_type` based on user role and pengajuan status
- Status < 20 = verifikator chat, Status >= 20 = pokja chat
- Only counts messages from OTHER users (not current user)
- Updates `read_at` field to current timestamp

### Backend - Routes
**File:** `routes/web.php`

Added routes in all three middleware groups:

```php
// Verifikator routes
Route::get('/api/unread-count/{id}', [ChatsController::class, 'getUnreadCount'])->name('api.unread.count');
Route::post('/api/mark-as-read/{id}', [ChatsController::class, 'markAsRead'])->name('api.mark.read');

// PPK routes
Route::get('/api/unread-count/{id}', [ChatsController::class, 'getUnreadCount'])->name('api.unread.count');
Route::post('/api/mark-as-read/{id}', [ChatsController::class, 'markAsRead'])->name('api.mark.read');

// Pokja Pemilihan routes
Route::get('/api/unread-count/{id}', [ChatsController::class, 'getUnreadCount'])->name('api.unread.count');
Route::post('/api/mark-as-read/{id}', [ChatsController::class, 'markAsRead'])->name('api.mark.read');
```

### Frontend - Views
**File:** `resources/views/dashboard/open.blade.php`

Updated chat button markup to include badge span:

```blade
<a href="{{ route('ppk_pengajuan.chat', [$data->id]) }}"
   class="btn btn-info btn-sm me-1 position-relative chat-button" 
   data-pengajuan-id="{{ $data->id }}" 
   data-chat-type="verifikator" 
   title="Chat dengan Verifikator">
    <i class="mdi mdi-chat-processing me-1"></i>Chat
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none chat-badge" 
          data-pengajuan-id="{{ $data->id }}">
        0
    </span>
</a>
```

Added JavaScript function to load unread counts:
```javascript
function loadUnreadCounts() {
    $('.chat-button').each(function() {
        var pengajuanId = $(this).data('pengajuan-id');
        $.ajax({
            url: "/api/unread-count/" + pengajuanId,
            success: function(response) {
                if (response.unread_count > 0) {
                    $badge.text(response.unread_count).removeClass('d-none');
                } else {
                    $badge.addClass('d-none');
                }
            }
        });
    });
}
```

**File:** `resources/views/chatsnew.blade.php`

Added function call to mark messages as read when chat opens:
```javascript
// Mark messages as read when user opens chat
markAllAsRead();

function markAllAsRead() {
    $.ajax({
        url: "{{ route(auth()->user()->role.'_api.mark.read', ['id' => $pengajuan->id]) }}",
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            console.log('Messages marked as read');
        }
    });
}
```

## Database Fields
- `chat_messages.read_at` - timestamp when message was read (nullable)
- Field was already present in migration, now actively used

## User Flow

1. **User sees notification badge on chat button:**
   - Unread count appears as red badge on button
   - Updates every 5 seconds automatically

2. **User clicks chat button:**
   - Opens chat page
   - AJAX call marks all unread messages as read
   - `read_at` timestamp is set

3. **Chat page displays messages:**
   - All messages load
   - User can read and send new messages

4. **User navigates away:**
   - Returns to pengajuan detail page
   - Badge is no longer shown (all messages read)

## API Endpoints

### Get Unread Count
```
GET /verifikator/api/unread-count/{pengajuanId}
GET /ppk/api/unread-count/{pengajuanId}
GET /pokjapemilihan/api/unread-count/{pengajuanId}

Response:
{
    "unread_count": 3
}
```

### Mark Messages as Read
```
POST /verifikator/api/mark-as-read/{pengajuanId}
POST /ppk/api/mark-as-read/{pengajuanId}
POST /pokjapemilihan/api/mark-as-read/{pengajuanId}

Response:
{
    "success": true
}
```

## Technical Details

### Chat Type Determination
```php
// For PPK
$chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';

// For Verifikator
$chatType = 'verifikator';

// For Pokja Pemilihan
$chatType = 'pokja';
```

### Unread Message Query
```php
$unreadCount = ChatMessage::where('pengajuan_id', $pengajuanId)
    ->where('chat_type', $chatType)
    ->where('user_id', '!=', $user->id)  // Messages from others only
    ->whereNull('read_at')               // Not yet read
    ->count();
```

### Mark as Read Query
```php
ChatMessage::where('pengajuan_id', $pengajuanId)
    ->where('chat_type', $chatType)
    ->where('user_id', '!=', $user->id)  // Don't mark own messages
    ->whereNull('read_at')               // Only unread
    ->update(['read_at' => now()]);
```

## Styling
- Uses Bootstrap badge class: `.badge rounded-pill bg-danger`
- Positioned with: `position-absolute top-0 start-100 translate-middle`
- Hides with: `.d-none` class when count = 0

## Polling Interval
- Unread count refreshes every 5 seconds (5000ms)
- Configurable in JavaScript via: `setInterval(loadUnreadCounts, 5000);`

## Security
- All API endpoints check user authentication
- Routes use role-based middleware
- Only counts messages for authorized pengajuan

## Performance
- Uses simple COUNT query (indexed on pengajuan_id, chat_type)
- Polling every 5 seconds is reasonable for notification system
- Badge updates are lightweight (small JSON response)

## Future Enhancements
- Real-time updates via WebSocket instead of polling
- Sound notification when new message arrives
- Browser notification permission
- Mark individual messages as read
- Read receipts (show who read the message)

## Testing Checklist
- [ ] Badge appears when user receives message
- [ ] Badge disappears after clicking chat button
- [ ] Unread count is correct
- [ ] Badge updates in real-time (within 5 seconds)
- [ ] Mark as read works for all user roles
- [ ] Works for both verifikator and pokja chat types
- [ ] Handles multiple pengajuan correctly

## Commit Info
- **Hash:** e971095
- **Message:** "feat: Add unread message badge notification on chat buttons"
- **Date:** 2025-11-12
