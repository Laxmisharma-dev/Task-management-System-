<?php
// config.php
$host = 'localhost';
$db   = 'task_m';
$user = 'root';
$pass = ''; // your MySQL password

$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch(PDOException $e) {
    // If we're just setting up, we might not have the DB yet
}
