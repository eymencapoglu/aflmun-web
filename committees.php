<?php
require_once 'config.php';

$committees = $pdo->query("SELECT * FROM committees ORDER BY id")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Committees - AFLMUN'25</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <style>
        /* Mevcut temanın CSS'leri buraya */
        
        .committees-hero {
            height: 90vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .committees-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .committees-hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('/assets/images/carousel/peace.jpeg') center/cover;

            z-index: 0;
        }
        .committees-hero-content {
            position: relative;
            z-index: 2;
        }
        
        .committees-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            padding: 60px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .committee-card {
            background: rgba(16, 15, 74, 0.3);
            border: 1px solid rgba(201, 201, 209, 0.1);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }
        .committee-card:hover {
            transform: translateY(-10px);
            border-color: rgba(201, 201, 209, 0.3);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }
        .committee-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .committee-content {
            padding: 25px;
        }
        .committee-content h3 {
            color: #f9fff0;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .committee-content p {
            line-height: 1.6;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        .committee-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #f9fff0;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .committee-link:hover {
            gap: 12px;
            color: #c9c9d1;
        }
    </style>
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
        /* Gradient overlay - siyah kararma */
        .carousel-slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(0,0,0,0.3) 0%, rgba(0, 0, 0, 0.31) 100%);
        }
        /* Ekstra overlay daha belirgin siyah kararma için */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.23), rgba(0, 0, 0, 0.36));
            z-index: 0;
        }
        .hero-content {
            position: relative;
            z-index: 1;
            animation: fadeIn 1.2s ease-out;
        }
        .hero h1 {
            font-size: 4.5rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            margin-bottom: 20px;
            color: #f9fff0;
        }
        .hero-subtitle {
            font-size: 1.8rem;
            font-weight: 300;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            opacity: 0.9;
            transition: all 0.5s ease-in-out;
            color: #c9c9d1;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Ana içerik bölümü - aşağı indikçe laciverte geçiş */
        .main-content {
            background: linear-gradient(to bottom, #000, #0708249c, #0f0f1f, #100f4a98);
            position: relative;
            z-index: 1;
        }
        
        /* Countdown */
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
            gap: 30px;
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
            margin-top: 8px;
            opacity: 0.8;
            color: #c9c9d1;
        }
        /* Logo Section */
        .logo-section {
            padding: 60px 0;
            text-align: center;
        }
        .main-logo {
            height: 300px;
            filter: drop-shadow(0 0 15px rgba(201, 201, 209, 0.5));
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        /* FAQ */
        .faq-section {
            padding: 60px 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .faq-item {
            margin-bottom: 20px;
            padding: 20px;
            background: rgba(16, 15, 74, 0.3);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid rgba(201, 201, 209, 0.1);
        }
        .faq-item:hover {
            background: rgba(16, 15, 74, 0.5);
            border-color: rgba(201, 201, 209, 0.3);
        }
        .faq-item h3 {
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #f9fff0;
        }
        .faq-item p {
            display: none;
            margin-top: 12px;
            line-height: 1.6;
        }
        .faq-item.active p {
            display: block;
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
            filter: drop-shadow(0 0 10px rgba(201, 201, 209, 0.4));
        }
        .footer-right {
            font-size: 1.4rem;
            opacity: 0.7;
            flex: 1;
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.8rem; }
            .hero-subtitle { font-size: 1.3rem; }
            .countdown-value { font-size: 3rem; }
            .countdown { gap: 15px; }
            footer {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
    <style>
        
        .hero-title {
            font-weight: 800;
            font-size: 6rem;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255,255,255,0.5);
            margin-bottom: 20px;
            color: #f9fff0;
        }

        @media (max-width: 1200px) {
          .hero-title {
            font-size: 4.5rem;
          }
        }

        @media (max-width: 768px) {
          .hero-title {
            font-size: 3rem;
          }
        }

        @media (max-width: 480px) {
          .hero-title {
            font-size: 2.2rem;
          }
        }

    </style>

</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="committees-hero">
    <div class="committees-hero-bg"></div>
    <div class="committees-hero-content">
        <h1 class="hero-title">Our Committees</h1>
        <div class="hero-subtitle">Explore the diverse committees of AFLMUN'25</div>
    </div>
</section>

<div class="main-content">
    <div class="committees-grid">
        <?php foreach ($committees as $committee): ?>
        <div class="committee-card" onclick="window.location.href='committee_detail.php?id=<?= $committee['id'] ?>'">
            <?php if ($committee['image_path']): ?>
            <img src="<?= htmlspecialchars($committee['image_path']) ?>" alt="<?= htmlspecialchars($committee['name']) ?>" class="committee-image">
            <?php endif; ?>
            <div class="committee-content">
                <h3><?= htmlspecialchars($committee['name']) ?></h3>
                <p class="committee-shortname"><?= htmlspecialchars($committee['description']) ?></p>
                <br>
                <a href="committee-detail/<?= $committee['id'] ?>" class="committee-link">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>