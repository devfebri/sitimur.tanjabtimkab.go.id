<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking chat_messages table structure ===\n\n";

if (Schema::hasTable('chat_messages')) {
    echo "✓ Table 'chat_messages' exists\n\n";
    
    // Get all columns
    $columns = Schema::getColumnListing('chat_messages');
    echo "Columns in table:\n";
    foreach ($columns as $column) {
        echo "  - " . $column . "\n";
    }
    
    echo "\n=== Detailed column info ===\n\n";
    
    // Get detailed information
    $results = DB::select("DESCRIBE chat_messages");
    
    foreach ($results as $row) {
        echo "Field: " . $row->Field . "\n";
        echo "  Type: " . $row->Type . "\n";
        echo "  Null: " . $row->Null . "\n";
        echo "  Key: " . ($row->Key ?: "None") . "\n";
        echo "  Default: " . ($row->Default ?: "NULL") . "\n";
        echo "  Extra: " . ($row->Extra ?: "None") . "\n\n";
    }
    
    // Check if read_at column exists
    if (Schema::hasColumn('chat_messages', 'read_at')) {
        echo "\n✓ Column 'read_at' EXISTS\n";
    } else {
        echo "\n✗ Column 'read_at' DOES NOT EXIST - NEEDS TO BE ADDED\n";
    }
    
    // Check if chat_type column exists
    if (Schema::hasColumn('chat_messages', 'chat_type')) {
        echo "✓ Column 'chat_type' EXISTS\n";
    } else {
        echo "✗ Column 'chat_type' DOES NOT EXIST\n";
    }
    
} else {
    echo "✗ Table 'chat_messages' does NOT exist\n";
}

// Sample data
echo "\n=== Sample data from chat_messages ===\n\n";
$sample = DB::table('chat_messages')->limit(3)->get();
echo "Count: " . count($sample) . " records\n\n";
foreach ($sample as $msg) {
    echo "ID: " . $msg->id . "\n";
    echo "  user_id: " . $msg->user_id . "\n";
    echo "  pengajuan_id: " . $msg->pengajuan_id . "\n";
    echo "  message: " . substr($msg->message ?? '', 0, 50) . "...\n";
    echo "  chat_type: " . ($msg->chat_type ?? 'NOT SET') . "\n";
    echo "  read_at: " . ($msg->read_at ?? 'NULL') . "\n";
    echo "  created_at: " . $msg->created_at . "\n\n";
}
