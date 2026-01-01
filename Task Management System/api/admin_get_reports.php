<?php
// admin_get_reports.php - Admin only - Get reports
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $period = $_GET['period'] ?? 'daily'; // daily, weekly, monthly
    
    // Calculate date range based on period
    $now = new DateTime();
    $startDate = clone $now;
    
    switch ($period) {
        case 'weekly':
            $startDate->modify('-7 days');
            break;
        case 'monthly':
            $startDate->modify('-30 days');
            break;
        case 'daily':
        default:
            $startDate->modify('-1 day');
            break;
    }
    
    $startDateStr = $startDate->format('Y-m-d 00:00:00');
    $endDateStr = $now->format('Y-m-d 23:59:59');
    
    // Overall statistics
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalTasks = $pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
    $completedTasks = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'completed'")->fetchColumn();
    $pendingTasks = $totalTasks - $completedTasks;
    
    // Overdue tasks
    $overdueStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM tasks 
        WHERE status != 'completed' 
        AND due_date IS NOT NULL 
        AND due_date < NOW()
    ");
    $overdueStmt->execute();
    $overdueTasks = $overdueStmt->fetchColumn();
    
    // Tasks in period
    $periodTasksStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM tasks 
        WHERE created_at BETWEEN :start_date AND :end_date
    ");
    $periodTasksStmt->execute(['start_date' => $startDateStr, 'end_date' => $endDateStr]);
    $periodTasks = $periodTasksStmt->fetchColumn();
    
    // Completed tasks in period
    $periodCompletedStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM tasks 
        WHERE status = 'completed' 
        AND completed_at BETWEEN :start_date AND :end_date
    ");
    $periodCompletedStmt->execute(['start_date' => $startDateStr, 'end_date' => $endDateStr]);
    $periodCompleted = $periodCompletedStmt->fetchColumn();
    
    // Tasks by priority
    $priorityStmt = $pdo->query("
        SELECT priority, COUNT(*) as count 
        FROM tasks 
        GROUP BY priority
    ");
    $tasksByPriority = $priorityStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Tasks by status
    $statusStmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM tasks 
        GROUP BY status
    ");
    $tasksByStatus = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // User activity (users with most tasks)
    $userActivityStmt = $pdo->query("
        SELECT u.id, u.full_name, u.email, COUNT(t.id) as task_count,
               SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_count
        FROM users u
        LEFT JOIN tasks t ON u.id = t.user_id
        GROUP BY u.id, u.full_name, u.email
        ORDER BY task_count DESC
        LIMIT 10
    ");
    $userActivity = $userActivityStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent activity
    $recentStmt = $pdo->query("
        SELECT t.*, u.full_name as user_name, u.email as user_email
        FROM tasks t
        LEFT JOIN users u ON t.user_id = u.id
        ORDER BY t.created_at DESC
        LIMIT 20
    ");
    $recentActivity = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'period' => $period,
        'statistics' => [
            'total_users' => (int)$totalUsers,
            'total_tasks' => (int)$totalTasks,
            'completed_tasks' => (int)$completedTasks,
            'pending_tasks' => (int)$pendingTasks,
            'overdue_tasks' => (int)$overdueTasks,
            'period_tasks' => (int)$periodTasks,
            'period_completed' => (int)$periodCompleted
        ],
        'tasks_by_priority' => $tasksByPriority,
        'tasks_by_status' => $tasksByStatus,
        'user_activity' => $userActivity,
        'recent_activity' => $recentActivity
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

