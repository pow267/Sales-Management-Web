<?php

require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class OrderController
{
    private OrderService $service;

    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        AuthMiddleware::check();
        $this->service = new OrderService();
    }

    public function checkout(): void
    {
        if (empty($_SESSION['cart'])) {
            $_SESSION['flash'] = "Giỏ hàng trống.";
            header("Location: /");
            exit;
        }

        require __DIR__ . '/../views/checkout.php';
    }

    public function store(): void
    {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            exit('Invalid CSRF token');
        }

        try {

            $this->service->createOrder(
                $_SESSION['user']['id'],
                $_SESSION['cart']
            );

            $_SESSION['cart'] = [];
            $_SESSION['flash'] = "Đặt hàng thành công!";

            header("Location: /");
            exit;

        } catch (Exception $e) {

            $_SESSION['flash'] = $e->getMessage();
            header("Location: /cart");
            exit;
        }
    }
}