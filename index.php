<?php
session_start();
require_once 'config.php';
require_once 'models/User.php';
require_once 'models/Course.php';

$userModel = new User($pdo);
$courseModel = new Course($pdo);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = $userModel->getUserById($_SESSION['user_id']);
$courses = $courseModel->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Learning Platform</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">E-Learning</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?> (<?php echo $user['role']; ?>)</h1>
        
        <?php if ($user['role'] === 'instructor' || $user['role'] === 'admin'): ?>
            <a href="create_course.php" class="btn btn-primary">Create Course</a>
        <?php endif; ?>
        
        <h2>Available Courses</h2>
        <ul class="list-group">
            <?php foreach ($courses as $course): ?>
                <li class="list-group-item">
                    <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-info">View</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>