<?php

// Simple test file to check if everything is working
echo "=== TESTING CHAT SYSTEM ===\n";

// Test autoload
try {
    echo "1. Testing autoload...\n";
    if (class_exists('\App\Livewire\CustomChat')) {
        echo "   ✅ CustomChat class found\n";
    } else {
        echo "   ❌ CustomChat class NOT found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test models
try {
    echo "2. Testing models...\n";
    if (class_exists('\App\Models\ChatConversation')) {
        echo "   ✅ ChatConversation model found\n";
    } else {
        echo "   ❌ ChatConversation model NOT found\n";
    }
    
    if (class_exists('\App\Models\ChatMessage')) {
        echo "   ✅ ChatMessage model found\n";
    } else {
        echo "   ❌ ChatMessage model NOT found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test controller
try {
    echo "3. Testing controller...\n";
    if (class_exists('\App\Http\Controllers\ChatsController')) {
        echo "   ✅ ChatsController found\n";
    } else {
        echo "   ❌ ChatsController NOT found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
