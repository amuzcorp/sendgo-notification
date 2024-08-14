<?php

return [
    'debug' => env('SENDGO_DEBUG', false),
    'endpoint' => 'https://sendgo.io/api/notification',
    'content_type' => 'application/json',
    'accept' => 'application/json',
    'access_key' => env('SENDGO_ACCESS_KEY', 'test'),
    'secret_key' => env('SENDGO_PRIVATE_KEY', 'test'),
    'sms' => [
        'sender_key' => env('SENDGO_SENDER_KEY', 'test'),
    ],
    'kakao' => [
        'sender_key' => env('SENDGO_KAKAO_SENDER_KEY', 'test'),
    ],
];
