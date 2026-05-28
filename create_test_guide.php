<?php
$user = App\Models\User::create([
    'name' => 'Test Guide',
    'email' => 'testguide@example.com',
    'password' => Hash::make('password123'),
    'role' => 'guide',
    'guide_status' => 'approved',
    'guide_specialty' => 'Testing',
]);
echo "Test guide created.\n";
