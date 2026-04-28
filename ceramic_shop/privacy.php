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
    <title>Privacy Protocol — TSC</title>
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
    <h1>Privacy Protocol</h1>
    <p class="last-updated">DATA PROTECTION STANDARDS — 2026</p>
</div>

<div class="content-wrapper">
    
    <div class="legal-section">
        <h2>1. DATA COLLECTION</h2>
        <p>To provide a bespoke acquisition experience, we collect essential identifiers including your name, encrypted email address, and delivery coordinates. We do not engage in mass data harvesting; we only retain what is necessary for the fulfillment of your collection.</p>
    </div>

    <div class="legal-section">
        <h2>2. SECURE LOGISTICS</h2>
        <p>Your payment information is never stored on TSC servers. All transactions are processed through end-to-end encrypted gateways. We only receive a confirmation of acquisition to trigger the white-glove delivery process.</p>
    </div>

    <div class="legal-section">
        <h2>3. THE USE OF "COOKIES"</h2>
        <p>We utilize minimal tracking technology to remember your bag selections and maintain your secure session within the archives. These digital breadcrumbs are localized and do not track your movement across the wider web.</p>
    </div>

    <div class="legal-section">
        <h2>4. THIRD-PARTY DISCLOSURE</h2>
        <p>TSC does not sell, trade, or leak client identities. Data is only shared with verified logistics partners (for delivery) and payment processors (for acquisition). Under no other circumstances is your data accessible to outside entities.</p>
    </div>

    <div class="legal-section">
        <h2>5. CLIENT RIGHTS</h2>
        <p>As a member of the TSC Private Collection, you retain the right to request a full transcript of your data or the immediate "forgetting" of your account from our digital vault, provided all active acquisitions are complete.</p>
    </div>

    <div class="legal-section">
        <h2>6. ARCHIVE SECURITY</h2>
        <p>Our database utilizes multi-layer encryption. In the highly unlikely event of a security breach, all affected clients will be notified via their registered email within 72 hours as per Indian data protection protocols.</p>
    </div>

</div>

<div class="legal-footer">
    <a href="register.php" class="back-btn">RETURN TO REGISTRATION</a>
    <p style="margin-top: 40px; font-size: 0.6rem; opacity: 0.3; letter-spacing: 4px;">SECURED BY TSC ENCRYPTION &bull; 2026</p>
</div>

<?php include 'global_footer.php'; ?>
</body>
</html>