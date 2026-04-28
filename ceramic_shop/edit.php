<?php
include 'db.php';
session_start();

// IMPORTANT: Check for ADMIN session, not user session
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php"); // Redirect back to admin dashboard if not authorized
    exit();
}

// Get product details
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'");
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        die("Product not found.");
    }
} else {
    header("Location: admin.php?page=inventory");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Asset — TSC Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400;1,700&family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
        :root { 
            --gold: #c5a059; 
            --deep-red: #8b0000; 
            --dark-bg: #0a0a0a; 
            --card-bg: #111111;
            --text-dim: #a0a0a0;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--dark-bg); 
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-image: radial-gradient(circle at center, #1a0505 0%, #0a0a0a 100%);
        }

        .edit-card { 
            background: var(--card-bg); 
            width: 100%;
            max-width: 480px; 
            padding: 40px; 
            border-radius: 4px; 
            border: 1px solid rgba(197, 160, 89, 0.2);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            position: relative;
            margin: 20px;
        }

        .edit-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        h2 { 
            font-family: 'Cinzel', serif;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-align: center;
            color: var(--gold);
        }

        .form-group { margin-bottom: 25px; }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        label {
            display: block;
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            letter-spacing: 1px;
            margin-bottom: 8px;
            color: var(--text-dim);
            text-transform: uppercase;
        }

        input, textarea { 
            width: 100%; 
            padding: 12px; 
            background: #181818;
            border: 1px solid #222; 
            border-radius: 0; 
            box-sizing: border-box; 
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--deep-red);
            background: #1d1d1d;
        }

        /* Simplified file input area */
        .file-upload-box {
            background: #0f0f0f;
            padding: 15px;
            border: 1px dashed #333;
        }

        input[type="file"] {
            font-size: 0.8rem;
            padding: 5px;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .btn-update { 
            background: var(--deep-red); 
            color: white; 
            border: none; 
            padding: 15px; 
            width: 100%; 
            cursor: pointer; 
            font-family: 'Cinzel', serif;
            font-size: 0.9rem; 
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-update:hover { 
            background: #b30000;
            box-shadow: 0 0 15px rgba(139, 0, 0, 0.4);
        }

        .btn-back { 
            display: block; 
            text-align: center; 
            margin-top: 20px; 
            color: var(--text-dim); 
            text-decoration: none; 
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-back:hover { color: var(--gold); }

        span.id-badge {
            font-style: italic;
            font-family: 'Playfair Display', serif;
            color: var(--text-dim);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="edit-card">
    <h2>Modify Archive <span class="id-badge">#<?php echo $product['id']; ?></span></h2>
    
    <form action="update_logic.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        
        <div class="form-group">
            <label>Asset Designation</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Valuation (INR)</label>
                <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label>Inventory Stock</label>
                <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Upload New Archived Visual (Optional)</label>
            <div class="file-upload-box">
                <input type="file" name="product_image" accept="image/*">
            </div>
        </div>

        <div class="form-group">
            <label>Technical Specifications / History</label>
            <textarea name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <button type="submit" class="btn-update">Update Registry</button>
        <a href="admin.php?page=inventory" class="btn-back">Dismiss & Return to Vault</a>
    </form>
</div>

</body>
</html>