<?php

$server   = "localhost";
$username = "root";
$password = "";
$database = "rallyspot";

$con = mysqli_connect($server, $username, $password, $database);
if (!$con) {
    die("Connection error: " . mysqli_connect_error());
}


    $name     = trim(ucwords($_POST['name']))   ;
    $email    = trim(strtolower($_POST['email']))   ;
    $loc    = $_POST['myDropdown']   ;
    $phone    = $_POST['phone']   ;
    $cpassword = $_POST['cpassword'];
    $gender   = $_POST['gender']  ;
if(!empty($email)){
$checkSql = "SELECT id FROM admins WHERE email = '$email' LIMIT 1";
$result = $con->query($checkSql);

if ($result->num_rows > 0) {
     echo "<script>
        alert('❌ User already exists with this email!');
        window.location.href = 'Adminlogin.php';
    </script>";
    exit;
} else {
    // Insert new record
    $sql = "INSERT INTO `admins` (`name`, `email`, `phone`, `loc`, `gender`, `password`, `lastlogin`) 
            VALUES ('$name', '$email', '$phone', '$loc', '$gender', '$cpassword', current_timestamp())";

    if ($con->query($sql) === true) {
        echo "<script>
        alert('✅ Registered Successfully!');
        window.location.href = 'Adminlogin.php';
    </script>";
    exit;
    } else {
          echo "<script>
        alert('❌ Registration failed');
        window.location.href = 'index.php';
    </script>";
    exit;
        // echo "❌ Registration failed: " . $con->error;
    }
}
 } else {
     echo "⚠️ You must enter at least email.";
}

$con->close();
?>
