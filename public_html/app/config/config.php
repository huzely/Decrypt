<?php
return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'decrypt_news',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'BASE_URL' => 'http://localhost',
    'SESSION_SALT' => 'change_me_to_random_salt',
    'TELEGRAM_BOT_TOKEN' => '',
    'TELEGRAM_CHAT_ID' => '',
    'TELEGRAM_ADMIN_IDS' => [123456789],
    'CACHE_PATH' => __DIR__ . '/../cache',
    'CACHE_TTL' => [
        'settings' => 300,
        'home' => 45,
        'article' => 45,
    ],
];
