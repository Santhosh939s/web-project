<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$booking_id = $_POST['booking_id'];

try {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'Completed' WHERE booking_id = ? AND status = 'Confirmed'");
    $stmt->execute([$booking_id]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Booking marked as completed!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid or already processed booking!'); window.location='admin_dashboard.php';</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Error completing booking: " . $e->getMessage() . "'); window.location='admin_dashboard.php';</script>";
}
?>