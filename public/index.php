<?php

session_start();

/* ================= LOAD CONTROLLERS ================= */
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';

/* ================= ROUTING ================= */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {

    /* ================= AUTH ================= */

    case '/login':
        $controller = new AuthController();
        $method === 'POST'
            ? $controller->login()
            : $controller->showLogin();
        break;

    case '/register':
        $controller = new AuthController();
        $method === 'POST'
            ? $controller->register()
            : $controller->showRegister();
        break;

    case '/logout':
        (new AuthController())->logout();
        break;

    /* ================= CART ================= */

    case '/cart':
        (new CartController())->index();
        break;

    case '/cart/add':
        (new CartController())->add();
        break;

    case '/cart/remove':
        (new CartController())->remove();
        break;

    /* ================= CHECKOUT ================= */

    case '/checkout':
        (new CheckoutController())->index();
        break;

    /* ================= PRODUCT ================= */

    case '/':
        (new ProductController())->index();
        break;

    /* ================= 404 ================= */

    default:
        http_response_code(404);
        echo "404 - Trang không tồn tại.";
        break;
}