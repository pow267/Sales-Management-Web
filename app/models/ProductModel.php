<?php

require_once __DIR__ . '/../../config/Database.php';

class ProductModel
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function countAll(string $search = ''): int
    {
        $search = trim($search);

        if ($search === '') {
            return (int)$this->conn->query("SELECT COUNT(*) FROM products")
                ->fetchColumn();
        }

        $stmt = $this->conn->prepare("
            SELECT COUNT(*)
            FROM products p
            LEFT JOIN hang_sua h ON p.ma_hang_sua = h.ma_hs
            WHERE p.ten_sua ILIKE :search
               OR COALESCE(p.loai_sua, '') ILIKE :search
               OR COALESCE(h.ten_hs, '') ILIKE :search
        ");

        $stmt->execute([
            'search' => '%' . $search . '%'
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function getPaginated(int $limit, int $offset, string $search = ''): array
    {
        $sql = "
            SELECT p.*, h.ten_hs
            FROM products p
            LEFT JOIN hang_sua h ON p.ma_hang_sua = h.ma_hs
        ";

        if (trim($search) !== '') {
            $sql .= "
                WHERE p.ten_sua ILIKE :search
                   OR COALESCE(p.loai_sua, '') ILIKE :search
                   OR COALESCE(h.ten_hs, '') ILIKE :search
            ";
        }

        $sql .= "
            ORDER BY p.id
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        if (trim($search) !== '') {
            $stmt->bindValue(':search', '%' . trim($search) . '%', PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT p.*, h.ten_hs
            FROM products p
            LEFT JOIN hang_sua h ON p.ma_hang_sua = h.ma_hs
            WHERE p.id = :id
        ");

        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAllHangSua(): array
    {
        return $this->conn->query("
            SELECT ma_hs, ten_hs
            FROM hang_sua
            ORDER BY ten_hs
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(array $data): int
    {
        $stmt = $this->conn->prepare("
            INSERT INTO products
            (ten_sua, ma_hang_sua, loai_sua, trong_luong, don_gia, tpdd, loi_ich, hinh)
            VALUES
            (:ten_sua, :ma_hang_sua, :loai_sua, :trong_luong, :don_gia, :tpdd, :loi_ich, :hinh)
        ");

        $stmt->execute($data);

        return (int)$this->conn->lastInsertId();
    }

    public function update(array $data): void
    {
        $stmt = $this->conn->prepare("
            UPDATE products SET
                ten_sua = :ten_sua,
                ma_hang_sua = :ma_hang_sua,
                loai_sua = :loai_sua,
                trong_luong = :trong_luong,
                don_gia = :don_gia,
                tpdd = :tpdd,
                loi_ich = :loi_ich,
                hinh = :hinh
            WHERE id = :id
        ");

        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->conn->prepare("
            DELETE FROM products WHERE id = :id
        ");

        $stmt->execute(['id' => $id]);
    }
}
