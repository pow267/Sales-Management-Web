<?php

require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class OrderService
{
    private OrderModel $orderModel;
    private ProductModel $productModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }

    public function createOrder(int $userId, array $cart): array
    {
        if (empty($cart)) {
            throw new Exception("Giỏ hàng trống.");
        }

        $this->orderModel->begin();

        try {

            $total = 0;
            $itemsData = [];

            foreach ($cart as $productId => $qty) {

                $product = $this->productModel->getById($productId);

                if (!$product) {
                    throw new Exception("Sản phẩm không tồn tại.");
                }

                $price = (int)$product['don_gia'];
                $subtotal = $price * $qty;

                $total += $subtotal;

                $itemsData[] = [
                    'product_id' => $productId,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
            }

            $orderId = $this->orderModel->createOrder($userId, $total);

            foreach ($itemsData as $item) {
                $this->orderModel->createOrderItem(
                    $orderId,
                    $item['product_id'],
                    $item['qty'],
                    $item['price'],
                    $item['subtotal']
                );
            }

            $this->orderModel->commit();

            return [
                'id' => $orderId,
                'total' => $total,
                'items' => $itemsData
            ];

        } catch (Exception $e) {
            $this->orderModel->rollback();
            throw $e;
        }
    }

    public function listOrders(?int $userId = null): array
    {
        $orders = $this->orderModel->getAll($userId);

        return array_map(
            fn(array $order): array => $this->normalizeOrder($order),
            $orders
        );
    }

    public function getOrderDetails(int $orderId, ?int $userId = null): ?array
    {
        $order = $this->orderModel->getById($orderId, $userId);

        if (!$order) {
            return null;
        }

        $items = $this->orderModel->getItems($orderId);
        $order = $this->normalizeOrder($order);
        $order['items'] = array_map(
            fn(array $item): array => $this->normalizeOrderItem($item),
            $items
        );

        return $order;
    }

    private function normalizeOrder(array $order): array
    {
        $order['id'] = (int)($order['id'] ?? 0);
        $order['user_id'] = (int)($order['user_id'] ?? 0);
        $order['total_amount'] = (int)($order['total_amount'] ?? 0);

        return $order;
    }

    private function normalizeOrderItem(array $item): array
    {
        $item['id'] = (int)($item['id'] ?? 0);
        $item['order_id'] = (int)($item['order_id'] ?? 0);
        $item['product_id'] = (int)($item['product_id'] ?? 0);
        $item['quantity'] = (int)($item['quantity'] ?? 0);
        $item['price'] = (int)($item['price'] ?? 0);
        $item['subtotal'] = (int)($item['subtotal'] ?? 0);

        return $item;
    }
}
