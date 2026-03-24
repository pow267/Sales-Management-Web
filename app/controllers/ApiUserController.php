<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../models/UserModel.php';

class ApiUserController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
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

    public function me(): void
    {
        $this->ensureAuthenticated();

        $user = $this->userModel->findById((int)$_SESSION['user']['id']);

        if (!$user) {
            ApiResponse::error('Người dùng không tồn tại.', 404);
        }

        ApiResponse::success([
            'user' => $this->sanitizeUser($user)
        ]);
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
