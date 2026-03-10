<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class UserController
{
    private UserModel $userModel;

    public function __construct()
    {
        AuthMiddleware::check();
        RoleMiddleware::admin();

        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $users = $this->userModel->getAll();

        require __DIR__ . '/../views/users/list.php';
    }

    public function showProfile(): void
    {
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);

        require __DIR__ . '/../views/users/profile.php';
    }
}