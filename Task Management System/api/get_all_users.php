<?php
// get_all_users.php - Admin only
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, full_name, email, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get task counts for each user
    foreach ($users as &$user) {
        $taskStmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed FROM tasks WHERE user_id = :user_id");
        $taskStmt->execute(['user_id' => $user['id']]);
        $taskCount = $taskStmt->fetch(PDO::FETCH_ASSOC);
        $user['task_count'] = (int)$taskCount['total'];
        $user['completed_count'] = (int)$taskCount['completed'];
        $user['pending_count'] = $user['task_count'] - $user['completed_count'];
    }
    
    echo json_encode(['success' => true, 'users' => $users]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

