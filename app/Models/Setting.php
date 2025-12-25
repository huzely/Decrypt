<?php
namespace App\Models;

use App\Core\Database;

class Setting
{
    public static function get(string $key, $default = null)
    {
        $stmt = Database::getInstance()->prepare('SELECT value FROM settings WHERE `key` = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? json_decode($value, true) : $default;
    }

    public static function set(string $key, $value): void
    {
        $stmt = Database::getInstance()->prepare('INSERT INTO settings (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE value=:value');
        $stmt->execute([
            ':key' => $key,
            ':value' => json_encode($value),
        ]);
    }

    public static function getAll(): array
    {
        $stmt = Database::getInstance()->query('SELECT `key`, `value` FROM settings');
        $data = [];
        foreach ($stmt->fetchAll() as $row) {
            $data[$row['key']] = json_decode($row['value'], true);
        }
        return $data;
    }
}
