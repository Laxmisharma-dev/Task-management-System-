<?php
// admin_update_task.php - Admin only - Update any task
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
    
    $task_id = $data['task_id'] ?? null;
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $priority = $data['priority'] ?? 'medium';
    $category = $data['category'] ?? 'personal';
    $status = $data['status'] ?? 'pending';
    $due_date = $data['due_date'] ?? null;
    $user_id = $data['user_id'] ?? null;
    
    if (!$task_id) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        exit;
    }
    
    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Title is required']);
        exit;
    }
    
    $updateFields = [];
    $params = ['task_id' => $task_id];
    
    if ($title) {
        $updateFields[] = "title = :title";
        $params['title'] = $title;
    }
    if ($description !== null) {
        $updateFields[] = "description = :description";
        $params['description'] = $description;
    }
    if ($priority) {
        $updateFields[] = "priority = :priority";
        $params['priority'] = $priority;
    }
    if ($category) {
        $updateFields[] = "category = :category";
        $params['category'] = $category;
    }
    if ($status) {
        $updateFields[] = "status = :status";
        $params['status'] = $status;
        if ($status === 'completed' && !isset($data['completed_at'])) {
            $updateFields[] = "completed_at = NOW()";
        } elseif ($status === 'pending') {
            $updateFields[] = "completed_at = NULL";
        }
    }
    if ($due_date) {
        $updateFields[] = "due_date = :due_date";
        $params['due_date'] = $due_date;
    }
    if ($user_id) {
        $updateFields[] = "user_id = :user_id";
        $params['user_id'] = $user_id;
    }
    
    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit;
    }
    
    $sql = "UPDATE tasks SET " . implode(', ', $updateFields) . " WHERE id = :task_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

