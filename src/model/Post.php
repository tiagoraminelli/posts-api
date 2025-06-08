<?php
namespace App\Models;

class Post {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM posts");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO posts (title, content, status) VALUES (?, ?, ?)"
        );
        $stmt->execute([$data['title'], $data['content'], $data['status']]);
        return $this->pdo->lastInsertId();
    }

public function update(array $data): void {
    // Verificar si el post existe
    $stmt = $this->pdo->prepare('SELECT id FROM posts WHERE id = ?');
    $stmt->execute([$data['id']]);
    
    if ($stmt->rowCount() === 0) {
        throw new \RuntimeException('Post no encontrado', 404);
    }

    $sql = 'UPDATE posts SET title = ?, content = ?, status = ? WHERE id = ?';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        htmlspecialchars($data['title']),
        htmlspecialchars($data['content']),
        $data['status'],
        $data['id']
    ]);
}

public function delete(int $id): void {
    // Verificar si el post existe
    $stmt = $this->pdo->prepare('SELECT id FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        throw new \RuntimeException('Post no encontrado', 404);
    }

    $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
}


    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }


    public function getByStatus(string $status): array {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function search(string $query): array {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE title LIKE ? OR content LIKE ?");
        $stmt->execute(['%' . $query . '%', '%' . $query . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function countByStatus(string $status): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE status = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }


    public function getRecent(int $limit = 5): array {
        $stmt = $this->pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
