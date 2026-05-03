<?php
session_start();

require_once __DIR__ . '/db.php';

$stage = 'select_vehicle';
$message = null;
$bookingDetails = null;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['select_vehicle_btn'])) {
        $vehicleTypeId = $_POST['vehicle_type'];

        $stmt = $con->prepare("SELECT `slot_id`, `slot_number` FROM `slots` WHERE `vehicle_type_id` = ? AND `is_available` = 1 LIMIT 1");
        $stmt->bind_param("i", $vehicleTypeId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['temp_slot_id'] = $row['slot_id'];
            $_SESSION['temp_slot_number'] = $row['slot_number'];
            $_SESSION['temp_vehicle_type_id'] = $vehicleTypeId;
            $stage = 'enter_details';
        } else {
            $message = ['type' => 'danger', 'text' => '❌ No available slots for the selected vehicle type.'];
            $stage = 'select_vehicle';
        }
        $stmt->close();
    } elseif (isset($_POST['book_slot_btn'])) {
        if (!isset($_SESSION['temp_slot_id'])) {
            $message = ['type' => 'danger', 'text' => '❌ Session expired. Please restart the booking process.'];
            $stage = 'select_vehicle';
        } else {
            $vehicleNumber = $_POST['vehicle_number'];
            $startTime = $_POST['start_datetime'];
            $endTime = $_POST['end_datetime'];
            $userId = $_SESSION['user_id'];
            $slotId = $_SESSION['temp_slot_id'];
            $vehicleTypeId = $_SESSION['temp_vehicle_type_id'];
            
            // Generate a short, unique reference number
            $refNumber = 'RALLY' . substr(uniqid(), -4);

            $start_timestamp = strtotime($startTime);
            $start=explode('T', $startTime);
            $starttd = implode(" ", $start);
           

            $end_timestamp = strtotime($endTime);
             $end=explode('T', $endTime);
            $endtd= implode(" ",$end);

            $duration_seconds = $end_timestamp - $start_timestamp;

            // --- Corrected Calculation Logic: Only daily rate is used ---
            $duration_days = intdiv($duration_seconds, 86400);  // 86400 seconds in a day
            if ($duration_seconds % 86400 < 86400 && $duration_seconds % 86400 > 0) $duration_days++;
            $stmt = $con->prepare("SELECT `cost_per_day` , `type_name` FROM `vehicle_types` WHERE `vehicle_type_id` = ?");
            $stmt->bind_param("i", $vehicleTypeId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                $totalCost = ceil($duration_days) * $row['cost_per_day'];
                $vehicle_type = $row['type_name'];
            }
             
            $stmt = $con->prepare("INSERT INTO `bookings` (`sno`, `slot_id`, `booked_at`, `start_time`, `end_time`, `total_cost`, `ref_number`, `is_active`,`vehicle_number`,`vehicle_type`) VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?, ?, ?, 1, ?, ?)");
            $stmt->bind_param("iissdsss", $userId, $slotId,  $startTime, $endTime, $totalCost, $refNumber,$vehicleNumber,$vehicle_type);
            $stmt->execute();
            $stmt->close();

               
                // Prepare the SQL query to fetch the date and time
                $stmt = $con->prepare("SELECT DATE(`booked_at`) AS `booking_date`, TIME_FORMAT(TIME(`booked_at`), '%H:%i') AS `booking_time` FROM `bookings` WHERE `ref_number` = ?");

                // Bind the booking ID and execute the query
                $stmt->bind_param("s", $refNumber);
                $stmt->execute();

                // Get the result and fetch the data
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                if ($row) {
                    $booked_at = $row['booking_date']." ".$row['booking_time'];
                }
                $stmt->close();
            $stmt = $con->prepare("UPDATE `slots` SET `is_available` = 0 WHERE `slot_id` = ?");
            $stmt->bind_param("i", $slotId);
            $stmt->execute();
            $stmt->close();

            $stmt = $con->prepare("SELECT `name`, `email` FROM `user` WHERE `sno` = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $userResult = $stmt->get_result()->fetch_assoc();
                $stmt->close();

            $stmt = $con->prepare("SELECT `type_name` FROM `vehicle_types` WHERE `vehicle_type_id` = ?");
            $stmt->bind_param("i", $vehicleTypeId);
            $stmt->execute();
            $vehicleTypeResult = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $bookingDetails = [
                'vehicle_type' => $vehicleTypeResult['type_name'],
                'vehicle_number' => $vehicleNumber,
                'user_name' => $userResult['name'],
                // 'start_time' => $startTime,
                // 'end_time' => $endTime,
                'total_cost' => $totalCost,
                'slot_number' => $_SESSION['temp_slot_number'],
                'ref_number' => $refNumber
            ];

            unset($_SESSION['temp_slot_id']);
            unset($_SESSION['temp_slot_number']);
            unset($_SESSION['temp_vehicle_type_id']);

            $_SESSION['name'] = $userResult['name'];
            $_SESSION['email']=$userResult['email'];
            $_SESSION['vehicle_type']=$vehicleTypeResult['type_name'];
            $_SESSION['vehicle_number']=$vehicleNumber;
            $_SESSION['start_time']=$starttd;
            $_SESSION['end_time']=$endtd;
            $_SESSION['ref_number']=$refNumber;
            $_SESSION['booked_at']=$booked_at;
            


            $stage = 'confirmation';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Slot</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #2c3e50;
            color: #fff;
        }

        .wrapper {
            width: 420px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(15px);
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            padding: 40px 30px;
            animation: fadeIn 1s ease-in-out;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .message-box {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        .message-box.danger {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid rgba(255, 0, 0, 0.3);
            color: #ffcccc;
        }
        .message-box.success {
            background: rgba(0, 255, 0, 0.2);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #ccffcc;
        }

        .form-input-group {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .form-input-group label {
            display: block;
            color: #fff;
            margin-bottom: 5px;
        }
        
        .form-input-group input, .form-input-group select {
            width: 100%;
            height: 50px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 40px;
            font-size: 16px;
            color: #fff;
            padding: 0 45px 0 20px;
            outline: none;
            transition: 0.3s;
        }
        .form-input-group input::placeholder { color: #ddd; }
        .form-input-group input:focus { border-color: #fff; }

        .form-input-group select {
            color: #fff;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        .form-input-group select option {
            background-color: #fff;
            color: #333;
        }

        .form-input-group i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #fff;
        }

        .btn {
            width: 100%;
            height: 45px;
            background: #fff;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 18px;
            color: #333;
            font-weight: 600;
            transition: 0.3s ease-in-out;
            margin-top: 20px;
        }
        .btn:hover {
            background: #764ba2;
            color: #fff;
            transform: scale(1.05);
        }

        /* Receipt and Print Styles */
        .receipt-box {
            background: #fff;
            color: #333;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        .receipt-box::before {
  content: "RallySpot";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(-30deg);
  font-size: 80px;
  font-weight: bold;
  color: rgba(0, 0, 0, 0.08); /* very light gray */
  pointer-events: none; /* won't block clicks */
  white-space: nowrap;
  z-index: 0;
}
.receipt-box * {
  position: relative;
  z-index: 1;
}
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .receipt-header hr, .receipt-body hr {
            border: 0;
            border-top: 1px dashed #bbb;
            margin: 10px 0;
        }

        .receipt-body {
            margin-bottom: 20px;
        }

        .receipt-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .receipt-item strong, .receipt-total strong {
            font-weight: 600;
            color: #555;
        }

        .receipt-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 20px;
            font-weight: 700;
            margin-top: 15px;
            color: #000;
        }

        .receipt-footer {
            text-align: center;
            font-style: italic;
            color: #777;
            margin-top: 20px;
        }

        .print-btn {
            width: 100%;
            height: 45px;
            background: #5cb85c;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 18px;
            color: #fff;
            font-weight: 600;
            transition: 0.3s ease-in-out;
            margin-top: 20px;
        }

        .print-btn:hover {
            background: #4cae4c;
            transform: scale(1.05);
        }

        @media print {
            body {
                background: none;
                color: #000;
            }
            .wrapper {
                border: none;
                box-shadow: none;
                backdrop-filter: none;
            }
            .print-btn {
                display: none;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .back-button {
    position: absolute;
    top: 25px;
    left: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
    transition: transform 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.back-button svg {
    transition: transform 0.3s ease;
}

.back-button:hover svg {
    transform: translateX(-3px);
}
    </style>
</head>
<body>
     <a href="#" onclick="history.back(); return false;" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <span>Back</span>
    </a>
    <div class="wrapper">
        <?php echo '<h1>Book a Slot</h1>'; ?>
        <?php if (isset($message)): ?>
            <div class="message-box <?php echo htmlspecialchars($message['type']); ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>

        <?php if ($stage === 'select_vehicle'): ?>
            <form action="" method="post">
                <div class="form-input-group">
                    <label for="vehicle_type">Select Vehicle Type</label>
                    <select name="vehicle_type" id="vehicle_type">
                        <?php
                            $result = mysqli_query($con, "SELECT `vehicle_type_id`, `type_name` FROM `vehicle_types`");
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['vehicle_type_id'] . '">' . $row['type_name'] . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <button type="submit" name="select_vehicle_btn" class="btn">Check Availability</button>
            </form>
        <?php elseif ($stage === 'enter_details'): ?>
    <form action="" method="post" id="booking-form">
    <div class="form-input-group">
        <input type="text" name="vehicle_number" placeholder="Vehicle Number" required onkeyup="this.value = this.value.toUpperCase();">
        <i class='bx bxs-car'></i>
    </div>
    <div class="form-input-group">
        <label for="start_datetime">Booking Start Date & Time</label>
        <input type="datetime-local" id="start_datetime" name="start_datetime" required>
    </div>
    <div class="form-input-group">
        <label for="end_datetime">Booking End Date & Time</label>
        <input type="datetime-local" id="end_datetime" name="end_datetime" required>
    </div>
    <button type="submit" name="book_slot_btn" class="btn">Confirm Booking</button>
</form>
        <?php elseif ($stage === 'confirmation'):  ?>
           <?php include('booked_mail.php'); ?>
            <div class="receipt-box">
                <div class="receipt-header">
                    <h2>RallySpot Booking Receipt</h2>
                    <hr>
                </div>
                <div class="receipt-body">
                    <p class="receipt-item"><strong>Reference Number:</strong> <span><?php echo htmlspecialchars($bookingDetails['ref_number']); ?></span></p>
                    <p class="receipt-item"><strong>User Name:</strong> <span><?php echo htmlspecialchars($bookingDetails['user_name']); ?></span></p>
                    <hr>
                    <p class="receipt-item"><strong>Vehicle Type:</strong> <span><?php echo htmlspecialchars($bookingDetails['vehicle_type']); ?></span></p>
                    <p class="receipt-item"><strong>Vehicle Number:</strong> <span><?php echo htmlspecialchars($bookingDetails['vehicle_number']); ?></span></p>
                    <p class="receipt-item"><strong>Slot Number:</strong> <span><?php echo htmlspecialchars($bookingDetails['slot_number']); ?></span></p>
                    <hr>
                    <p class="receipt-item"><strong>Booking Time:</strong> <span><?php echo htmlspecialchars($booked_at); ?></span></p>
                    <p class="receipt-item"><strong>Booking Start:</strong> <span><?php echo htmlspecialchars($starttd); ?></span></p>
                    <p class="receipt-item"><strong>Booking End:</strong> <span><?php echo htmlspecialchars($endtd); ?></span></p>
                    <hr>
                    <p class="receipt-total"><strong>Total Cost:</strong> <span>₹<?php echo number_format($bookingDetails['total_cost'], 2); ?></span></p>
                </div>
                <div class="receipt-footer">
                    <p>Thank you for choosing RallySpot!</p>
                </div>
            </div>
            <button onclick="window.print()" class="print-btn">Print Receipt</button>
        
        <?php endif; ?>
    </div>
   <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const form = document.getElementById('booking-form');
        const startDatetimeInput = document.getElementById('start_datetime');
        const endDatetimeInput = document.getElementById('end_datetime');

        // Function to set the min attribute for the end datetime input
        function setMinEndDate() {
            if (startDatetimeInput.value) {
                endDatetimeInput.min = startDatetimeInput.value;
            }
        }

        // Set the minimum start date to now
        // Set the minimum start date to the current local datetime
const now = new Date();
const offset = now.getTimezoneOffset(); // in minutes (e.g. -330 for IST)
const localDate = new Date(now.getTime() - offset * 60 * 1000);
const nowFormatted = localDate.toISOString().slice(0, 16);
startDatetimeInput.min = nowFormatted;

        // Listen for changes on the start date input to update the end date's min value
        startDatetimeInput.addEventListener('change', setMinEndDate);

        form.addEventListener('submit', function(e) {
            const startDate = new Date(startDatetimeInput.value);
            const endDate = new Date(endDatetimeInput.value);

            // Check if end date is less than or equal to start date
            if (endDate <= startDate) {
                // Prevent the form from submitting
                e.preventDefault(); 
                alert('Error: Booking end date and time must be after the start date and time.');
            }
        });
    });
</script>
</body>
</html>
