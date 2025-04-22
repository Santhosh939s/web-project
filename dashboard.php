<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php
    session_start();
    require_once 'config.php';
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
    ?>
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between">
            <h1 class="text-lg font-bold">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <a href="logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-bold mb-4">Available Slots</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM slots WHERE status = 'Available' AND current_bookings < max_capacity");
            while ($slot = $stmt->fetch()) {
                echo '<div class="bg-white p-4 rounded-lg shadow">';
                echo '<p><strong>Date:</strong> ' . htmlspecialchars($slot['date']) . '</p>';
                echo '<p><strong>Time:</strong> ' . htmlspecialchars($slot['time']) . '</p>';
                echo '<p><strong>Available:</strong> ' . (10 - $slot['current_bookings']) . '</p>';
                echo '<form action="book.php" method="POST" class="mt-2">';
                echo '<input type="hidden" name="slot_id" value="' . $slot['slot_id'] . '">';
                echo '<button type="submit" class="bg-green-600 text-white p-2 rounded-md hover:bg-green-700">Book Slot</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
        <h2 class="text-xl font-bold mt-8 mb-4">Your Current Booking</h2>
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <?php
            $user_id = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT b.*, s.date, s.time FROM bookings b JOIN slots s ON b.slot_id = s.slot_id WHERE b.user_id = ? AND b.status = 'Confirmed'");
            $stmt->execute([$user_id]);
            $booking = $stmt->fetch();
            if ($booking) {
                echo '<div class="border-b py-2">';
                echo '<p><strong>Date:</strong> ' . htmlspecialchars($booking['date']) . '</p>';
                echo '<p><strong>Time:</strong> ' . htmlspecialchars($booking['time']) . '</p>';
                echo '<form action="cancel.php" method="POST" class="inline">';
                echo '<input type="hidden" name="booking_id" value="' . $booking['booking_id'] . '">';
                echo '<button type="submit" class="bg-red-600 text-white p-1 rounded-md hover:bg-red-700">Cancel</button>';
                echo '</form>';
                echo '</div>';
            } else {
                echo '<p>No active booking found.</p>';
            }
            ?>
        </div>
        <h2 class="text-xl font-bold mt-8 mb-4">Booking History (Last 10)</h2>
        <div class="bg-white p-4 rounded-lg shadow">
            <?php
            $stmt = $pdo->prepare("SELECT b.*, s.date, s.time FROM bookings b JOIN slots s ON b.slot_id = s.slot_id WHERE b.user_id = ? ORDER BY b.booking_time DESC LIMIT 10");
            $stmt->execute([$user_id]);
            $bookings = $stmt->fetchAll();
            if ($bookings) {
                foreach ($bookings as $booking) {
                    echo '<div class="border-b py-2">';
                    echo '<p><strong>Date:</strong> ' . htmlspecialchars($booking['date']) . '</p>';
                    echo '<p><strong>Time:</strong> ' . htmlspecialchars($booking['time']) . '</p>';
                    echo '<p><strong>Status:</strong> ' . htmlspecialchars($booking['status']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No booking history found.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>