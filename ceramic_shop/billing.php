<?php
session_start();
include 'db.php';

// MANDATORY LOGIN CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$grand_total = 0;
$items_list = [];
foreach ($_SESSION['cart'] as $id => $qty) {
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'");
    if ($product = mysqli_fetch_assoc($res)) {
        $grand_total += ($product['price'] * $qty);
        $items_list[] = $product['name'] . " (x" . $qty . ")";
    }
}

// --- ADDED: CRITICAL SESSION CAPTURE ---
// This ensures that the next pages (confirm_order and process_order) have the data
$_SESSION['email'] = $_SESSION['user_email'] ?? ''; // This pulls your logged-in user's email
$_SESSION['last_purchased_item'] = implode(", ", $items_list);
$_SESSION['last_price'] = number_format($grand_total, 2);
// ---------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Acquisition Details — TSC</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
            --glass: rgba(18, 18, 18, 0.9);
        }

        body { 
            background: var(--bg-dark); 
            color: #fff; 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            padding-bottom: 100px;
        }

        /* --- NAVIGATION --- */
        nav {
            padding: 25px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            background: var(--glass);
            backdrop-filter: blur(15px);
        }

        .logo { 
            font-family: 'Cinzel', serif; 
            color: var(--gold); 
            text-decoration: none; 
            letter-spacing: 4px; 
            font-size: 1.2rem;
        }

        /* --- BILLING LAYOUT --- */
        .container { 
            max-width: 1100px; 
            margin: 80px auto; 
            display: grid; 
            grid-template-columns: 1.8fr 1fr; 
            gap: 40px; 
            padding: 0 20px;
        }

        .box { 
            background: var(--card-bg); 
            padding: 50px; 
            border: 1px solid #1a1a1a; 
        }

        h2, h3 { 
            font-family: 'Cinzel', serif; 
            font-weight: 400; 
            letter-spacing: 2px; 
            color: var(--gold); 
            margin-bottom: 30px;
            font-size: 1rem;
            text-transform: uppercase;
        }

        input, textarea { 
            width: 100%; 
            padding: 18px; 
            margin-bottom: 20px; 
            background: #000; 
            border: 1px solid #222; 
            color: #fff; 
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            transition: 0.3s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--gold);
        }

        .summary-item {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 12px;
            display: block;
            letter-spacing: 0.5px;
        }

        .total-row {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #222;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-price {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            font-size: 1.2rem;
        }

        .btn { 
            width: 100%; 
            padding: 20px; 
            background: var(--gold); 
            color: #000; 
            border: none; 
            font-weight: 700; 
            font-family: 'Cinzel', serif;
            letter-spacing: 2px;
            cursor: pointer; 
            transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            margin-top: 10px;
        }

        .btn:hover { 
            background: #fff; 
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #555;
            text-decoration: none;
            font-size: 0.7rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .back-link:hover { color: var(--gold); }

    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo">TSC.</a>
    <div style="font-size: 0.7rem; letter-spacing: 2px; color: #555;">SECURE CHECKOUT</div>
</nav>

<div class="container">
    <div class="box">
        <h2>Shipping & Delivery</h2>
        <form id="billingForm" action="confirm_order.php" method="POST">
            <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
            <input type="text" id="phone" name="phone" placeholder="CONTACT NUMBER" required>
            <textarea id="address" name="address" rows="4" placeholder="FULL DELIVERY ADDRESS" required></textarea>
            
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            
            <button type="button" class="btn" onclick="payWithRazorpay()">FINALIZE ACQUISITION</button>
        </form>
        <a href="index.php" class="back-link">← Return to Collection</a>
    </div>

    <div class="box" style="background: #0d0d0d;">
        <h3>Order Summary</h3>
        <div style="margin-bottom: 40px;">
            <?php foreach($items_list as $i): ?>
                <span class="summary-item">• <?php echo htmlspecialchars($i); ?></span>
            <?php endforeach; ?>
        </div>
        
        <div class="total-row">
            <span style="font-size: 0.7rem; color: #555; letter-spacing: 1px;">TOTAL ACQUISITION</span>
            <span class="total-price">INR <?php echo number_format($grand_total, 2); ?></span>
        </div>
    </div>
</div>

<script>
function payWithRazorpay() {
    var name = document.getElementById('customer_name').value;
    var phone = document.getElementById('phone').value;
    var address = document.getElementById('address').value;

    if(!name || !phone || !address) {
        alert("Please ensure all delivery details are completed.");
        return;
    }

    var options = {
        "key": "ENTER YOUR API", 
        "amount": "<?php echo ($grand_total * 100); ?>", 
        "currency": "INR",
        "name": "The Ceramic Shop",
        "description": "Artisanal Acquisition",
        "image": "images/logo.png",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('billingForm').submit();
        },
        "prefill": {
            "name": name,
            "contact": phone
        },
        "theme": {
            "color": "#d4af37" 
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
}
</script>

<?php include 'global_footer.php'; ?>
</body>
</html>