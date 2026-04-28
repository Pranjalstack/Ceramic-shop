<?php
session_start();
include 'db.php'; // Connecting to ceramic_db

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check database for user
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // --- THE FIX: STORE THE USER'S ACTUAL NAME ---
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = !empty($user['name']) ? $user['name'] : "Valued Client";

        // Redirect to your homepage
        header("Location: index.php"); 
        exit();
    } else {
        $error = "Invalid Email or Password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN — THE CERAMIC SHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Restored your TSC aesthetic styling */
        body { 
            background: #0a0a0a; 
            color: #fff; 
            font-family: 'Inter', sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
        }

        /* --- NEON BORDER BOX --- */
        .login-box { 
            background: #111; 
            padding: 50px; 
            text-align: center; 
            width: 420px; 
            position: relative;
            border: 1px solid #d4af37; /* Gold base border */
            
            /* The Neon Glow Effect */
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.2), 
                        0 0 30px rgba(212, 175, 55, 0.1), 
                        inset 0 0 15px rgba(212, 175, 55, 0.05);
            
            /* Animation for the "Neon flicker" */
            animation: neonPulse 3s infinite alternate;
        }

        @keyframes neonPulse {
            from {
                box-shadow: 0 0 10px rgba(212, 175, 55, 0.2), 0 0 20px rgba(212, 175, 55, 0.1);
                border-color: #d4af37;
            }
            to {
                box-shadow: 0 0 20px rgba(212, 175, 55, 0.4), 0 0 40px rgba(212, 175, 55, 0.2);
                border-color: #f1d592; /* Brighter gold at peak pulse */
            }
        }

        .logo-text { 
            font-family: 'Cinzel', serif; 
            color: #d4af37; 
            letter-spacing: 8px; 
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.5); /* Glowing Logo */
        }

        h2 { 
            font-family: 'Playfair Display', serif; 
            font-weight: 400; 
            font-style: italic; 
            font-size: 2rem; 
            margin-bottom: 30px; 
        }

        .input-group { text-align: left; margin-bottom: 20px; }
        .input-group label { 
            display: block; 
            color: #d4af37; 
            font-size: 0.65rem; 
            letter-spacing: 2px; 
            text-transform: uppercase; 
            margin-bottom: 8px; 
        }

        input { 
            width: 100%; 
            padding: 15px; 
            background: #050505; 
            border: 1px solid #222; 
            color: #fff; 
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        input:focus { 
            border-color: #d4af37; 
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3); /* Focus glow */
        }

        button { 
            width: 100%; 
            padding: 18px; 
            background: #d4af37; 
            border: none; 
            font-weight: 700; 
            letter-spacing: 2px;
            cursor: pointer; 
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        button:hover { 
            background: #fff; 
            color: #000; 
            box-shadow: 0 0 20px rgba(255,255,255,0.4);
        }

        .error-msg { 
            background: rgba(255, 68, 68, 0.1); 
            color: #ff4444; 
            padding: 10px; 
            margin-bottom: 20px; 
            font-size: 0.75rem; 
            border: 1px solid rgba(255, 68, 68, 0.2);
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.2);
        }

        .footer-links { margin-top: 30px; font-size: 0.75rem; color: #555; }
        .footer-links a { color: #888; text-decoration: none; margin: 0 10px; transition: 0.3s; }
        .footer-links a:hover { color: #d4af37; text-shadow: 0 0 5px rgba(212, 175, 55, 0.5); }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-text">T S C .</div>
        <h2>Client Entry</h2>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login">SIGN IN</button>
        </form>
        
        <div class="footer-links">
            <a href="register.php">Register Here</a> • 
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
<?php include 'global_footer.php'; ?>
</body>
</html>