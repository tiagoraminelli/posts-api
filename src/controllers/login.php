<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');

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

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['email'], $input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos']);
    exit;
}

// Buscar el usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$input['email']]);
$user = $stmt->fetch();

if (!$user || !password_verify($input['password'], $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales incorrectas']);
    exit;
}

echo json_encode([
    'success' => true,
    'usuario' => [
        'id' => $user['id'],
        'nombre' => $user['nombre'],
        'email' => $user['email'],
        'rol' => $user['rol'],
        'api_key' => $user['api_key'] // Se devuelve para usar en el header
    ]
]);

// Actualizar el último acceso del usuario
$stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
$stmt->execute([$user['id']]);
exit;