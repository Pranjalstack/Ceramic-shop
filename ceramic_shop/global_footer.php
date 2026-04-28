<div class="cursor-dot" data-cursor-dot></div>
<div class="cursor-glow" data-cursor-glow></div>

<button id="floating-theme-toggle" title="Toggle Light/Dark Mode">☀</button>

<style>
    /* 1. Global Suppression of System Cursor */
    *, body, a, button, input, select, .card, .logo, .btn, .cart-pill, .faq-link, .profile-link {
        cursor: none !important;
    }

    /* 2. The Precision Dot - DEFAULT GOLD */
    .cursor-dot {
        width: 8px;
        height: 8px;
        background-color: #d4af37;
        position: fixed;
        top: 0; left: 0;
        border-radius: 50%;
        z-index: 10001;
        pointer-events: none;
        box-shadow: 0 0 10px rgba(212, 175, 55, 0.8);
        transition: transform 0.15s ease-out, background-color 0.15s ease-out, box-shadow 0.15s ease-out;
        transform: translate(-50%, -50%) scale(1);
    }

    /* 3. The Fluid Gold Aura - DEFAULT GOLD */
    .cursor-glow {
        width: 24px; 
        height: 24px;
        background: radial-gradient(circle, rgba(212,175,55,0.3) 0%, rgba(212,175,55,0) 70%);
        position: fixed;
        top: 0; left: 0;
        border-radius: 50%;
        z-index: 10000;
        pointer-events: none;
        filter: blur(4px);
        transform: translate(-50%, -50%);
    }

    /* 4. Sparkle Particles */
    .gold-dust {
        position: fixed;
        background-color: #d4af37;
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        opacity: 0.8;
        /* Twinkle animation */
        animation: twinkle var(--duration) ease-in-out infinite;
    }

    @keyframes twinkle {
        0%, 100% { opacity: 0.3; transform: scale(0.8); }
        50% { opacity: 1; transform: scale(1.2); }
    }

    /* INTERACTION STATES */
    body:not(.light-mode).cursor-hovering .cursor-dot {
        transform: translate(-50%, -50%) scale(2.8);
        background-color: #fff;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.8);
    }
    body.cursor-clicking .cursor-dot {
        transform: translate(-50%, -50%) scale(0.4);
    }

    /* =========================================================
        --- THE CLEAN LIGHT MODE (TOTAL WHITEOUT) --- 
        ========================================================= */
        
    /* 1. FORCE GLOBAL BACKGROUND AND TEXT */
    body.light-mode, 
    body.light-mode main, 
    body.light-mode section, 
    body.light-mode header, 
    body.light-mode footer,
    body.light-mode .hero,
    body.light-mode [class*="hero"],
    body.light-mode [class*="banner"] {
        background-color: #FFFFFF !important;
        background: #FFFFFF !important;
        color: #000000 !important;
    }

    /* 2. FORCE ALL TEXT TO BLACK */
    body.light-mode *, 
    body.light-mode p, 
    body.light-mode h1, 
    body.light-mode h2, 
    body.light-mode h3, 
    body.light-mode h4, 
    body.light-mode span, 
    body.light-mode label,
    body.light-mode a:not(.btn) {
        color: #000000 !important;
    }

    /* 3. AI CHATBOX FIX: Forcing internal white background and Cyan text */
    body.light-mode [id*="ai-chat"],
    body.light-mode [class*="chat-box"],
    body.light-mode [class*="concierge"],
    body.light-mode [id*="ai-chat"] *, 
    body.light-mode [class*="chat-box"] *, 
    body.light-mode [class*="concierge"] * {
        background-color: #FFFFFF !important;
        background: #FFFFFF !important;
        color: #00E5FF !important; 
        border-color: #E0E0E0 !important;
        background-image: none !important;
    }

    /* Ensure user text input is still black for readability */
    body.light-mode [id*="ai-chat"] input,
    body.light-mode [class*="chat-box"] input,
    body.light-mode [class*="concierge"] input,
    body.light-mode [id*="ai-chat"] textarea {
        color: #000000 !important;
        background-color: #FFFFFF !important;
    }

    /* 4. THE BOX & PANEL FIX */
    body.light-mode div, 
    body.light-mode .card, 
    body.light-mode .panel,
    body.light-mode [class*="box"],
    body.light-mode [class*="container"],
    body.light-mode [class*="wrapper"],
    body.light-mode [class*="section"] {
        background-color: transparent; 
        border-color: #E5E7EB !important;
    }

    body.light-mode .legal-section,
    body.light-mode .content-wrapper,
    body.light-mode [class*="admin"] div,
    body.light-mode form div {
        background-color: #F3F4F6 !important;
        color: #000000 !important;
    }

    /* 5. NAVIGATION & SIDEBAR */
    body.light-mode nav, 
    body.light-mode aside, 
    body.light-mode .sidebar {
        background-color: #FFFFFF !important;
        border-right: 1px solid #E0E0E0 !important;
        border-bottom: 1px solid #E0E0E0 !important;
    }

    /* 6. INPUTS & FORM ELEMENTS */
    body.light-mode input, 
    body.light-mode textarea, 
    body.light-mode select {
        background-color: #FFFFFF !important;
        color: #000000 !important;
        border: 1px solid #CCCCCC !important;
    }

    /* 7. CURSOR TRANSITION TO BLUE */
    body.light-mode .cursor-dot {
        background-color: #00E5FF !important;
        box-shadow: 0 0 10px rgba(0, 229, 255, 0.8) !important;
    }
    body.light-mode .cursor-glow {
        background: radial-gradient(circle, rgba(0,229,255,0.3) 0%, rgba(0,229,255,0) 70%) !important;
    }
    body.light-mode.cursor-hovering .cursor-dot {
        background-color: #000000 !important;
    }

    /* --- FLOATING TOGGLE --- */
    #floating-theme-toggle {
        position: fixed;
        bottom: 30px;
        left: 30px; 
        background-color: #1a1a1a;
        color: #d4af37;
        border: 1px solid #d4af37;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        cursor: none !important;
        z-index: 10002;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    body.light-mode #floating-theme-toggle {
        background-color: #FFFFFF !important;
        color: #000000 !important;
        border: 1px solid #000000 !important;
    }
</style>

<script>
    const dot = document.querySelector("[data-cursor-dot]");
    const glow = document.querySelector("[data-cursor-glow]");
    let mouseX = 0, mouseY = 0;     
    let glowX = 0, glowY = 0;       

    window.addEventListener("mousemove", (e) => {
        mouseX = e.clientX; mouseY = e.clientY;
        dot.style.left = `${mouseX}px`;
        dot.style.top = `${mouseY}px`;
        // Increased frequency for a more sparkling "trail"
        if (Math.random() > 0.1) createDust(e.clientX, e.clientY);
    });

    function animateCursor() {
        glowX += (mouseX - glowX) * 0.25;
        glowY += (mouseY - glowY) * 0.25;
        glow.style.transform = `translate(-50%, -50%) translate(${glowX}px, ${glowY}px)`;
        requestAnimationFrame(animateCursor);
    }
    animateCursor();

    function createDust(x, y) {
        const dust = document.createElement("div");
        dust.className = "gold-dust";
        
        // Randomize size for "sparkle" variety
        const size = Math.random() * 4;
        const isLight = document.body.classList.contains('light-mode');
        
        // Randomize colors between Theme Primary and White for shimmering effect
        const colors = isLight ? ["#00E5FF", "#FFFFFF", "#E0F7FA"] : ["#d4af37", "#FFFFFF", "#f9f1d7"];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        
        dust.style.width = dust.style.height = `${size}px`;
        dust.style.backgroundColor = randomColor;
        dust.style.left = `${x}px`; 
        dust.style.top = `${y}px`;
        dust.style.setProperty('--duration', `${Math.random() * 0.5 + 0.2}s`); // Random twinkle speed
        
        document.body.appendChild(dust);

        // Movement: slight scatter + fade out
        const anim = dust.animate([
            { transform: 'translate(-50%, -50%) scale(1) rotate(0deg)', opacity: 1 },
            { transform: `translate(${(Math.random()-0.5)*80}px, ${(Math.random()-0.5)*80}px) scale(0) rotate(180deg)`, opacity: 0 }
        ], { duration: 800 + Math.random() * 600 });

        anim.onfinish = () => dust.remove();
    }

    function updateSensors() {
        document.querySelectorAll('a, button, input, select, .card, #floating-theme-toggle').forEach(el => {
            el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hovering'));
            el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hovering'));
        });
    }

    updateSensors();
    setInterval(updateSensors, 2000);

    document.addEventListener('DOMContentLoaded', () => {
        const floatingToggleBtn = document.getElementById('floating-theme-toggle');
        const currentTheme = localStorage.getItem('tsc_theme');

        if (currentTheme === 'light') {
            document.body.classList.add('light-mode');
            floatingToggleBtn.innerHTML = '☾';
        }

        floatingToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
            const isLight = document.body.classList.contains('light-mode');
            localStorage.setItem('tsc_theme', isLight ? 'light' : 'dark');
            floatingToggleBtn.innerHTML = isLight ? '☾' : '☀';
        });
    });
</script>