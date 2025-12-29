<?php
// Cấu hình hệ thống đơn giản, chỉnh lại giá trị cho hosting
return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'decrypt_news',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'BASE_URL' => 'http://localhost',
    'SESSION_SALT' => 'change_this_salt',
    'CACHE_PATH' => __DIR__ . '/cache',
    'CACHE_TTL' => 60,
    'TELEGRAM_BOT_TOKEN' => '',
    'TELEGRAM_ADMIN_CHAT_ID' => '',
    'APP_DEBUG' => true,
    'LOG_PATH' => __DIR__ . '/logs/app.log',
];
