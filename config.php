<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Default XAMPP user
define('DB_PASS', '');      // Default XAMPP password (empty)
define('DB_NAME', 'elearning_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>