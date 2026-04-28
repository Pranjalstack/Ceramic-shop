<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

// Force login to see history
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch ONLY this user's orders
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
$result = mysqli_query($conn, $sql);

// --- SURGERY: SAFETY CHECK ---
// This prevents the "Fatal Error" if the table 'orders' doesn't exist yet
$result_count = 0;
if ($result) {
    $result_count = mysqli_num_rows($result);
}
// --- END OF SURGERY ---

// Calculate cart count for nav
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Archives — TSC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #d4af37;
            --neon-gold: rgba(212, 175, 55, 0.8);
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
            --glass: rgba(18, 18, 18, 0.85);
        }

        body { 
            background: var(--bg-dark);
            font-family: 'Inter', sans-serif; 
            margin: 0; color: #ffffff;
            overflow-x: hidden;
        }

        /* --- PREMIUM NAV --- */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            backdrop-filter: blur(20px); background: var(--glass);
            border-bottom: 1px solid rgba(255,255,255,0.05); padding: 20px 0;
        }

        .nav-wrapper {
            max-width: 1400px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center; padding: 0 40px;
        }

        .logo { 
            font-family: 'Cinzel', serif; font-size: 1.4rem; letter-spacing: 4px; 
            color: var(--gold); text-decoration: none;
        }

        .menu-links { display: flex; align-items: center; gap: 30px; }

        .menu-links a { 
            color: #fff; text-decoration: none; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;
        }

        .cart-pill {
            background: #fff; color: #000 !important;
            padding: 8px 20px; border-radius: 50px; font-weight: 700;
        }

        /* --- CONTENT --- */
        .container {
            max-width: 1100px; margin: 150px auto 100px; padding: 0 40px;
        }

        header h2 {
            font-family: 'Playfair Display', serif; font-size: 4rem; font-style: italic;
            margin: 0; background: linear-gradient(to bottom, #fff, #666);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .user-label {
            text-align: center; color: var(--gold); font-family: 'Cinzel';
            font-size: 0.7rem; letter-spacing: 3px; margin-top: 10px; margin-bottom: 60px;
        }

        /* --- GOLDEN NEON BOX --- */
        .history-card {
            background: var(--card-bg); 
            padding: 40px;
            border: 1px solid var(--gold);
            box-shadow: 
                0 0 10px var(--neon-gold),
                0 0 25px rgba(212, 175, 55, 0.2),
                inset 0 0 10px rgba(212, 175, 55, 0.1);
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            text-align: left; padding: 20px; border-bottom: 1px solid #333;
            color: var(--gold); font-family: 'Cinzel'; font-size: 0.7rem; letter-spacing: 2px;
        }

        td { padding: 25px 20px; border-bottom: 1px solid #1a1a1a; font-size: 0.9rem; }

        .status-pill {
            border: 1px solid var(--gold); color: var(--gold);
            padding: 4px 12px; border-radius: 50px; font-size: 0.65rem;
            font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            box-shadow: 0 0 8px var(--neon-gold);
        }

        .back-btn {
            display: inline-block; margin-top: 40px; color: #666;
            text-decoration: none; font-size: 0.75rem; letter-spacing: 1px;
            transition: 0.3s;
        }
        .back-btn:hover { color: var(--gold); }

    </style>
</head>
<body>

<nav>
    <div class="nav-wrapper">
        <a href="index.php" class="logo">TSC.</a>
        <div class="menu-links">
            <a href="index.php">Collection</a>
            <a href="logout.php">Sign Out</a>
            <span style="color: var(--gold); font-size: 0.7rem;">[ <?php echo htmlspecialchars($_SESSION['user_name']); ?> ]</span>
            <a href="cart.php" class="cart-pill">BAG (<?php echo $cart_count; ?>)</a>
        </div>
    </div>
</nav>

<div class="container">
    <header>
        <h2>The Archives</h2>
        <div class="user-label">ACQUISITIONS BY <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    </header>

    <div class="history-card">
        <table>
            <thead>
                <tr>
                    <th>REF CODE</th> <th>DATE</th>
                    <th>ITEMS</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result_count > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="color: var(--gold); font-family: 'Cinzel'; letter-spacing: 2px; font-weight: 700;">
                            <?php echo isset($row['order_ref_code']) ? $row['order_ref_code'] : '------'; ?>
                        </td> <td style="color: #666;">
                            <?php 
                            $date = isset($row['order_date']) ? $row['order_date'] : 'today';
                            echo date('d M Y', strtotime($date)); 
                            ?>
                        </td>
                        <td style="letter-spacing: 0.5px;">
                            <?php echo isset($row['items_summary']) ? htmlspecialchars($row['items_summary']) : 'Curated Items'; ?>
                        </td>
                        <td style="font-weight: 700; color: var(--gold);">
                            INR <?php echo number_format($row['total_amount'], 2); ?>
                        </td>
                        <td><span class="status-pill">Processing</span></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 60px; color: #444;">
                            The archives are currently empty.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="index.php" class="back-btn">← RETURN TO COLLECTION</a>
</div>
<?php include 'global_footer.php'; ?>
</body>
</html>