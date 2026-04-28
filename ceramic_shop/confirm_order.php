<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

// --- DATA PERSISTENCE LOGIC ---
// If this is a new POST (from Razorpay), process and save to session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // 1. CAPTURE THE RAZORPAY PAYMENT ID
    $payment_id = isset($_POST['razorpay_payment_id']) ? mysqli_real_escape_string($conn, $_POST['razorpay_payment_id']) : 'N/A';
    
    $grand_total = 0;
    $items_array = [];

    // Safety check for cart existence to prevent warnings
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        // Calculate total and prepare summary from session cart
        foreach ($_SESSION['cart'] as $id => $qty) {
            $res = mysqli_query($conn, "SELECT name, price FROM products WHERE id = '$id'");
            if ($product = mysqli_fetch_assoc($res)) {
                $subtotal = $product['price'] * $qty;
                $grand_total += $subtotal;
                $items_array[] = $product['name'] . " (x" . $qty . ")";
            }
        }
    }

    $items_summary = implode(", ", $items_array);

    // 2. SAVE TO DATABASE
    $query = "INSERT INTO orders (user_id, items_summary, total_amount, payment_id) 
              VALUES ('$user_id', '$items_summary', '$grand_total', '$payment_id')";
    
    if (mysqli_query($conn, $query)) {
        
        // --- START NEW STOCK REDUCTION LOGIC ---
        // Loop through the cart again to decrease the vault inventory
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product_id = mysqli_real_escape_string($conn, $product_id);
                $quantity = (int)$quantity;
                
                // Deduct the purchased quantity from the products table
                $update_stock_query = "UPDATE products 
                                       SET stock = stock - $quantity 
                                       WHERE id = '$product_id' AND stock >= $quantity";
                
                mysqli_query($conn, $update_stock_query);
            }
        }
        // --- END NEW STOCK REDUCTION LOGIC ---

        // STORE DATA IN SESSION FOR PERSISTENCE ON REFRESH
        $_SESSION['last_confirmed_order'] = [
            'total' => $grand_total,
            'pay_id' => $payment_id
        ];
        unset($_SESSION['cart']); // Clear cart only after successful save
    }
} 
// IF NOT A POST, CHECK IF WE ALREADY HAVE ORDER DATA STORED IN SESSION
elseif (isset($_SESSION['last_confirmed_order'])) {
    $grand_total = $_SESSION['last_confirmed_order']['total'];
    $payment_id = $_SESSION['last_confirmed_order']['pay_id'];
}
else {
    // If accessed directly without a POST and no session data, redirect
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Acquisition Confirmed — TSC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@1,400..900&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
        }

        body { 
            background: var(--bg-dark); 
            color: #fff; 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .success-box { 
            background: var(--card-bg); 
            max-width: 550px; 
            padding: 60px; 
            border: 1px solid #1a1a1a;
            position: relative;
        }

        .success-box::before {
            content: '';
            position: absolute;
            top: 10px; right: 10px;
            width: 50px; height: 50px;
            border-top: 1px solid var(--gold);
            border-right: 1px solid var(--gold);
        }

        h1 { 
            font-family: 'Playfair Display', serif; 
            font-style: italic; 
            font-size: 3.5rem;
            margin: 0 0 10px 0;
            background: linear-gradient(to bottom, #fff, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .confirmation-text {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            letter-spacing: 2px;
            font-size: 0.8rem;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        p { color: #888; line-height: 1.6; font-size: 0.9rem; }

        .txn-id { 
            background: #000; 
            padding: 15px; 
            border: 1px solid #222;
            color: var(--gold); 
            font-family: monospace; 
            font-size: 0.85rem; 
            margin: 25px 0 40px;
            letter-spacing: 1px;
            word-break: break-all;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn { 
            flex: 1;
            padding: 18px; 
            text-decoration: none; 
            font-family: 'Cinzel', serif;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 2px;
            transition: 0.4s;
            text-transform: uppercase;
        }

        .btn-primary {
            background: var(--gold); 
            color: #000; 
        }

        .btn-secondary {
            background: transparent;
            color: #fff;
            border: 1px solid #333;
        }

        .btn:hover { 
            background: #fff; 
            color: #000;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="success-box">
        <div style="font-family: 'Cinzel'; font-size: 0.6rem; letter-spacing: 5px; color: #444; margin-bottom: 10px;">ORDER CONFIRMED</div>
        <h1>Success</h1>
        <div class="confirmation-text">The Acquisition is Complete</div>
        
        <p>Your order for <strong>INR <?php echo number_format($grand_total, 2); ?></strong> has been successfully processed and added to your private archives.</p>
        
        <div style="font-size: 0.65rem; color: #555; letter-spacing: 2px; margin-top: 30px;">REFERENCE ID</div>
        <div class="txn-id"><?php echo htmlspecialchars($payment_id); ?></div>
        
        <div class="btn-container">
            <a href="index.php" class="btn btn-primary">Return to Shop</a>
            <a href="order_history.php" class="btn btn-secondary">View Archives</a>
        </div>
    </div>
</body>
</html>