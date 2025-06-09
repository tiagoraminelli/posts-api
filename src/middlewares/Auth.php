<?php
// config/auth.php
// src/Middlewares/Auth.php
namespace App\Middlewares;

use PDO;

class Auth
{
   public static function check(): void {
    $config = require __DIR__ . '/../../config/auth.php';
    
    // 1. Verificar si la autenticación está requerida
    if (!$config['require_auth']) return;

    // 2. Obtener API Key de (en orden de prioridad):
    $apiKey = null;
    
    // a) Sesión PHP
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['api_key'])) {
        $apiKey = $_SESSION['api_key'];
    }
    // b) Header Authorization
    elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $apiKey = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
    }
    // c) API Key directa (para compatibilidad)
    elseif (isset($_SERVER['HTTP_API_KEY'])) {
        $apiKey = $_SERVER['HTTP_API_KEY'];
    }

    if (!$apiKey) {
        http_response_code(401);
        echo json_encode(['error' => 'Autenticación requerida']);
        exit;
    }

    // Resto de tu lógica de verificación en BD...
}
}
