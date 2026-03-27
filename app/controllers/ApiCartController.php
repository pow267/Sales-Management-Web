<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../helpers/ValidationException.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/CartService.php';

class ApiCartController
{
    private AuthService $authService;
    private CartService $cartService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->cartService = new CartService();
    }

    public function index(): void
    {
        $this->ensureAuthenticated();
        ApiResponse::success($this->cartService->getSummary());
    }

    public function storeItem(): void
    {
        $this->ensureAuthenticated();

        $payload = ApiResponse::input();
        $this->assertCsrf($payload);

        try {
            $summary = $this->cartService->add(
                (int)($payload['product_id'] ?? 0),
                (int)($payload['quantity'] ?? 1)
            );

            $_SESSION['flash'] = 'Đã thêm vào giỏ hàng';

            ApiResponse::success([
                'cart' => $summary,
                'redirect' => '/cart'
            ], 'Đã thêm vào giỏ hàng.');
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

    public function destroyItem(int $productId): void
    {
        $this->ensureAuthenticated();

        $payload = ApiResponse::input();
        $this->assertCsrf($payload);

        try {
            $summary = $this->cartService->remove($productId);
            $_SESSION['flash'] = 'Đã xóa sản phẩm khỏi giỏ';

            ApiResponse::success([
                'cart' => $summary,
                'redirect' => '/cart'
            ], 'Đã xóa sản phẩm khỏi giỏ.');
        } catch (OutOfBoundsException $e) {
            ApiResponse::error($e->getMessage(), 404);
        }
    }

    private function ensureAuthenticated(): void
    {
        if (empty($_SESSION['user'])) {
            ApiResponse::error('Bạn cần đăng nhập để sử dụng API.', 401);
        }
    }

    private function assertCsrf(array $payload): void
    {
        if (!$this->authService->validateCsrf($payload['csrf_token'] ?? null)) {
            ApiResponse::error('Invalid CSRF token.', 403);
        }
    }
}
