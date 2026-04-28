<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic cart count for the nav bar
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
    <title>Inquiry Archive — TSC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #d4af37;
            --bg-dark: #0a0a0a;
            --card-bg: #121212;
            --glass: rgba(18, 18, 18, 0.9);
            --border: rgba(212, 175, 55, 0.2);
        }

        body { 
            background: var(--bg-dark);
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            color: #ffffff;
            line-height: 1.6;
        }

        /* --- NAVIGATION (Matching index.php) --- */
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
            text-transform: uppercase; letter-spacing: 2px;
        }
        .cart-pill {
            background: #fff; color: #000 !important;
            padding: 8px 20px; border-radius: 50px;
            font-weight: 700; display: flex; align-items: center; gap: 8px;
        }

        /* --- HERO SECTION --- */
        .faq-hero {
            height: 50vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: radial-gradient(circle at center, #1a1a1a 0%, #0a0a0a 100%);
            border-bottom: 1px solid var(--border);
        }
        .faq-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            font-weight: 400;
            font-style: italic;
            margin: 0;
            background: linear-gradient(to bottom, #fff, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .subtitle {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            letter-spacing: 5px;
            font-size: 0.8rem;
            margin-top: 10px;
        }

        /* --- FAQ ACCORDION --- */
        .faq-container {
            max-width: 900px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .faq-item {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 15px;
            transition: 0.4s;
        }
        .faq-item:hover {
            border-color: var(--gold);
        }

        .faq-question {
            width: 100%;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border: none;
            background: none;
            color: #fff;
            font-family: 'Cinzel', serif;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-align: left;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out;
            background: rgba(0,0,0,0.2);
        }

        .answer-content {
            padding: 0 30px 30px 30px;
            color: #a0a0a0;
            font-size: 0.95rem;
            font-weight: 300;
        }

        /* Arrow Icon */
        .arrow {
            width: 12px;
            height: 12px;
            border-right: 2px solid var(--gold);
            border-bottom: 2px solid var(--gold);
            transform: rotate(45deg);
            transition: 0.4s;
        }

        /* Active State logic via JS */
        .faq-item.active .faq-answer {
            max-height: 500px;
        }
        .faq-item.active .arrow {
            transform: rotate(-135deg);
        }

        /* --- FOOTER --- */
        footer { 
            text-align: center; padding: 100px 0; background: #050505; 
            border-top: 1px solid #111; margin-top: 100px;
        }
        .footer-logo { font-family: 'Cinzel', serif; color: var(--gold); font-size: 2rem; }
    </style>
</head>
<body>

<nav>
    <div class="nav-wrapper">
        <a href="index.php" class="logo">TSC.</a>
        <div class="menu-links">
            <a href="index.php">Collection</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" style="color:var(--gold)">[ Profile ]</a>
                <a href="logout.php">Sign Out</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-pill">
                <span>BAG</span>
                <span>(<?php echo $cart_count; ?>)</span>
            </a>
        </div>
    </div>
</nav>

<section class="faq-hero">
    <p class="subtitle">CLIENT RELATIONS</p>
    <h1>Inquiry Archive</h1>
</section>

<div class="faq-container">
    
    <div class="faq-item">
        <button class="faq-question">
            <span>The Acquisition Process</span>
            <div class="arrow"></div>
        </button>
        <div class="faq-answer">
            <div class="answer-content">
                To acquire a piece from the TSC archives, select 'Acquire Item' on the collection page. Your selection will be held in your Bag until checkout. Due to the limited nature of our editions, items are only secured once the transaction is complete.
            </div>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
            <span>Shipping & Logistics</span>
            <div class="arrow"></div>
        </button>
        <div class="faq-answer">
            <div class="answer-content">
                We provide white-glove delivery across Delhi, Uttar Pradesh, and Haryana. Each piece is crated in custom-engineered packaging to ensure its structural integrity during transit. Tracking details are dispatched within 24 hours of shipment.
            </div>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
            <span>The Ceramic Quality Standard</span>
            <div class="arrow"></div>
        </button>
        <div class="faq-answer">
            <div class="answer-content">
                Every asset in our collection is hand-inspected for thermal resilience and glaze perfection. As these are artisanal forms, minor variations in texture are not defects but markers of authenticity and unique provenance.
            </div>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
            <span>Returns & Exchanges</span>
            <div class="arrow"></div>
        </button>
        <div class="faq-answer">
            <div class="answer-content">
                Due to the fragile and exclusive nature of our inventory, we do not accept general returns. However, if a piece arrives compromised, please contact the AI Concierge or our support portal within 48 hours with photographic evidence for an immediate replacement or valuation credit.
            </div>
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
            <span>Privacy of the Archives</span>
            <div class="arrow"></div>
        </button>
        <div class="faq-answer">
            <div class="answer-content">
                TSC employs military-grade encryption for all client data. Your acquisition history and personal details are stored within an offline vault, accessible only for order fulfillment and personalized archive recommendations.
            </div>
        </div>
    </div>

</div>

<footer>
    <div class="footer-logo">THE CERAMIC SHOP</div>
    <p style="font-size: 0.6rem; letter-spacing: 5px; opacity: 0.4; margin-top: 20px;">DELHI &bull; UTTAR PRADESH &bull; HARYANA</p>
</footer>

<script>
    // Accordion Logic
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const faqItem = button.parentElement;
            
            // Close other items (Optional - remove if you want multiple open)
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) item.classList.remove('active');
            });

            faqItem.classList.toggle('active');
        });
    });
</script>

<?php include 'global_footer.php'; ?>
</body>
</html>