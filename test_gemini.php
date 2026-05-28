<?php
$key = 'AIzaSyDWdnkeJ24qaJzzPhYYnLnr1bueZtzsl40';
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$key}";

$data = json_encode([
    'contents' => [
        ['parts' => [['text' => 'Hello']]]
    ]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
echo $response;
