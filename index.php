<?php
require_once 'config.php';

// Sayacı al
$stmt = $pdo->query("SELECT event_date FROM countdown WHERE id = 1");
$countdown_row = $stmt->fetch();
$event_date = $countdown_row ? $countdown_row['event_date'] : '2025-12-12 09:00:00';
$event_timestamp = strtotime($event_date) * 1000; // JS için milisaniye
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AFL-MUN</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
  font-family: "Raleway", sans-serif;
  font-optical-sizing: auto;
  font-weight: <weight>;
  font-style: normal;
        }
        body {
            background: #000;
            color: #c9c9d1;
            overflow-x: hidden;
        }
        /* Hero Section */
        .hero {
            height: 90vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('/assets/images/carousel/new.jpg') center/cover;
        }
        .carousel {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .carousel-slide.active {
            opacity: 1;
        }
        .carousel-slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        .hero-content {
            position: relative;
            z-index: 1;
            animation: fadeIn 1.2s ease-out;
        }
        .hero-title {
            font-weight: 800;
            font-size: 6rem;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255,255,255,0.5);
            margin-bottom: 20px;
            color: #f9fff0;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 1200px) {
          .hero-title { font-size: 4.5rem; }
        }
        @media (max-width: 768px) {
          .hero-title { font-size: 3rem; }
        }
        @media (max-width: 480px) {
          .hero-title { font-size: 2.2rem; }
        }

        /* Ana içerik */
        .main-content {
            background: linear-gradient(to bottom, #000, #0708249c, #0f0f1f, #100f4a98);
            position: relative;
            z-index: 1;
        }

        /* ===== Countdown ===== */
        .countdown-section {
            padding: 100px 0 60px;
            text-align: center;
        }
        .countdown-title {
            font-size: 1.8rem;
            margin-bottom: 40px;
            color: #c9c9d1;
            letter-spacing: 1px;
        }
        .countdown {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        .countdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .countdown-value {
            font-size: 4rem;
            font-weight: 700;
            color: #f9fff0;
            line-height: 1;
            text-shadow: 0 0 10px rgba(201, 201, 209, 0.3);
        }
        .countdown-label {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
            color: #000;
            background: #f5f2e9;
            padding: 5px 15px;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
            background-image: repeating-linear-gradient(
                to bottom,
                #f5f2e9,
                #f5f2e9 18px,
                #e4e0d4 19px
            );
        }

        /* Logo Section */
        .logo-section {
            padding: 60px 0;
            text-align: center;
        }
        .main-logo {
            height: 300px;
        }

        /* ===== FAQ ===== */
        .faq-section {
            max-width: 800px;
            margin: 0 auto;
            padding: 60px 20px;
        }
        .faq-item {
            border-bottom: 1px solid rgba(201, 201, 209, 0.2);
            padding: 18px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        .faq-item h3 {
            font-size: 1.2rem;
            color: #f9fff0;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-item h3 .arrow {
            transform: rotate(-90deg);
            transition: transform 0.3s ease;
            font-size: 1.1rem;
            opacity: 0.8;
        }
        .faq-item.active h3 .arrow {
            transform: rotate(0deg);
        }
        .faq-item p {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            line-height: 1.6;
            transition: all 0.4s ease;
            color: #c9c9d1;
            margin-top: 8px;
        }
        .faq-item.active p {
            max-height: 200px;
            opacity: 1;
        }

        /* Footer */
        footer {
            background: #04033b;
            padding: 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            text-align: center;
            border-top: 1px solid rgba(201, 201, 209, 0.1);
        }
        .footer-left {
            font-size: 1.8rem;
            font-weight: 600;
            color: #f9fff0;
            flex: 1;
        }
        .footer-center img {
            height: 160px;
            filter: drop-shadow(0 0 10px rgba(201,201,209,0.4));
        }
        .footer-right {
            font-size: 1.4rem;
            opacity: 0.7;
            flex: 1;
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.8rem; }
            .countdown-value { font-size: 3rem; }
            .countdown { gap: 15px; }
            footer { flex-direction: column; gap: 15px; }
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Hero -->
<section class="hero">
    <div class="carousel">
        <div class="carousel-slide active" style="background-image: url('/assets/images/carousel/culture.jpg')" data-subtitle="Model United Nations For Tomorrow"></div>
        <div class="carousel-slide" style="background-image: url('/assets/images/carousel/building.jpg')" data-subtitle="Model United Nations For Culture"></div>
        <div class="carousel-slide" style="background-image: url('/assets/images/carousel/new.jpg')" data-subtitle="Model United Nations For Unity & Peace"></div>
        
    </div>
    <div class="hero-content">
       <h1 class="hero-title">#SpeakforPeace</h1>
    </div>
</section>
<style>
/* Hero arka planına lacivert gölgeli ve hafif blur efekti */
.hero .carousel-slide {
    filter: grayscale(100%) brightness(50%) sepia(20%) hue-rotate(200deg) saturate(150%) blur(2px);
    background-size: cover;
    background-position: center;
}

/* Countdown mobil düzenleme */
@media (max-width: 480px) {
    .countdown {
        flex-wrap: nowrap; /* Alt satıra geçmesini engelle */
        gap: 6px;         /* Aralarındaki boşluğu biraz küçült */
        overflow-x: auto;  /* Gerekirse yatay kaydırma ekle */
    }

    .countdown-item {
        flex: 1 1 auto;    /* Eşit genişlikte yer kaplasın */
    }

    .countdown-value {
        font-size: 2rem;   /* Yazı boyutunu küçült */
    }

    .countdown-label {
        font-size: 0.8rem; /* Label boyutunu küçült */
    }
}

</style>
<div class="main-content">
    <!-- Countdown -->
    <section class="countdown-section">
        <div class="countdown-title">AFLMUN'25 Starts In</div>
        <div class="countdown">
            <div class="countdown-item">
                <div class="countdown-value" id="days">00</div>
                <div class="countdown-label">Days</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-value" id="hours">00</div>
                <div class="countdown-label">Hours</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-value" id="minutes">00</div>
                <div class="countdown-label">Minutes</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-value" id="seconds">00</div>
                <div class="countdown-label">Seconds</div>
            </div>
        </div>
    </section>

    <!-- Logo -->
    <section class="logo-section">
        <img src="/assets/images/logo.png" alt="AFL MUN Logo" class="main-logo">
    </section>

    <h1 style="text-align: center; margin-bottom: 8px; color: #f9fff0;">Frequently Asked Questions</h1>

    <!-- FAQ -->
    <section class="faq-section">
        <div class="faq-item">
            <h3>What is MUN? <span class="arrow">›</span></h3>
            <p>MUN is the abbreviation for Model United Nations. These conferences simulate UN meetings with committees that tackle global issues. Students attend as delegates representing countries, organizations, or individuals to propose and negotiate solutions. Before the conference, they research and prepare position papers. During sessions, they debate, amend, and vote on draft resolutions. Outstanding delegates may receive awards for performance and diplomacy.</p>
        </div>

        <div class="faq-item">
            <h3>How do I apply? <span class="arrow">›</span></h3>
            <p>Just click "Apply" on our home page.</p>
        </div>

        <div class="faq-item">
            <h3>What is your procedure? <span class="arrow">›</span></h3>
            <p>We follow Harvard procedure.</p>
        </div>

        <div class="faq-item">
            <h3>What is your dress code? <span class="arrow">›</span></h3>
            <p>Our dress code is Western Business Attire.</p>
        </div>

        <div class="faq-item">
            <h3>Will food be provided during the conference? <span class="arrow">›</span></h3>
            <p>Yes, meals and snacks during coffee breaks will be provided throughout the conference.</p>
        </div>

        <div class="faq-item">
            <h3>Can we change our countries/committees? <span class="arrow">›</span></h3>
            <p>Allocations are decided by our academic team after careful evaluation, so changes are not possible.</p>
        </div>

        <div class="faq-item">
            <h3>Will participants receive certificates? <span class="arrow">›</span></h3>
            <p>Yes, all delegates who attend the required number of sessions will receive certificates.</p>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // Countdown
    const eventTime = <?= $event_timestamp ?>;
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = eventTime - now;
        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // FAQ Toggle
    document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('click', () => {
            item.classList.toggle('active');
        });
    });

    // Carousel
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const totalSlides = slides.length;
    function showNextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % totalSlides;
        slides[currentSlide].classList.add('active');
    }
    setInterval(showNextSlide, 5000);
</script>

</body>
</html>
