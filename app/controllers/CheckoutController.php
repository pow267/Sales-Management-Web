<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CheckoutController
{
    public function index(): void
    {
        AuthMiddleware::check();

        if (empty($_SESSION['cart'])) {
            $_SESSION['flash'] = "Giỏ hàng trống.";
            header("Location: /cart");
            exit;
        }

        require __DIR__ . '/../views/checkout.php';
    }
}