<?php
class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_rol']  = $user['rol'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['user_rol'] ?? '') === 'administrador';
    }

    public static function requireAdmin(): void
    {
        self::check();
        if (!self::isAdmin()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    public static function id(): int
    {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    public static function name(): string
    {
        return $_SESSION['user_name'] ?? '';
    }

    public static function rol(): string
    {
        return $_SESSION['user_rol'] ?? '';
    }

    public static function isLogged(): bool
    {
        return !empty($_SESSION['user_id']);
    }
}
