#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== TESTING CHAT FUNCTIONALITY ===\n\n";

// Test 1: Check broadcasting configuration
echo "1. Broadcasting Configuration:\n";
echo "   Default Driver: " . config('broadcasting.default') . "\n";
echo "   Reverb App Key: " . config('broadcasting.connections.reverb.key') . "\n";
echo "   Reverb Host: " . config('broadcasting.connections.reverb.options.host') . "\n";
echo "   Reverb Port: " . config('broadcasting.connections.reverb.options.port') . "\n\n";

// Test 2: Check users
echo "2. Users Check:\n";
$pokja = App\Models\User::where('username', 'pokja1')->first();
if ($pokja) {
    echo "   Pokja1 Found: ID={$pokja->id}, Name={$pokja->name}, Role={$pokja->role}\n";
} else {
    echo "   ❌ Pokja1 user not found\n";
}

$ppk = App\Models\User::where('role', 'ppk')->first();
if ($ppk) {
    echo "   PPK Found: ID={$ppk->id}, Name={$ppk->name}, Role={$ppk->role}\n";
} else {
    echo "   ❌ PPK user not found\n";
}
echo "\n";

// Test 3: Check conversations
echo "3. Conversations Check:\n";
$conversations = App\Models\ChatConversation::all();
echo "   Total Conversations: " . $conversations->count() . "\n";

if ($pokja && $ppk) {
    $conversation = App\Models\ChatConversation::whereJsonContains('participants', $pokja->id)
        ->whereJsonContains('participants', $ppk->id)
        ->first();
    
    if ($conversation) {
        echo "   ✅ Conversation exists between Pokja1 and PPK (ID: {$conversation->id})\n";
        echo "   Participants: " . json_encode($conversation->participants) . "\n";
        
        // Test authorization
        echo "   Authorization Test:\n";
        echo "     Pokja1 has access: " . ($conversation->hasParticipant($pokja->id) ? 'YES' : 'NO') . "\n";
        echo "     PPK has access: " . ($conversation->hasParticipant($ppk->id) ? 'YES' : 'NO') . "\n";
    } else {
        echo "   ⚠️ No conversation found between Pokja1 and PPK\n";
    }
}
echo "\n";

// Test 4: Check recent messages
echo "4. Recent Messages:\n";
$recentMessages = App\Models\ChatMessage::with('user', 'conversation')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($recentMessages->count() > 0) {
    foreach ($recentMessages as $message) {
        echo "   Message ID: {$message->id}\n";
        echo "   From: {$message->user->name} (ID: {$message->user_id})\n";
        echo "   Conversation: {$message->conversation_id}\n";
        echo "   Message: " . substr($message->message, 0, 50) . "...\n";
        echo "   Created: {$message->created_at}\n";
        echo "   ---\n";
    }
} else {
    echo "   No messages found\n";
}

echo "\n=== TEST COMPLETE ===\n";
