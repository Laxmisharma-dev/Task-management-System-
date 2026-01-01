<?php
// register.php
header('Content-Type: application/json');

// Disable all error output to prevent JSON breakage
ini_set('display_errors', 0);
error_reporting(0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';

// If config.php couldn't connect because DB doesn't exist, we might need to handle that here
// But setup_database.php should be run first.

try {
    if (!$pdo) {
        throw new Exception("Database connection failed. Please run setup_database.php.");
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        throw new Exception("Invalid or missing JSON data in request.");
    }
    
    $full_name = trim($data['full_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($full_name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        exit;
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)");
    $stmt->execute([
        'full_name' => $full_name,
        'email' => $email, 
        'password' => $hashedPassword
    ]);

    // Don't set session - user needs to login first
    echo json_encode(['success' => true, 'message' => 'Account created successfully. Please login to continue.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
