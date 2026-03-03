<?php
session_start();
require_once 'config.php';
require_once 'models/Course.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'instructor' && $_SESSION['role'] !== 'admin')) {
    header('Location: index.php');
    exit;
}

$courseModel = new Course($pdo);
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error = "Invalid CSRF token";
    } else {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        if ($courseModel->create($title, $description, $_SESSION['user_id'], $category)) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Failed to create course";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Course</h2>
        <?php if (isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</body>
</html>