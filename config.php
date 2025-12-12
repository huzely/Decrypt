<?php
// Database and Telegram configuration
$db_host = 'localhost';
$db_name = 'decrypt_db';
$db_user = 'db_user';
$db_pass = 'db_pass';
$telegram_bot_token = 'YOUR_TELEGRAM_BOT_TOKEN';

$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $db_user, $db_pass, $options);

date_default_timezone_set('Asia/Ho_Chi_Minh');

function telegram_api_url(string $method): string
{
    global $telegram_bot_token;
    return "https://api.telegram.org/bot{$telegram_bot_token}/{$method}";
}
