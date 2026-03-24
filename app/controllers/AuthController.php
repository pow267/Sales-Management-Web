<?php

require_once __DIR__ . '/../services/AuthService.php';

class AuthController
{
    private AuthService $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function showLogin(): void
    {
        $this->service->ensureCsrfToken();
        require __DIR__ . '/../views/login.php';
    }

    public function login(): void
    {
        try {
            $this->service->attemptLogin(
                trim((string)($_POST['username'] ?? '')),
                (string)($_POST['password'] ?? '')
            );

            header("Location: /");
            exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /login");
            exit;
        }
    }

    public function showRegister(): void
    {
        $this->service->ensureCsrfToken();
        require __DIR__ . '/../views/register.php';
    }

    public function register(): void
    {
        try {
            $this->service->register($_POST);
            $_SESSION['flash'] = "Đăng ký thành công. Vui lòng đăng nhập.";

            header("Location: /login");
            exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /register");
            exit;
        }
    }

    public function logout(): void
    {
        $this->service->logout();
        header("Location: /login");
        exit;
    }
}
