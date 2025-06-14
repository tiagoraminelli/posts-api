<?php
declare(strict_types=1);

// Iniciar sesión al principio
session_start();

// Cargar dependencias
require_once __DIR__ . '/../src/model/Post.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/db.php';

// Configuración CORS segura
$allowedDomains = [
    'http://localhost',
    'http://localhost:8080',
    'https://tudominio.com'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? null;
if ($origin && in_array($origin, $allowedDomains)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: *');
}

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Vary: Origin');

// Manejo de CORS para solicitudes preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Autenticación
try {
    \App\Middlewares\Auth::check();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
// Importar clases necesarias
use App\Utils\Validator;
use App\Utils\Response;

// Conexión PDO
$pdo = new PDO(
    "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
    $config['username'],
    $config['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

// Instancia del Modelo
$postModel = new App\Models\Post($pdo);

// Manejador de excepciones global
set_exception_handler(function (Throwable $e) {
    error_log('API Error: ' . $e->getMessage());
    http_response_code($e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
});

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    // Validación del método HTTP
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'])) {
        throw new RuntimeException('Método no permitido', 405);
    }

    // Enrutamiento
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $post = $postModel->getById((int)$_GET['id']);
                if (!$post) {
                    throw new RuntimeException('Post no encontrado', 404);
                }
                Response::success($post);
            } else {
                $posts = $postModel->getAll();
                Response::success($posts);
            }
            break;
            
        case 'POST':
            Validator::validate($input, ['title', 'content', 'status']);
            $id = $postModel->create($input);
            Response::created(['id' => $id, 'message' => 'Post creado exitosamente']);
            break;

        case 'PUT':
            Validator::validate($input, ['id', 'title', 'content', 'status']);
            $postModel->update($input);
            Response::success(['message' => 'Post actualizado exitosamente']);
            break;

        case 'DELETE':
            Validator::validate($input, ['id']);
            $postModel->delete($input['id']);
            Response::success(['message' => 'Post eliminado exitosamente']);
            break;
    }
} finally {
    $pdo = null;
}
