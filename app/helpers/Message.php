<?php

class Message
{
    public static function get(string $key): string
    {
        static $messages = null;

        if ($messages === null) {
            $messages = require __DIR__ . '/../../config/messages.php';
        }

        return $messages[$key] ?? 'Thông báo không xác định.';
    }
}