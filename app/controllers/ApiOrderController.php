<?php

require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/CartService.php';
require_once __DIR__ . '/../services/OrderService.php';

class ApiOrderController
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

    public function index(): void
    {
        $this->ensureAuthenticated();

        $userId = $this->isAdmin()
            ? null
            : (int)$_SESSION['user']['id'];

        ApiResponse::success([
            'orders' => $this->orderService->listOrders($userId)
        ]);
    }

    public function show(int $id): void
    {
        $this->ensureAuthenticated();

        $userId = $this->isAdmin()
            ? null
            : (int)$_SESSION['user']['id'];

        $order = $this->orderService->getOrderDetails($id, $userId);

        if (!$order) {
            ApiResponse::error('Đơn hàng không tồn tại.', 404);
        }

        ApiResponse::success(['order' => $order]);
    }

    public function store(): void
    {
        $this->ensureAuthenticated();

        $payload = ApiResponse::input();

        if (!$this->authService->validateCsrf($payload['csrf_token'] ?? null)) {
            ApiResponse::error('Invalid CSRF token.', 403);
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
            ], 'Đặt hàng thành công.', 201);
        } catch (OutOfBoundsException $e) {
            ApiResponse::error($e->getMessage(), 404);
        } catch (InvalidArgumentException $e) {
            ApiResponse::error($e->getMessage(), 422);
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

    private function isAdmin(): bool
    {
        return (($_SESSION['user']['role'] ?? 'user') === 'admin');
    }
}
