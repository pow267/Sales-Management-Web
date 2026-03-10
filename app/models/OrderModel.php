<?php

require_once __DIR__ . '/../../config/Database.php';

class OrderModel
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function createOrder(int $userId, int $total): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO orders (user_id, total_amount)
            VALUES (:user_id, :total)
            RETURNING id
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':total' => $total
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function createOrderItem(
        int $orderId,
        int $productId,
        int $qty,
        int $price,
        int $subtotal
    ): void {
        $stmt = $this->conn->prepare("
            INSERT INTO order_items
            (order_id, product_id, quantity, price, subtotal)
            VALUES (:order_id, :product_id, :qty, :price, :subtotal)
        ");

        $stmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':qty' => $qty,
            ':price' => $price,
            ':subtotal' => $subtotal
        ]);
    }

    public function begin(): void
    {
        $this->conn->beginTransaction();
    }

    public function commit(): void
    {
        $this->conn->commit();
    }

    public function rollback(): void
    {
        $this->conn->rollBack();
    }
}