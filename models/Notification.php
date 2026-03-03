<?php
require_once __DIR__ . '/../config.php';

class Notification {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($user_id, $message) {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        return $stmt->execute([$user_id, $message]);
    }

    public function getUnread($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND read_status = FALSE ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>