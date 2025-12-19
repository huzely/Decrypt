<?php
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'decrypt_news',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'base_url' => 'http://localhost',
    'session_secret' => 'change_this_secret_key',
    'session_salt' => 'news_salt_123',
    'telegram' => [
        'bot_token' => 'YOUR_TELEGRAM_BOT_TOKEN',
        'admin_ids' => [123456789],
    ],
    'cache' => [
        'enabled' => true,
        'path' => __DIR__ . '/../cache',
        'ttl' => 300,
    ],
];
