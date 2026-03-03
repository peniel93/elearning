<?php
session_start();
require_once 'config.php';
require_once 'models/User.php';
require_once 'models/Course.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$userModel = new User($pdo);
$courseModel = new Course($pdo);

// Enrollment stats report
$stmt = $pdo->query("SELECT c.title, COUNT(e.id) AS enrollments FROM courses c LEFT JOIN enrollments e ON c.id = e.course_id GROUP BY c.id");
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">E-Learning</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <h1>Admin Panel</h1>
        <h2>Enrollment Reports</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Course Title</th>
                    <th>Enrollments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['title']); ?></td>
                        <td><?php echo $report['enrollments']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>