<?php
// 1. Setup - Connect to your existing ceramic_db
include 'db.php'; 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Get the product ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 3. Fetch specific product details
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<h2 style='color:white; text-align:center; margin-top:50px;'>Product not found. <a href='index.php' style='color:#d4af37;'>Return to Shop</a></h2>";
    exit();
}

// --- STOCK CHECK LOGIC ---
// Check how many of this specific item are already in the session cart
$in_cart_qty = 0;
if (isset($_SESSION['cart'][$product_id])) {
    $in_cart_qty = $_SESSION['cart'][$product_id];
}
$available_stock = $product['stock'];
$can_add_more = ($in_cart_qty < $available_stock);

// --- FETCH RATING DATA & COUNT ---
$user_rating = 0; 
$total_reviews = 0;

$count_query = "SELECT COUNT(*) AS total FROM product_reviews WHERE product_id = $product_id AND rating > 0";
$count_result = mysqli_query($conn, $count_query);
if ($count_result) {
    $count_row = mysqli_fetch_assoc($count_result);
    $total_reviews = $count_row['total'];
}

if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $rate_query = "SELECT rating FROM product_reviews WHERE product_id = $product_id AND user_id = $u_id AND rating > 0 LIMIT 1";
    $rate_result = mysqli_query($conn, $rate_query);
    if ($rate_result && $rate_row = mysqli_fetch_assoc($rate_result)) {
        $user_rating = $rate_row['rating']; 
    }
}

// --- IMAGE FIX ---
$img_name = !empty($product['image']) ? $product['image'] : $product['image_url'];
$display_image = "images/" . $img_name;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?> — TSC ARCHIVE</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #d4af37;
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
            --glass-bg: rgba(10, 10, 10, 0.75);
        }

        body { 
            background: var(--bg-dark); 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            color: #ffffff; 
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding-top: 100px; 
        }

        .glass-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
            padding: 20px 0;
            transition: 0.4s ease;
        }

        .nav-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
        }

        .logo {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            text-decoration: none;
            letter-spacing: 5px;
            font-size: 1.1rem;
            text-transform: uppercase;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-left: 30px;
            opacity: 0.7;
            transition: 0.3s;
        }

        .nav-links a:hover {
            opacity: 1;
            color: var(--gold);
        }

        .container { max-width: 1100px; width: 90%; margin: 40px auto; }

        .details-card { 
            display: flex; 
            gap: 60px; 
            background: var(--card-bg); 
            padding: 60px; 
            border: 1px solid rgba(212, 175, 55, 0.1);
            box-shadow: 0 40px 100px rgba(0,0,0,0.8);
            align-items: flex-start;
        }

        .product-image { flex: 1; }
        .product-image img { 
            width: 100%; 
            height: auto; 
            max-height: 600px;
            object-fit: contain;
            border: 1px solid #1a1a1a;
            filter: contrast(1.1);
            transition: 0.5s ease;
        }
        .product-image img:hover { border-color: var(--gold); }

        .product-info { flex: 1.2; }
        
        .brand-label { font-family: 'Cinzel', serif; color: var(--gold); letter-spacing: 5px; font-size: 0.7rem; margin-bottom: 10px; text-transform: uppercase; }
        .product-info h1 { font-family: 'Playfair Display', serif; font-size: 3.5rem; font-style: italic; font-weight: 400; color: #fff; margin: 0 0 15px 0; line-height: 1; }
        .product-info p { color: #888; line-height: 1.8; font-size: 0.95rem; margin-bottom: 25px; }
        .price { font-family: 'Inter', sans-serif; color: #fff; font-size: 1.8rem; font-weight: 300; margin-bottom: 40px; }

        .thank-you-note { background: rgba(46, 125, 50, 0.1); color: #81c784; padding: 12px 20px; border-radius: 4px; margin-bottom: 25px; font-size: 0.85rem; border: 1px solid rgba(46, 125, 50, 0.3); }

        .rating-system { display: flex; flex-direction: row-reverse; justify-content: flex-end; margin: 10px 0; }
        .rating-system input { display: none; }
        .rating-system label { font-size: 32px; color: #222; cursor: pointer; transition: 0.2s; }
        .rating-system label:hover,
        .rating-system label:hover ~ label,
        .rating-system input:checked ~ label { color: var(--gold); }
        .rating-system label:before { content: "★"; padding-right: 5px; }

        .review-count { color: #444; font-size: 0.7rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 20px; }

        .btn-submit-review { background: transparent; border: 1px solid var(--gold); color: var(--gold); padding: 10px 20px; border-radius: 0; cursor: pointer; font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase; transition: 0.3s; }
        .btn-submit-review:hover { background: var(--gold); color: #000; }

        .btn-add { background: var(--gold); color: #000; border: none; padding: 20px 40px; width: 100%; cursor: pointer; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; font-size: 0.8rem; transition: 0.3s; margin-top: 20px; }
        .btn-add:hover:not([disabled]) { background: #fff; transform: translateY(-5px); }

        .btn-sold-out { background: #222 !important; color: #555 !important; cursor: not-allowed !important; border: 1px solid #333 !important; transform: none !important; }
        
        .back-link { display: inline-block; margin-top: 30px; color: #444; text-decoration: none; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; border-bottom: 1px solid transparent; transition: 0.3s; }
        .back-link:hover { color: var(--gold); border-color: var(--gold); }

        .textarea-custom { width: 100%; background: transparent; border: 1px solid #333; color: #fff; padding: 15px; font-family: 'Inter', sans-serif; font-size: 0.85rem; margin-top: 15px; margin-bottom: 15px; resize: vertical; min-height: 80px; box-sizing: border-box; transition: 0.3s; }
        .textarea-custom:focus { border-color: var(--gold); outline: none; }
        .comments-container { margin-top: 60px; border-top: 1px solid #1a1a1a; padding-top: 40px; }
        .comments-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-style: italic; color: #fff; margin-bottom: 30px; }
        .comment-card { background: var(--card-bg); border: 1px solid #1a1a1a; padding: 25px; margin-bottom: 20px; }
        .comment-meta { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #222; padding-bottom: 10px; }
        .comment-author { font-family: 'Cinzel', serif; color: var(--gold); font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; }
        .comment-body { color: #aaa; font-size: 0.9rem; line-height: 1.6; }

        .curators-note {
            display: flex;
            margin: 100px 0;
            gap: 60px;
            padding: 40px;
            background: rgba(212, 175, 55, 0.02);
            border-left: 1px solid var(--gold);
        }

        .vertical-title {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            text-align: left;
            color: var(--gold);
            font-family: 'Cinzel', serif;
            font-size: 0.8rem;
            letter-spacing: 6px;
            text-transform: uppercase;
            opacity: 0.5;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 60px;
            width: 100%;
        }

        .poetic-desc h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-style: italic;
            margin-bottom: 20px;
            color: #fff;
        }

        .poetic-desc p {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            line-height: 1.8;
            color: #bbb;
            font-style: italic;
        }

        .technical-specs { border-top: 1px solid #1a1a1a; align-self: start; }
        .spec-row { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #1a1a1a; }
        .spec-label { font-family: 'Inter', sans-serif; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 2px; color: #555; }
        .spec-value { font-family: 'Inter', sans-serif; font-size: 0.8rem; color: var(--gold); text-align: right; }

        @media (max-width: 850px) { 
            .details-card { flex-direction: column; padding: 30px; } 
            .curators-note { flex-direction: column; }
            .content-grid { grid-template-columns: 1fr; }
            .vertical-title { writing-mode: horizontal-tb; transform: none; }
        }
    </style>
</head>
<body>

<nav class="glass-header">
    <div class="nav-container">
        <a href="index.php" class="logo">TSC Archive</a>
        <div class="nav-links">
            <a href="index.php">Archives</a>
            <a href="cart.php">Cart</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="details-card">
        <div class="product-image">
            <img src="<?php echo $display_image; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 style="<?php echo ($product['stock'] <= 0) ? 'filter: grayscale(1) opacity(0.4);' : ''; ?>">
        </div>

        <div class="product-info">
            <div class="brand-label">The Private Collection</div>
            
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="thank-you-note">✨ Your appreciation has been archived. Thank you for rating.</div>
            <?php endif; ?>

            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>

            <p style="margin-bottom: 5px; font-size: 0.7rem; letter-spacing: 1px; color: #555; text-transform: uppercase;">Rate this piece:</p>
            
            <form action="save_rating.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="rating-system">
                    <input type="radio" id="star5" name="rating" value="5" <?php echo ($user_rating == 5) ? 'checked' : ''; ?>><label for="star5"></label>
                    <input type="radio" id="star4" name="rating" value="4" <?php echo ($user_rating == 4) ? 'checked' : ''; ?>><label for="star4"></label>
                    <input type="radio" id="star3" name="rating" value="3" <?php echo ($user_rating == 3) ? 'checked' : ''; ?>><label for="star3"></label>
                    <input type="radio" id="star2" name="rating" value="2" <?php echo ($user_rating == 2) ? 'checked' : ''; ?>><label for="star2"></label>
                    <input type="radio" id="star1" name="rating" value="1" <?php echo ($user_rating == 1) ? 'checked' : ''; ?>><label for="star1"></label>
                </div>
                
                <div class="review-count">
                    (<?php echo $total_reviews; ?> <?php echo ($total_reviews == 1) ? 'Archive Rating' : 'Archive Ratings'; ?>)
                </div>
                <button type="submit" name="submit_rating" class="btn-submit-review">Update Rating</button>
            </form>

            <form action="save_rating.php" method="POST" style="margin-top: 15px;">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <textarea name="review_text" class="textarea-custom" placeholder="Share your thoughts or feedback on this piece..."></textarea>
                
                <button type="submit" name="submit_review" class="btn-submit-review" style="border-color: #888; color: #aaa;">Submit Written Impression</button>
            </form>

            <div style="margin-top: 40px; border-top: 1px solid #1a1a1a; padding-top: 20px;">
                
                <?php if ($product['stock'] > 0): ?>
                    <?php if ($can_add_more): ?>
                        <a href="manage_cart.php?id=<?php echo $product['id']; ?>&action=add" style="text-decoration:none;">
                            <button class="btn-add">Acquire Piece</button>
                        </a>
                        <p style="color: #444; font-size: 0.6rem; letter-spacing: 1px; margin-top: 10px; text-transform: uppercase; text-align: center;">Vault Count: <?php echo $product['stock']; ?> available</p>
                    <?php else: ?>
                        <button class="btn-add btn-sold-out" disabled>Maximum Reached</button>
                        <p style="color: #d4af37; font-size: 0.65rem; letter-spacing: 1px; margin-top: 10px; text-transform: uppercase; text-align: center;">You have all <?php echo $product['stock']; ?> available units in your cart</p>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn-add btn-sold-out" disabled>Sold Out</button>
                    <p style="color: #666; font-size: 0.65rem; letter-spacing: 1px; margin-top: 10px; text-transform: uppercase; text-align: center;">This piece is currently unavailable</p>
                <?php endif; ?>
                
                <br>
                <a href="index.php" class="back-link">← Return to Archives</a>
            </div>
        </div>
    </div>

    <section class="curators-note">
        <div class="vertical-title">
            <span>ARCHIVE — PROMPT ID: <?php echo $product['id'] * 123; ?></span>
        </div>
        <div class="content-grid">
            <div class="poetic-desc">
                <h3>The Curator's Note</h3>
                <p>
                    "This silhouette serves as a study in balance. Every curve is deliberate, 
                    intended to bridge the gap between ancient stone craft and the digital 
                    landscape. A relic of the future, preserved for the present."
                </p>
            </div>
            <div class="technical-specs">
                <div class="spec-row">
                    <span class="spec-label">Form Category</span>
                    <span class="spec-value">Functional Sculpture</span>
                </div>
                <div class="spec-row">
                    <span class="spec-label">Texture</span>
                    <span class="spec-value">Wabi-Sabi Matte</span>
                </div>
                <div class="spec-row">
                    <span class="spec-label">Firing Method</span>
                    <span class="spec-value">Electric Kiln (1280°C)</span>
                </div>
                <div class="spec-row">
                    <span class="spec-label">Glaze DNA</span>
                    <span class="spec-value">Mineral Oxides</span>
                </div>
            </div>
        </div>
    </section>

    <div class="comments-container">
        <h2 class="comments-title">Collector Impressions</h2>
        <?php
        $reviews_query = "SELECT * FROM product_reviews WHERE product_id = $product_id AND review_text IS NOT NULL AND review_text != '' ORDER BY id DESC";
        $reviews_result = mysqli_query($conn, $reviews_query);
        
        if ($reviews_result && mysqli_num_rows($reviews_result) > 0) {
            while ($rev = mysqli_fetch_assoc($reviews_result)) {
                echo '<div class="comment-card">';
                echo '  <div class="comment-meta">';
                echo '      <div class="comment-author">Collector #' . htmlspecialchars($rev['user_id']) . '</div>';
                echo '  </div>';
                echo '  <div class="comment-body">' . htmlspecialchars($rev['review_text']) . '</div>';
                echo '</div>';
            }
        } else {
            echo '<p style="color: #666; font-size: 0.85rem; letter-spacing: 1px; font-style: italic;">No written impressions have been archived for this piece yet.</p>';
        }
        ?>
    </div>
</div>

<?php include 'global_footer.php'; ?>
</body>
</html>