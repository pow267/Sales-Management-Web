<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../helpers/ValidationException.php';
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

    public function storeSession(): void
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
        } catch (ValidationException $e) {
            ApiResponse::error(
                $e->getMessage(),
                $e->getCode() >= 400 ? $e->getCode() : 422,
                $e->getErrors()
            );
        } catch (InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        } catch (RuntimeException $e) {
            ApiResponse::error($e->getMessage(), 401);
        }
    }

    public function destroySession(): void
    {
        $data = ApiResponse::input();

        if (!empty($_SESSION['user']) && !$this->service->validateCsrf($data['csrf_token'] ?? null)) {
            ApiResponse::error('Invalid CSRF token.', 403);
        }

        $this->service->logout();
        ApiResponse::success(['redirect' => '/login'], 'Đăng xuất thành công.');
    }
}
