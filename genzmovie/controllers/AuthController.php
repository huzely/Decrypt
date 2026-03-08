<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public function login(): void
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validate_csrf($_POST['csrf_token'] ?? null)) {
                $error = 'CSRF token invalid.';
            } else {
                $userModel = new User();
                $user = $userModel->findByEmail(trim($_POST['email']));
                if ($user && password_verify($_POST['password'], $user['password'])) {
                    $_SESSION['user'] = $user;
                    redirect('/');
                }
                $error = 'Email hoặc mật khẩu không đúng.';
            }
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    public function register(): void
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validate_csrf($_POST['csrf_token'] ?? null)) {
                $error = 'CSRF token invalid.';
            } else {
                $userModel = new User();
                $ok = $userModel->create(trim($_POST['name']), trim($_POST['email']), $_POST['password']);
                if ($ok) {
                    redirect('/?route=login');
                }
                $error = 'Không thể tạo tài khoản.';
            }
        }
        include __DIR__ . '/../views/auth/register.php';
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        redirect('/');
    }
}
