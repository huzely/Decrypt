<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class Ad
{
    public function activeByPosition(string $position): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM ads WHERE position = :position AND status = 1 ORDER BY id DESC');
        $stmt->execute(['position' => $position]);
        return $stmt->fetchAll();
    }
}
