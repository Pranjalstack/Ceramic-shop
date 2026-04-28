<?php
session_start();
include 'db.php';

// 1. Get the Product ID from the URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$pid = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Handle Review Submission
if (isset($_POST['submit_review'])) {
    $u_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $u_rating = $_POST['rating'];
    $u_comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $insert = "INSERT INTO reviews (product_id, user_name, rating, comment) 
               VALUES ('$pid', '$u_name', '$u_rating', '$u_comment')";
    mysqli_query($conn, $insert);
}

// 3. Fetch Product Details
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE id = '$pid'");
$product = mysqli_fetch_assoc($product_query);

// 4. Fetch Reviews
$reviews = mysqli_query($conn, "SELECT * FROM reviews WHERE product_id = '$pid' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['name']; ?> | Details</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fdfaf7; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .product-flex { display: flex; gap: 40px; border-bottom: 2px solid #eee; padding-bottom: 30px; }
        .product-image img { width: 350px; border-radius: 10px; }
        .info h1 { color: #6d4c41; margin-top: 0; }
        .price { font-size: 1.5rem; color: #8d6e63; font-weight: bold; }
        
        /* Review Styling */
        .review-box { margin-top: 40px; }
        .rev-card { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #8d6e63; }
        .stars { color: #f4c150; margin-bottom: 5px; }
        .form-box { background: #efebe9; padding: 20px; border-radius: 10px; margin-top: 30px; }
        input, textarea, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { background: #6d4c41; color: white; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" style="color: #8d6e63; text-decoration: none;">← Back to Shop</a>
    
    <div class="product-flex">
        <div class="product-image">
            <img src="images/<?php echo $product['image']; ?>" alt="Ceramic Product">
        </div>
        <div class="info">
            <h1><?php echo $product['name']; ?></h1>
            <p class="price">$<?php echo $product['price']; ?></p>
            <p><?php echo $product['description']; ?></p>
            <button class="btn">Add to Cart</button>
        </div>
    </div>

    <div class="review-box">
        <h2>Customer Reviews</h2>
        <?php while($row = mysqli_fetch_assoc($reviews)): ?>
            <div class="rev-card">
                <div class="stars"><?php echo str_repeat('★', $row['rating']); ?></div>
                <strong><?php echo htmlspecialchars($row['user_name']); ?></strong>
                <p><?php echo htmlspecialchars($row['comment']); ?></p>
            </div>
        <?php endwhile; ?>

        <div class="form-box">
            <h3>Share Your Experience</h3>
            <form method="POST">
                <input type="text" name="user_name" placeholder="Your Name" required>
                <select name="rating">
                    <option value="5">5 Stars - Love it!</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
                <textarea name="comment" rows="3" placeholder="Write your review here..."></textarea>
                <button type="submit" name="submit_review" class="btn">Post My Review</button>
            </form>
        </div>
    </div>
</div>

<?php include 'global_footer.php'; ?>
</body>
</html>