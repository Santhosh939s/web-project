<?php
require_once 'config.php';

$users = [
    ['ration_card_number' => 'AP1234567890', 'password' => 'password123'],
    ['ration_card_number' => 'AP0987654321', 'password' => 'password456'],
    ['ration_card_number' => 'AP1122334455', 'password' => 'password789']
];

$admin = ['username' => 'admin', 'password' => 'admin123'];

foreach ($users as $user) {
    $hashed_password = password_hash($user['password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE ration_card_number = ?");
    $stmt->execute([$hashed_password, $user['ration_card_number']]);
}

$hashed_password = password_hash($admin['password'], PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
$stmt->execute([$hashed_password, $admin['username']]);

echo "Passwords updated successfully!";
?>