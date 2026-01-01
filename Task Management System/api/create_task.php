<?php
// create_task.php
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
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $priority = $data['priority'] ?? 'medium';
    $status = $data['status'] ?? 'pending';
    $due_date = $data['due_date'] ?? null;
    $reminder = $data['reminder'] ?? null;

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Task title is required.']);
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

    // Convert datetime-local to MySQL format
    if ($due_date) {
        $due_date = date('Y-m-d H:i:s', strtotime($due_date));
    }

    if ($reminder) {
        $reminder = date('Y-m-d H:i:s', strtotime($reminder));
    }

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, status, due_date, reminder) VALUES (:user_id, :title, :description, :priority, :status, :due_date, :reminder)");
    $stmt->execute([
        'user_id' => $user_id,
        'title' => $title,
        'description' => $description,
        'priority' => $priority,
        'status' => $status,
        'due_date' => $due_date,
        'reminder' => $reminder
    ]);

    echo json_encode(['success' => true, 'message' => 'Task created successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
