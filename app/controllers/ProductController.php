<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class ProductController
{
    public function index(): void
    {
        AuthMiddleware::check();
        require __DIR__ . '/../views/list.php';
    }
}
