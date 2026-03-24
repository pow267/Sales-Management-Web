<?php

session_start();

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';
require_once __DIR__ . '/../app/controllers/ApiAuthController.php';
require_once __DIR__ . '/../app/controllers/ApiProductController.php';
require_once __DIR__ . '/../app/controllers/ApiCartController.php';
require_once __DIR__ . '/../app/controllers/ApiCheckoutController.php';
require_once __DIR__ . '/../app/controllers/ApiUserController.php';
require_once __DIR__ . '/../app/controllers/ApiOrderController.php';

function routeApi(string $uri, string $method): void
{
    if ($uri === '/api/auth/session' && $method === 'GET') {
        (new ApiAuthController())->session();
        return;
    }

    if ($uri === '/api/auth/login' && $method === 'POST') {
        (new ApiAuthController())->login();
        return;
    }

    if ($uri === '/api/auth/register' && $method === 'POST') {
        (new ApiAuthController())->register();
        return;
    }

    if ($uri === '/api/auth/logout' && $method === 'POST') {
        (new ApiAuthController())->logout();
        return;
    }

    if ($uri === '/api/brands' && $method === 'GET') {
        (new ApiProductController())->brands();
        return;
    }

    if ($uri === '/api/products' && $method === 'GET') {
        (new ApiProductController())->index();
        return;
    }

    if ($uri === '/api/products' && $method === 'POST') {
        (new ApiProductController())->store();
        return;
    }

    if (preg_match('#^/api/products/(\d+)$#', $uri, $matches)) {
        $id = (int)$matches[1];

        if ($method === 'GET') {
            (new ApiProductController())->show($id);
            return;
        }

        if ($method === 'POST') {
            (new ApiProductController())->update($id);
            return;
        }

        if ($method === 'DELETE') {
            (new ApiProductController())->destroy($id);
            return;
        }
    }

    if ($uri === '/api/cart' && $method === 'GET') {
        (new ApiCartController())->index();
        return;
    }

    if ($uri === '/api/cart/items' && $method === 'POST') {
        (new ApiCartController())->storeItem();
        return;
    }

    if (preg_match('#^/api/cart/items/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
        (new ApiCartController())->destroyItem((int)$matches[1]);
        return;
    }

    if ($uri === '/api/checkout' && $method === 'POST') {
        (new ApiCheckoutController())->store();
        return;
    }

    if ($uri === '/api/users' && $method === 'GET') {
        (new ApiUserController())->index();
        return;
    }

    if ($uri === '/api/users/me' && $method === 'GET') {
        (new ApiUserController())->me();
        return;
    }

    if (preg_match('#^/api/users/(\d+)$#', $uri, $matches) && $method === 'GET') {
        (new ApiUserController())->show((int)$matches[1]);
        return;
    }

    if ($uri === '/api/orders' && $method === 'GET') {
        (new ApiOrderController())->index();
        return;
    }

    if ($uri === '/api/orders' && $method === 'POST') {
        (new ApiOrderController())->store();
        return;
    }

    if (preg_match('#^/api/orders/(\d+)$#', $uri, $matches) && $method === 'GET') {
        (new ApiOrderController())->show((int)$matches[1]);
        return;
    }

    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'API endpoint không tồn tại.'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
