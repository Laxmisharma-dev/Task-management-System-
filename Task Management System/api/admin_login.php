<?php
// admin_login.php
header('Content-Type: application/json');

// Disable all error output to prevent JSON breakage
ini_set('display_errors', 0);
error_reporting(0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

function cleanOutput() {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}

function logDebug($message) {
    file_put_contents('../debug_log.txt', date('Y-m-d H:i:s') . " - ADMIN LOGIN: " . $message . "\n", FILE_APPEND);
}

logDebug("Admin login script started");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("This is an API endpoint. Please use the <a href='../index.html'>Admin Login Page</a>.");
}

try {
    require_once '../config.php';
    logDebug("Config loaded");

    if (!isset($pdo) || !$pdo) {
        throw new Exception("Database connection failed (PDO not set)");
    }

    $input = file_get_contents('php://input');
    logDebug("Input received: " . $input);

    $data = json_decode($input, true);
    if ($data === null) {
        throw new Exception("Invalid or missing JSON data.");
    }

    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($email) || empty($password)) {
        cleanOutput();
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id, full_name, password FROM admin WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    logDebug("Admin query result: " . ($admin ? "Found admin ID: " . $admin['id'] : "No admin found"));
    
    if (!$admin) {
        logDebug("Admin not found with email: " . $email);
        cleanOutput();
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }

    // Check if password is hashed (starts with $2y$ or $2a$ or $2b$) or plain text
    $passwordHash = $admin['password'];
    $isHashed = (substr($passwordHash, 0, 4) === '$2y$' || substr($passwordHash, 0, 4) === '$2a$' || substr($passwordHash, 0, 4) === '$2b$');
    
    logDebug("Password is hashed: " . ($isHashed ? "Yes" : "No"));
    
    $passwordValid = false;
    if ($isHashed) {
        // Password is hashed, use password_verify
        $passwordValid = password_verify($password, $passwordHash);
        logDebug("Password verification result: " . ($passwordValid ? "Valid" : "Invalid"));
    } else {
        // Password is plain text, compare directly (for manually inserted passwords)
        $passwordValid = ($password === $passwordHash);
        logDebug("Plain text password comparison result: " . ($passwordValid ? "Valid" : "Invalid"));
        
        // If plain text password matches, hash it and update the database
        if ($passwordValid) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE admin SET password = :password WHERE id = :id");
            $updateStmt->execute(['password' => $hashedPassword, 'id' => $admin['id']]);
            logDebug("Password hashed and updated in database for admin ID: " . $admin['id']);
        }
    }

    if ($passwordValid) {
        // Login successful
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['user_type'] = 'admin';
        logDebug("Admin login successful for: " . $email);
        
        cleanOutput();
        echo json_encode(['success' => true, 'redirect' => 'admin_dashboard.html']);
    } else {
        logDebug("Admin login failed for: " . $email . " - Password mismatch");
        cleanOutput();
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }

} catch (PDOException $e) {
    logDebug("PDO Error: " . $e->getMessage());
    cleanOutput();
    echo json_encode(['success' => false, 'message' => 'Database error.']);
} catch (Exception $e) {
    logDebug("General Error: " . $e->getMessage());
    cleanOutput();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

