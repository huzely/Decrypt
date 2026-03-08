<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function current_user_id(): ?int
{
    return isset($_SESSION['user']) ? (int) $_SESSION['user']['id'] : null;
}

function slugify(string $title): string
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title) ?? ''));
    return trim($slug, '-');
}
