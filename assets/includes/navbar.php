<?php
// includes/navbar.php
require_once __DIR__ . '/../config.php';
?>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<style>
    * {
        font-family: "Raleway", sans-serif;
        font-optical-sizing: auto;
        font-style: normal;
    }

    /* Sabit Navbar */
    nav.fixed-navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 5%;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 1001;
        transition: background 0.3s;
    }

    nav.fixed-navbar .logo-link {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    nav.fixed-navbar .logo-link img {
        height: 40px;
        width: auto;
    }

    .desktop-nav-links {
        display: flex;
        align-items: center;
    }

    .desktop-nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 28px;
        font-weight: 500;
        font-size: 1.05rem;
        transition: color 0.2s;
    }

    .desktop-nav-links a:hover {
        color: #e0e0ff;
    }

    /* Mobil menü butonu - ANİMASYONLU HAMBURGER */
    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        background: none;
        border: none;
        cursor: pointer;
        width: 28px;
        height: 20px;
        padding: 0;
        justify-content: space-between;
    }

    .mobile-menu-toggle span {
        height: 3px;
        width: 100%;
        background: white;
        border-radius: 2px;
        transition: all 0.3s ease;
        position: relative;
    }

    /* Animasyon: Menü açıkken */
    .mobile-menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(6px, 6px);
    }

    .mobile-menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .mobile-menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(6px, -6px);
    }

    /* Mobil overlay */
    .mobile-nav-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.93);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    .mobile-nav-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .mobile-nav-button {
        background: transparent;
        border: none;
        color: #f9fff0;
        font-size: 1.45rem;
        font-weight: 600;
        padding: 22px 0;
        margin: 10px 0;
        display: flex;
        align-items: center;
        gap: 18px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 85%;
        max-width: 500px;
        text-align: left;
    }

    .mobile-nav-button:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: scale(1.02);
        color: #ffffff;
    }

    .mobile-nav-button i {
        font-size: 1.9rem;
        color: #ffffffff;
        min-width: 36px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .desktop-nav-links {
            display: none;
        }

        .mobile-menu-toggle {
            display: flex;
        }

        nav.fixed-navbar .logo-link img {
            height: 34px;
        }
    }
</style>

<nav class="fixed-navbar">
    <a class="logo-link">
        <img src="/assets/images/navbar.png" alt="AFL-MUN Logo">
    </a>

    <div class="desktop-nav-links">
        <a href="/">Home</a>
        <a href="/committees">Committees</a>
        <a href="/apply">Apply</a>
        <?php if ($show_executive): ?>
            <a href="/team">Executive</a>
        <?php endif; ?>
        <a href="/contact">Contact</a>
    </div>

    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
</nav>

<div class="mobile-nav-overlay" id="mobileNavOverlay">
    <button class="mobile-nav-button" onclick="window.location.href='/'">
        <i class="fas fa-home"></i> Home
    </button>
    <button class="mobile-nav-button" onclick="window.location.href='/committees'">
        <i class="fas fa-landmark"></i> Committees
    </button>
    <button class="mobile-nav-button" onclick="window.location.href='/apply'">
        <i class="fas fa-user-plus"></i> Apply
    </button>
    <?php if ($show_executive): ?>
    <button class="mobile-nav-button" onclick="window.location.href='/team'">
        <i class="fas fa-users"></i> Executive
    </button>
    <?php endif; ?>
    <button class="mobile-nav-button" onclick="window.location.href='/contact'">
        <i class="fas fa-phone"></i> Contact
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('mobileMenuToggle');
    const overlay = document.getElementById('mobileNavOverlay');

    if (toggle && overlay) {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            overlay.classList.toggle('active');
            toggle.classList.toggle('active'); // ← Bu satır animasyonu tetikler
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                toggle.classList.remove('active'); // ← Menü kapanınca X'ten geri dön
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                overlay.classList.remove('active');
                toggle.classList.remove('active');
            }
        });
    }
});
</script>