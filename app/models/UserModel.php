<?php

require_once __DIR__ . '/../../config/Database.php';

class UserModel
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE username = :username LIMIT 1"
        );

        $stmt->execute(['username' => $username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO users (full_name, username, password, email, role)
             VALUES (:full_name, :username, :password, :email, :role)"
        );

        return $stmt->execute([
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'password'  => $data['password'],
            'email'     => $data['email'],
            'role'      => $data['role'] ?? 'user'
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->conn->query(
            "SELECT id, full_name, username, email, role, created_at
             FROM users
             ORDER BY created_at DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}