<?php
return [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_name' => getenv('DB_NAME') ?: 'decrypt',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASS') ?: '',
    'telegram_bot_token' => getenv('TELEGRAM_BOT_TOKEN') ?: '',
    'telegram_chat_id' => getenv('TELEGRAM_CHAT_ID') ?: '',
    'site_name' => 'Link Wrapper',
    'default_admin_user' => getenv('ADMIN_USER') ?: 'admin',
    // 123456
    'default_admin_pass_hash' => getenv('ADMIN_PASS_HASH') ?: '$2y$10$GrZkqCKsRJd9rvKIIzcCfO8pt7bhjjdGLDnWPt/YkHZ5fejKQ.4KS',
];
