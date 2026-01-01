<?php
// create_admin.php - Helper script to create admin with hashed password
// Run this once to create an admin account with properly hashed password
// Usage: http://localhost/work/create_admin.php?name=Admin&email=admin@example.com&password=admin123

require_once '../config.php';

if (!$pdo) {
    die("Database connection failed. Please check config.php");
}

// Get parameters
$full_name = $_GET['name'] ?? 'Admin User';
$email = $_GET['email'] ?? '';
$password = $_GET['password'] ?? '';

if (empty($email) || empty($password)) {
    die("Usage: create_admin.php?name=Admin Name&email=admin@example.com&password=yourpassword");
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM admin WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE admin SET full_name = :full_name, password = :password WHERE email = :email");
        $stmt->execute([
            'full_name' => $full_name,
            'password' => $hashedPassword,
            'email' => $email
        ]);
        echo "Admin updated successfully!<br>";
        echo "Email: $email<br>";
        echo "Password: (hashed)<br>";
        echo "You can now login with this account.";
    } else {
        // Insert new admin
        $stmt = $pdo->prepare("INSERT INTO admin (full_name, email, password) VALUES (:full_name, :email, :password)");
        $stmt->execute([
            'full_name' => $full_name,
            'email' => $email,
            'password' => $hashedPassword
        ]);
        echo "Admin created successfully!<br>";
        echo "Email: $email<br>";
        echo "Password: (hashed)<br>";
        echo "You can now login with this account.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

