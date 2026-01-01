<?php
// check_admin_auth.php
header('Content-Type: application/json');
require_once '../config.php';

if (isset($_SESSION['admin_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    echo json_encode([
        'authenticated' => true,
        'user_type' => 'admin',
        'admin' => [
            'id' => $_SESSION['admin_id'],
            'full_name' => $_SESSION['admin_name'] ?? 'Admin'
        ]
    ]);
} else {
    echo json_encode(['authenticated' => false, 'user_type' => null]);
}
?>

