<?php
// setup_database.php
// Check if PDO MySQL is enabled
if (!extension_loaded('pdo_mysql')) {
    die("<div style='font-family: sans-serif; padding: 20px; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'><h2>Setup Error</h2><p>The <b>pdo_mysql</b> extension is not enabled in your PHP configuration. Please enable it in XAMPP (php.ini).</p></div>");
}

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS task_m CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE task_m");

    // Create/Update users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Add missing columns to users
    $userCols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('full_name', $userCols)) $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(255) AFTER id");
    if (!in_array('phone', $userCols)) $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email");
    if (!in_array('bio', $userCols)) $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT AFTER phone");

    // Create/Update tasks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Add missing columns to tasks
    $taskCols = $pdo->query("SHOW COLUMNS FROM tasks")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('priority', $taskCols)) $pdo->exec("ALTER TABLE tasks ADD COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER description");
    if (!in_array('category', $taskCols)) $pdo->exec("ALTER TABLE tasks ADD COLUMN category ENUM('work', 'personal', 'study') DEFAULT 'personal' AFTER priority");
    if (!in_array('due_date', $taskCols)) $pdo->exec("ALTER TABLE tasks ADD COLUMN due_date DATETIME AFTER category");
    if (!in_array('status', $taskCols)) $pdo->exec("ALTER TABLE tasks ADD COLUMN status ENUM('pending', 'completed') DEFAULT 'pending' AFTER due_date");
    if (!in_array('completed_at', $taskCols)) $pdo->exec("ALTER TABLE tasks ADD COLUMN completed_at DATETIME AFTER status");

    echo "<div style='font-family: sans-serif; padding: 20px; color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h2>Success!</h2>";
    echo "<p>Database and tables have been set up successfully.</p>";
    echo "<p><a href='../index.html' style='color: #155724; font-weight: bold;'>Go to Login</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='font-family: sans-serif; padding: 20px; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h2>Setup Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
