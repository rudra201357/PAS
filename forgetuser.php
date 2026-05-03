<?php
session_start();

require_once __DIR__ . '/db.php';

$stage = 'verification';
$message = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_details_btn'])) {
        // --- Stage 1: Verify Email and Mobile Number ---
        $email = trim(strtolower($_POST['email']));
        $phone = trim(strtolower($_POST['mobile']));

        $stmt = $con->prepare("SELECT sno FROM `user` WHERE `email` = ? AND `phone` = ? LIMIT 1");
        if ($stmt === false) {
            die("Error preparing statement: " . $con->error);
        }

        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['reset_user_id'] = $row['sno'];
            $stage = 'reset_password';
        } else {
            $message = ['type' => 'danger', 'text' => '❌ Invalid details. Please check your email and mobile number.'];
        }
        $stmt->close();
    } elseif (isset($_POST['reset_password_btn'])) {
        // --- Stage 2: Reset Password ---
        if (!isset($_SESSION['reset_user_id'])) {
            $message = ['type' => 'danger', 'text' => '❌ Session expired. Please restart the process.'];
            $stage = 'verification';
        } else {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password !== $confirm_password) {
                $message = ['type' => 'danger', 'text' => '❌ Passwords do not match.'];
                $stage = 'reset_password';
            } else {
                // **WARNING: Storing plain text password. HIGHLY INSECURE.**
                // The next line replaces the password_hash() function.
                $plain_password = $new_password;

                $stmt = $con->prepare("UPDATE `user` SET `password` = ? WHERE `sno` = ?");
                if ($stmt === false) {
                    die("Error preparing statement: " . $con->error);
                }
                $stmt->bind_param("si", $plain_password, $_SESSION['reset_user_id']);
                $stmt->execute();
                
                unset($_SESSION['reset_user_id']);
                $_SESSION['message'] = ['type' => 'success', 'text' => '✅ Password successfully updated.'];
                header("Location: login.php");
                exit;
            }
        }
    }
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        /* General Reset */
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
            background: url("pricepic.jpg") no-repeat center center/cover;
        }

        /* Wrapper */
        .wrapper {
            width: 380px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(15px);
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            color: #fff;
            padding: 40px 30px;
            animation: fadeIn 1s ease-in-out;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Message Box */
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

        /* Input Box */
        .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 20px 0;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 40px;
            font-size: 16px;
            color: #fff;
            padding: 0 45px 0 20px;
            outline: none;
            transition: 0.3s;
        }

        .input-box input::placeholder {
            color: #ddd;
        }

        .input-box input:focus {
            border-color: #fff;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #fff;
        }

        /* Button */
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
        }

        .btn:hover {
            background: #764ba2;
            color: #fff;
            transform: scale(1.05);
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .home-btn {
    position: absolute;
    top: 20px;
    left: 20px;
    padding: 10px 20px;
    background-color: #ffffffff;
    color: #333;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    z-index: 1000;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.home-btn:hover {
    background-color: #764ba2;
    color: #fff;
}
    </style>
</head>
<body>
     <p> <a href="index.php" class="home-btn">Home</a></p>
    <div class="wrapper">
        <h1>Forgot Password</h1>
        <?php
        if (isset($message)) {
            echo '<div class="message-box ' . htmlspecialchars($message['type']) . '">' . htmlspecialchars($message['text']) . '</div>';
        }
        ?>

        <?php if ($stage === 'verification'): ?>
        <form action="" method="post">
            
            <div class="input-box">
                <input type="text" name="email" placeholder="Your Email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="text" name="mobile" placeholder="Mobile Number" required>
                <i class='bx bxs-phone'></i>
            </div>
            <button type="submit" name="verify_details_btn" class="btn">Verify</button>
        </form>

        <?php elseif ($stage === 'reset_password'): ?>
        <form action="" method="post">
            <input type="hidden" name="reset_password_btn" value="1">
            <div class="input-box">
                <input type="password" name="new_password" placeholder="New Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <button type="submit" name="reset_password_btn" class="btn">Change Password</button>
        </form>

        <?php endif; ?>
        <div class="register-link">
            <p>Remember your password? <a href="login.php">Log In</a></p>
        </div>
    </div>
</body>
</html>
