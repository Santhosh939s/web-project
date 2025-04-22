<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_login.php");
    exit;
}

$date = $_POST['date'];
$time = $_POST['time'];
$max_capacity = $_POST['max_capacity'];

try {
    $stmt = $pdo->prepare("INSERT INTO slots (date, time, max_capacity) VALUES (?, ?, ?)");
    $stmt->execute([$date, $time, $max_capacity]);
    echo "<script>alert('Slot added successfully!'); window.location='admin_dashboard.php';</script>";
} catch (Exception $e) {
    echo "<script>alert('Error adding slot: " . $e->getMessage() . "'); window.location='admin_dashboard.php';</script>";
}
?>