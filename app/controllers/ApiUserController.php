<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../helpers/ValidationException.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../services/AuthService.php';

class ApiUserController
{
    private AuthService $authService;
    private UserModel $userModel;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userModel = new UserModel();
    }

    public function store(): void
    {
        $data = ApiResponse::input();

        try {
            $this->authService->register($data);
            $user = $this->userModel->findByUsername(
                trim((string)($data['username'] ?? ''))
            );

            $_SESSION['flash'] = 'Đăng ký thành công. Vui lòng đăng nhập.';

            ApiResponse::success([
                'user' => $user ? $this->sanitizeUser($user) : null,
                'redirect' => '/login'
            ], 'Đăng ký thành công.', 201);
        } catch (ValidationException $e) {
            ApiResponse::error(
                $e->getMessage(),
                $e->getCode() >= 400 ? $e->getCode() : 422,
                $e->getErrors()
            );
        } catch (DomainException $e) {
            ApiResponse::error($e->getMessage(), 409);
        } catch (InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        } catch (RuntimeException $e) {
            ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function index(): void
    {
        $this->ensureAdmin();

        $users = array_map(
            fn(array $user): array => $this->sanitizeUser($user),
            $this->userModel->getAll()
        );

        ApiResponse::success(['users' => $users]);
    }

    public function show(int $id): void
    {
        $this->ensureAdmin();

        $user = $this->userModel->findById($id);

        if (!$user) {
            ApiResponse::error('Người dùng không tồn tại.', 404);
        }

        ApiResponse::success([
            'user' => $this->sanitizeUser($user)
        ]);
    }

    private function ensureAuthenticated(): void
    {
        if (empty($_SESSION['user'])) {
            ApiResponse::error('Bạn cần đăng nhập để sử dụng API.', 401);
        }
    }

    private function ensureAdmin(): void
    {
        $this->ensureAuthenticated();

        if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            ApiResponse::error('Bạn không có quyền thực hiện thao tác này.', 403);
        }
    }

    private function sanitizeUser(array $user): array
    {
        unset($user['password']);
        $user['id'] = (int)($user['id'] ?? 0);
        return $user;
    }
}
