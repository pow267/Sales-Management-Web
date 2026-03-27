<?php

session_start();

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';
require_once __DIR__ . '/../app/controllers/ApiAuthController.php';
require_once __DIR__ . '/../app/controllers/ApiProductController.php';
require_once __DIR__ . '/../app/controllers/ApiCartController.php';
require_once __DIR__ . '/../app/controllers/ApiUserController.php';
require_once __DIR__ . '/../app/controllers/ApiOrderController.php';

function apiJson(array $payload, int $status, array $headers = []): void
{
    http_response_code($status);

    foreach ($headers as $name => $value) {
        header($name . ': ' . $value);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function apiNotFound(): void
{
    apiJson([
        'success' => false,
        'message' => 'API endpoint không tồn tại.'
    ], 404);
}

function apiMethodNotAllowed(array $allowedMethods): void
{
    $allowedMethods = array_values(array_unique($allowedMethods));
    sort($allowedMethods);

    apiJson([
        'success' => false,
        'message' => 'Method không được hỗ trợ cho endpoint này.',
        'allowed_methods' => $allowedMethods
    ], 405, [
        'Allow' => implode(', ', $allowedMethods)
    ]);
}

function routeApi(string $uri, string $method): void
{
    $exactRoutes = [
        '/api/session' => [
            'GET' => fn() => (new ApiAuthController())->session(),
            'POST' => fn() => (new ApiAuthController())->storeSession(),
            'DELETE' => fn() => (new ApiAuthController())->destroySession()
        ],
        '/api/brands' => [
            'GET' => fn() => (new ApiProductController())->brands()
        ],
        '/api/products' => [
            'GET' => fn() => (new ApiProductController())->index(),
            'POST' => fn() => (new ApiProductController())->store()
        ],
        '/api/cart' => [
            'GET' => fn() => (new ApiCartController())->index()
        ],
        '/api/cart/items' => [
            'POST' => fn() => (new ApiCartController())->storeItem()
        ],
        '/api/users' => [
            'GET' => fn() => (new ApiUserController())->index(),
            'POST' => fn() => (new ApiUserController())->store()
        ],
        '/api/orders' => [
            'GET' => fn() => (new ApiOrderController())->index(),
            'POST' => fn() => (new ApiOrderController())->store()
        ]
    ];

    if (isset($exactRoutes[$uri])) {
        if (isset($exactRoutes[$uri][$method])) {
            $exactRoutes[$uri][$method]();
            return;
        }

        apiMethodNotAllowed(array_keys($exactRoutes[$uri]));
    }

    if (preg_match('#^/api/products/(\d+)$#', $uri, $matches)) {
        $id = (int)$matches[1];
        $routes = [
            'GET' => fn() => (new ApiProductController())->show($id),
            'PUT' => fn() => (new ApiProductController())->update($id),
            'PATCH' => fn() => (new ApiProductController())->update($id),
            'DELETE' => fn() => (new ApiProductController())->destroy($id)
        ];

        if (isset($routes[$method])) {
            $routes[$method]();
            return;
        }

        apiMethodNotAllowed(array_keys($routes));
    }

    if (preg_match('#^/api/cart/items/(\d+)$#', $uri, $matches)) {
        $productId = (int)$matches[1];
        $routes = [
            'DELETE' => fn() => (new ApiCartController())->destroyItem($productId)
        ];

        if (isset($routes[$method])) {
            $routes[$method]();
            return;
        }

        apiMethodNotAllowed(array_keys($routes));
    }

    if (preg_match('#^/api/users/(\d+)$#', $uri, $matches)) {
        $id = (int)$matches[1];
        $routes = [
            'GET' => fn() => (new ApiUserController())->show($id)
        ];

        if (isset($routes[$method])) {
            $routes[$method]();
            return;
        }

        apiMethodNotAllowed(array_keys($routes));
    }

    if (preg_match('#^/api/orders/(\d+)$#', $uri, $matches)) {
        $id = (int)$matches[1];
        $routes = [
            'GET' => fn() => (new ApiOrderController())->show($id)
        ];

        if (isset($routes[$method])) {
            $routes[$method]();
            return;
        }

        apiMethodNotAllowed(array_keys($routes));
    }

    apiNotFound();
}

function methodNotAllowed(array $allowedMethods): void
{
    http_response_code(405);
    header('Allow: ' . implode(', ', $allowedMethods));
    echo '405 - Method Not Allowed.';
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
$uri = $uri === '' ? '/' : $uri;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $methodOverride = $_POST['_method']
        ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']
        ?? null;

    if (is_string($methodOverride) && $methodOverride !== '') {
        $method = strtoupper(trim($methodOverride));
    }
}

if (str_starts_with($uri, '/api/')) {
    routeApi($uri, $method);
    exit;
}

switch ($uri) {
    case '/login':
        if ($method !== 'GET') {
            methodNotAllowed(['GET']);
        }

        (new AuthController())->showLogin();
        break;

    case '/register':
        if ($method !== 'GET') {
            methodNotAllowed(['GET']);
        }

        (new AuthController())->showRegister();
        break;

    case '/logout':
        methodNotAllowed(['POST']);
        break;

    case '/cart':
        if ($method !== 'GET') {
            methodNotAllowed(['GET']);
        }

        (new CartController())->index();
        break;

    case '/checkout':
        if ($method !== 'GET') {
            methodNotAllowed(['GET']);
        }

        (new CheckoutController())->index();
        break;

    case '/':
        if ($method !== 'GET') {
            methodNotAllowed(['GET']);
        }

        (new ProductController())->index();
        break;

    default:
        http_response_code(404);
        echo "404 - Trang không tồn tại.";
        break;
}
