<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../services/AuthService.php';

class ApiAuthController
{
    private AuthService $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function session(): void
    {
        ApiResponse::success($this->service->getSessionData());
    }

    public function login(): void
    {
        $data = ApiResponse::input();

        try {
            $user = $this->service->attemptLogin(
                trim((string)($data['username'] ?? '')),
                (string)($data['password'] ?? '')
            );

            ApiResponse::success([
                'user' => $user,
                'csrf_token' => $this->service->ensureCsrfToken(),
                'redirect' => '/'
            ], 'Đăng nhập thành công.');
        } catch (RuntimeException $e) {
            ApiResponse::error($e->getMessage(), 401);
        }
    }

    public function register(): void
    {
        $data = ApiResponse::input();

        try {
            $this->service->register($data);
            $_SESSION['flash'] = 'Đăng ký thành công. Vui lòng đăng nhập.';

            ApiResponse::success([
                'redirect' => '/login'
            ], 'Đăng ký thành công.', 201);
        } catch (RuntimeException $e) {
            ApiResponse::error($e->getMessage(), 422);
        }
    }

    public function logout(): void
    {
        $data = ApiResponse::input();

        if (!empty($_SESSION['user']) && !$this->service->validateCsrf($data['csrf_token'] ?? null)) {
            ApiResponse::error('Invalid CSRF token.', 419);
        }

        $this->service->logout();
        ApiResponse::success(['redirect' => '/login'], 'Đăng xuất thành công.');
    }
}
