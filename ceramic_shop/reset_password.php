<?php
ob_start();
session_start();
include 'db.php'; // Points to ceramic_db

// Security: Only allow users who passed the OTP check
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$message = "";

if (isset($_POST['update_password'])) {
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if ($new_pass === $confirm_pass) {
        // Update the password in your users table
        $query = "UPDATE users SET password = '$new_pass' WHERE email = '$email'";
        
        if (mysqli_query($conn, $query)) {
            // Success! Clear the session so they have to login
            session_destroy();
            $message = "<p style='color:green'>Password updated! <a href='login.php' style='color:#d4af37'>Login here</a></p>";
        } else {
            $message = "<p style='color:red'>Error updating record.</p>";
        }
    } else {
        $message = "<p style='color:red'>Passwords do not match.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Password - TSC</title>
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #111; padding: 40px; border: 1px solid #d4af37; text-align: center; width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; background: #222; border: 1px solid #333; color: #fff; box-sizing: border-box; }
        button { background: #d4af37; border: none; padding: 10px; width: 100%; cursor: pointer; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="color:#d4af37">New Password</h2>
        <?php echo $message; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="update_password">Update Password</button>
        </form>
    </div>
<?php include 'global_footer.php'; ?>
</body>
</html>