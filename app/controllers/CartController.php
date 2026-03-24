<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CartController
{
    public function index(): void
    {
        AuthMiddleware::check();
        require __DIR__ . '/../views/cart.php';
    }
}
