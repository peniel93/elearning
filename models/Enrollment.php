<?php
require_once '../config.php';

class Enrollment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function enroll($user_id, $course_id) {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO enrollments (user_id, course_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $course_id]);
    }

    public function getProgress($user_id, $course_id) {
        $stmt = $this->pdo->prepare("SELECT progress FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        return $stmt->fetchColumn() ?? 0;
    }

    // Update progress method...
    public function updateProgress($user_id, $course_id, $progress) {
        $stmt = $this->pdo->prepare("UPDATE enrollments SET progress = ? WHERE user_id = ? AND course_id = ?");
        return $stmt->execute([$progress, $user_id, $course_id]);
    }
}
?>