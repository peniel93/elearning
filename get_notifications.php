<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';
require_once 'models/Notification.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$notificationModel = new Notification($pdo);
$notifications = $notificationModel->getUnread($_SESSION['user_id']);
echo json_encode($notifications);
?>