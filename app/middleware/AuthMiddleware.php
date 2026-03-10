<?php

class AuthMiddleware
{
    public static function check(): void
    {
        if (empty($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }
    }

    public static function role(string $role): void
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
            http_response_code(403);
            echo "Bạn không có quyền truy cập chức năng này.";
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        return !empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
}