<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

// --- SURGERY: REGISTRATION LOGIC ---
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Plain text for now to match your login style

    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $run_check = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($run_check) > 0) {
        echo "<script>alert('This email is already registered in the archives.');</script>";
    } else {
        // Insert new user (Role defaults to 'client' based on our table structure)
        $insert_query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'client')";
        
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Account created successfully. Welcome to TSC.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error in archives: " . mysqli_error($conn) . "');</script>";
        }
    }
}
// --- END OF SURGERY ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CREATE ACCOUNT — THE CERAMIC SHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --gold: #d4af37; --noir: #080808; --card: #111; --border: #222; }
        body { 
            background: var(--noir); color: #fff; font-family: 'Inter', sans-serif; 
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 40px 0;
        }

        /* --- NEON BORDER REGISTRATION CARD --- */
        .register-card {
            background: var(--card); 
            width: 100%; max-width: 450px; padding: 60px 40px; text-align: center;
            position: relative;
            
            /* Neon Base */
            border: 1px solid var(--gold);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.2), 
                        0 0 30px rgba(212, 175, 55, 0.1), 
                        inset 0 0 15px rgba(212, 175, 55, 0.05);
            
            /* Pulse Animation */
            animation: neonPulse 3s infinite alternate;
        }

        @keyframes neonPulse {
            from {
                box-shadow: 0 0 10px rgba(212, 175, 55, 0.2), 0 0 20px rgba(212, 175, 55, 0.1);
                border-color: #d4af37;
            }
            to {
                box-shadow: 0 0 20px rgba(212, 175, 55, 0.4), 0 0 40px rgba(212, 175, 55, 0.2);
                border-color: #f1d592;
            }
        }

        .logo-mark { 
            font-family: 'Cinzel', serif; 
            color: var(--gold); 
            font-size: 1.5rem; 
            letter-spacing: 5px; 
            margin-bottom: 40px; 
            display: block; 
            text-decoration: none; 
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.5); /* Glowing Logo */
        }
        
        h1 { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 400; margin-bottom: 30px; }

        .form-group { margin-bottom: 25px; text-align: left; }
        .form-group label { font-size: 0.65rem; letter-spacing: 2px; text-transform: uppercase; color: var(--gold); margin-bottom: 10px; display: block; }

        input {
            width: 100%; background: #000; border: 1px solid var(--border);
            padding: 18px; color: #fff; outline: none; box-sizing: border-box;
            transition: 0.3s; font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
        }
        input:focus { 
            border-color: var(--gold); 
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3); /* Focus glow */
        }

        /* --- LUXURY CHECKBOX STYLING --- */
        .legal-box {
            margin: 25px 0;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .custom-check {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.7rem;
            letter-spacing: 1.5px;
            color: #888;
            text-transform: uppercase;
            position: relative;
            padding-left: 35px;
        }

        .custom-check input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0; width: 0;
        }

        .checkmark {
            position: absolute;
            left: 0;
            height: 20px;
            width: 20px;
            background: #000;
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .custom-check:hover .checkmark {
            border-color: var(--gold);
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
        }

        .custom-check input:checked ~ .checkmark {
            background: var(--gold);
            border-color: var(--gold);
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.5);
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px; top: 2px;
            width: 5px; height: 10px;
            border: solid #000;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-check input:checked ~ .checkmark:after {
            display: block;
        }

        .custom-check a {
            color: var(--gold);
            text-decoration: none;
            margin-left: 4px;
            border-bottom: 1px solid transparent;
            transition: 0.3s;
        }

        .custom-check a:hover {
            border-bottom: 1px solid var(--gold);
        }

        .register-btn {
            width: 100%; background: var(--gold); color: #000; border: none;
            padding: 20px; margin-top: 10px; cursor: pointer; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .register-btn:hover { 
            background: #fff; 
            transform: translateY(-2px); 
            box-shadow: 0 0 20px rgba(255,255,255,0.4);
        }

        .footer-links { margin-top: 35px; font-size: 0.75rem; opacity: 0.6; }
        .footer-links a { color: #fff; text-decoration: none; border-bottom: 1px solid var(--gold); padding-bottom: 2px; transition: 0.3s; }
        .footer-links a:hover { color: var(--gold); text-shadow: 0 0 5px rgba(212, 175, 55, 0.5); }
    </style>
</head>
<body>

<div class="register-card">
    <a href="index.php" class="logo-mark">TSC.</a>
    <h1>Create Account</h1>
    
    <form action="register.php" method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" required placeholder="Enter your full name">
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="Enter your email address">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Create a password">
        </div>

        <div class="legal-box">
            <label class="custom-check">
                <input type="checkbox" name="terms_agree" required>
                <span class="checkmark"></span>
                Accept <a href="terms.php" target="_blank">Terms & Conditions</a>
            </label>

            <label class="custom-check">
                <input type="checkbox" name="privacy_agree" required>
                <span class="checkmark"></span>
                Accept <a href="privacy.php" target="_blank">Privacy Policy</a>
            </label>
        </div>
        
        <button type="submit" name="register" class="register-btn">Sign Up</button>
    </form>

    <div class="footer-links">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<?php include 'global_footer.php'; ?>
</body>
</html>