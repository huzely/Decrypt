<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s',
                Config::get('db.host'),
                Config::get('db.name'),
                Config::get('db.charset', 'utf8mb4')
            );
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                self::$instance = new PDO($dsn, Config::get('db.user'), Config::get('db.pass'), $options);
            } catch (PDOException $e) {
                if (Config::get('app.debug')) {
                    throw $e;
                }
                die('Database connection failed.');
            }
        }
        return self::$instance;
    }
}
