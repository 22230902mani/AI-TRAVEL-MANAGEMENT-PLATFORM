<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'manilukka143@gmail.com')->first();
echo "AVATAR: " . $user->avatar . "\n";
echo "PROFILE AVATAR: " . ($user->profile ? $user->profile->avatar : 'null') . "\n";
echo "AVATAR URL: " . $user->avatar_url . "\n";
