<?php

require_once __DIR__ . '/../../config/Database.php';

class AuthModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM users WHERE username = :username
        ");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function createUser(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (
                full_name,
                username,
                password,
                email,
                phone,
                address,
                role
            )
            VALUES (
                :full_name,
                :username,
                :password,
                :email,
                :phone,
                :address,
                'user'
            )
        ");

        return $stmt->execute([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address']
        ]);
    }
}