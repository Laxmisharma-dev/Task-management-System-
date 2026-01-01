<?php
// admin_delete_user.php - Admin only - Delete user
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }
    
    $user_id = $data['user_id'] ?? null;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        exit;
    }
    
    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['admin_id']) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
        exit;
    }
    
    // Check if user exists
    $userCheck = $pdo->prepare("SELECT id, email FROM users WHERE id = :user_id");
    $userCheck->execute(['user_id' => $user_id]);
    $user = $userCheck->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Delete user (tasks will be deleted automatically due to CASCADE)
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

