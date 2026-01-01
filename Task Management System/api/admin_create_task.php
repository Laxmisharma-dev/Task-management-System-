<?php
// admin_create_task.php - Admin only - Create and assign task to user
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
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $priority = $data['priority'] ?? 'medium';
    $category = $data['category'] ?? 'personal';
    $due_date = $data['due_date'] ?? null;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        exit;
    }
    
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Title is required']);
        exit;
    }
    
    // Verify user exists
    $userCheck = $pdo->prepare("SELECT id FROM users WHERE id = :user_id");
    $userCheck->execute(['user_id' => $user_id]);
    if (!$userCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, category, due_date) VALUES (:user_id, :title, :description, :priority, :category, :due_date)");
    $stmt->execute([
        'user_id' => $user_id,
        'title' => $title,
        'description' => $description,
        'priority' => $priority,
        'category' => $category,
        'due_date' => $due_date
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Task assigned successfully', 'task_id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

