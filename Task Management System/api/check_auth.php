<?php
// check_auth.php
header('Content-Type: application/json');
require_once '../config.php';


if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['full_name'] ?? 'User'
        ]
    ]);
} else {
    echo json_encode(['authenticated' => false]);
}
?>
