<?php
session_start();

// Redirect to registration page if no POST data or OTP is already sent
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_SESSION['otp'])) {
    header("Location: sihnUp.php");
    exit;
}

// ⚠️ SECURITY WARNING: Storing passwords in plain text is highly insecure.
// This is done as per your previous request but is not recommended.

// --- Server-side Validation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(ucwords($_POST['name']));
    $email = trim(strtolower($_POST['email']));
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Basic Server-Side Validation (as before)
    if (empty($name) || empty($email) || empty($dob) || empty($phone) || empty($gender) || empty($password) || empty($cpassword)) {
        $_SESSION['error'] = '❌ All fields are required.';
        header("Location: sihnUp.php");
        exit;
    }
    if ($password !== $cpassword) {
        $_SESSION['error'] = '❌ Passwords do not match.';
        header("Location: sihnUp.php");
        exit;
    }
    
    // Store data in session
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['dob'] = $dob;
    $_SESSION['phone'] = $phone;
    $_SESSION['gender'] = $gender;
    $_SESSION['password'] = $password;
}


// --- 🔑 FIX: Only generate a new OTP if one doesn't exist in the session ---
if (!isset($_SESSION['otp'])) {
    include('smtp/PHPMailerAutoload.php');
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    $receiverEmail = $_SESSION['email'];
    $subject = "RallySpot Email Verification";
    $emailbody = "Hello " . explode(" ", $_SESSION['name'])[0] . ",<br>Welcome to RallySpot!<br>Your 6-digit OTP code is: <b>" . $otp . "</b><br>Do not share this code with anyone.<br><br>Thank You.";

    if (smtp_mailer($receiverEmail, $subject, $emailbody)) {
        header("Location: otpform.php");
        exit;
    } else {
        $_SESSION['error'] = "❌ Failed to send OTP email. Please try again.";
        header("Location: sihnUp.php");
        exit;
    }
} else {
    // If OTP already exists, redirect to the OTP form to prevent resending
    header("Location: otpform.php");
    exit;
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
    $mail->Username = "onlinehotelbookingfromrudra@gmail.com";
    $mail->Password = "xxxx xxxx xxxx xxxx";
    $mail->SetFrom("onlinehotelbookingfromrudra@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions=array('ssl'=>array(
        'verify_peer'=>false,
        'verify_peer_name'=>false,
        'allow_self_signed'=>false
    ));
    return $mail->Send();
}
?>
