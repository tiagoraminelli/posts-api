<?php
declare(strict_types=1);

require 'db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejo de CORS para solicitudes preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

set_exception_handler(function (Throwable $e) {
    error_log('API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error interno en el servidor']);
});

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    // Validación básica del método HTTP
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'])) {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        exit;
    }

    // Enrutamiento
    switch ($method) {
        case 'GET':
            handleGet($pdo);
            break;
        case 'POST':
            validateInput($input, ['title', 'content', 'status']);
            handlePost($pdo, $input);
            break;
        case 'PUT':
            validateInput($input, ['id', 'title', 'content', 'status']);
            handlePut($pdo, $input);
            break;
        case 'DELETE':
            validateInput($input, ['id']);
            handleDelete($pdo, $input);
            break;
    }

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos']);
} finally {
    $pdo = null;
}

/**
 * Valida que los campos requeridos estén presentes en el input
 */
function validateInput(array $input, array $requiredFields): void {
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            throw new InvalidArgumentException("El campo '$field' es requerido");
        }
    }
}

function handleGet(PDO $pdo): void {
    try {
        $sql = 'SELECT id, title, content, status, created_at FROM posts';
        $stmt = $pdo->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $posts
        ]);
    } catch (PDOException $e) {
        throw new PDOException("Error al obtener los posts");
    }
}

function handlePost(PDO $pdo, array $input): void {
    try {
        $sql = 'INSERT INTO posts (title, content, status) VALUES (:title, :content, :status)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title' => htmlspecialchars($input['title']),
            'content' => htmlspecialchars($input['content']),
            'status' => in_array($input['status'], ['draft', 'published']) ? $input['status'] : 'draft'
        ]);
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Post creado exitosamente',
            'id' => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        throw new PDOException("Error al crear el post");
    }
}

function handlePut(PDO $pdo, array $input): void {
    try {
        // Verificar si el post existe
        $checkStmt = $pdo->prepare('SELECT id FROM posts WHERE id = :id');
        $checkStmt->execute(['id' => $input['id']]);
        
        if ($checkStmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Post no encontrado']);
            return;
        }

        $sql = 'UPDATE posts SET title = :title, content = :content, status = :status WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $input['id'],
            'title' => htmlspecialchars($input['title']),
            'content' => htmlspecialchars($input['content']),
            'status' => in_array($input['status'], ['draft', 'published']) ? $input['status'] : 'draft'
        ]);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Post actualizado exitosamente'
        ]);
    } catch (PDOException $e) {
        throw new PDOException("Error al actualizar el post");
    }
}

function handleDelete(PDO $pdo, array $input): void {
    try {
        // Verificar si el post existe
        $checkStmt = $pdo->prepare('SELECT id FROM posts WHERE id = :id');
        $checkStmt->execute(['id' => $input['id']]);
        
        if ($checkStmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Post no encontrado']);
            return;
        }

        $sql = 'DELETE FROM posts WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $input['id']]);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Post eliminado exitosamente'
        ]);
    } catch (PDOException $e) {
        throw new PDOException("Error al eliminar el post");
    }
}