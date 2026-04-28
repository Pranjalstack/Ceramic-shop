<?php
// 1. Maintain existing session and DB logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// 2. Calculate Total for Razorpay & Display
$total_amount = 0;
$cart_items = [];
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
        if ($product = mysqli_fetch_assoc($res)) {
            $product['quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;
            $total_amount += $product['subtotal'];
            $cart_items[] = $product;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YOUR BAG — THE CERAMIC SHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@200;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #d4af37;
            --noir: #080808;
            --card: #111111;
            --border: #222;
        }

        body { 
            background: var(--noir); color: #fff; font-family: 'Inter', sans-serif; 
            margin: 0; padding-top: 100px;
        }

        /* --- SHARED LUXURY NAV --- */
        nav {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(8, 8, 8, 0.95); backdrop-filter: blur(15px);
            border-bottom: 1px solid #1a1a1a; padding: 25px 0;
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto; display: flex;
            justify-content: space-between; align-items: center; padding: 0 40px;
        }
        .logo { font-family: 'Cinzel', serif; font-size: 1.5rem; color: var(--gold); text-decoration: none; letter-spacing: 4px; }
        .nav-links a { color: #fff; text-decoration: none; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; }

        /* --- CART CONTAINER --- */
        .cart-wrapper { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        
        .cart-header { 
            border-bottom: 1px solid var(--border); padding-bottom: 20px; margin-bottom: 40px;
            display: flex; justify-content: space-between; align-items: flex-end;
        }
        .cart-header h1 { font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 400; font-style: italic; margin: 0; }

        /* --- TABLE STYLING --- */
        table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
        th { text-align: left; font-size: 0.6rem; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); padding-bottom: 20px; border-bottom: 1px solid var(--border); }
        
        .cart-item { border-bottom: 1px solid #151515; transition: 0.3s; }
        .cart-item:hover { background: #0c0c0c; }
        
        td { padding: 30px 0; }
        
        .product-info { display: flex; align-items: center; gap: 20px; }
        .product-info img { width: 80px; height: 100px; object-fit: cover; border: 1px solid #222; }
        .product-name { font-family: 'Cinzel', serif; font-size: 1rem; letter-spacing: 1px; }

        .qty-controls { display: flex; align-items: center; gap: 15px; }
        .qty-btn { color: #fff; text-decoration: none; font-size: 1.2rem; opacity: 0.5; transition: 0.3s; }
        .qty-btn:hover { opacity: 1; color: var(--gold); }

        .remove-link { font-size: 0.6rem; letter-spacing: 1px; color: #ff4444; text-decoration: none; text-transform: uppercase; }

        /* --- SUMMARY SECTION --- */
        .cart-summary { 
            background: #0c0c0c; padding: 40px; border: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .total-label { font-family: 'Cinzel', serif; font-size: 0.8rem; letter-spacing: 3px; opacity: 0.6; }
        .total-value { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--gold); }

        /* --- CHECKOUT BUTTON (Razorpay Link) --- */
        .checkout-btn { 
            background: var(--gold); color: #000; padding: 20px 60px; 
            text-decoration: none; font-weight: 700; font-size: 0.8rem; 
            letter-spacing: 3px; text-transform: uppercase; transition: 0.3s;
            display: inline-block; border: none; cursor: pointer;
        }
        .checkout-btn:hover { background: #fff; transform: translateY(-3px); }
        
        .empty-msg { text-align: center; padding: 100px 0; font-family: 'Playfair Display'; font-style: italic; font-size: 1.5rem; opacity: 0.4; }

    </style>
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="index.php" class="logo">TSC.</a>
        <div class="nav-links">
            <a href="index.php">Continue Shopping</a>
        </div>
    </div>
</nav>

<div class="cart-wrapper">
    <div class="cart-header">
        <h1>Your Selection</h1>
        <span style="font-size: 0.7rem; letter-spacing: 2px; opacity: 0.5;"><?php echo count($cart_items); ?> PIECES</span>
    </div>

    <?php if (!empty($cart_items)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr class="cart-item">
                        <td>
                            <div class="product-info">
                               <?php 
    $img_value = !empty($item['image']) ? $item['image'] : $item['image_url'];
    $final_src = (strpos($img_value, 'images/') === false && strpos($img_value, 'http') === false) ? "images/" . $img_value : $img_value;
?>
<img src="<?php echo htmlspecialchars($final_src); ?>" alt="">
                                <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="qty-controls">
                                <a href="manage_cart.php?id=<?php echo $item['id']; ?>&action=decrease" class="qty-btn">—</a>
                                <span><?php echo $item['quantity']; ?></span>
                                <a href="manage_cart.php?id=<?php echo $item['id']; ?>&action=increase" class="qty-btn">+</a>
                            </div>
                        </td>
                        <td style="font-weight: 600;">₹<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td>
                            <a href="manage_cart.php?id=<?php echo $item['id']; ?>&action=remove" class="remove-link">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div>
                <div class="total-label">Estimated Total</div>
                <div class="total-value">₹<?php echo number_format($total_amount, 2); ?></div>
            </div>
            <div>
                <a href="billing.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-msg">
            Your archive is currently empty.
            <br><br>
            <a href="index.php" style="color: var(--gold); font-size: 1rem; font-family: 'Inter'; text-transform: uppercase; letter-spacing: 2px;">Return to Collection</a>
        </div>
    <?php endif; ?>
</div>

<footer style="margin-top: 100px; padding: 60px; text-align: center; opacity: 0.2; font-size: 0.6rem; letter-spacing: 4px;">
    &copy; 2026 THE CERAMIC SHOP &bull; AGRA
</footer>

<?php include 'global_footer.php'; ?>
</body>
</html>