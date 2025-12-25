<?php
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'news_site',
        'user' => 'db_user',
        'pass' => 'db_pass',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => 'http://localhost',
        'environment' => 'production',
        'debug' => false,
        'session_name' => 'decrypt_news_session',
    ],
    'security' => [
        'csrf_key' => 'change_this_csrf_key',
    ],
];
