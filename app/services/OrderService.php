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

    public function createOrder(int $userId, array $cart): void
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

        } catch (Exception $e) {
            $this->orderModel->rollback();
            throw $e;
        }
    }
}