<?php

class Database
{
    private static $pdo = null;

    public static function getConnection()
    {
        if (self::$pdo === null) {

            $databaseUrl = getenv('DATABASE_URL');

            if (!$databaseUrl) {
                error_log("DATABASE_URL not found");
                die("Không thể kết nối cơ sở dữ liệu.");
            }

            $db = parse_url($databaseUrl);

            if ($db === false) {
                error_log("Invalid DATABASE_URL format");
                die("Cấu hình cơ sở dữ liệu không hợp lệ.");
            }

            $host = $db['host'] ?? 'localhost';
            $port = $db['port'] ?? 5432;
            $user = $db['user'] ?? '';
            $pass = $db['pass'] ?? '';
            $dbname = isset($db['path']) ? ltrim($db['path'], '/') : '';

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

            try {
                self::$pdo = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                error_log("DB Connection Error: " . $e->getMessage());
                die("Không thể kết nối cơ sở dữ liệu.");
            }
        }

        return self::$pdo;
    }
}