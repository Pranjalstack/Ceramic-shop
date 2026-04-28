<?php
ob_start();
session_start();

// Security: If no OTP session exists, the user shouldn't be here
if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = "";
$success = "";

// Check if the form was submitted
if (isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp']);
    
    // Compare the user's input with the code stored in the Session
    if ($entered_otp == $_SESSION['reset_otp']) {
        // Mark as verified so reset_password.php knows it's safe to proceed
        $_SESSION['otp_verified'] = true; 
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "The code you entered is incorrect. Please check your email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code — TSC</title>
    <style>
        body { 
            background: #080808; 
            color: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
        }
        .card { 
            background: #111; 
            border: 1px solid #222; 
            padding: 40px; 
            text-align: center; 
            width: 350px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        }
        .gold-text { color: #d4af37; letter-spacing: 3px; font-weight: bold; margin-bottom: 20px; }
        p { color: #888; font-size: 0.9rem; line-height: 1.5; }
        input { 
            width: 100%; 
            padding: 15px; 
            margin: 25px 0; 
            background: #000; 
            border: 1px solid #333; 
            color: #d4af37; 
            text-align: center; 
            font-size: 1.5rem; 
            letter-spacing: 8px;
            box-sizing: border-box;
        }
        input:focus { border-color: #d4af37; outline: none; }
        button { 
            width: 100%; 
            padding: 15px; 
            background: #d4af37; 
            color: #000; 
            border: none; 
            font-weight: bold; 
            cursor: pointer; 
            text-transform: uppercase;
            transition: 0.3s;
        }
        button:hover { background: #b8962d; }
        .error { color: #ff4444; background: rgba(255,68,68,0.1); padding: 10px; font-size: 0.8rem; margin-bottom: 20px; border: 1px solid #ff4444; }
    </style>
</head>
<body>
    <div class="card">
        <div class="gold-text">IDENTITY CHECK</div>
        <h2>Verify OTP</h2>
        <p>A 6-digit verification code has been sent to:<br>
           <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong></p>

        <?php if($error): ?> 
            <div class="error"><?php echo $error; ?></div> 
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="otp" placeholder="000000" required maxlength="6" autocomplete="off">
            <button type="submit" name="verify_otp">Verify Code</button>
        </form>
        
        <p style="margin-top: 20px; font-size: 0.75rem;">
            Didn't get a code? <a href="forgot_password.php" style="color:#d4af37; text-decoration:none;">Try again</a>
        </p>
    </div>
<?php include 'global_footer.php'; ?>
</body>
</html>