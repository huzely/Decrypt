<?php
function start_secure_session(array $config): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('news_' . substr(hash('sha256', $config['session_salt']), 0, 6));
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => !empty($_SERVER['HTTPS']),
            'cookie_samesite' => 'Lax',
        ]);
    }
}

function require_admin_auth(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
