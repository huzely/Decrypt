<?php
$config = require __DIR__ . '/../config/config.php';

class DB
{
    private static ?PDO $instance = null;

    public static function conn(): PDO
    {
        global $config;
        if (self::$instance === null) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['DB_HOST'], $config['DB_NAME']);
            self::$instance = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$instance;
    }
}
