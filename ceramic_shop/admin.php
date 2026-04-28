<?php
include 'db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in'])) {
    // header("Location: admin_login.php"); 
}
// --- SURGERY: SAFETY CHECKS FOR QUERIES ---
// Total Products
$prod_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$total_products = ($prod_result) ? mysqli_fetch_assoc($prod_result)['count'] : 0;
// Total Reviews (Table might be missing/broken)
$rev_check = mysqli_query($conn, "SHOW TABLES LIKE 'product_reviews'");
$total_reviews = 0;
$avg_rating = 0;
if (mysqli_num_rows($rev_check) > 0) {
    $rev_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM product_reviews");
    $total_reviews = ($rev_res) ? mysqli_fetch_assoc($rev_res)['count'] : 0;
    $avg_res = mysqli_query($conn, "SELECT AVG(rating) as avg FROM product_reviews");
    $avg_rating = ($avg_res) ? mysqli_fetch_assoc($avg_res)['avg'] : 0;
}
// Fetch Data for Dashboard Charts
$chart_labels = [];
$chart_prices = [];
$product_query = mysqli_query($conn, "SELECT name, price FROM products LIMIT 10");
if ($product_query) {
    while($p = mysqli_fetch_assoc($product_query)) {
        $chart_labels[] = $p['name'];
        $chart_prices[] = $p['price'];
    }
}
// FIX: Added check to see if 'orders' table exists to avoid Fatal Error
$sales_labels = [];
$sales_totals = [];
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
if(mysqli_num_rows($table_check) > 0) {
    $sales_query = mysqli_query($conn, "SELECT DATE(order_date) as date, SUM(total_amount) as total 
                                        FROM orders GROUP BY DATE(order_date) 
                                        ORDER BY date DESC LIMIT 7");
    if($sales_query) {
        while($s = mysqli_fetch_assoc($sales_query)) {
            $sales_labels[] = date("M d", strtotime($s['date']));
            $sales_totals[] = $s['total'];
        }
    }
}
$sales_labels = array_reverse($sales_labels);
$sales_totals = array_reverse($sales_totals);
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Control Center — TSC Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { 
            --accent-red: #8e1a1a; 
            --accent-yellow: #d4af37; 
            --deep-grey: #1a1a1a;
            --mid-grey: #2a2a2a;
            --light-grey: #3a3a3a;
            --border-muted: #333;
            --text-silver: #cccccc;
            --text-dim: #777;
            --bg-body: #121212;
        }
        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: var(--bg-body); color: var(--text-silver); min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: var(--deep-grey); position: fixed; padding: 40px 0; border-right: 1px solid var(--border-muted); z-index: 10; }
        .sidebar h2 { text-align: center; font-family: 'Cinzel', serif; font-size: 1rem; letter-spacing: 4px; margin-bottom: 50px; color: var(--accent-red); text-transform: uppercase; }
        .nav-btn { width: 100%; padding: 18px 35px; border: none; background: none; color: var(--text-dim); text-align: left; cursor: pointer; font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 2px; transition: 0.3s; display: block; text-decoration: none; box-sizing: border-box; }
        .nav-btn:hover { color: #fff; background: var(--mid-grey); }
        .nav-btn.active { color: #fff; background: var(--mid-grey); border-right: 3px solid var(--accent-red); }
        .main-content { margin-left: 260px; flex: 1; padding: 60px; position: relative; }
        .btn-logout { position: absolute; top: 40px; right: 60px; background: var(--accent-red); color: #fff; border: none; padding: 8px 18px; text-decoration: none; font-family: 'Cinzel', serif; font-size: 0.65rem; letter-spacing: 1px; transition: 0.3s; border-radius: 2px; }
        .btn-logout:hover { background: #b12020; }
        h2.page-title { font-family: 'Playfair Display', serif; font-style: italic; font-size: 2.5rem; margin-bottom: 45px; font-weight: 500; color: #fff; }
        .stats-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: var(--deep-grey); padding: 35px; border: 1px solid var(--border-muted); border-radius: 4px; text-align: center; }
        .stat-card h3 { margin: 0; color: #fff; font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 400; font-style: italic; }
        .stat-card p { margin: 12px 0 0; color: var(--accent-red); font-family: 'Cinzel', serif; letter-spacing: 2px; font-size: 0.7rem; }
        .charts-wrapper { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        .chart-container, .section-card { background: var(--deep-grey); padding: 35px; border: 1px solid var(--border-muted); border-radius: 4px; }
        .chart-container h3, .section-card h3 { font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 2px; color: var(--accent-red); margin-bottom: 30px; text-transform: uppercase; }
        .chart-container.rating-box h3 { color: var(--accent-yellow); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-family: 'Cinzel', serif; font-size: 0.65rem; color: var(--text-dim); margin-bottom: 8px; }
        input, textarea { width: 100%; padding: 15px; background: var(--mid-grey); border: 1px solid var(--border-muted); color: #fff; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        input:focus { outline: none; border-color: var(--accent-red); }
        .btn-add { background: var(--accent-red); color: white; border: none; padding: 15px 35px; cursor: pointer; font-family: 'Cinzel', serif; letter-spacing: 2px; font-size: 0.75rem; transition: 0.3s; }
        .btn-add:hover { background: #b12020; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { border-bottom: 2px solid var(--mid-grey); color: var(--text-dim); padding: 15px; text-align: left; font-family: 'Cinzel', serif; font-size: 0.7rem; }
        td { padding: 20px 15px; border-bottom: 1px solid var(--border-muted); font-size: 0.9rem; }
        .status-pill { padding: 4px 10px; font-family: 'Cinzel', serif; font-size: 0.65rem; background: var(--light-grey); border-radius: 2px; }
        .alert { padding: 15px; margin-bottom: 30px; font-family: 'Inter', sans-serif; font-size: 0.8rem; border-radius: 4px; }
        .alert-success { background: #1b2e1b; color: #7cfc00; border: 1px solid #2e4d2e; }
        .alert-danger { background: #2e1b1b; color: #ff4d4d; border: 1px solid #4d2e2e; }
        .backup-list th { color: var(--accent-yellow); border-bottom: 1px solid var(--light-grey); }
        .backup-list td { font-size: 0.75rem; color: var(--text-silver); padding: 10px 15px; }
        /* Pagination Style */
        .pagination { display: flex; gap: 8px; margin-top: 25px; justify-content: center; }
        .pg-link { padding: 8px 14px; background: var(--mid-grey); color: var(--text-dim); text-decoration: none; font-family: 'Cinzel'; font-size: 0.7rem; transition: 0.3s; border-radius: 2px; }
        .pg-link:hover { background: var(--light-grey); color: #fff; }
        .pg-link.active { background: var(--accent-red); color: #fff; }
        /* Stock Status Styles */
        .stock-tag { font-family: 'Cinzel', serif; font-size: 0.6rem; padding: 3px 8px; border-radius: 2px; display: inline-block; min-width: 60px; text-align: center; }
        .stock-critical { background: rgba(142, 26, 26, 0.2); color: #ff4d4d; border: 1px solid var(--accent-red); }
        .stock-ok { background: rgba(212, 175, 55, 0.1); color: var(--accent-yellow); border: 1px solid var(--accent-yellow); }
        /* NEW: Stock Adjuster Styles */
        .stock-adjuster { display: flex; align-items: center; gap: 10px; }
        .arrow-btn { 
            background: var(--mid-grey); 
            color: var(--text-silver); 
            border: 1px solid var(--border-muted); 
            cursor: pointer; 
            padding: 2px 8px; 
            font-size: 0.8rem; 
            border-radius: 2px;
            transition: 0.2s;
        }
        .arrow-btn:hover { background: var(--light-grey); color: #fff; border-color: var(--accent-yellow); }
        /* --- FEEDBACK PAGE STYLES --- */
        .feedback-item { background: var(--mid-grey); border-left: 3px solid var(--accent-yellow); padding: 20px; margin-bottom: 15px; position: relative; }
        .feedback-meta { font-family: 'Cinzel'; font-size: 0.65rem; color: var(--accent-yellow); margin-bottom: 10px; display: flex; justify-content: space-between; }
        .feedback-text { font-style: italic; color: #fff; line-height: 1.6; font-size: 0.9rem; }
        .feedback-product { font-size: 0.7rem; color: var(--text-dim); margin-top: 10px; text-transform: uppercase; letter-spacing: 1px; }
        /* --- PROCUREMENT GRID STYLES --- */
        .procurement-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        .procurement-card { background: var(--deep-grey); border: 1px solid var(--border-muted); border-radius: 4px; overflow: hidden; transition: 0.3s; }
        .procurement-card:hover { border-color: var(--accent-yellow); }
        .procurement-card img { width: 100%; height: 200px; object-fit: cover; }
        .procurement-info { padding: 20px; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>TSC. Control</h2>
    <a href="admin.php?page=dashboard" class="nav-btn <?php echo $page == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
    <a href="admin.php?page=inventory" class="nav-btn <?php echo $page == 'inventory' ? 'active' : ''; ?>">Inventory</a>
    <a href="admin.php?page=procurement" class="nav-btn <?php echo $page == 'procurement' ? 'active' : ''; ?>">Procurement</a>
    <a href="admin.php?page=orders" class="nav-btn <?php echo $page == 'orders' ? 'active' : ''; ?>">Orders</a>
    <a href="admin.php?page=feedbacks" class="nav-btn <?php echo $page == 'feedbacks' ? 'active' : ''; ?>">Feedbacks</a>
    <a href="index.php" class="nav-btn" style="margin-top: 40px; border-top: 1px solid var(--border-muted);">Public Shop</a>
</div>
<div class="main-content">
    <a href="logout.php" class="btn-logout">Logout (Admin)</a>
    <?php if(isset($_GET['status'])): ?>
        <?php if($_GET['status'] == 'success'): ?>
            <div id="success-banner" style="background: rgba(212, 175, 55, 0.1); border: 1px solid #d4af37; color: #d4af37; padding: 20px; margin-bottom: 30px; text-align: center; font-family: 'Cinzel', serif; position: relative; animation: fadeIn 0.5s ease-in-out;">
                <strong style="letter-spacing: 2px;">✓ TRANSACTION AUTHORIZED</strong>
                <p style="margin: 5px 0 0; font-family: 'Inter', sans-serif; font-size: 0.8rem; color: #ccc;">
                    Payment ID: <?php echo htmlspecialchars($_GET['pid'] ?? 'N/A'); ?> | Inventory has been replenished.
                </p>
                <button onclick="document.getElementById('success-banner').style.display='none'" style="position: absolute; right: 10px; top: 10px; background: none; border: none; color: #d4af37; cursor: pointer; font-size: 1.2rem;">×</button>
            </div>
            <script>
                setTimeout(function() {
                    var banner = document.getElementById('success-banner');
                    if(banner) {
                        banner.style.transition = "opacity 0.5s";
                        banner.style.opacity = '0';
                        setTimeout(function() { banner.style.display = 'none'; }, 500);
                    }
                }, 7000);
            </script>
        <?php elseif($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-success">PURGE COMPLETE: Record has been removed from the database.</div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($page == 'dashboard'): ?>
        <h2 class="page-title">Shop Overview</h2>
        <div class="section-card" style="margin-bottom: 40px; border: 1px solid var(--accent-yellow);">
            <h3 style="color: var(--accent-yellow);">Vault Security & Backups</h3>
            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 25px;">
                <a href="db_backup.php" class="btn-add" style="background: var(--accent-yellow); color: #000; text-decoration: none;">GENERATE NEW BACKUP</a>
                <p style="font-size: 0.7rem; color: var(--text-dim); font-family: 'Cinzel';">
                    LATEST POINT: <?php 
                        $files = glob("backups/*.sql");
                        if ($files) { echo date("d M Y - H:i", filemtime(end($files))); } 
                        else { echo "NO ARCHIVES FOUND"; }
                    ?>
                </p>
            </div>
            <div style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 4px;">
                <h4 style="font-family: 'Cinzel'; font-size: 0.6rem; color: var(--text-dim); margin: 0 0 10px 0;">RECENT SNAPSHOTS (LAST 3)</h4>
                <table class="backup-list">
                    <?php
                    $backups = array_reverse(glob("backups/*.sql"));
                    $display_backups = array_slice($backups, 0, 3);
                    if($display_backups):
                        foreach ($display_backups as $file): ?>
                            <tr>
                                <td><?php echo basename($file); ?></td>
                                <td style="text-align: right;"><?php echo round(filesize($file) / 1024, 1); ?> KB</td>
                            </tr>
                        <?php endforeach;
                    else: echo "<tr><td colspan='2' style='color:var(--text-dim)'>No backups available.</td></tr>"; endif; ?>
                </table>
            </div>
        </div>
        <div class="stats-container">
            <div class="stat-card"><h3><?php echo $total_products; ?></h3><p>Active Archives</p></div>
            <div class="stat-card"><h3><?php echo $total_reviews; ?></h3><p>Client Feedback</p></div>
            <div class="stat-card"><h3><?php echo number_format($avg_rating, 1); ?></h3><p>Quality Rating</p></div>
        </div>
        <div class="charts-wrapper">
            <div class="chart-container">
                <h3>Price Distribution</h3>
                <canvas id="priceChart"></canvas>
            </div>
            <div class="chart-container rating-box">
                <h3>Global Rating</h3>
                <canvas id="ratingChart"></canvas>
            </div>
        </div>
    <?php elseif ($page == 'inventory'): ?>
        <h2 class="page-title">Vault Management</h2>
        <div class="section-card" style="margin-bottom: 40px;">
            <h3>Append New Record</h3>
            <form action="add_product_logic.php" method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <input type="text" name="name" placeholder="Item Designation" required>
                    <input type="number" name="price" placeholder="Valuation (INR)" step="0.01" required>
                    <input type="number" name="stock" placeholder="Initial Stock" min="0" required>
                </div>
                <div class="form-group"><textarea name="description" placeholder="Technical Specifications / Description" rows="3" required></textarea></div>
                <div class="form-group">
                    <label>Digital Asset Upload</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn-add">Authorize Upload</button>
            </form>
        </div>
        <div class="section-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Logged Inventory</h3>
                <form action="admin.php" method="GET" style="display: flex; gap: 10px;">
                    <input type="hidden" name="page" value="inventory">
                    <input type="text" name="search" placeholder="Search Designations..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                           style="padding: 8px 15px; width: 250px; font-size: 0.75rem; background: var(--deep-grey);">
                    <button type="submit" class="btn-add" style="padding: 8px 20px;">Filter</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr><th>Ref ID</th><th>Designation</th><th>Valuation</th><th>Status / Stock</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php
                    $limit = 10;
                    $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
                    $offset = ($p - 1) * $limit;
                    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
                    $where_clause = $search ? "WHERE name LIKE '%$search%'" : "";
                    $total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM products $where_clause");
                    $total_items = mysqli_fetch_assoc($total_res)['total'];
                    $total_pages = ceil($total_items / $limit);
                    $result = mysqli_query($conn, "SELECT * FROM products $where_clause LIMIT $limit OFFSET $offset");
                    if($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)): 
                            $stock_lvl = isset($row['stock']) ? $row['stock'] : 0;
                        ?>
                        <tr>
                            <td style="color: var(--text-dim); font-size: 0.8rem;">#<?php echo $row['id']; ?></td>
                            <td style="font-family: 'Playfair Display'; font-weight: 500;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td style="color: var(--accent-red);">₹<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <div class="stock-adjuster">
                                    <button class="arrow-btn" onclick="updateStock(<?php echo $row['id']; ?>, 'decrease')">▼</button>
                                    <span id="stock-count-<?php echo $row['id']; ?>" class="stock-tag <?php echo $stock_lvl <= 5 ? 'stock-critical' : 'stock-ok'; ?>">
                                        <?php echo $stock_lvl; ?> IN VAULT
                                    </span>
                                    <button class="arrow-btn" onclick="updateStock(<?php echo $row['id']; ?>, 'increase')">▲</button>
                                </div>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" style="color: #fff; text-decoration: none; font-family: 'Cinzel'; font-size: 0.7rem; margin-right: 15px; border-bottom: 1px solid var(--accent-red);">Modify</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" style="color: var(--accent-red); text-decoration: none; font-family: 'Cinzel'; font-size: 0.7rem;" onclick="return confirm('Purge this record?')">Purge</a>
                            </td>
                        </tr>
                        <?php endwhile;
                    } else {
                        echo '<tr><td colspan="5" style="text-align:center; padding: 30px; color: var(--text-dim);">No matching records found in the vault.</td></tr>';
                    } ?>
                </tbody>
            </table>
            <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="admin.php?page=inventory&p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                       class="pg-link <?php echo ($i == $p) ? 'active' : ''; ?>">
                       <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php elseif ($page == 'procurement'): ?>
        <h2 class="page-title">Resource Acquisition</h2>
        <p style="color: var(--text-dim); font-family: 'Cinzel'; font-size: 0.7rem; letter-spacing: 1px; margin-bottom: 40px;">Select archive pieces to restock. Razorpay authorization required for all transfers.</p>
        <div class="procurement-grid">
            <?php
            $proc_result = mysqli_query($conn, "SELECT * FROM products ORDER BY name ASC");
            if($proc_result && mysqli_num_rows($proc_result) > 0) {
                while($row = mysqli_fetch_assoc($proc_result)): ?>
                    <div class="procurement-card">
                        <img src="images/<?php echo $row['image_url']; ?>" alt="Product">
                        <div class="procurement-info">
                            <h4 style="font-family: 'Playfair Display'; font-size: 1.1rem; margin: 0 0 10px 0; color: #fff;"><?php echo htmlspecialchars($row['name']); ?></h4>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <span style="color: var(--accent-yellow); font-family: 'Inter'; font-weight: 600;">₹<?php echo number_format($row['price'], 2); ?></span>
                                <span style="font-size: 0.65rem; color: var(--text-dim); font-family: 'Cinzel';">STOCK: <?php echo $row['stock']; ?></span>
                            </div>
                            <form action="initiate_restock.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                                <input type="hidden" name="unit_price" value="<?php echo $row['price']; ?>">
                                <div style="display: flex; gap: 10px;">
                                    <input type="number" name="quantity" value="10" min="1" required 
                                           style="width: 70px; padding: 10px; text-align: center; font-size: 0.8rem; background: var(--bg-body);">
                                    <button type="submit" class="btn-add" style="flex: 1; padding: 10px; font-size: 0.65rem; background: var(--accent-yellow); color: #000;">
                                        INITIATE RESTOCK
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile;
            } ?>
        </div>
    <?php elseif ($page == 'orders'): ?>
        <h2 class="page-title">Order Ledger</h2>
        <div class="chart-container" style="margin-bottom:40px; height: 350px;">
            <h3>Acquisition Trend (7 Days)</h3>
            <canvas id="salesTrendChart"></canvas>
        </div>

        <div class="section-card">
            <h3>Recent Transactions</h3>
            <table>
                <thead>
                    <tr><th>Entry #</th><th>Client Entity</th><th>Amount</th><th>Timestamp</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php
                    if(mysqli_num_rows($table_check) > 0) {
                        // --- PAGINATION FOR ORDERS ---
                        $ord_limit = 10;
                        $ord_p = isset($_GET['p']) ? (int)$_GET['p'] : 1;
                        $ord_offset = ($ord_p - 1) * $ord_limit;
                        $total_ord_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
                        $total_ord_items = mysqli_fetch_assoc($total_ord_res)['total'];
                        $total_ord_pages = ceil($total_ord_items / $ord_limit);

                        $orders_query = "SELECT orders.*, users.name as fullname FROM orders 
                                         LEFT JOIN users ON orders.user_id = users.id 
                                         ORDER BY orders.order_date DESC 
                                         LIMIT $ord_limit OFFSET $ord_offset";
                        $orders = mysqli_query($conn, $orders_query);
                        if($orders && mysqli_num_rows($orders) > 0):
                            while($o = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td>#<?php echo $o['id']; ?></td>
                                <td><?php echo htmlspecialchars($o['fullname'] ?? 'Guest Entity'); ?></td>
                                <td style="color: var(--accent-red);">₹<?php echo number_format($o['total_amount'], 2); ?></td>
                                <td><?php echo date("d M Y", strtotime($o['order_date'])); ?></td>
                                <td>
                                    <span class="status-pill" style="color: <?php echo ($o['status'] == 'Pending') ? '#ffcc00' : '#7cfc00'; ?>;">
                                        <?php echo strtoupper($o['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile;
                        else: echo '<tr><td colspan="5" style="text-align:center; color:var(--text-dim); padding: 40px;">No transaction history found.</td></tr>'; endif;
                    } else { echo '<tr><td colspan="5" style="text-align:center; color:var(--accent-red); padding: 40px;">CRITICAL: Connection to [orders] table failed.</td></tr>'; } ?>
                </tbody>
            </table>
            <?php if(isset($total_ord_pages) && $total_ord_pages > 1): ?>
            <div class="pagination">
                <?php for($i = 1; $i <= $total_ord_pages; $i++): ?>
                    <a href="admin.php?page=orders&p=<?php echo $i; ?>" 
                       class="pg-link <?php echo ($i == $ord_p) ? 'active' : ''; ?>">
                       <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php elseif ($page == 'feedbacks'): ?>
        <h2 class="page-title">Collector Impressions</h2>
        <div class="section-card">
            <h3>Archived Feedback</h3>
            <?php
            // --- PAGINATION FOR FEEDBACK ---
            $f_limit = 10;
            $f_p = isset($_GET['fp']) ? (int)$_GET['fp'] : 1; 
            $f_offset = ($f_p - 1) * $f_limit;
            // FIX: Count total rows excluding NULL, empty strings, and specific placeholders
            $f_count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM product_reviews WHERE review_text IS NOT NULL AND TRIM(review_text) != '' AND review_text != 'No text provided.'");
            $f_total_items = mysqli_fetch_assoc($f_count_res)['total'];
            $f_total_pages = ceil($f_total_items / $f_limit);
            // FIX: Fetch current slice excluding NULL, empty strings, and specific placeholders
            $f_query = "SELECT pr.*, p.name as product_name, u.name as user_name 
                        FROM product_reviews pr 
                        JOIN products p ON pr.product_id = p.id 
                        LEFT JOIN users u ON pr.user_id = u.id 
                        WHERE pr.review_text IS NOT NULL AND TRIM(pr.review_text) != '' AND pr.review_text != 'No text provided.'
                        ORDER BY pr.id DESC 
                        LIMIT $f_limit OFFSET $f_offset";
            $f_result = mysqli_query($conn, $f_query);
            if($f_result && mysqli_num_rows($f_result) > 0):
                while($f = mysqli_fetch_assoc($f_result)): ?>
                    <div class="feedback-item">
                        <div class="feedback-meta">
                            <span>BY: <?php echo htmlspecialchars($f['user_name'] ?? 'Guest Collector'); ?></span>
                        </div>
                        <div class="feedback-text">
                            "<?php echo htmlspecialchars($f['review_text']); ?>"
                        </div>
                        <div class="feedback-product">
                            Archive Piece: <?php echo htmlspecialchars($f['product_name']); ?>
                        </div>
                        <div style="margin-top:15px; text-align:right;">
                             <a href="delete_feedback.php?id=<?php echo $f['id']; ?>" style="color:var(--accent-red); font-size:0.65rem; text-decoration:none; border-bottom:1px solid var(--accent-red); font-family:'Cinzel';">Purge Impression</a>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if($f_total_pages > 1): ?>
                <div class="pagination">
                    <?php for($i = 1; $i <= $f_total_pages; $i++): ?>
                        <a href="admin.php?page=feedbacks&fp=<?php echo $i; ?>" 
                           class="pg-link <?php echo ($i == $f_p) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            <?php else: 
                echo '<p style="color:var(--text-dim); text-align:center; padding:40px;">No written feedbacks found in the archives.</p>'; 
            endif; ?>
        </div>
    <?php endif; ?>
</div>
<script>
// NEW: AJAX Function to update stock
function updateStock(id, action) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', action);
    fetch('update_stock_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(newCount => {
        const span = document.getElementById('stock-count-' + id);
        span.innerText = newCount + ' IN VAULT';
        // Dynamic class switching based on level
        if (parseInt(newCount) <= 5) {
            span.className = 'stock-tag stock-critical';
        } else {
            span.className = 'stock-tag stock-ok';
        }
    })
    .catch(error => console.error('Error:', error));
}
// Chart scripts remain identical to your original code
Chart.defaults.color = '#777';
Chart.defaults.font.family = "'Inter', sans-serif";
if (document.getElementById('priceChart')) {
    const priceCtx = document.getElementById('priceChart').getContext('2d');
    new Chart(priceCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($chart_prices); ?>,
                backgroundColor: '#8e1a1a',
                hoverBackgroundColor: '#b12020'
            }]
        },
        options: { 
            responsive: true, 
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#222' }, ticks: { color: '#555' }, beginAtZero: true },
                x: { grid: { display: false }, ticks: { color: '#555' } }
            }
        }
    });
    const ratingCtx = document.getElementById('ratingChart').getContext('2d');
    new Chart(ratingCtx, {
        type: 'doughnut',
        data: {
            labels: ['Rating', 'Remainder'],
            datasets: [{
                data: [<?php echo $avg_rating; ?>, <?php echo max(0, 5 - $avg_rating); ?>],
                backgroundColor: ['#d4af37', '#222'],
                hoverBackgroundColor: ['#f1c40f', '#222'],
                borderWidth: 0
            }]
        },
        options: { cutout: '80%', plugins: { legend: { display: false } } }
    });
}
if (document.getElementById('salesTrendChart')) {
    const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($sales_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($sales_totals); ?>,
                borderColor: '#8e1a1a',
                backgroundColor: 'rgba(142, 26, 26, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#8e1a1a',
                clip: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 5 },
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#222' } },
                x: { grid: { color: '#222' } }
            }
        }
    });
}
</script>
<?php include 'global_footer.php'; ?>
</body>
</html>