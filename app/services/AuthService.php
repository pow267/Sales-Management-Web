<?php

require_once __DIR__ . '/../models/UserModel.php';

class AuthService
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function attemptLogin(string $username, string $password): bool
    {
        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        return true;
    }

    public function register(array $data): bool
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role'] = 'user';

        return $this->userModel->create($data);
    }

    public function logout(): void
    {
        session_destroy();
    }
}