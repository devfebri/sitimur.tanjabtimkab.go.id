#!/bin/bash
# Chat System Troubleshooting Script
# Usage: php artisan tinker then paste commands from this file

<?php
// Chat System Auto-Diagnostics

echo "=== 🔍 CHAT SYSTEM DIAGNOSTICS ===\n\n";

// 1. Check symlink
echo "1️⃣  Checking Storage Symlink...\n";
$symlink = public_path('storage');
if (is_link($symlink)) {
    echo "   ✓ Symlink exists\n";
    echo "   Target: " . readlink($symlink) . "\n";
} else {
    echo "   ❌ Symlink missing. Run: php artisan storage:link\n";
}

// 2. Check storage directory
echo "\n2️⃣  Checking Storage Directory...\n";
$storagePath = storage_path('app/public');
if (is_dir($storagePath)) {
    echo "   ✓ Directory exists\n";

    if (is_writable($storagePath)) {
        echo "   ✓ Directory is writable\n";
    } else {
        echo "   ❌ Directory is not writable. Check permissions.\n";
    }
} else {
    echo "   ❌ Directory doesn't exist. Creating...\n";
    @mkdir($storagePath, 0755, true);
}

// 3. Check chat_messages table
echo "\n3️⃣  Checking Database...\n";
try {
    $count = \App\Models\ChatMessage::count();
    echo "   ✓ chat_messages table exists\n";
    echo "   Total messages: $count\n";

    $withFiles = \App\Models\ChatMessage::whereNotNull('file_path')->count();
    echo "   Messages with files: $withFiles\n";

    if ($withFiles > 0) {
        echo "\n   Recent files:\n";
        $recent = \App\Models\ChatMessage::whereNotNull('file_path')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recent as $msg) {
            $exists = file_exists(storage_path('app/public/' . $msg->file_path));
            $status = $exists ? '✓' : '❌';
            echo "   $status {$msg->file_path}\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 4. Check file permissions
echo "\n4️⃣  Checking File Permissions...\n";
$dirs = [
    'storage' => base_path('storage'),
    'storage/app' => storage_path('app'),
    'storage/app/public' => storage_path('app/public'),
];

foreach ($dirs as $name => $path) {
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? '✓' : '❌';
        echo "   $writable $name ($perms)\n";
    }
}

// 5. Check database migration
echo "\n5️⃣  Checking Database Schema...\n";
try {
    $schema = \Illuminate\Support\Facades\Schema::getColumns('chat_messages');
    $hasFilePath = false;
    foreach ($schema as $col) {
        if ($col['name'] === 'file_path') {
            $hasFilePath = true;
            echo "   ✓ file_path column exists\n";
            echo "   Type: " . $col['type'] . "\n";
            echo "   Nullable: " . ($col['nullable'] ? 'Yes' : 'No') . "\n";
            break;
        }
    }
    if (!$hasFilePath) {
        echo "   ❌ file_path column missing\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 6. Test file upload
echo "\n6️⃣  Quick Test File Upload...\n";
try {
    // Create a test image
    $testImage = UploadedFile::fake()->image('test.jpg');
    $testPath = 'test-uploads/' . uniqid() . '.jpg';

    \Illuminate\Support\Facades\Storage::disk('public')->put($testPath, $testImage);

    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($testPath)) {
        echo "   ✓ Test file uploaded successfully\n";
        echo "   URL: /storage/$testPath\n";

        // Cleanup
        \Illuminate\Support\Facades\Storage::disk('public')->delete($testPath);
        echo "   ✓ Test file cleaned up\n";
    } else {
        echo "   ❌ Test file not found after upload\n";
    }
} catch (\Exception $e) {
    echo "   ⚠️  Test upload skipped: " . $e->getMessage() . "\n";
}

echo "\n=== END DIAGNOSTICS ===\n";
echo "\n💡 Tips:\n";
echo "- If symlink missing: php artisan storage:link\n";
echo "- If file path issues: Check storage/app/public permissions\n";
echo "- If database error: Run migrations\n";
echo "- Clear cache: php artisan cache:clear\n";
