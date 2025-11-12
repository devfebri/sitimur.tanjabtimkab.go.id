# ✅ FIX: Route [ppk_api.unread.count] not defined

## Problem
Error: `Route [ppk_api.unread.count] not defined`

This happened because the JavaScript was trying to call `route('ppk_unread.count', ...)` using Blade's route() helper, but Blade couldn't evaluate it dynamically for different user roles.

---

## Solution Applied

### Changed From:
```javascript
var baseUrl = "{{ route('ppk_unread.count', ['id' => 'PLACEHOLDER']) }}";
var url = baseUrl.replace('ppk_', userRole + '_').replace('PLACEHOLDER', pengajuanId);
```

### Changed To:
```javascript
var url = '/' + userRole + '/pengajuan/' + pengajuanId + '/unread-count';
```

**Why?** 
- Simpler and cleaner
- No Blade template evaluation issues
- Direct URL string building
- Works for all 3 roles (ppk, verifikator, pokjapemilihan)

---

## Routes (Still Correctly Registered)

✅ All 3 routes verified and working:

```
GET|HEAD   ppk/pengajuan/{id}/unread-count              ppk_unread.count
GET|HEAD   verifikator/pengajuan/{id}/unread-count      verifikator_unread.count
GET|HEAD   pokjapemilihan/pengajuan/{id}/unread-count   pokjapemilihan_unread.count
```

---

## How It Works Now

```javascript
// Example for PPK user with pengajuan ID = 1:
var userRole = 'ppk';
var pengajuanId = 1;
var url = '/' + 'ppk' + '/pengajuan/' + 1 + '/unread-count';
// Result: /ppk/pengajuan/1/unread-count ✓

// Example for Verifikator user with pengajuan ID = 5:
var userRole = 'verifikator';
var pengajuanId = 5;
var url = '/' + 'verifikator' + '/pengajuan/' + 5 + '/unread-count';
// Result: /verifikator/pengajuan/5/unread-count ✓
```

---

## Testing

Now try this:
1. Buka pengajuan detail page
2. Press F12 → Network tab
3. Lihat request ke `/ppk/pengajuan/1/unread-count` (atau role Anda)
4. Status should be: **200 OK**
5. Response: `{"unread_count": N}`
6. Badge harus muncul di tombol Chat

---

## Commit

```
fbf2373 - fix: Use direct URL path instead of route() helper for unread-count
```

---

## Status

✅ **FIXED!** Sekarang seharusnya berjalan dengan baik.

Jika masih ada error, buka browser console (F12) dan lihat apa error-nya.
