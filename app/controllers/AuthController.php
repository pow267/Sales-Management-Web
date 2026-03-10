<?php

require_once __DIR__ . '/../models/AuthModel.php';

class AuthController
{
    private AuthModel $model;

    public function __construct()
    {
        $this->model = new AuthModel();
    }

    public function showLogin(): void
    {
        require __DIR__ . '/../views/login.php';
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $user = $this->model->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = "Sai tên đăng nhập hoặc mật khẩu.";
            header("Location: /login");
            exit;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        header("Location: /");
        exit;
    }

    public function showRegister(): void
    {
        require __DIR__ . '/../views/register.php';
    }

    public function register(): void
    {
        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? '')
        ];

        if ($this->model->findByUsername($data['username'])) {
            $_SESSION['error'] = "Tên đăng nhập đã tồn tại.";
            header("Location: /register");
            exit;
        }

        if (strlen($data['password']) < 6) {
            $_SESSION['error'] = "Mật khẩu phải có ít nhất 6 ký tự.";
            header("Location: /register");
            exit;
        }

        $this->model->createUser($data);

        $_SESSION['flash'] = "Đăng ký thành công. Vui lòng đăng nhập.";
        header("Location: /login");
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: /login");
        exit;
    }
}