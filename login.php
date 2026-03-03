<?php
session_start();
require_once 'config.php';
require_once 'models/User.php';

$csrf_token = generate_csrf_token();  // Generate CSRF token

$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $error = "Invalid CSRF token";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];
        if ($userModel->login($email, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid credentials";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <?php if (isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <a href="register.php">Register</a>
    </div>
</body>
</html>