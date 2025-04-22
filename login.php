<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];
    $login_type = $_POST['login_type'];

    if ($login_type === 'user') {
        // User authentication
        $stmt = $pdo->prepare("SELECT * FROM users WHERE ration_card_number = ?");
        $stmt->execute([$identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid user credentials'); window.location='index.php';</script>";
        }
    } elseif ($login_type === 'dealer') {
        // Dealer (admin) authentication
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$identifier]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid dealer credentials'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid login type'); window.location='index.php';</script>";
    }
}
?>