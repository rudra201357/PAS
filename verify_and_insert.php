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

$error = '';
$stage = 'registration_form'; // Default stage

// --- Handle initial form submission from signUp.php ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {

    // --- ⚠️ Server-side validation for security ---
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['dob']) || empty($_POST['phone']) || empty($_POST['gender']) || empty($_POST['password'])) {
        $error = '❌ All fields are required.';
    } elseif ($_POST['password'] !== $_POST['cpassword']) {
        $error = '❌ Passwords do not match.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = '❌ Invalid email address.';
    } else {
        // --- Store data in session and send OTP ---
        $_SESSION['name'] = trim(ucwords($_POST['name']));
        $_SESSION['email'] = trim(strtolower($_POST['email']));
        $_SESSION['dob'] = $_POST['dob'];
        $_SESSION['phone'] = $_POST['phone'];
        $_SESSION['gender'] = $_POST['gender'];
        $_SESSION['password'] = $_POST['password']; // ⚠️ Plain text password, as requested

        include('smtp/PHPMailerAutoload.php');
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;

        $receiverEmail = $_SESSION['email'];
        $subject = "RallySpot Email Verification";
        $emailbody = "Hello " . explode(" ", $_SESSION['name'])[0] . ",<br>Welcome to RallySpot!<br>Your 6-digit OTP code is: <b>" . $otp . "</b><br>Don't share this code with anyone.<br><br>Thank You.";

        if (smtp_mailer($receiverEmail, $subject, $emailbody)) {
            $stage = 'otp_form';
        } else {
            $error = '❌ Failed to send OTP email. Please try again.';
            // Clear session data if email fails
            session_unset();
        }
    }

}
// --- Handle OTP form submission ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {

    if (!isset($_SESSION['otp'])) {
        $error = '❌ Session expired. Please re-register.';
    } elseif (trim($_POST['verify_otp']) == $_SESSION['otp']) { // ✅ FIX: Changed to $_POST['verify_otp']
        // --- ✅ OTP is correct, insert data into database ---

        $name = $_SESSION['name'];
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];
        $dob = $_SESSION['dob'];
        $phone = $_SESSION['phone'];
        $gender = $_SESSION['gender'];
        $islog = 1; // Set islog to 1 on registration

        // Check for existing user to prevent duplicates
        $stmt_check = $con->prepare("SELECT sno FROM `user` WHERE `email` = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $stmt_check->close();

        if ($result_check->num_rows > 0) {
            $error = '❌ An account with this email already exists.';
        } else {
            // Insert new user into the database
            $stmt_insert = $con->prepare("INSERT INTO `user` (`name`, `email`, `password`, `dob`, `phone`, `gender`, `islogin`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("ssssssi", $name, $email, $password, $dob, $phone, $gender, $islog);
            $stmt_insert->execute();
            $stmt_insert->close();

            // Clear all session data after successful registration
            session_unset();
            session_destroy();
            
            // Redirect to a success page
            header("Location: login.php?status=registered");
            exit;
        }

    } else {
        $error = '❌ Invalid OTP, please try again.';
    }
    $stage = 'otp_form'; // Stay on the OTP form
}
// --- Check if the OTP form should be displayed on page load ---
elseif (isset($_SESSION['otp']) && !isset($_POST['verify_otp'])) {
    $stage = 'otp_form';
}


function smtp_mailer($to, $subject, $msg){
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "bookingonlinefromme@gmail.com";
    $mail->Password = "ynnp qism woau woow";
    $mail->SetFrom("bookingonlinefromme@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));
    return $mail->Send();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Registration</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            margin-top: 20px;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        .container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .container button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php if ($stage === 'otp_form'): ?>
        <div class="container">
            <h2>Enter OTP</h2>
            <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
            <form method="post">
                <label for="otp">A 6-digit code has been sent to <?php echo $_SESSION['email']; ?>.</label><br>
                <input type="text" id="otp" name="verify_otp" maxlength="6" required>
                <button type="submit">Verify</button>
            </form>
        </div>
    <?php else: ?>
        <div class="container">
            <h2>Registration Error</h2>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <p>Please <a href="signUp.php">go back</a> and try again.</p>
        </div>
    <?php endif; ?>
</body>
</html>
