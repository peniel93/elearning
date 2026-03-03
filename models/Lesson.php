<?php
require_once '../config.php';

class Lesson {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($course_id, $title, $content, $media_path = null) {
        $stmt = $this->pdo->prepare("INSERT INTO lessons (course_id, title, content, media_path) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$course_id, $title, $content, $media_path]);
    }

    public function getByCourseId($course_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM lessons WHERE course_id = ?");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>