<?php
require_once __DIR__ . '/../helpers/db.php';
require_once __DIR__ . '/../helpers/util.php';

class Admin
{
    public static function findByUsername(string $username): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function log(int $adminId, string $action): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO admin_logs (admin_id, action) VALUES (:admin_id, :action)');
        $stmt->execute([':admin_id' => $adminId, ':action' => $action]);
    }
}
