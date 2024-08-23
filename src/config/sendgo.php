<?php

return [
    'debug' => env('SENDGO_DEBUG', false),
    'endpoint' => 'https://sendgo.io/api/',
    'content_type' => 'application/json',
    'accept' => 'application/json',
    'access_key' => env('SENDGO_ACCESS_KEY'),
    'secret_key' => env('SENDGO_SECRET_KEY'),
    'sms' => [
        'sender_key' => env('SENDGO_SENDER_KEY'),
    ],
    'kakao' => [
        'sender_key' => env('SENDGO_KAKAO_SENDER_KEY'),
    ],
];
