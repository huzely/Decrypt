<?php

declare(strict_types=1);

function require_login(): void
{
    if (empty($_SESSION['user'])) {
        redirect('/?route=login');
    }
}

function require_admin(): void
{
    if (empty($_SESSION['admin'])) {
        header('Location: /genzmovie/admin/login.php');
        exit;
    }
}
