<?php
session_start();

require_once __DIR__ . '/db.php';

$message = null;

// Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// --- Handle Cancellation Request ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $refNumber = $_POST['ref_number'];

    // Get the slot_id associated with the booking before deleting
    $stmt = $con->prepare("SELECT `slot_id` FROM `bookings` WHERE `ref_number` = ? AND `sno` = ? LIMIT 1");
    $stmt->bind_param("si", $refNumber, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if ($booking) {
        $slotId = $booking['slot_id'];

        // Start a transaction for data integrity
        mysqli_begin_transaction($con);

        try {
            // 1. Delete the booking record
            $stmt = $con->prepare("DELETE FROM `bookings` WHERE `ref_number` = ? AND `sno` = ?");
            $stmt->bind_param("si", $refNumber, $userId);
            $stmt->execute();
            $stmt->close();

            // 2. Update the slot status to available
            $stmt = $con->prepare("UPDATE `slots` SET `is_available` = 1 WHERE `slot_id` = ?");
            $stmt->bind_param("i", $slotId);
            $stmt->execute();
            $stmt->close();

            // Commit the transaction
            mysqli_commit($con);
            $message = ['type' => 'success', 'text' => '✅ Booking ' . htmlspecialchars($refNumber) . ' has been canceled.'];

        } catch (mysqli_sql_exception $e) {
            // Rollback on error
            mysqli_rollback($con);
            $message = ['type' => 'danger', 'text' => '❌ Failed to cancel booking. Please try again.'];
        }
    } else {
        $message = ['type' => 'danger', 'text' => '❌ Booking not found or you do not have permission to cancel it.'];
    }
}

// --- Fetch Booked Slots for the User ---
$stmt = $con->prepare("
    SELECT 
        b.`ref_number`, 
        vt.`type_name`, 
        s.`slot_number`, 
        b.`booked_at`, 
        b.`start_time`, 
        b.`end_time`,
        b.`is_active`
    FROM `bookings` b
    JOIN `slots` s ON b.slot_id = s.slot_id
    JOIN `vehicle_types` vt ON s.vehicle_type_id = vt.vehicle_type_id
    WHERE b.`sno` = ?
    ORDER BY b.`booked_at` DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2c3e50;
            color: #fff;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(15px);
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            padding: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }

        .message-box {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        .message-box.danger { background: rgba(255, 0, 0, 0.2); border: 1px solid rgba(255, 0, 0, 0.3); color: #ffcccc; }
        .message-box.success { background: rgba(0, 255, 0, 0.2); border: 1px solid rgba(0, 255, 0, 0.3); color: #ccffcc; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        th {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .cancel-btn {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .cancel-btn:hover {
            background-color: #c0392b;
        }
  
    </style>
</head>
<body>
    
    <div class="container">
        <h1>My Bookings</h1>

        <?php if (isset($message)): ?>
            <div class="message-box <?php echo htmlspecialchars($message['type']); ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>

        <?php if ($bookings->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking Ref No.</th>
                        <th>Area</th>
                        <th>Vehicle Type</th>
                        <th>Slot No.</th>
                        <th>Booked Time</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ref_number']); ?></td>
                        <td>Midnapore</td>
                        <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['slot_number']); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['booked_at']))); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['start_time']))); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['end_time']))); ?></td>
                        <td><?php echo $row['is_active'] ? 'Active' : 'Cancelled'; ?></td>
                        <td>
                            <?php if ($row['is_active']): ?>
                                <form action="" method="post">
                                    <input type="hidden" name="ref_number" value="<?php echo htmlspecialchars($row['ref_number']); ?>">
                                    <button type="submit" name="cancel_booking" class="cancel-btn">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin-top: 30px;">You have no active bookings.</p>
        <?php endif; ?>
    </div>
</body>
</html>
