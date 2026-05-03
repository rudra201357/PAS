<?php
session_start();

require_once __DIR__ . '/db.php';

// Check if a user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $con->prepare("UPDATE `user` SET `islogin` = 0 WHERE `sno` = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}
// Check if an admin is logged in
elseif (isset($_SESSION['admin_id'])) {
    $adminId = $_SESSION['admin_id'];
    $stmt = $con->prepare("UPDATE `admins` SET `islogin` = 0 WHERE `id` = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $stmt->close();
}

$con->close();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the index page
header("Location: index.php");
exit;
?>
