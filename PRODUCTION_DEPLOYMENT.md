# 🚀 PRODUCTION DEPLOYMENT GUIDE - Chat Fixes

## Pre-Deployment Verification

### Step 1: Code Review
```bash
# Check modified files
git status

# Expected output:
# M app/Http/Controllers/ChatsController.php
# M resources/views/chatsnew.blade.php
```

### Step 2: Staging Test
```bash
# On staging server, run:
php artisan storage:link
chmod -R 755 storage/app/public
php artisan cache:clear
```

### Step 3: Manual Testing (Staging)
1. [ ] Open chat page
2. [ ] Upload image
3. [ ] Verify image displays
4. [ ] Send multiple messages
5. [ ] Test rapid submissions (should prevent double send)
6. [ ] Test error recovery
7. [ ] Check browser console (no errors)
8. [ ] Verify file exists in storage
9. [ ] Check database record

---

## Deployment Steps

### Phase 1: Preparation (No Downtime)

```bash
cd /path/to/sitimur

# Backup current code
git stash

# Verify current state
php artisan tinker
> ChatMessage::whereNotNull('file_path')->count()
> exit

# Backup database
# (use your backup tool/command)
```

### Phase 2: Code Deployment

```bash
# Pull latest code (if using git)
git pull origin main

# OR manually copy files:
# - app/Http/Controllers/ChatsController.php
# - resources/views/chatsnew.blade.php

# Install/update dependencies if needed
composer install --no-dev --optimize-autoloader

# Clear application cache
php artisan cache:clear
```

### Phase 3: File System Setup

```bash
# Create/verify storage symlink
php artisan storage:link

# Verify symlink
ls -la public/storage

# Set correct permissions
chmod -R 755 storage/app/public
chmod -R 755 storage/app/private
chmod -R 755 storage/framework
```

### Phase 4: Verification

```bash
# Check everything is working
php artisan tinker

# Test database access
> DB::connection()->getPdo();
> ChatMessage::count()

# Test file storage
> Storage::disk('public')->exists('test')
> exit

# Clear cache one more time
php artisan cache:clear
php artisan config:cache
```

### Phase 5: Go Live Testing

1. Open production site
2. [ ] Access chat page (should load)
3. [ ] Upload test image
4. [ ] Verify image displays
5. [ ] Check file in storage
6. [ ] Monitor error logs

---

## Rollback Plan

If something goes wrong:

```bash
# Revert code
git revert <commit-hash>
# OR restore from backup

# Clear cache
php artisan cache:clear

# Verify database is intact
SELECT COUNT(*) FROM chat_messages;
```

---

## Post-Deployment Monitoring

### Day 1
- [ ] Monitor error logs: `storage/logs/laravel.log`
- [ ] Check chat uploads are being created
- [ ] Verify user feedback is positive
- [ ] Check storage disk usage growth

### Week 1
- [ ] Monitor for any recurring issues
- [ ] Check file permissions haven't changed
- [ ] Verify no orphaned files accumulating
- [ ] Performance metrics normal

### Monthly
- [ ] Clean up old test files
- [ ] Monitor storage growth
- [ ] Archive old chat files if needed

---

## Troubleshooting During/After Deploy

### Symlink Issue
```bash
# Verify
ls -la public/storage

# Fix
rm public/storage
php artisan storage:link
```

### Permission Issue
```bash
# Fix
chmod -R 755 storage/app/public
chown -R www-data:www-data storage/  # If using www-data
```

### Images Not Showing
```bash
# Check logs
tail -f storage/logs/laravel.log

# Check file exists
ls -la storage/app/public/pengajuan/

# Check database
SELECT file_path FROM chat_messages WHERE id = X;
```

### Double Submission Still Happening
```bash
# Check browser console for JavaScript errors
# Verify jQuery is loaded
# Check that chatsnew.blade.php is being used (not old version)
```

---

## Verification Checklist Post-Deploy

```bash
#!/bin/bash

echo "🔍 Post-Deployment Chat System Verification"
echo "==========================================="

# 1. Symlink check
echo "1. Checking symlink..."
if [ -L "public/storage" ]; then
    echo "   ✓ Symlink exists"
else
    echo "   ✗ Symlink missing"
fi

# 2. Directory check
echo "2. Checking directories..."
if [ -d "storage/app/public" ]; then
    echo "   ✓ Storage directory exists"
else
    echo "   ✗ Storage directory missing"
fi

# 3. Permissions check
echo "3. Checking permissions..."
stat -c "%a %n" storage/app/public | grep "755\|777"
if [ $? -eq 0 ]; then
    echo "   ✓ Permissions are correct"
else
    echo "   ✗ Permissions need fixing"
fi

# 4. File count
echo "4. Checking uploaded files..."
COUNT=$(find storage/app/public/pengajuan -type f 2>/dev/null | wc -l)
echo "   Total files: $COUNT"

# 5. Recent uploads
echo "5. Most recent uploads..."
find storage/app/public/pengajuan -type f -printf '%T@ %p\n' 2>/dev/null | \
    sort -rn | head -5 | \
    while read timestamp path; do
        echo "   - $path"
    done

echo ""
echo "✓ Verification complete"
```

Save as `verify_chat.sh` and run:
```bash
chmod +x verify_chat.sh
./verify_chat.sh
```

---

## Quick Reference - What Changed

| Item | Change |
|------|--------|
| Files Modified | 2 |
| Files Added | 6 |
| Lines Added | ~300 |
| Database Changes | None (backward compatible) |
| Breaking Changes | None |
| Requires Migration | No |
| Requires Config Change | No |

---

## Support Contact

If issues occur during deployment:

1. Check error logs: `storage/logs/laravel.log`
2. Check file: `public/debug_chat.php` (in browser)
3. Run: `php public/inspect_chat_files.php`
4. Review: Documentation files created
5. Reference: This deployment guide

---

## Success Criteria

✅ Deployment successful if:
- [ ] No errors in `storage/logs/laravel.log`
- [ ] Symlink exists and works
- [ ] Can upload file without error
- [ ] Image displays in chat
- [ ] Can upload another file (no double-submit issue)
- [ ] File exists in `storage/app/public/pengajuan/`
- [ ] Button has loading state during submit
- [ ] No broken images in chat
- [ ] Error messages are informative

---

**Deployment Date:** _______________  
**Deployed By:** _______________  
**Verified By:** _______________  

---

*Keep this guide for future reference*
