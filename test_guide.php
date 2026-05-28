<?php
$user = App\Models\User::where('role', 'guide')->first();
if (!$user) {
    echo "No guide found.\n";
    exit;
}
echo "Name: " . $user->name . "\n";
echo "Role: " . $user->role . "\n";
echo "Hash matches 'password': " . (Hash::check('password', $user->password) ? 'yes' : 'no') . "\n";
echo "Hash matches '12345678': " . (Hash::check('12345678', $user->password) ? 'yes' : 'no') . "\n";
echo "Hash matches '123456789': " . (Hash::check('123456789', $user->password) ? 'yes' : 'no') . "\n";
