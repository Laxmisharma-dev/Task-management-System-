<?php
// update_profile.php
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing JSON data in request.']);
        exit;
    }
    $user_id = $_SESSION['user_id'];
    
    // Check if this is a password change request
    if (isset($data['current_password']) && isset($data['new_password'])) {
        // Handle password change
        $current_password = $data['current_password'];
        $new_password = $data['new_password'];

        if (empty($current_password) || empty($new_password)) {
            echo json_encode(['success' => false, 'message' => 'Current and new passwords are required.']);
            exit;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
            exit;
        }

        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current_password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $hashed_password, 'id' => $user_id]);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        exit;
    }

    // Handle profile update
    $full_name = $data['full_name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $bio = $data['bio'] ?? '';

    if (empty($full_name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and Email are required.']);
        exit;
    }

    // Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
    $stmt->execute(['email' => $email, 'id' => $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already in use by another account.']);
        exit;
    }

    // Update user profile
    $stmt = $pdo->prepare("UPDATE users SET full_name = :name, email = :email, phone = :phone, bio = :bio WHERE id = :id");
    $stmt->execute([
        'name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'bio' => $bio,
        'id' => $user_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
