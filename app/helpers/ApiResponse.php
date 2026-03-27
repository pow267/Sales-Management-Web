<?php

class ApiResponse
{
    private static array $parsedFiles = [];

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
        self::$parsedFiles = [];

        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');

            if ($raw === false || trim($raw) === '') {
                return [];
            }

            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        if (
            in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['PUT', 'PATCH', 'DELETE'], true)
            && stripos($contentType, 'multipart/form-data') !== false
        ) {
            return self::parseMultipartInput($contentType);
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

    public static function files(): array
    {
        return !empty(self::$parsedFiles) ? self::$parsedFiles : $_FILES;
    }

    private static function parseMultipartInput(string $contentType): array
    {
        if (!preg_match('/boundary="?([^";]+)"?/i', $contentType, $matches)) {
            return [];
        }

        $boundary = $matches[1];
        $raw = file_get_contents('php://input');

        if ($raw === false || $raw === '') {
            return [];
        }

        $fields = [];
        $parts = explode('--' . $boundary, $raw);

        foreach ($parts as $part) {
            $part = ltrim($part, "\r\n");

            if ($part === '' || $part === '--' || $part === "--\r\n") {
                continue;
            }

            [$rawHeaders, $body] = array_pad(explode("\r\n\r\n", $part, 2), 2, null);

            if ($rawHeaders === null || $body === null) {
                continue;
            }

            $body = preg_replace("/\r\n$/", '', $body);
            $headers = self::parseMultipartHeaders($rawHeaders);
            $disposition = $headers['content-disposition'] ?? '';

            if (!preg_match('/name="([^"]+)"/i', $disposition, $nameMatches)) {
                continue;
            }

            $fieldName = $nameMatches[1];

            if (preg_match('/filename="([^"]*)"/i', $disposition, $fileMatches)) {
                $fileName = $fileMatches[1];

                if ($fileName === '') {
                    continue;
                }

                $tmpFile = tempnam(sys_get_temp_dir(), 'api_upload_');

                if ($tmpFile === false) {
                    throw new RuntimeException('Không thể tạo file tạm để xử lý upload.');
                }

                file_put_contents($tmpFile, $body);
                register_shutdown_function(static function () use ($tmpFile): void {
                    if (is_file($tmpFile)) {
                        @unlink($tmpFile);
                    }
                });

                self::$parsedFiles[$fieldName] = [
                    'name' => $fileName,
                    'type' => $headers['content-type'] ?? 'application/octet-stream',
                    'tmp_name' => $tmpFile,
                    'error' => UPLOAD_ERR_OK,
                    'size' => strlen($body)
                ];
                continue;
            }

            $fields[$fieldName] = $body;
        }

        return $fields;
    }

    private static function parseMultipartHeaders(string $rawHeaders): array
    {
        $headers = [];

        foreach (explode("\r\n", $rawHeaders) as $headerLine) {
            if (!str_contains($headerLine, ':')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode(':', $headerLine, 2));
            $headers[strtolower($name)] = $value;
        }

        return $headers;
    }
}
