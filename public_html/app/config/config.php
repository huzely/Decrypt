<?php
// Cấu hình hệ thống (không dùng .env)
define('DB_HOST', 'localhost');
define('DB_NAME', 'news_site');
define('DB_USER', 'db_user');
define('DB_PASS', 'db_pass');

define('BASE_URL', 'https://yourdomain.com');
define('SESSION_SALT', 'change_me_salt');
define('CACHE_PATH', __DIR__ . '/../cache');
define('CACHE_TTL', 300); // giây

define('TELEGRAM_BOT_TOKEN', '');
define('TELEGRAM_ADMIN_CHAT_ID', '');
