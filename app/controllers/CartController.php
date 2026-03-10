<?php

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CartController
{
    private ProductModel $productModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        AuthMiddleware::check();

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->productModel = new ProductModel();
    }

    /* ================= THÊM SẢN PHẨM ================= */
    public function add(): void
    {
        $id  = (int)($_GET['id'] ?? 0);
        $qty = (int)($_GET['qty'] ?? 1);

        if ($id <= 0) {
            header("Location: /");
            exit;
        }

        if ($qty <= 0) {
            $qty = 1;
        }

        $product = $this->productModel->getById($id);

        if (!$product) {
            header("Location: /");
            exit;
        }

        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = $qty;
        } else {
            $_SESSION['cart'][$id] += $qty;
        }

        $_SESSION['flash'] = "Đã thêm vào giỏ hàng";

        header("Location: /cart");
        exit;
    }

    /* ================= XÓA SẢN PHẨM ================= */
    public function remove(): void
    {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            exit('Invalid CSRF token');
        }

        $id = (int)($_POST['id'] ?? 0);

        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        $_SESSION['flash'] = "Đã xóa sản phẩm khỏi giỏ";

        header("Location: /cart");
        exit;
    }

    /* ================= HIỂN THỊ GIỎ ================= */
    public function index(): void
    {
        $items = [];
        $total = 0;

        foreach ($_SESSION['cart'] as $id => $qty) {

            $product = $this->productModel->getById($id);

            if ($product) {

                $product['quantity'] = $qty;
                $product['subtotal'] = $qty * $product['don_gia'];

                $total += $product['subtotal'];
                $items[] = $product;
            }
        }

        require __DIR__ . '/../views/cart.php';
    }
}