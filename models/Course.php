<?php
require_once '../config.php';

class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($title, $description, $instructor_id, $category) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (title, description, instructor_id, category) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $description, $instructor_id, $category]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT c.*, u.username AS instructor FROM courses c JOIN users u ON c.instructor_id = u.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update, delete methods similar...
}
?>