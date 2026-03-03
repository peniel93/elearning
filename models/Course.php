<?php
require_once __DIR__ . '/../config.php';

class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($title, $description, $instructor_id, $category) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (title, description, instructor_id, category) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $description, $instructor_id, $category]);
    }

    public function search($query = '', $limit = 10, $offset = 0) {
        $sql = "SELECT c.*, u.username AS instructor FROM courses c JOIN users u ON c.instructor_id = u.id";
        if ($query) {
            $sql .= " WHERE c.title LIKE :query OR c.description LIKE :query";
        }
        $sql .= " LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        if ($query) {
            $stmt->bindValue(':query', '%' . $query . '%');
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCount($query = '') {
        $sql = "SELECT COUNT(*) FROM courses";
        if ($query) {
            $sql .= " WHERE title LIKE :query OR description LIKE :query";
        }
        $stmt = $this->pdo->prepare($sql);
        if ($query) {
            $stmt->bindValue(':query', '%' . $query . '%');
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>