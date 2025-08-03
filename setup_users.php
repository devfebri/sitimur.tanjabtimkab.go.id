#!/usr/bin/env php
<?php

// Simple script to create users using artisan commands
echo "Creating test users...\n";

// PPK User
$ppkCommand = 'php artisan tinker --execute="use App\\Models\\User; use Illuminate\\Support\\Facades\\Hash; User::updateOrCreate([\'username\' => \'ppk_test\'], [\'name\' => \'PPK Test User\', \'email\' => \'ppk@test.com\', \'password\' => Hash::make(\'password123\'), \'role\' => \'ppk\']); echo \'PPK user created\';"';

// Pokja User  
$pokjaCommand = 'php artisan tinker --execute="use App\\Models\\User; use Illuminate\\Support\\Facades\\Hash; User::updateOrCreate([\'username\' => \'pokja_test\'], [\'name\' => \'Pokja Test User\', \'email\' => \'pokja@test.com\', \'password\' => Hash::make(\'password123\'), \'role\' => \'pokjapemilihan\']); echo \'Pokja user created\';"';

echo "Creating PPK user...\n";
system($ppkCommand);

echo "\nCreating Pokja user...\n";  
system($pokjaCommand);

echo "\n=== USERS CREATED ===\n";
echo "Login with:\n";
echo "PPK: ppk_test / password123\n";
echo "Pokja: pokja_test / password123\n";
