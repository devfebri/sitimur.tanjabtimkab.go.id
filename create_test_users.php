<?php

// Create test users for chat system
echo "=== CREATING TEST USERS FOR CHAT ===\n";

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Create PPK user
    $ppkUser = User::updateOrCreate(
        ['username' => 'ppk_test'],
        [
            'name' => 'PPK Test User',
            'email' => 'ppk@test.com',
            'password' => Hash::make('password123'),
            'role' => 'ppk',
        ]
    );
    echo "✅ PPK User created: {$ppkUser->name} (ID: {$ppkUser->id})\n";

    // Create Pokja user
    $pokjaUser = User::updateOrCreate(
        ['username' => 'pokja_test'],
        [
            'name' => 'Pokja Test User',
            'email' => 'pokja@test.com',
            'password' => Hash::make('password123'),
            'role' => 'pokjapemilihan',
        ]
    );
    echo "✅ Pokja User created: {$pokjaUser->name} (ID: {$pokjaUser->id})\n";

    echo "\n=== TEST USERS READY ===\n";
    echo "Login credentials:\n";
    echo "1. PPK User:\n";
    echo "   Username: ppk_test\n";
    echo "   Password: password123\n";
    echo "   URL: http://localhost:8000/ppk/chats\n\n";
    echo "2. Pokja User:\n";
    echo "   Username: pokja_test\n";
    echo "   Password: password123\n";
    echo "   URL: http://localhost:8000/pokjapemilihan/chats\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
