<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../config/db.php';

session_start();

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['email'], $input['password'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Faltan campos']);
    exit;
}

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

// Buscar usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$input['email']]);
$user = $stmt->fetch();

if (!$user || !password_verify($input['password'], $user['password'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Credenciales incorrectas']);
    exit;
}

// Guardar API Key en sesión
$_SESSION['api_key'] = $user['api_key'];
$_SESSION['usuario'] = [
    'id' => $user['id'],
    'nombre' => $user['nombre'],
    'rol' => $user['rol']
];

// Actualizar último acceso
$stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
$stmt->execute([$user['id']]);

// Redireccionar
header('Location: /posts_api/public/index.php');
exit;