<?php
// update_task_status.php
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

    $task_id = $data['id'] ?? $data['task_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$task_id) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        exit;
    }

    // Handle deletion
    if (isset($data['action']) && $data['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $task_id, 'user_id' => $user_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found or access denied']);
        }
        exit;
    }

    // Handle task updates
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $priority = $data['priority'] ?? 'medium';
    $status = $data['status'] ?? 'pending';
    $due_date = $data['due_date'] ?? null;
    $reminder = $data['reminder'] ?? null;

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Task title is required']);
        exit;
    }

    // Validate priority
    if (!in_array($priority, ['low', 'medium', 'high'])) {
        $priority = 'medium';
    }

    // Validate status
    if (!in_array($status, ['pending', 'completed'])) {
        $status = 'pending';
    }

    // Set completed_at timestamp
    $completed_at = ($status === 'completed') ? date('Y-m-d H:i:s') : null;

    // Convert datetime-local to MySQL format
    if ($due_date) {
        $due_date = date('Y-m-d H:i:s', strtotime($due_date));
    }

    if ($reminder) {
        $reminder = date('Y-m-d H:i:s', strtotime($reminder));
    }

    $stmt = $pdo->prepare("UPDATE tasks SET
        title = :title,
        description = :description,
        priority = :priority,
        status = :status,
        due_date = :due_date,
        reminder = :reminder,
        completed_at = :completed_at
        WHERE id = :id AND user_id = :user_id");

    $stmt->execute([
        'title' => $title,
        'description' => $description,
        'priority' => $priority,
        'status' => $status,
        'due_date' => $due_date,
        'reminder' => $reminder,
        'completed_at' => $completed_at,
        'id' => $task_id,
        'user_id' => $user_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Task not found or access denied']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
