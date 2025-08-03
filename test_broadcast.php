<?php

require_once 'vendor/autoload.php';

use App\Events\MessageSent;
use App\Models\ChatMessage;

// Test broadcasting functionality
echo "Testing Chat Broadcasting...\n";

try {
    // Get first message from database
    $message = ChatMessage::with('user', 'conversation')->first();
    
    if (!$message) {
        echo "❌ No messages found in database. Create a message first.\n";
        exit;
    }
    
    echo "✅ Found message: {$message->message}\n";
    echo "📡 Broadcasting message...\n";
    
    // Fire the event
    event(new MessageSent($message));
    
    echo "✅ Message broadcasted successfully!\n";
    echo "📺 Check browser console for real-time updates.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
