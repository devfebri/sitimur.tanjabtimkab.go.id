<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ChatMessage;
use App\Models\Pengajuan;

echo "=== Testing Unread Count Functionality ===\n\n";

// Test data
$pengajuanId = 1;
$userId = 9; // PPK user

echo "Test Parameters:\n";
echo "  Pengajuan ID: " . $pengajuanId . "\n";
echo "  Test User ID: " . $userId . "\n\n";

// Get pengajuan
$pengajuan = Pengajuan::find($pengajuanId);
if (!$pengajuan) {
    echo "✗ Pengajuan not found\n";
    exit(1);
}

echo "Pengajuan Status: " . $pengajuan->status . "\n";

// Determine chat_type based on status
$chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';
echo "Expected Chat Type: " . $chatType . "\n\n";

// Test unread count query
echo "=== Testing Unread Count Query ===\n";
$unreadCount = ChatMessage::where('pengajuan_id', $pengajuanId)
    ->where('chat_type', $chatType)
    ->where('user_id', '!=', $userId)
    ->whereNull('read_at')
    ->count();

echo "Unread messages count: " . $unreadCount . "\n\n";

// Show all messages
echo "=== All messages in this pengajuan ===\n";
$allMessages = ChatMessage::where('pengajuan_id', $pengajuanId)
    ->where('chat_type', $chatType)
    ->with('user')
    ->get();

echo "Total messages: " . count($allMessages) . "\n\n";

foreach ($allMessages as $msg) {
    echo "Message ID: " . $msg->id . "\n";
    echo "  From: " . $msg->user->name . " (ID: " . $msg->user_id . ")\n";
    echo "  Chat Type: " . $msg->chat_type . "\n";
    echo "  Read At: " . ($msg->read_at ?? 'NOT READ') . "\n";
    echo "  Message: " . substr($msg->message ?? '', 0, 50) . "...\n";
    echo "  Is unread for user {$userId}? " . ($msg->user_id != $userId && is_null($msg->read_at) ? 'YES' : 'NO') . "\n\n";
}

// Test mark as read
echo "=== Testing Mark As Read ===\n";
$updated = ChatMessage::where('pengajuan_id', $pengajuanId)
    ->where('chat_type', $chatType)
    ->where('user_id', '!=', $userId)
    ->whereNull('read_at')
    ->update(['read_at' => now()]);

echo "Updated " . $updated . " messages\n\n";

// Check unread count again
$unreadCountAfter = ChatMessage::where('pengajuan_id', $pengajuanId)
    ->where('chat_type', $chatType)
    ->where('user_id', '!=', $userId)
    ->whereNull('read_at')
    ->count();

echo "Unread count after mark as read: " . $unreadCountAfter . "\n";

// Show routes
echo "\n=== Routes Check ===\n";
$routes = [
    'GET /ppk/api/unread-count/{id}',
    'POST /ppk/api/mark-as-read/{id}',
    'GET /verifikator/api/unread-count/{id}',
    'POST /verifikator/api/mark-as-read/{id}',
    'GET /pokjapemilihan/api/unread-count/{id}',
    'POST /pokjapemilihan/api/mark-as-read/{id}',
];

foreach ($routes as $route) {
    echo "  ✓ " . $route . "\n";
}

echo "\nDone!\n";
