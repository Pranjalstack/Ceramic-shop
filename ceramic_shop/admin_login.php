<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_user = "admin";
$admin_pass = "admin123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Admin Access Denied!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Center — TSC Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-red: #8b0000;
            --bright-red: #e74c3c;
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
        }

        body { 
            background: radial-gradient(circle at center, #1a0505 0%, #0a0a0a 100%);
            color: #fff; 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        /* Ambient Glow Background Effect */
        body::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--admin-red);
            filter: blur(150px);
            opacity: 0.15;
            z-index: 0;
        }

        .admin-box { 
            background: var(--card-bg); 
            width: 400px; 
            padding: 50px; 
            border: 1px solid #1a1a1a;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Top Red Accent Bar */
        .admin-box::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--admin-red), transparent);
        }

        .tsc-admin-logo {
            font-family: 'Cinzel', serif;
            color: var(--admin-red);
            letter-spacing: 5px;
            font-size: 1rem;
            margin-bottom: 10px;
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

        .error-msg {
            color: var(--bright-red);
            font-size: 0.75rem;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            margin-bottom: 20px;
            border-left: 3px solid var(--bright-red);
            text-align: left;
            font-family: 'Cinzel', serif;
        }

        input { 
            width: 100%; 
            padding: 16px; 
            margin-bottom: 15px; 
            background: #000; 
            border: 1px solid #222; 
            color: #fff; 
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--admin-red);
        }

        .btn-admin { 
            width: 100%; 
            padding: 18px; 
            background: var(--admin-red); 
            color: #fff; 
            border: none; 
            font-family: 'Cinzel', serif;
            font-weight: 700;
            letter-spacing: 2px;
            cursor: pointer; 
            transition: 0.4s;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .btn-admin:hover { 
            background: var(--bright-red); 
            box-shadow: 0 0 20px rgba(139, 0, 0, 0.4);
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #444;
            text-decoration: none;
            font-size: 0.7rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .back-link:hover { color: #fff; }
    </style>
</head>
<body>

    <div class="admin-box">
        <span class="tsc-admin-logo">TSC. CONTROL</span>
        <h2>System Administrator</h2>

        <?php if(isset($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="ADMIN USERNAME" required>
            <input type="password" name="password" placeholder="ADMIN PASSWORD" required>
            <button type="submit" class="btn-admin">Enter Dashboard</button>
        </form>

        <a href="index.php" class="back-link">← Return to Store</a>
    </div>

<?php include 'global_footer.php'; ?>
</body>
</html>