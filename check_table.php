<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Get database connection
$connection = DB::connection();

// Get table columns
$columns = $connection->getDoctrineSchemaManager()->listTableColumns('chat_messages');

echo "=== CHAT_MESSAGES TABLE STRUCTURE ===\n\n";
echo sprintf("%-20s %-20s %-15s %-20s\n", "Column", "Type", "Nullable", "Default");
echo str_repeat("-", 75) . "\n";

foreach ($columns as $column) {
    $name = $column->getName();
    $type = $column->getType()->getName();
    $nullable = $column->getNotnull() ? "NO" : "YES";
    $default = $column->getDefault() !== null ? $column->getDefault() : "-";
    
    echo sprintf("%-20s %-20s %-15s %-20s\n", $name, $type, $nullable, $default);
}

// Get indexes
$indexes = $connection->getDoctrineSchemaManager()->listTableIndexes('chat_messages');
echo "\n=== INDEXES ===\n\n";
foreach ($indexes as $index) {
    echo $index->getName() . ": " . implode(", ", $index->getColumns()) . "\n";
}

// Get foreign keys
$foreignKeys = $connection->getDoctrineSchemaManager()->listTableForeignKeys('chat_messages');
echo "\n=== FOREIGN KEYS ===\n\n";
if (empty($foreignKeys)) {
    echo "None\n";
} else {
    foreach ($foreignKeys as $fk) {
        echo $fk->getName() . ": " . implode(", ", $fk->getLocalColumns()) . " -> " . 
             $fk->getForeignTableName() . "(" . implode(", ", $fk->getForeignColumns()) . ")\n";
    }
}

// Count records
$count = DB::table('chat_messages')->count();
echo "\n=== RECORD COUNT ===\n\n";
echo "Total records: $count\n";

// Sample data
echo "\n=== SAMPLE RECORDS (Last 5) ===\n\n";
$samples = DB::table('chat_messages')->latest('id')->limit(5)->get();
foreach ($samples as $sample) {
    echo "ID: {$sample->id}, Pengajuan: {$sample->pengajuan_id}, Chat Type: {$sample->chat_type}, Created: {$sample->created_at}\n";
}
?>
