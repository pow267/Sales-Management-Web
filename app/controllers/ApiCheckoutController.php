<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/CartService.php';
require_once __DIR__ . '/../services/OrderService.php';

class ApiCheckoutController
{
    private AuthService $authService;
    private CartService $cartService;
    private OrderService $orderService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->cartService = new CartService();
        $this->orderService = new OrderService();
    }

    public function store(): void
    {
        if (empty($_SESSION['user'])) {
            ApiResponse::error('Bạn cần đăng nhập để sử dụng API.', 401);
        }

        $payload = ApiResponse::input();

        if (!$this->authService->validateCsrf($payload['csrf_token'] ?? null)) {
            ApiResponse::error('Invalid CSRF token.', 419);
        }

        try {
            $order = $this->orderService->createOrder(
                (int)$_SESSION['user']['id'],
                $_SESSION['cart'] ?? []
            );

            $this->cartService->clear();
            $_SESSION['flash'] = 'Đặt hàng thành công!';

            ApiResponse::success([
                'order' => $order,
                'redirect' => '/'
            ], 'Đặt hàng thành công.');
        } catch (Exception $e) {
            ApiResponse::error($e->getMessage(), 400);
        }
    }
}
