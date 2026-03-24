<?php

require_once __DIR__ . '/../models/ProductModel.php';

class CartService
{
    private ProductModel $productModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->productModel = new ProductModel();
    }

    public function getSummary(): array
    {
        $items = [];
        $total = 0;
        $totalQuantity = 0;

        foreach ($_SESSION['cart'] as $id => $qty) {
            $product = $this->productModel->getById((int)$id);

            if (!$product) {
                continue;
            }

            $price = (int)$product['don_gia'];
            $subtotal = $price * $qty;
            $image = trim($product['hinh'] ?? '');
            $imagePath = __DIR__ . '/../../public/assets/images/' . $image;

            if ($image === '' || !file_exists($imagePath)) {
                $image = 'default.png';
            }

            $items[] = [
                'id' => (int)$product['id'],
                'ten_sua' => $product['ten_sua'],
                'don_gia' => $price,
                'quantity' => (int)$qty,
                'subtotal' => $subtotal,
                'hinh' => $image,
                'image_url' => '/assets/images/' . rawurlencode($image)
            ];

            $total += $subtotal;
            $totalQuantity += $qty;
        }

        return [
            'items' => $items,
            'total' => $total,
            'total_quantity' => $totalQuantity
        ];
    }

    public function add(int $productId, int $quantity = 1): array
    {
        if ($productId <= 0) {
            throw new RuntimeException('Mã sản phẩm không hợp lệ.');
        }

        if ($quantity <= 0) {
            $quantity = 1;
        }

        $product = $this->productModel->getById($productId);

        if (!$product) {
            throw new RuntimeException('Sản phẩm không tồn tại.');
        }

        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = 0;
        }

        $_SESSION['cart'][$productId] += $quantity;

        return $this->getSummary();
    }

    public function remove(int $productId): array
    {
        unset($_SESSION['cart'][$productId]);
        return $this->getSummary();
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }
}
