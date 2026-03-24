<?php

require_once __DIR__ . '/../models/AuthModel.php';

class AuthService
{
    private AuthModel $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    public function ensureCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public function validateCsrf(?string $token): bool
    {
        return !empty($_SESSION['csrf_token'])
            && !empty($token)
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function getSessionData(): array
    {
        return [
            'authenticated' => !empty($_SESSION['user']),
            'user' => $_SESSION['user'] ?? null,
            'csrf_token' => $this->ensureCsrfToken()
        ];
    }

    public function attemptLogin(string $username, string $password): array
    {
        $username = trim($username);
        $password = trim($password);

        if ($username === '' || $password === '') {
            throw new RuntimeException('Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.');
        }

        $user = $this->authModel->findByUsername($username);

        if (!$user) {
            throw new RuntimeException('Sai tên đăng nhập hoặc mật khẩu.');
        }

        if (!password_verify($password, $user['password'])) {
            throw new RuntimeException('Sai tên đăng nhập hoặc mật khẩu.');
        }

        $_SESSION['user'] = [
            'id'       => (int)$user['id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        $this->ensureCsrfToken();

        return $_SESSION['user'];
    }

    public function register(array $data): void
    {
        $payload = [
            'full_name' => trim((string)($data['full_name'] ?? '')),
            'username' => trim((string)($data['username'] ?? '')),
            'password' => (string)($data['password'] ?? ''),
            'email' => trim((string)($data['email'] ?? '')),
            'phone' => trim((string)($data['phone'] ?? '')),
            'address' => trim((string)($data['address'] ?? ''))
        ];

        foreach (['full_name', 'username', 'password', 'email', 'phone', 'address'] as $field) {
            if ($payload[$field] === '') {
                throw new RuntimeException('Vui lòng nhập đầy đủ thông tin đăng ký.');
            }
        }

        if ($this->authModel->findByUsername($payload['username'])) {
            throw new RuntimeException('Tên đăng nhập đã tồn tại.');
        }

        if (strlen($payload['password']) < 6) {
            throw new RuntimeException('Mật khẩu phải có ít nhất 6 ký tự.');
        }

        if (!$this->authModel->createUser($payload)) {
            throw new RuntimeException('Không thể tạo tài khoản mới.');
        }
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
