<?php

class ApiResponse
{
    public static function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }

    public static function success(
        array $data = [],
        string $message = 'OK',
        int $status = 200
    ): void {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(
        string $message,
        int $status = 400,
        array $errors = []
    ): void {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public static function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');

            if ($raw === false || trim($raw) === '') {
                return [];
            }

            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        if (in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['PUT', 'PATCH', 'DELETE'], true)) {
            $raw = file_get_contents('php://input');
            $parsed = [];

            if ($raw !== false) {
                parse_str($raw, $parsed);
            }

            if (!empty($parsed)) {
                return $parsed;
            }
        }

        return $_POST;
    }
}
