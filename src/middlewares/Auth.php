<?php
// config/auth.php
// src/Middlewares/Auth.php
namespace App\Middlewares;

use PDO;

class Auth {
    public static function check(): void {
        $config = require __DIR__ . '/../../config/auth.php';
        if (!$config['require_auth']) return;

        if (!isset($_SERVER['HTTP_API_KEY'])) {
            http_response_code(401);
            echo json_encode(['error' => 'API Key requerida']);
            exit;
        }

        $apiKey = $_SERVER['HTTP_API_KEY'];
        $pdo = require __DIR__ . '/../../config/db.php';

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE api_key = :key LIMIT 1");
        $stmt->execute(['key' => $apiKey]);
        $user = $stmt->fetch();

        if (!$user) {
            http_response_code(403);
            echo json_encode(['error' => 'API Key inválida']);
            exit;
        }

        // Opcional: guardar en $_SESSION o definir constantes de usuario
        $_SERVER['usuario_actual'] = $user;
    }
}
