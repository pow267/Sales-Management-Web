<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../helpers/ValidationException.php';
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
        $payload = ApiResponse::input();
        $this->assertCsrf($payload);
        $page = max(1, (int)($payload['page'] ?? 1));

        try {
            $product = $this->productService->create($payload, ApiResponse::files());
            $_SESSION['flash'] = 'Thêm sản phẩm thành công!';

            ApiResponse::success([
                'product' => $product,
                'redirect' => '/?id=' . $product['id'] . '&page=' . $page . '#chitiet'
            ], 'Thêm sản phẩm thành công.', 201);
        } catch (ValidationException $e) {
            ApiResponse::error(
                $e->getMessage(),
                $e->getCode() >= 400 ? $e->getCode() : 422,
                $e->getErrors()
            );
        } catch (InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        } catch (RuntimeException $e) {
            ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function update(int $id): void
    {
        $this->ensureAdmin();

        $payload = ApiResponse::input();
        $payload['id'] = $id;
        $this->assertCsrf($payload);
        $page = max(1, (int)($payload['page'] ?? 1));

        try {
            $product = $this->productService->update($id, $payload, ApiResponse::files());
            $_SESSION['flash'] = 'Cập nhật thành công!';

            ApiResponse::success([
                'product' => $product,
                'redirect' => '/?id=' . $product['id'] . '&page=' . $page . '#chitiet'
            ], 'Cập nhật thành công.');
        } catch (OutOfBoundsException $e) {
            ApiResponse::error($e->getMessage(), 404);
        } catch (ValidationException $e) {
            ApiResponse::error(
                $e->getMessage(),
                $e->getCode() >= 400 ? $e->getCode() : 422,
                $e->getErrors()
            );
        } catch (InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
        } catch (RuntimeException $e) {
            ApiResponse::error($e->getMessage(), 500);
        }
    }

    public function destroy(int $id): void
    {
        $this->ensureAdmin();

        $payload = ApiResponse::input();
        $this->assertCsrf($payload);
        $page = max(1, (int)($payload['page'] ?? 1));

        try {
            $this->productService->delete($id);
            $_SESSION['flash'] = 'Xóa sản phẩm thành công!';

            ApiResponse::success([
                'redirect' => '/?page=' . $page
            ], 'Xóa sản phẩm thành công.');
        } catch (OutOfBoundsException $e) {
            ApiResponse::error($e->getMessage(), 404);
        } catch (RuntimeException $e) {
            ApiResponse::error($e->getMessage(), 500);
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

    private function assertCsrf(array $payload): void
    {
        if (!$this->authService->validateCsrf($payload['csrf_token'] ?? null)) {
            ApiResponse::error('Invalid CSRF token.', 403);
        }
    }
}
