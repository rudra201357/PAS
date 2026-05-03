<?php
session_start();

require_once __DIR__ . '/db.php';

$message = null;






// --- Earning Calculation Queries ---
// Today's In-Hand Earning: Bookings made today AND starting today
$stmt = $con->prepare("SELECT COALESCE(SUM(total_cost), 0) AS total FROM `bookings` WHERE DATE(`booked_at`) = CURDATE() AND DATE(`start_time`) = CURDATE()");
$stmt->execute();
$inHandEarning = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Today's Earning: All bookings made today
$stmt = $con->prepare("SELECT COALESCE(SUM(total_cost), 0) AS total FROM `bookings` WHERE DATE(`booked_at`) = CURDATE()");
$stmt->execute();
$todayEarning = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Total Income: All bookings ever
$stmt = $con->prepare("SELECT COALESCE(SUM(total_cost), 0) AS total FROM `bookings`");
$stmt->execute();
$totalIncome = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// --- Handle Booking Cancellation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_ref'])) {
    $refNumber = $_POST['cancel_booking_ref'];

    mysqli_begin_transaction($con);

    try {
        $stmt = $con->prepare("SELECT `slot_id` FROM `bookings` WHERE `ref_number` = ? LIMIT 1");
        $stmt->bind_param("s", $refNumber);
        $stmt->execute();
        $bookingResult = $stmt->get_result();
        $booking = $bookingResult->fetch_assoc();
        $stmt->close();

        if ($booking) {
            $slotId = $booking['slot_id'];

            $stmt = $con->prepare("DELETE FROM `bookings` WHERE `ref_number` = ?");
            $stmt->bind_param("s", $refNumber);
            $stmt->execute();
            $stmt->close();

            $stmt = $con->prepare("UPDATE `slots` SET `is_available` = 1 WHERE `slot_id` = ?");
            $stmt->bind_param("i", $slotId);
            $stmt->execute();
            $stmt->close();

            mysqli_commit($con);
            $message = ['type' => 'success', 'text' => '✅ Booking ' . htmlspecialchars($refNumber) . ' has been canceled.'];
        } else {
            $message = ['type' => 'danger', 'text' => '❌ Booking not found.'];
        }
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($con);
        $message = ['type' => 'danger', 'text' => '❌ Failed to cancel booking. Please try again.'];
    }
}

// --- Fetch all slots and their booking status ---
$stmt = $con->prepare("
    SELECT 
        s.slot_id, 
        s.slot_number, 
        s.is_available,
        vt.type_name,
        b.ref_number,
        b.booked_at,
        b.start_time,
        b.vehicle_number,
        b.end_time,
        u.name AS user_name,
        b.sno
    FROM `slots` s
    JOIN `vehicle_types` vt ON s.vehicle_type_id = vt.vehicle_type_id
    LEFT JOIN `bookings` b ON s.slot_id = b.slot_id AND b.is_active = 1
    LEFT JOIN `user` u ON b.sno = u.sno
    ORDER BY s.slot_id ASC
");
$stmt->execute();
$allSlots = $stmt->get_result();
$stmt->close();

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Dashboard</title>
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
            max-width: 1500px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(15px);
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            padding: 30px;
            position: relative; /* Added for positioning the logout button */
        }

        /* Styles for the new buttons */
        .dashboard-actions {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .dashboard-btn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 1rem;
            font-weight: 500;
        }
        .dashboard-btn.logout-btn {
            background-color: #e74c3c;
        }
        .dashboard-btn.logout-btn:hover {
            background-color: #c0392b;
        }
        .dashboard-btn:hover {
            background-color: #2980b9;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }

        .summary-cards {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .card .amount {
            font-size: 2.5rem;
            font-weight: 700;
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
            white-space: nowrap;
        }

        th {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .btn-group {
            display: flex;
            gap: 5px;
            white-space: nowrap;
        }

        .cancel-btn, .receipt-btn {
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .cancel-btn {
            background-color: #e74c3c;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }

        .receipt-btn {
            background-color: #3498db;
        }
        .receipt-btn:hover {
            background-color: #2980b9;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-actions">
            <a href="javascript:history.back()" class="dashboard-btn">Back</a>
            <a href="logout.php" class="dashboard-btn logout-btn">Logout</a>
        </div>
        
        <h1>Parking Slot Dashboard</h1>
        
        <div class="summary-cards">
            <div class="card">
                <h3>Today's In-Hand Earning</h3>
                <p class="amount">₹<?php echo number_format($inHandEarning, 2); ?></p>
            </div>
            <div class="card">
                <h3>Today's Earning</h3>
                <p class="amount">₹<?php echo number_format($todayEarning, 2); ?></p>
            </div>
            <div class="card">
                <h3>Total Income</h3>
                <p class="amount">₹<?php echo number_format($totalIncome, 2); ?></p>
            </div>
        </div>

        <?php if (isset($message)): ?>
            <div class="message-box <?php echo htmlspecialchars($message['type']); ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>

        <?php if ($allSlots->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        
                        <th>Slot No.</th>
                        <th>Vehicle Type</th>
                        <th>Status</th>
                        <th>Vehicle Number</th>
                        <th>Booked By (ID)</th>
                        <th>Booked Time</th>
                        <th>Booking Start</th>
                        <th>Booking End</th>
                        <th>Ref No.</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $allSlots->fetch_assoc()): ?>
                        <tr>
                           
                            <td><?php echo htmlspecialchars($row['slot_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                            <td>
                                <?php if ($row['is_available']): ?>
                                    <span style="color: #2ecc71;">Available</span>
                                <?php else: ?>
                                    <span style="color: #e74c3c;">Booked</span>
                                <?php endif; ?>
                            </td>
                              <td>
                                <?php if (!$row['is_available']): ?>
                                    <?php echo htmlspecialchars($row['vehicle_number']); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$row['is_available']): ?>
                                    <?php echo htmlspecialchars($row['user_name']) . ' (' . htmlspecialchars($row['sno']) . ')'; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$row['is_available']): ?>
                                    <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['booked_at']))); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$row['is_available']): ?>
                                    <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['start_time']))); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$row['is_available']): ?>
                                    <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['end_time']))); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$row['is_available']): ?>
                                    <?php echo htmlspecialchars($row['ref_number']); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$row['is_available']): ?>
                                    <div class="btn-group">
                                       <a href="print_receipt.php?ref=<?php echo urlencode($row['ref_number']); ?>&slot_no=<?php echo urlencode($row['slot_number']); ?>" target="_blank" class="receipt-btn">Print</a>

                                        <form action="" method="post" style="display: inline;">
                                            <input type="hidden" name="cancel_booking_ref" value="<?php echo htmlspecialchars($row['ref_number']); ?>">
                                            <button type="submit" name="cancel_booking" class="cancel-btn">Cancel</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin-top: 30px;">No parking slots found in the database.</p>
        <?php endif; ?>
    </div>
</body>
</html>
