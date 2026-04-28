<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service — TSC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #d4af37;
            --bg-dark: #0a0a0a;
            --card-bg: #111111;
            --border: rgba(212, 175, 55, 0.2);
        }

        body { 
            background: var(--bg-dark);
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            color: #ffffff;
            line-height: 1.8;
        }

        /* --- HEADER --- */
        .legal-header {
            padding: 80px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border);
            background: radial-gradient(circle at center, #161616 0%, #0a0a0a 100%);
        }

        .legal-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin: 0;
            font-weight: 400;
            font-style: italic;
        }

        .last-updated {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            font-size: 0.7rem;
            letter-spacing: 3px;
            margin-top: 15px;
        }

        /* --- CONTENT SECTION --- */
        .content-wrapper {
            max-width: 800px;
            margin: 60px auto;
            padding: 0 30px;
        }

        .legal-section {
            margin-bottom: 50px;
        }

        .legal-section h2 {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            font-size: 1.1rem;
            letter-spacing: 2px;
            border-bottom: 1px solid #222;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .legal-section p, .legal-section li {
            color: #b0b0b0;
            font-size: 0.95rem;
            font-weight: 300;
        }

        .legal-section ul {
            padding-left: 20px;
        }

        .legal-section li {
            margin-bottom: 10px;
        }

        /* --- FOOTER --- */
        .legal-footer {
            text-align: center;
            padding: 60px 0;
            border-top: 1px solid #111;
            margin-top: 100px;
        }

        .back-btn {
            display: inline-block;
            padding: 15px 40px;
            border: 1px solid var(--gold);
            color: var(--gold);
            text-decoration: none;
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            letter-spacing: 2px;
            transition: 0.4s;
        }

        .back-btn:hover {
            background: var(--gold);
            color: #000;
        }

        @media (max-width: 768px) {
            .legal-header h1 { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

<div class="legal-header">
    <h1>Terms of Service</h1>
    <p class="last-updated">EFFECTIVE AS OF FEBRUARY 2026</p>
</div>

<div class="content-wrapper">
    
    <div class="legal-section">
        <h2>1. THE PRIVATE ARCHIVE</h2>
        <p>By accessing <strong>The Ceramic Shop (TSC)</strong>, you enter a private digital archive. These terms govern the acquisition of artisanal ceramics and the use of our digital concierge services. Use of this portal implies unconditional acceptance of these protocols.</p>
    </div>

    <div class="legal-section">
        <h2>2. ACQUISITION & PAYMENTS</h2>
        <p>All prices listed are in INR. TSC reserves the right to modify pricing or withdraw items from the collection at any time without prior notice. Acquisitions are only confirmed upon the successful verification of funds by our secure payment gateways.</p>
    </div>

    <div class="legal-section">
        <h2>3. ARTISANAL AUTHENTICITY</h2>
        <p>Our pieces are handcrafted. You acknowledge that minor variations in glaze, texture, and form are not defects but unique identifiers of provenance. Digital representations are as accurate as technology permits, but slight chromatic variances may occur.</p>
    </div>

    <div class="legal-section">
        <h2>4. LIMITATION OF LIABILITY</h2>
        <p>TSC shall not be held liable for any indirect, incidental, or consequential damages arising from the use or inability to use the acquired pieces. Our maximum liability to you for any product purchased through the portal shall be strictly limited to the purchase price of said product.</p>
    </div>

    <div class="legal-section">
        <h2>5. INTELLECTUAL PROPERTY</h2>
        <p>All designs, code, imagery, and the "TSC" mark are the exclusive intellectual property of The Ceramic Shop. Unauthorized reproduction, digital scraping, or commercial redistribution is strictly prohibited and will be met with legal action.</p>
    </div>

    <div class="legal-section">
        <h2>6. GOVERNING LAW</h2>
        <p>These terms are governed by the laws of India. Any disputes arising from the use of this portal or the acquisition of goods shall be subject to the exclusive jurisdiction of the courts in <strong>New Delhi, India</strong>.</p>
    </div>

    <div class="legal-section">
        <h2>7. TERMINATION OF ACCESS</h2>
        <p>We reserve the right to revoke archive access and terminate user accounts if fraudulent activity or breach of these terms is detected, at our sole discretion.</p>
    </div>

</div>

<div class="legal-footer">
    <a href="register.php" class="back-btn">RETURN TO REGISTRATION</a>
    <p style="margin-top: 40px; font-size: 0.6rem; opacity: 0.3; letter-spacing: 4px;">&copy; 2026 TSC PRIVATE COLLECTION. ALL RIGHTS RESERVED.</p>
</div>

<?php include 'global_footer.php'; ?>
</body>
</html>