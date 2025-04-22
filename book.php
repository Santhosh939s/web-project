<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$slot_id = $_POST['slot_id'];
$user_id = $_SESSION['user_id'];

$pdo->beginTransaction();
try {
    // Check if user has an active (Confirmed) booking
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = 'Confirmed'");
    $stmt->execute([$user_id]);
    $active_bookings = $stmt->fetchColumn();

    if ($active_bookings > 0) {
        $pdo->rollBack();
        echo "<script>alert('You already have an active booking. Please cancel or complete it before booking another slot.'); window.location='dashboard.php';</script>";
        exit;
    }

    // Check slot availability
    $stmt = $pdo->prepare("SELECT current_bookings, max_capacity FROM slots WHERE slot_id = ? FOR UPDATE");
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch();

    if ($slot['current_bookings'] < $slot['max_capacity']) {
        $stmt = $pdo->prepare("UPDATE slots SET current_bookings = current_bookings + 1 WHERE slot_id = ?");
        $stmt->execute([$slot_id]);

        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, slot_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $slot_id]);

        if ($slot['current_bookings'] + 1 == $slot['max_capacity']) {
            $stmt = $pdo->prepare("UPDATE slots SET status = 'Full' WHERE slot_id = ?");
            $stmt->execute([$slot_id]);
        }

        $pdo->commit();
        echo "<script>alert('Slot booked successfully!'); window.location='dashboard.php';</script>";
    } else {
        $pdo->rollBack();
        echo "<script>alert('Slot is full!'); window.location='dashboard.php';</script>";
    }
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script>alert('Error booking slot: " . $e->getMessage() . "'); window.location='dashboard.php';</script>";
}
?>