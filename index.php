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

$query = $_GET['query'] ?? '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5; // Items per page
$offset = ($page - 1) * $limit;

$courses = $courseModel->search($query, $limit, $offset);
$total_courses = $courseModel->getCount($query);
$total_pages = ceil($total_courses / $limit);
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
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin Panel</a></li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-toggle="dropdown">Notifications</a>
                    <div class="dropdown-menu" id="notificationsMenu"></div>
                </li>
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
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="query" class="form-control" placeholder="Search courses..." value="<?php echo htmlspecialchars($query); ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </div>
            </div>
        </form>
        <ul class="list-group">
            <?php foreach ($courses as $course): ?>
                <li class="list-group-item">
                    <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                    <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-info">View</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination mt-3">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&query=<?php echo urlencode($query); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function loadNotifications() {
            $.get('get_notifications.php', function(data) {
                $('#notificationsMenu').empty();
                if (data.length === 0) {
                    $('#notificationsMenu').append('<a class="dropdown-item">No new notifications</a>');
                } else {
                    data.forEach(function(notif) {
                        $('#notificationsMenu').append('<a class="dropdown-item">' + notif.message + '</a>');
                    });
                }
            });
        }
        loadNotifications();
        setInterval(loadNotifications, 10000); // Poll every 10 seconds
    </script>
</body>
</html>