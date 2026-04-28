<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Security Check
if (!isset($_SESSION['admin_logged_in'])) {
    die("ACCESS DENIED: Administrative clearance required.");
}

// Logic to process data from the previous form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $unit_price = (float)$_POST['unit_price'];
    $quantity = (int)$_POST['quantity'];
    
    // Calculate total procurement cost
    $total_amount = $unit_price * $quantity;
    
    // YOUR KEY INTEGRATED
    $razorpay_key = "rzp_test_SIR6iwPgt3Lsg0"; 
} else {
    // Redirect back if accessed directly without POST data
    header("Location: admin.php?page=procurement");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorize Transfer — TSC Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #121212; color: #ccc; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .checkout-card { background: #1a1a1a; padding: 40px; border: 1px solid #d4af37; border-radius: 4px; width: 400px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h2 { font-family: 'Cinzel'; color: #d4af37; letter-spacing: 2px; margin-bottom: 5px; }
        .detail-row { display: flex; justify-content: space-between; margin: 15px 0; border-bottom: 1px solid #333; padding-bottom: 10px; font-size: 0.9rem; }
        .total-box { margin-top: 30px; padding: 20px; background: #222; border-radius: 4px; border: 1px inset #333; }
        .total-price { font-size: 1.8rem; color: #fff; font-weight: bold; margin-top: 5px; }
        #custom-pay-button { 
            background: #d4af37; color: #000; border: none; padding: 15px 30px; 
            font-family: 'Cinzel'; font-weight: bold; cursor: pointer;
            margin-top: 25px; width: 100%; transition: 0.3s; letter-spacing: 1px;
        }
        #custom-pay-button:hover { background: #f1c40f; transform: translateY(-2px); }
        .abort-link { color: #777; text-decoration: none; font-size: 0.7rem; display: block; margin-top: 20px; transition: 0.3s; }
        .abort-link:hover { color: #e74c3c; }
    </style>
</head>
<body>

<div class="checkout-card">
    <h2>AUTHORIZE TRANSFER</h2>
    <p style="font-size: 0.7rem; color: #777; margin-bottom: 30px;">VAULT PROCUREMENT PROTOCOL v2.1</p>

    <div class="detail-row">
        <span>Asset Type:</span>
        <span style="color: #fff;"><?php echo htmlspecialchars($product_name); ?></span>
    </div>
    <div class="detail-row">
        <span>Units to Restock:</span>
        <span style="color: #fff;"><?php echo $quantity; ?></span>
    </div>
    <div class="detail-row">
        <span>Unit Value:</span>
        <span style="color: #fff;">₹<?php echo number_format($unit_price, 2); ?></span>
    </div>

    <div class="total-box">
        <p style="margin: 0; font-size: 0.7rem; color: #d4af37; font-weight: bold;">TOTAL SETTLEMENT AMOUNT</p>
        <div class="total-price">₹<?php echo number_format($total_amount, 2); ?></div>
    </div>

    <button id="custom-pay-button">EXECUTE TRANSACTION</button>

    <form id="procurement-form" action="verify_procurement.php" method="POST">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <input type="hidden" name="added_stock" value="<?php echo $quantity; ?>">
    </form>
    
    <a href="admin.php?page=procurement" class="abort-link">ABORT PROTOCOL</a>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "<?php echo $razorpay_key; ?>", 
        "amount": "<?php echo ($total_amount * 100); ?>", // Amount in paise
        "currency": "INR",
        "name": "TSC Control Center",
        "description": "Restock Order: <?php echo addslashes($product_name); ?>",
        "image": "https://your-logo-url.com/logo.png", // Optional logo
        "handler": function (response){
            // 1. Capture the payment ID from Razorpay
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            
            // 2. Automatically submit the hidden form to update the database
            document.getElementById('procurement-form').submit();
        },
        "prefill": {
            "name": "TSC Admin",
            "email": "admin@tsc.com"
        },
        "theme": {
            "color": "#d4af37"
        },
        "modal": {
            "ondismiss": function(){
                console.log('Transaction cancelled by user.');
            }
        }
    };

    var rzp1 = new Razorpay(options);

    document.getElementById('custom-pay-button').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
</script>

</body>
</html>