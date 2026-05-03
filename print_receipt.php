<?php 
require_once __DIR__ . '/db.php';

$refNumber = $_GET['ref'] ?? '';
$slot_no   = $_GET['slot_no'] ;
  // sensitive:  not to do

$stmt = $con->prepare("SELECT * FROM bookings WHERE ref_number = ?");
$stmt->bind_param("s", $refNumber);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row) {
   $sno = $row['sno'];
   $slot_id = $row['slot_id'];
   $totalCost = $row['total_cost'];
   $vehicleNumber= $row['vehicle_number'];
   $vehicle_type= $row['vehicle_type'];
}
$stmt->close();

               // Prepare the SQL query to fetch booking details
$stmt = $con->prepare("
    SELECT 
        DATE_FORMAT(`booked_at`, '%Y-%m-%d %H:%i') AS `booking_time`,
        DATE_FORMAT(`start_time`, '%Y-%m-%d %H:%i') AS `starttd`,
        DATE_FORMAT(`end_time`, '%Y-%m-%d %H:%i') AS `endtd`
    FROM `bookings` 
    WHERE `ref_number` = ?
");

// Bind parameter and execute
$stmt->bind_param("s", $refNumber);
$stmt->execute();

// Get result and fetch row
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    $booked_at = $row['booking_time'];  // e.g. "2025-09-01 15:30"
    $starttd   = $row['starttd'];   // e.g. "2025-09-05 09:00"
    $endtd     = $row['endtd'];     // e.g. "2025-09-05 12:00"
}

$stmt->close();




$stmt = $con->prepare("SELECT `name`  FROM `user` WHERE `sno` = ?");
$stmt->bind_param("i", $sno);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($userResult) {
    $uname = $userResult['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RallySpot Receipt</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #34495e;
      margin: 0;
      padding: 100px;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    h1 {
      color: #fff;
      margin-bottom: 20px;
      font-size: 28px;
      font-weight: 600;
    }

    .receipt-box {
      background: #fff;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      width: 320px;
      height: 500px;
      max-width: 95%;
      text-align: left;
      position: relative;
  overflow: hidden;
    }


    .receipt-header {
      text-align: center;
      font-size: 30px;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 15px;
    }

    .receipt-header hr {
      border: none;
      border-top: 1px dashed #ccc;
      margin: 10px 0 15px;
    }

    .receipt-body {
      font-size: 15px;
      line-height: 1.8;
    }

    .receipt-item {
      display: flex;
      justify-content: space-between;
      margin: 6px 0;
    }

    .receipt-item strong {
      color: #2c3e50;
    }

    .receipt-total {
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
      font-size: 18px;
      font-weight: bold;
      color: #2c3e50;
    }

    .receipt-total span {
      color: #000;
    }

    .receipt-footer {
      text-align: center;
      margin-top: 15px;
      font-style: italic;
      color: #7f8c8d;
    }

    .print-btn {
      margin-top: 25px;
      padding: 12px 20px;
      background: #27ae60;
      color: #fff;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    .print-btn:hover {
      background: #219150;
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

/* Keep text above watermark */
.receipt-box * {
  position: relative;
  z-index: 1;
}
    @media print {
      body {
        background: #fff;
      }
      .print-btn, h1 {
        display: none;
      }
      .receipt-box {
        box-shadow: none;
        border: none;
        width: 100%;
        max-width: 100%;
      }
    }
    .receipt-header hr, .receipt-body hr {
            border: 0;
            border-top: 1px dashed #bbb;
            margin: 10px 0;
        }
  </style>
</head>
<body>
  <div class="receipt-box">
    <div class="receipt-header">
      RallySpot Booking Receipt 
      <hr>
    </div>
    <div class="receipt-body">
      <p class="receipt-item"><strong>Reference Number:</strong> <span><?php echo htmlspecialchars($refNumber); ?></span></p>
      <p class="receipt-item"><strong>User Name:</strong> <span><?php echo htmlspecialchars($uname); ?></span></p><hr>
      <p class="receipt-item"><strong>Vehicle Type:</strong> <span><?php echo htmlspecialchars($vehicle_type); ?></span></p>
      <p class="receipt-item"><strong>Vehicle Number:</strong> <span><?php echo htmlspecialchars($vehicleNumber); ?></span></p>
      <p class="receipt-item"><strong>Slot Number:</strong> <span><?php echo htmlspecialchars($slot_no); ?></span></p><hr>
      <p class="receipt-item"><strong>Booking Time:</strong> <span><?php echo htmlspecialchars($booked_at); ?></span></p>
      <p class="receipt-item"><strong>Booking Start:</strong> <span><?php echo htmlspecialchars($starttd); ?></span></p>
      <p class="receipt-item"><strong>Booking End:</strong> <span><?php echo htmlspecialchars($endtd); ?></span></p><hr>
      <div class="receipt-total"><strong>Total Cost:</strong> <span>₹<?php echo number_format($totalCost, 2); ?></span></div>
    </div>
    <div class="receipt-footer">
      Thank you for choosing RallySpot!
    </div>
  </div>

  <button onclick="window.print()" class="print-btn">Print Receipt</button>
</body>
</html>
