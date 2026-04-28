<?php
// 1. Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- SECURITY BLOCK: Ensure user is logged in to view the shop ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// --- DATABASE SYNC: Corrected to use 'name' instead of 'fullname' ---
if (!isset($_SESSION['user_name']) || $_SESSION['user_name'] === "Valued Client") {
    $u_id = $_SESSION['user_id'];
    $name_query = "SELECT name FROM users WHERE id = '$u_id' LIMIT 1"; 
    $name_result = mysqli_query($conn, $name_query);
    
    if ($name_result && mysqli_num_rows($name_result) > 0) {
        $user_data = mysqli_fetch_assoc($name_result);
        $_SESSION['user_name'] = !empty($user_data['name']) ? $user_data['name'] : "Valued Client";
    } else {
        $_SESSION['user_name'] = "Valued Client";
    }
}

// 2. Calculate actual total quantity in cart
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

// --- PAGINATION LOGIC ---
$limit = 9; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- SORTING LOGIC ---
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$order_by = "ORDER BY (stock > 0) DESC, id DESC"; 

switch ($sort) {
    case 'az':
        $order_by = "ORDER BY (stock > 0) DESC, name ASC";
        break;
    case 'za':
        $order_by = "ORDER BY (stock > 0) DESC, name DESC";
        break;
    case 'price_low':
        $order_by = "ORDER BY (stock > 0) DESC, price ASC";
        break;
    case 'price_high':
        $order_by = "ORDER BY (stock > 0) DESC, price DESC";
        break;
    case 'oldest':
        $order_by = "ORDER BY (stock > 0) DESC, id ASC";
        break;
    case 'latest':
    default:
        $order_by = "ORDER BY (stock > 0) DESC, id DESC";
        break;
}

// 3. Handle Search and Product Display
$search_query = "";
$where_clause = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause = " WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
    $search_query = $search;
}

$total_sql = "SELECT COUNT(*) FROM products $where_clause";
$total_result = mysqli_query($conn, $total_sql);
$total_rows = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT * FROM products $where_clause $order_by LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSC — THE PRIVATE COLLECTION</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #d4af37;
            --gold-glow: rgba(212, 175, 55, 0.3);
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
            --text-gray: #a0a0a0;
            --glass: rgba(18, 18, 18, 0.85);
        }

        html { scroll-behavior: smooth; }
        body { 
            background: var(--bg-dark);
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            color: #ffffff;
            overflow-x: hidden;
        }

        nav {
            position: fixed;
            top: 0; width: 100%;
            z-index: 1000;
            backdrop-filter: blur(20px);
            background: var(--glass);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 20px 0;
        }

        .nav-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
        }

        .logo { 
            font-family: 'Cinzel', serif; 
            font-size: 1.4rem; 
            letter-spacing: 4px; 
            color: var(--gold); 
            text-decoration: none;
        }

        .menu-links { display: flex; align-items: center; gap: 30px; }

        .menu-links a { 
            color: #fff; text-decoration: none; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 2px; font-weight: 400;
            transition: 0.3s;
        }
        .menu-links a:hover { color: var(--gold); }

        /* FAQ Button Style */
        .faq-link {
            border: 1px solid rgba(255,255,255,0.1);
            padding: 5px 12px;
            border-radius: 2px;
            font-size: 0.7rem !important;
        }

        .profile-link {
            color: var(--gold) !important;
            border: 1px solid rgba(212, 175, 55, 0.3);
            padding: 5px 12px;
            border-radius: 2px;
        }
        .profile-link:hover {
            background: var(--gold);
            color: #000 !important;
        }

        .cart-pill {
            background: #fff; color: #000 !important;
            padding: 8px 20px; border-radius: 50px;
            font-weight: 700; display: flex; align-items: center; gap: 8px;
        }

        header {
            height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
            background: radial-gradient(circle at center, #1a1a1a 0%, #0a0a0a 100%);
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }

        header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 5rem; font-weight: 400; font-style: italic;
            margin: 0; background: linear-gradient(to bottom, #fff, #666);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .search-area { margin-top: 50px; }
        .search-container {
            display: inline-flex; background: #1a1a1a; padding: 5px;
            border-radius: 50px; border: 1px solid #333; width: 450px;
        }
        .search-container input {
            flex: 1; background: transparent; border: none; padding: 15px 25px;
            color: #fff; outline: none; font-size: 0.9rem;
        }
        .search-container button {
            background: var(--gold); color: #000; border: none;
            padding: 0 30px; border-radius: 50px; cursor: pointer;
            font-weight: 700; transition: 0.3s;
        }
        .search-container button:hover { transform: scale(1.05); }

        .sorting-bar {
            max-width: 1500px;
            margin: 50px auto -80px;
            padding: 0 50px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
        }
        .sorting-bar label {
            font-family: 'Cinzel', serif;
            font-size: 0.7rem;
            letter-spacing: 2px;
            color: var(--text-gray);
        }
        .sort-select {
            background: #0a0a0a;
            color: var(--gold);
            border: 1px solid #333;
            padding: 8px 15px;
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            outline: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .sort-select:hover {
            border-color: var(--gold);
        }

        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
            gap: 40px; padding: 100px 50px 50px 50px; 
            max-width: 1500px; margin: 0 auto; 
        }

        .card { 
            background: var(--card-bg); 
            border-radius: 0; 
            overflow: hidden; 
            transition: 0.5s ease;
            border: 1px solid #1a1a1a;
            position: relative;
        }

        .card:hover { 
            border-color: var(--gold); 
            box-shadow: 0 0 30px var(--gold-glow);
            transform: translateY(-10px);
        }

        .image-box { height: 450px; overflow: hidden; position: relative; }
        .card img { 
            width: 100%; height: 100%; object-fit: cover; 
            filter: grayscale(40%); transition: 1s ease;
        }
        .card:hover img { filter: grayscale(0%); transform: scale(1.1); }

        .card-body { padding: 30px; }
        .card h3 { 
            font-family: 'Playfair Display', serif; font-size: 1.6rem; 
            margin: 0; font-weight: 400; letter-spacing: 1px;
        }
        .price-tag { 
            color: var(--gold); font-size: 1.2rem; margin: 15px 0; display: block;
        }

        .card-actions { display: flex; gap: 10px; margin-top: 20px; }
        
        .btn-add { 
            background: var(--gold); color: #000; border: none; padding: 15px; 
            flex: 2; cursor: pointer; font-weight: 700; text-transform: uppercase;
            font-size: 0.7rem; letter-spacing: 2px; transition: 0.3s;
            width: 100%;
        }
        .btn-details { 
            border: 1px solid #444; color: #fff; text-decoration: none;
            padding: 15px; flex: 1; text-align: center; font-size: 0.7rem;
            letter-spacing: 2px; transition: 0.3s;
        }
        .btn-add:hover:not([disabled]) { background: #fff; }
        .btn-details:hover { border-color: #fff; }

        .btn-sold-out {
            background: #222 !important;
            color: #555 !important;
            cursor: not-allowed;
            opacity: 0.6;
            border: 1px solid #333;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 40px 0 100px 0;
        }
        .page-link {
            text-decoration: none;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 0.7rem;
            padding: 10px 25px;
            border: 1px solid #333;
            transition: 0.3s;
        }
        .page-link:hover:not(.disabled) {
            border-color: var(--gold);
            color: var(--gold);
        }
        .page-info {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            font-size: 0.9rem;
        }
        .disabled {
            opacity: 0.2;
            cursor: not-allowed;
        }

        footer { 
            text-align: center; padding: 100px 0; background: #050505; 
            border-top: 1px solid #111;
        }
        .footer-logo { font-family: 'Cinzel', serif; color: var(--gold); font-size: 2rem; }

        /* AI CONCIERGE STYLES */
        #ai-chat-launcher {
            position: fixed; bottom: 30px; right: 30px;
            background: var(--gold); color: #000;
            padding: 12px 24px; border-radius: 2px;
            cursor: pointer; font-family: 'Cinzel', serif;
            font-weight: 700; letter-spacing: 2px; font-size: 0.75rem;
            z-index: 9999; display: flex; align-items: center; gap: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
        }
        .pulse-icon {
            width: 8px; height: 8px; background: #000;
            border-radius: 50%; animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 1; }
            70% { transform: scale(1.5); opacity: 0; }
            100% { transform: scale(0.95); opacity: 0; }
        }
        #ai-vault-window {
            position: fixed; bottom: 90px; right: 30px;
            width: 380px; height: 500px; background: rgba(17, 17, 17, 0.98);
            border: 1px solid rgba(212, 175, 55, 0.3);
            display: flex; flex-direction: column; z-index: 9999;
            box-shadow: 0 20px 60px rgba(0,0,0,1);
        }
        .vault-header {
            background: #0a0a0a; padding: 15px; border-bottom: 1px solid var(--gold);
            display: flex; justify-content: space-between; align-items: center;
        }
        .header-title { font-family: 'Cinzel', serif; color: var(--gold); font-size: 0.7rem; letter-spacing: 2px; }
        .close-vault { background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem; }
        #chat-messages-container { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
        .msg { max-width: 85%; padding: 12px; font-size: 0.85rem; line-height: 1.5; }
        .ai-msg { align-self: flex-start; background: rgba(212, 175, 55, 0.05); color: #a0a0a0; border-left: 1px solid var(--gold); }
        .user-msg { align-self: flex-end; background: #1a1a1a; color: #fff; border-right: 1px solid #8b0000; }
        .vault-input-area { padding: 15px; border-top: 1px solid #222; display: flex; gap: 10px; }
        #ai-user-input { flex: 1; background: #0a0a0a; border: 1px solid #333; color: white; padding: 10px; outline: none; }
        #send-btn { background: #8b0000; color: white; border: none; padding: 0 15px; font-family: 'Cinzel', serif; font-size: 0.7rem; cursor: pointer; }

        /* =========================================================
           LIGHT MODE OVERRIDES FOR AI CONCIERGE 
           ========================================================= */
        body.light-mode #ai-vault-window,
        body.light-mode #ai-vault-window .vault-header,
        body.light-mode #ai-vault-window #chat-messages-container,
        body.light-mode #ai-vault-window .ai-msg,
        body.light-mode #ai-vault-window .vault-input-area,
        body.light-mode #ai-vault-window #ai-user-input {
            background-color: #FFFFFF !important;
            background: #FFFFFF !important;
            border-color: #E0E0E0 !important;
        }

        body.light-mode #ai-vault-window .header-title,
        body.light-mode #ai-vault-window .ai-msg,
        body.light-mode #ai-vault-window .close-vault {
            color: #00E5FF !important;
        }

        body.light-mode #ai-vault-window .user-msg {
            background: #f0f0f0 !important;
            color: #000 !important;
            border-right: 1px solid #00E5FF !important;
        }

        body.light-mode #ai-vault-window #ai-user-input {
            color: #000 !important;
            border: 1px solid #ddd !important;
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-wrapper">
        <a href="index.php" class="logo">TSC.</a>
        
        <div class="menu-links">
            <a href="faq.php" class="faq-link">FAQ</a>
            
            <a href="index.php">Collection</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="order_history.php">History</a>
                <a href="logout.php">Sign Out</a>
                <a href="profile.php" class="profile-link">[ <?php echo htmlspecialchars($_SESSION['user_name']); ?> ]</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <a href="admin_login.php" style="opacity: 0.3;">Portal</a>
            
            <a href="cart.php" class="cart-pill">
                <span>BAG</span>
                <span>(<?php echo $cart_count; ?>)</span>
            </a>
        </div>
    </div>
</nav>

<header>
    <h2>Timeless Forms</h2>
    <div class="search-area">
        <div class="search-container">
            <form action="index.php" method="GET" style="display: flex; width: 100%;">
                <input type="text" name="search" placeholder="Search the archives..." value="<?php echo htmlspecialchars($search_query); ?>">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                <button type="submit">FIND</button>
            </form>
        </div>
    </div>
</header>

<div class="sorting-bar">
    <label>SORT BY:</label>
    <form action="index.php" method="GET" id="sortForm">
        <?php if(!empty($search_query)): ?>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
        <?php endif; ?>
        <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
            <option value="latest" <?php echo ($sort == 'latest') ? 'selected' : ''; ?>>New Arrivals</option>
            <option value="oldest" <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>Classic Editions</option>
            <option value="az" <?php echo ($sort == 'az') ? 'selected' : ''; ?>>Alphabetical (A-Z)</option>
            <option value="za" <?php echo ($sort == 'za') ? 'selected' : ''; ?>>Alphabetical (Z-A)</option>
            <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Valuation (High to Low)</option>
            <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Valuation (Low to High)</option>
        </select>
    </form>
</div>

<div class="grid">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="card">
                <div class="image-box">
                    <?php 
                        $img_value = !empty($row['image']) ? $row['image'] : $row['image_url'];
                        $final_src = (strpos($img_value, 'images/') === false && strpos($img_value, 'http') === false) ? "images/" . $img_value : $img_value;
                    ?>
                    <img src="<?php echo htmlspecialchars($final_src); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" 
                         style="<?php echo ($row['stock'] <= 0) ? 'filter: grayscale(100%) opacity(0.5);' : ''; ?>">
                </div>
                
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <span class="price-tag">INR <?php echo number_format($row['price'], 2); ?></span>
                    
                    <div class="card-actions">
                        <a href="product_review.php?id=<?php echo $row['id']; ?>" class="btn-details">VIEW</a>
                        
                        <?php if ($row['stock'] > 0): ?>
                            <a href="manage_cart.php?id=<?php echo $row['id']; ?>&action=add" style="flex: 2; text-decoration: none;">
                                <button class="btn-add">ACQUIRE ITEM</button>
                            </a>
                        <?php else: ?>
                            <div style="flex: 2;">
                                <button class="btn-add btn-sold-out" disabled>SOLD OUT</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column: 1/-1; text-align: center; opacity: 0.5; letter-spacing: 2px;">NO PIECES FOUND IN THE ARCHIVE</p>
    <?php endif; ?>
</div>

<?php if ($total_pages > 1): ?>
<div class="pagination">
    <?php 
        $search_param = !empty($search_query) ? "&search=" . urlencode($search_query) : "";
        $sort_param = "&sort=" . urlencode($sort);
    ?>
    
    <a href="?page=<?php echo $page - 1; ?><?php echo $search_param; ?><?php echo $sort_param; ?>" class="page-link <?php echo ($page <= 1) ? 'disabled' : ''; ?>">PREVIOUS</a>
    
    <span class="page-info"><?php echo $page; ?> / <?php echo $total_pages; ?></span>
    
    <a href="?page=<?php echo $page + 1; ?><?php echo $search_param; ?><?php echo $sort_param; ?>" class="page-link <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">NEXT</a>
</div>
<?php endif; ?>

<footer>
    <div class="footer-logo">THE CERAMIC SHOP</div>
    <p style="font-size: 0.6rem; letter-spacing: 5px; opacity: 0.4; margin-top: 20px;">DELHI &bull; UTTAR PRADESH &bull; HARYANA</p>
    
    <div style="margin-top: 25px;">
        <a href="ENTER LINK FOR INSTAGRAM PAGE" target="_blank" style="color: var(--gold); opacity: 0.7; transition: opacity 0.3s ease;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
        </a>
    </div>
</footer>

<div id="ai-chat-launcher" onclick="toggleArchiveAssistant()">
    <span class="pulse-icon"></span>
    <span class="launcher-text">AI CONCIERGE</span>
</div>

<div id="ai-vault-window" style="display: none;">
    <div class="vault-header">
        <div class="header-title">TSC ARCHIVE ASSISTANT</div>
        <button class="close-vault" onclick="toggleArchiveAssistant()">✕</button>
    </div>
    
    <div id="chat-messages-container">
        <div class="msg ai-msg">Greetings. I am the TSC intelligence unit. How may I assist your acquisition process today?</div>
    </div>

    <div class="vault-input-area">
        <input type="text" id="ai-user-input" placeholder="Inquire about an asset..." autocomplete="off">
        <button id="send-btn" onclick="processInquiry()">SEND</button>
    </div>
</div>

<script>
function toggleArchiveAssistant() {
    const win = document.getElementById('ai-vault-window');
    win.style.display = (win.style.display === 'none') ? 'flex' : 'none';
}

document.getElementById('ai-user-input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') processInquiry();
});

function processInquiry() {
    const input = document.getElementById('ai-user-input');
    const container = document.getElementById('chat-messages-container');
    const userText = input.value.trim();
    
    if (!userText) return;

    const userDiv = document.createElement('div');
    userDiv.className = 'msg user-msg';
    userDiv.textContent = userText;
    container.appendChild(userDiv);
    input.value = "";
    container.scrollTop = container.scrollHeight;

    const typingDiv = document.createElement('div');
    typingDiv.className = 'msg ai-msg';
    typingDiv.textContent = "Processing...";
    container.appendChild(typingDiv);

    fetch('ai_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userText })
    })
    .then(res => res.json())
    .then(data => {
        typingDiv.textContent = data.reply;
        container.scrollTop = container.scrollHeight;
    })
    .catch(() => {
        typingDiv.textContent = "The connection to the archive is interrupted.";
    });
}
</script>

<?php include 'global_footer.php'; ?>
</body>
</html>