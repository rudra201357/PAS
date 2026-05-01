<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$database = "rallyspot";

$con = mysqli_connect($server, $username, $password, $database);
if (!$con) {
    die("Connection error: " . mysqli_connect_error());
}

$message = null;

// Query to fetch all available slots for the Midnapore area
$stmt = $con->prepare("
    SELECT 
        s.`slot_id`, 
        s.`slot_number`, 
        vt.`type_name`
    FROM `slots` s
    JOIN `vehicle_types` vt ON s.vehicle_type_id = vt.vehicle_type_id
    WHERE s.`is_available` = 1
    ORDER BY vt.`type_name`, s.`slot_number`
");

$stmt->execute();
$availableSlots = $stmt->get_result();
$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Slots - Midnapore</title>
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
            max-width: 700px;
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
    <div class="container">
        <h1>Available Slots in Midnapore</h1>
        
        <?php if ($availableSlots->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Slot ID</th>
                        <th>Slot Number</th>
                        <th>Vehicle Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $availableSlots->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['slot_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['slot_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin-top: 30px;">❌ No slots are currently available.</p>
        <?php endif; ?>
        
        
    </div>
</body>
</html>