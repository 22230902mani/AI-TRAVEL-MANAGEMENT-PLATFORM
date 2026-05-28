<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Razorpay\Api\Api as RazorpayApi;

$keyId = config('services.razorpay.key_id');
$keySecret = config('services.razorpay.key_secret');

echo "Key ID: " . $keyId . "\n";
echo "Key Secret: " . (empty($keySecret) ? 'EMPTY' : 'PRESENT') . "\n";

try {
    $api = new RazorpayApi($keyId, $keySecret);
    $order = $api->order->create([
        'amount'          => 100, // 1 INR in paise
        'currency'        => 'INR',
        'receipt'         => 'TEST-' . time(),
        'payment_capture' => 1,
    ]);
    echo "Success! Order ID: " . $order->id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
    if (method_exists($e, 'getTraceAsString')) {
        echo "Trace:\n" . $e->getTraceAsString() . "\n";
    }
}
