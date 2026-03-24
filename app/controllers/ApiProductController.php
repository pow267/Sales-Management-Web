<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ProductService.php';

class ApiProductController
{
    private AuthService $authService;
    private ProductService $productService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->productService = new ProductService();
    }

    public function index(): void
    {
        $this->ensureAuthenticated();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, (int)($_GET['per_page'] ?? 9));
        $search = trim((string)($_GET['search'] ?? ''));

        ApiResponse::success(
            $this->productService->list($page, $perPage, $search)
        );
    }

    public function show(int $id): void
    {
        $this->ensureAuthenticated();

        $product = $this->productService->getById($id);

        if (!$product) {
            ApiResponse::error('Sản phẩm không tồn tại.', 404);
        }

        ApiResponse::success(['product' => $product]);
    }

    public function brands(): void
    {
        $this->ensureAuthenticated();

        ApiResponse::success([
            'brands' => $this->productService->getBrands()
        ]);
    }

    public function store(): void
    {
        $this->ensureAdmin();
        $page = max(1, (int)($_POST['page'] ?? 1));
        $redirect = '/?action=them&page=' . $page;
        $this->assertCsrf($_POST, $redirect);

        try {
            $product = $this->productService->create($_POST, $_FILES);
            $_SESSION['flash'] = 'Thêm sản phẩm thành công!';
            $this->respondSuccess([
                'product' => $product,
                'redirect' => '/?id=' . $product['id'] . '&page=' . $page . '#chitiet'
            ], 'Thêm sản phẩm thành công.', 201);
        } catch (InvalidArgumentException $e) {
            $this->respondError($e->getMessage(), 422, $redirect);
        } catch (RuntimeException $e) {
            $this->respondError($e->getMessage(), 400, $redirect);
        }
    }

    public function update(int $id): void
    {
        $this->ensureAdmin();

        $payload = $_POST;
        $payload['id'] = $id;
        $page = max(1, (int)($payload['page'] ?? 1));
        $redirect = '/?id=' . $id . '&page=' . $page . '&action=sua#formsua';
        $this->assertCsrf($payload, $redirect);

        try {
            $product = $this->productService->update($id, $payload, $_FILES);
            $_SESSION['flash'] = 'Cập nhật thành công!';
            $this->respondSuccess([
                'product' => $product,
                'redirect' => '/?id=' . $product['id'] . '&page=' . $page . '#chitiet'
            ], 'Cập nhật thành công.');
        } catch (InvalidArgumentException $e) {
            $this->respondError($e->getMessage(), 422, $redirect);
        } catch (RuntimeException $e) {
            $this->respondError($e->getMessage(), 400, $redirect);
        }
    }

    public function destroy(int $id): void
    {
        $this->ensureAdmin();

        $payload = ApiResponse::input();
        $page = max(1, (int)($payload['page'] ?? 1));
        $redirect = '/?id=' . $id . '&page=' . $page . '#chitiet';
        $this->assertCsrf($payload, $redirect);

        try {
            $this->productService->delete($id);
            $_SESSION['flash'] = 'Xóa sản phẩm thành công!';

            $this->respondSuccess([
                'redirect' => '/?page=' . $page
            ], 'Xóa sản phẩm thành công.');
        } catch (RuntimeException $e) {
            $this->respondError($e->getMessage(), 404, $redirect);
        }
    }

    private function ensureAuthenticated(): void
    {
        if (empty($_SESSION['user'])) {
            ApiResponse::error('Bạn cần đăng nhập để sử dụng API.', 401);
        }
    }

    private function ensureAdmin(): void
    {
        if (empty($_SESSION['user'])) {
            ApiResponse::error('Bạn cần đăng nhập để sử dụng API.', 401);
        }

        if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            ApiResponse::error('Bạn không có quyền thực hiện thao tác này.', 403);
        }
    }

    private function assertCsrf(array $payload, string $redirect): void
    {
        if (!$this->authService->validateCsrf($payload['csrf_token'] ?? null)) {
            $this->respondError('Invalid CSRF token.', 419, $redirect);
        }
    }

    private function respondSuccess(array $data, string $message, int $status = 200): void
    {
        if ($this->expectsJson()) {
            ApiResponse::success($data, $message, $status);
        }

        $this->redirectTo((string)($data['redirect'] ?? '/'));
    }

    private function respondError(string $message, int $status, string $redirect): void
    {
        if ($this->expectsJson()) {
            ApiResponse::error($message, $status);
        }

        $_SESSION['flash'] = $message;
        $this->redirectTo($redirect);
    }

    private function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

        return stripos($accept, 'application/json') !== false
            || $requestedWith === 'xmlhttprequest';
    }

    private function redirectTo(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
