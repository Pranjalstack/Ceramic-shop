<?php
// 1. Force error reporting ON to catch hidden crashes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); 
session_start();
include 'db.php'; // Connecting to ceramic_db

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$error = "";

if (isset($_POST['send_otp'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_email'] = $email;

        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0; 
            
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ENTER YOUR EMAIL'; 
            $mail->Password   = 'ENTER YOUR APP PASSWORD'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('pranjalaggarwal706@gmail.com', 'TSC forms');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'CERAMIC SHOP OTP';
            $mail->Body    = "Your verification code is: <b>$otp</b>";

            if($mail->send()) {
                header("Location: verify_otp.php");
                exit(); 
            }
        } catch (Exception $e) {
            $error = "Mail Error: Authentication Failed. Please check App Password.";
        }
    } else {
        $error = "Email not found in database.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FORGOT PASSWORD — THE CERAMIC SHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #000;
            color: #fff; 
            font-family: 'Inter', sans-serif; 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0; 
        }

        .card { 
            background: #0a0a0a; 
            border: 1px solid #d4af37; 
            padding: 60px 50px; 
            text-align: center; 
            width: 420px; 
            /* Neon Lighting Effect */
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3), 
                        inset 0 0 10px rgba(212, 175, 55, 0.1);
            position: relative;
        }

        .tsc-logo {
            font-family: 'Cinzel', serif;
            color: #d4af37;
            letter-spacing: 8px;
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: block;
        }

        h2 { 
            font-family: 'Playfair Display', serif; 
            font-style: italic;
            font-weight: 400;
            font-size: 2.2rem;
            margin: 0 0 40px 0;
            color: #fff;
        }

        .label-text {
            display: block;
            text-align: left;
            color: #d4af37;
            font-size: 0.7rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        input { 
            width: 100%; 
            padding: 16px; 
            margin-bottom: 30px; 
            background: #000; 
            border: 1px solid #1a1a1a; 
            color: #fff; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.2);
        }

        button { 
            width: 100%; 
            padding: 18px; 
            background: #d4af37; 
            color: #000; 
            border: none; 
            font-family: 'Inter', sans-serif;
            font-weight: 700; 
            cursor: pointer; 
            letter-spacing: 2px;
            transition: 0.3s;
            text-transform: uppercase;
            margin-top: 10px;
        }

        button:hover { 
            background: #fff; 
            color: #000;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }

        .error { 
            color: #ff4d4d; 
            font-size: 0.8rem; 
            margin-bottom: 25px; 
            border-left: 3px solid #ff4d4d;
            padding: 12px;
            background: rgba(255, 0, 0, 0.05);
            text-align: left;
        }

        .back-link { 
            display: inline-block;
            margin-top: 35px;
            color: #555; 
            text-decoration: none; 
            font-size: 0.7rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .back-link:hover { color: #d4af37; }
    </style>
</head>
<body>
    <div class="card">
        <span class="tsc-logo">T S C .</span>
        <h2>Reset Access</h2>
        
        <?php if($error): ?> 
            <div class="error"><?php echo htmlspecialchars($error); ?></div> 
        <?php endif; ?>

        <form method="POST">
            <span class="label-text">Verify Email</span>
            <input type="email" name="email" required placeholder="your@email.com">
            
            <button type="submit" name="send_otp">Initialize OTP</button>
        </form>

        <a href="login.php" class="back-link">← Return to Login</a>
    </div>

    <?php include 'global_footer.php'; ?>
</body>
</html>