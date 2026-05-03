<?php
session_start();

require_once __DIR__ . '/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email']));
    $password = $_POST['password'];

    // This query is highly vulnerable to SQL injection. It is strongly recommended to use a prepared statement.
    $sql = "SELECT * FROM `user` WHERE `email` = '$email' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if ($result->num_rows === 1) {
        $row = mysqli_fetch_assoc($result);
        if ($row['password'] === $password) {
            $_SESSION['user_id'] = $row['sno'];
            $_SESSION['name'] = $row['name'];
            $update = "UPDATE user SET islogin = 1 WHERE email = '$email'";
            mysqli_query($con, $update);
            
            // Set a session variable for a success message and redirect
            // $_SESSION['message'] = ['type' => 'success', 'text' => '✅ Login successful!'];
            header("Location: home.php");
            exit;
        } else {
            // Set a session variable for an error message
            $_SESSION['message'] = ['type' => 'danger', 'text' => '❌ Wrong password!'];
            // Re-directing to the same page, no need for alert
        }
    } else {
        // Set a session variable for an error message
        $_SESSION['message'] = ['type' => 'danger', 'text' => '❌ No user found with this email!'];
        // Re-directing to the same page, no need for alert
    }
}

$con->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css">
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
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
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

    /* Remember + Forgot */
    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      margin: 10px 0 20px;
    }

    .remember-forgot label input {
      accent-color: #fff;
      margin-right: 5px;
    }

    .remember-forgot a {
      color: #fff;
      text-decoration: none;
      transition: 0.3s;
    }

    .remember-forgot a:hover {
      color: #ffdd57;
      text-decoration: underline;
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

    /* Register Link */
    .register-link {
      font-size: 14px;
      text-align: center;
      margin: 20px 0 10px;
    }

    .register-link p a {
      color: #ffdd57;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .register-link p a:hover {
      text-decoration: underline;
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
    <script>
window.addEventListener('pageshow', function(event) {
    // Check if the page is being loaded from the browser's back/forward cache
    if (event.persisted) {
        // Force a page reload
        window.location.reload();
    }
});
</script>
 <p> <a href="index.php" class="home-btn">Home</a></p>
  <div class="wrapper">
    <form action="" method="post">
      <h1>Login</h1>
      
      <!-- PHP code to display the message box -->
      <?php
      if (isset($_SESSION['message'])) {
          $message = $_SESSION['message'];
          echo '<div class="message-box">' . htmlspecialchars($message['text']) . '</div>';
          // Clear the message to prevent it from reappearing on a refresh
          unset($_SESSION['message']);
      }
      ?>

      <div class="input-box">
        <input type="text" name="email" placeholder="Username" required>
        <i class='bx bxs-user'></i>
      </div>

      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
        <i class='bx bxs-lock-alt'></i>
      </div>

      <div class="remember-forgot">
        <label><input type="checkbox"> Remember me</label>
        <a href="forgetuser.php">Forgot Password?</a>
      </div>

      <button type="submit" class="btn">Login</button>
      <div class="register-link">
        <p>Don't have an account? <a href="signUp.php">Register</a></p>
      </div>
    </form>
  </div>
</body>
</html>
