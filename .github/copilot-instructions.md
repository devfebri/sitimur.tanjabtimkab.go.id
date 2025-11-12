# AI Coding Assistant Instructions for SITIMUR

## Project Overview
SITIMUR is a Laravel-based web application for managing procurement submissions (pengajuan) in the Tanjung Jabung Timur government. Key features include:
- Procurement submission management with multi-stage approval workflow
- Real-time chat functionality using Laravel WebSockets
- Document management and verification system
- Auto-expiry system for submissions

## Core Architecture

### Key Components
1. **Procurement Module**
   - `app/Models/Pengajuan.php` - Core submission model
   - `app/Http/Controllers/PengajuanOpenController.php` - Main submission controller
   - States tracked via status codes in `pengajuans` table

2. **Chat System**
   - `app/Livewire/CustomChat.php` - Real-time chat component
   - `app/Events/MessageSent.php` - WebSocket events
   - Uses Laravel WebSockets for real-time communication

3. **Auto-Expiry System**
   - `app/Console/Commands/AutoChangeExpiredPengajuanStatus.php` - Daily status updates
   - Automatically changes submission status 14/34 → 88 after expiry
   - Runs daily at 08:00 via Laravel scheduler

## Development Workflows

### Local Development
```bash
# Start development server
php artisan serve

# Run scheduler locally
php artisan schedule:work

# Run WebSocket server
php artisan websockets:serve
```

### Key Test Routes
- `/test/auto-expire-pengajuan` - Test submission expiry
- `/test/pengajuan-status-14-34` - Monitor submission statuses

## Project Conventions

### Status Code System
- Status 14/34: Pending review
- Status 88: Expired
- Check `IMPLEMENTATION_SUMMARY.md` for complete status documentation

### File Organization
- Submission files stored in `storage/app/pengajuan/`
- Chat attachments in `storage/app/chat/`
- Views in `resources/views/` following Laravel conventions

### Authorization
- Role-based access using Laravel Gates and Policies
- Key roles: verifikator, kepalaukpbj, pokja
- Check user roles before any submission status changes

## Common Patterns
1. **Submission Status Updates**
```php
$pengajuan->status = $newStatus;
$pengajuan->status_updated = now();
$pengajuan->save();
```

2. **User Role Checks**
```php
$userId = Auth::user()->id;
$query->where('pokja1_id', $userId)
      ->orWhere('pokja2_id', $userId)
      ->orWhere('pokja3_id', $userId);
```

## Integration Points
1. **WebSocket Events**: Publish through `MessageSent` event for real-time updates
2. **File Storage**: Use `Storage::disk('public')` for uploaded documents
3. **PDF Generation**: Use `Barryvdh\DomPDF\Facade\Pdf` for reports
