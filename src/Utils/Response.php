<?php
namespace App\Utils;

class Response {
    public static function success(array $data = [], int $code = 200): void {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        exit;
    }

    public static function created(array $data = []): void {
        self::success($data, 201);
    }

    public static function error(string $message, int $code = 400): void {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'code' => $code
        ]);
        exit;
    }
}