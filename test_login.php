<?php
use Illuminate\Support\Facades\Http;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'testguide@example.com')->first();
echo "Guide email: " . $user->email . "\n";
echo "Hash matches 'password123': " . (Hash::check('password123', $user->password) ? 'yes' : 'no') . "\n";
