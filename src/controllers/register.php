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
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato JSON inválido']);
    exit;
}


if (!isset($input['nombre'], $input['email'], $input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos requeridos']);
    exit;
}


// Verificamos si el email ya está registrado
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$input['email']]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'El email ya está registrado']);
    exit;
}

// Crear usuario
$apiKey = bin2hex(random_bytes(32));
$hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, api_key) VALUES (?, ?, ?, ?)");
$stmt->execute([$input['nombre'], $input['email'], $hashedPassword, $apiKey]);

echo json_encode(['success' => true, 'api_key' => $apiKey]);
