<?php

class RoleMiddleware
{
    public static function admin(): void
    {
        if (
            empty($_SESSION['user']) ||
            $_SESSION['user']['role'] !== 'admin'
        ) {
            http_response_code(403);
            exit('403 - Forbidden');
        }
    }

    public static function user(): void
    {
        if (empty($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }
    }
}