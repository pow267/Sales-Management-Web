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

    public function getAll(?int $userId = null): array
    {
        $sql = "
            SELECT
                o.id,
                o.user_id,
                o.total_amount,
                o.status,
                o.created_at,
                u.username,
                u.full_name
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
        ";

        if ($userId !== null) {
            $sql .= " WHERE o.user_id = :user_id";
        }

        $sql .= " ORDER BY o.created_at DESC, o.id DESC";

        $stmt = $this->conn->prepare($sql);

        if ($userId !== null) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $orderId, ?int $userId = null): ?array
    {
        $sql = "
            SELECT
                o.id,
                o.user_id,
                o.total_amount,
                o.status,
                o.created_at,
                u.username,
                u.full_name
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            WHERE o.id = :order_id
        ";

        if ($userId !== null) {
            $sql .= " AND o.user_id = :user_id";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);

        if ($userId !== null) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getItems(int $orderId): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                oi.id,
                oi.order_id,
                oi.product_id,
                oi.quantity,
                oi.price,
                oi.subtotal,
                p.ten_sua,
                p.hinh
            FROM order_items oi
            INNER JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id
        ");

        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
