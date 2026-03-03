<?php
// === CHANGE THESE VALUES TO MATCH YOUR INFINITYFREE DATABASE DETAILS ===
// You can find them in Control Panel → MySQL Databases

define('DB_HOST',     'sql301.infinityfree.com');          // ← Replace sqlXXX.epizy.com with your actual hostname
define('DB_USER',     'if0_41295569');              // ← Your MySQL username (epiz_ or if0_)
define('DB_PASS',     'MzK0xWTcmX'); // ← The password for this MySQL user
define('DB_NAME',     'if0_41295569_elearning_db');   // ← Full database name

// === DO NOT CHANGE BELOW THIS LINE ===

try {
    // Basic PDO connection (most reliable for InfinityFree)
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    
    // Important settings for better error reporting and compatibility
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: If you get connection timeout issues, you can try adding these (rarely needed)
    // $pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);
    
} catch (PDOException $e) {
    // Show detailed error only during development (remove or hide on production)
    die("Database connection failed: " . $e->getMessage() . 
        "<br><br><small>Common fixes:<br>" .
        "1. Check hostname (sqlXXX.epizy.com)<br>" .
        "2. Verify username starts with epiz_ or if0_<br>" .
        "3. Password is correct (check/show in control panel)<br>" .
        "4. Database name includes prefix (epiz_XXXXXXXX_...)<br>" .
        "5. Make sure database user has ALL PRIVILEGES on this database</small>");
}
?>