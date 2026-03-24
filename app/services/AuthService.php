<?php

require_once __DIR__ . '/../models/AuthModel.php';

class AuthService
{
    private const CSRF_COOKIE_NAME = 'XSRF-TOKEN';

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

        $this->syncCsrfCookie($_SESSION['csrf_token']);
        return $_SESSION['csrf_token'];
    }

    public function regenerateCsrfToken(): string
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $this->syncCsrfCookie($_SESSION['csrf_token']);

        return $_SESSION['csrf_token'];
    }

    public function validateCsrf(?string $token): bool
    {
        $token = $this->resolveCsrfToken($token);

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

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'       => (int)$user['id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        $this->regenerateCsrfToken();

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

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'] ?: '',
                'secure' => (bool)$params['secure'],
                'httponly' => (bool)$params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax'
            ]);
        }

        setcookie(self::CSRF_COOKIE_NAME, '', [
            'expires' => time() - 42000,
            'path' => '/',
            'secure' => $this->isSecureRequest(),
            'httponly' => false,
            'samesite' => 'Lax'
        ]);

        session_destroy();
    }

    private function resolveCsrfToken(?string $token): ?string
    {
        $candidates = [
            $token,
            $_POST['csrf_token'] ?? null,
            $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null,
            $_SERVER['HTTP_X_XSRF_TOKEN'] ?? null
        ];

        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }

            $candidate = trim($candidate);

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    private function syncCsrfCookie(string $token): void
    {
        setcookie(self::CSRF_COOKIE_NAME, $token, [
            'expires' => 0,
            'path' => '/',
            'secure' => $this->isSecureRequest(),
            'httponly' => false,
            'samesite' => 'Lax'
        ]);
    }

    private function isSecureRequest(): bool
    {
        return !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
    }
}
