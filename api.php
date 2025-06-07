<?php
require 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$methods = ['GET', 'POST', 'PUT', 'DELETE'];

$input = json_decode(file_get_contents('php://input'), true);

match ($method) {
    'GET' => handleGet($pdo, $input),
    'POST' => handlePost($pdo, $input),
    'PUT' => handlePut($pdo, $input),
    'DELETE' => handleDelete($pdo, $input),
    default => http_response_code(405)
};

function handleGet($pdo, $input) {
    $sql = 'SELECT * FROM posts';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($posts);
}

function handlePost($pdo, $input) {
    $sql = 'INSERT INTO posts (title, content, status) VALUES (:title, :content, :status)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' => $input['title'],
        'content' => $input['content'],
        'status' => $input['status']
    ]);
    echo json_encode(['message' => 'Post creado exitosamente', 'id' => $pdo->lastInsertId()]);
}

function handlePut($pdo, $input) {
    $sql = 'UPDATE posts SET title = :title, content = :content, status = :status WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $input['id'],
        'title' => $input['title'],
        'content' => $input['content'],
        'status' => $input['status']

    ]);
    echo json_encode(['message' => 'Post actualizado exitosamente']);
}

function handleDelete($pdo, $input) {
    $sql = 'DELETE FROM posts WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $input['id']]);
    echo json_encode(['message' => 'Post eliminado exitosamente']);
}
// Close the database connection
$pdo = null;