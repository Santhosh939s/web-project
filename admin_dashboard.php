<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dealer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php
    session_start();
    require_once 'config.php';
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit;
    }

    // Delete completed bookings beyond the last 10
    $stmt = $pdo->query("SELECT booking_id FROM bookings WHERE status = 'Completed' ORDER BY booking_time DESC LIMIT 10");
    $keep_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $in_clause = $keep_ids ? implode(',', array_fill(0, count($keep_ids), '?')) : '0';
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE status = 'Completed' AND booking_id NOT IN ($in_clause)");
    $stmt->execute($keep_ids);
    ?>
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between">
            <h1 class="text-lg font-bold">Dealer Dashboard</h1>
            <a href="logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-bold mb-4">Manage Slots</h2>
        <form action="add_slot.php" method="POST" class="mb-6 bg-white p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" id="date" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                    <input type="time" name="time" id="time" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="max_capacity" class="block text-sm font-medium text-gray-700">Max Capacity</label>
                    <input type="number" name="max_capacity" id="max_capacity" value="10" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Add Slot</button>
        </form>
        <h2 class="text-xl font-bold mb-4">Available Slots</h2>
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <?php
            $stmt = $pdo->query("SELECT * FROM slots WHERE status = 'Available' AND current_bookings < max_capacity ORDER BY date, time");
            $slots = $stmt->fetchAll();
            if ($slots) {
                foreach ($slots as $slot) {
                    echo '<div class="border-b py-2">';
                    echo '<p><strong>Date:</strong> ' . htmlspecialchars($slot['date']) . '</p>';
                    echo '<p><strong>Time:</strong> ' . htmlspecialchars($slot['time']) . '</p>';
                    echo '<p><strong>Available Spots:</strong> ' . ($slot['max_capacity'] - $slot['current_bookings']) . '/' . $slot['max_capacity'] . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No available slots found.</p>';
            }
            ?>
        </div>
        <h2 class="text-xl font-bold mb-4">Uncompleted Bookings (Confirmed/Cancelled)</h2>
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <?php
            $stmt = $pdo->query("SELECT s.slot_id, s.date, s.time, s.current_bookings, s.max_capacity, b.booking_id, b.user_id, b.status, u.name, u.ration_card_number 
                                 FROM slots s 
                                 LEFT JOIN bookings b ON s.slot_id = b.slot_id 
                                 LEFT JOIN users u ON b.user_id = u.user_id 
                                 WHERE b.booking_id IS NOT NULL AND b.status IN ('Confirmed', 'Cancelled') 
                                 ORDER BY s.date, s.time, b.status");
            $bookings = $stmt->fetchAll();
            $current_slot_id = null;
            if ($bookings) {
                foreach ($bookings as $booking) {
                    if ($current_slot_id !== $booking['slot_id']) {
                        if ($current_slot_id !== null) {
                            echo '</div>';
                        }
                        $current_slot_id = $booking['slot_id'];
                        echo '<div class="border-b py-2">';
                        echo '<p><strong>Slot Date:</strong> ' . htmlspecialchars($booking['date']) . '</p>';
                        echo '<p><strong>Slot Time:</strong> ' . htmlspecialchars($booking['time']) . '</p>';
                        echo '<p><strong>Bookings:</strong> ' . $booking['current_bookings'] . '/' . $booking['max_capacity'] . '</p>';
                        echo '<p><strong>Users Booked:</strong></p>';
                    }
                    echo '<div class="ml-4 flex items-center space-x-2">';
                    echo '<p>- ' . htmlspecialchars($booking['name']) . ' (Ration Card: ' . htmlspecialchars($booking['ration_card_number']) . ', Status: ' . $booking['status'] . ')</p>';
                    if ($booking['status'] === 'Confirmed') {
                        echo '<form action="complete_booking.php" method="POST" class="inline">';
                        echo '<input type="hidden" name="booking_id" value="' . $booking['booking_id'] . '">';
                        echo '<button type="submit" class="bg-green-600 text-white p-1 rounded-md hover:bg-green-700">Complete</button>';
                        echo '</form>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No uncompleted bookings found.</p>';
            }
            ?>
        </div>
        <h2 class="text-xl font-bold mb-4">Completed Bookings (Last 10)</h2>
        <div class="bg-white p-4 rounded-lg shadow">
            <?php
            $stmt = $pdo->query("SELECT s.slot_id, s.date, s.time, s.current_bookings, s.max_capacity, b.booking_id, b.user_id, b.status, u.name, u.ration_card_number 
                                 FROM slots s 
                                 LEFT JOIN bookings b ON s.slot_id = b.slot_id 
                                 LEFT JOIN users u ON b.user_id = u.user_id 
                                 WHERE b.booking_id IS NOT NULL AND b.status = 'Completed' 
                                 ORDER BY b.booking_time DESC LIMIT 10");
            $bookings = $stmt->fetchAll();
            $current_slot_id = null;
            if ($bookings) {
                foreach ($bookings as $booking) {
                    if ($current_slot_id !== $booking['slot_id']) {
                        if ($current_slot_id !== null) {
                            echo '</div>';
                        }
                        $current_slot_id = $booking['slot_id'];
                        echo '<div class="border-b py-2">';
                        echo '<p><strong>Slot Date:</strong> ' . htmlspecialchars($booking['date']) . '</p>';
                        echo '<p><strong>Slot Time:</strong> ' . htmlspecialchars($booking['time']) . '</p>';
                        echo '<p><strong>Bookings:</strong> ' . $booking['current_bookings'] . '/' . $booking['max_capacity'] . '</p>';
                        echo '<p><strong>Users Booked:</strong></p>';
                    }
                    echo '<div class="ml-4">';
                    echo '<p>- ' . htmlspecialchars($booking['name']) . ' (Ration Card: ' . htmlspecialchars($booking['ration_card_number']) . ', Status: ' . $booking['status'] . ')</p>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No completed bookings found.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>