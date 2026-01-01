<?php
// get_all_tasks_admin.php - Admin only - Get all tasks from all users
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->query("
        SELECT t.*, u.full_name as user_name, u.email as user_email 
        FROM tasks t 
        LEFT JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC
    ");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'tasks' => $tasks]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

