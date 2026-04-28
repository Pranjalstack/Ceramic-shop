<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $new_password = $_POST['new_password'];

    // 1. Update basic info
    $sql = "UPDATE users SET fullname = '$fullname', shipping_address = '$address' WHERE id = '$u_id'";
    $result = mysqli_query($conn, $sql);

    // 2. Update password only if provided
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $pass_sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$u_id'";
        mysqli_query($conn, $pass_sql);
    }

    if ($result) {
        // Sync session name immediately
        $_SESSION['user_name'] = $fullname;
        header("Location: profile.php?status=success");
    } else {
        header("Location: profile.php?status=error");
    }
} else {
    header("Location: login.php");
}
?>