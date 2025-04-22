<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$booking_id = $_POST['booking_id'];
$user_id = $_SESSION['user_id'];

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("SELECT slot_id FROM bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch();

    if ($booking) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);

        $stmt = $pdo->prepare("UPDATE slots SET current_bookings = current_bookings - 1, status = 'Available' WHERE slot_id = ?");
        $stmt->execute([$booking['slot_id']]);

        $pdo->commit();
        echo "<script>alert('Booking cancelled successfully!'); window.location='dashboard.php';</script>";
    } else {
        $pdo->rollBack();
        echo "<script>alert('Invalid booking!'); window.location='dashboard.php';</script>";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script>alert('Error cancelling booking: " . $e->getMessage() . "'); window.location='dashboard.php';</script>";
}
?>