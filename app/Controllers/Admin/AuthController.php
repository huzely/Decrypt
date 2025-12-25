<?php
namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\User;

class AuthController
{
    public function showLogin(): void
    {
        View::render('admin/login', ['error' => null]);
    }

    public function login(): void
    {
        verify_csrf();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
        $_SESSION['login_locked_until'] = $_SESSION['login_locked_until'] ?? 0;
        if (time() < $_SESSION['login_locked_until']) {
            View::render('admin/login', ['error' => 'Tạm khóa đăng nhập, thử lại sau ít phút.']);
            return;
        }
        $user = User::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_locked_until'] = 0;
            redirect('admin');
        }
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 300;
        }
        View::render('admin/login', ['error' => 'Sai email hoặc mật khẩu']);
    }

    public function logout(): void
    {
        session_destroy();
        redirect('admin/login');
    }
}
