<?php
/**
 * Cấu hình hệ thống (không dùng .env)
 */
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'news_portal',
        'user' => 'db_user',
        'pass' => 'db_password',
        'charset' => 'utf8mb4',
    ],
    'base_url' => 'https://example.com',
    'session_salt' => 'change_me_strong_salt',
    'telegram_bot_token' => 'TELEGRAM_BOT_TOKEN',
    'telegram_admin_ids' => [123456789],
    'cache_path' => __DIR__ . '/../cache',
    'cache_ttl' => [
        'settings' => 300,
        'home' => 45,
        'post' => 45,
    ],
    'pagination' => 9,
    'rate_limit_seconds' => 30,
];
