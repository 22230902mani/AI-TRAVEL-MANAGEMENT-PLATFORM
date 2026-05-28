<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
\Illuminate\Support\Facades\Auth::login(App\Models\User::first());
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/itineraries/create', 'GET'
    )
);
file_put_contents('rendered.html', $response->getContent());
