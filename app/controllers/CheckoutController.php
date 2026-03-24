<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CheckoutController
{
    public function index(): void
    {
        AuthMiddleware::check();
        require __DIR__ . '/../views/checkout.php';
    }
}
