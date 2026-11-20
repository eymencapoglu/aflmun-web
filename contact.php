<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - AFL MUN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Raleway", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;

        }
        body {
            background: #000;
            color: #c9c9d1;
            overflow-x: hidden;

        }

        /* Hero Section */
        .contact-hero {
            height: 90vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .contact-hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
             background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('/assets/images/carousel/building.jpg') center/cover;
            z-index: 0;
        }
        .contact-hero-content {
            position: relative;
            z-index: 2;
        }
        .hero {
            height: 90vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .hero-title {
            font-weight: 800;
            font-size: 6rem;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255,255,255,0.5);
            margin-bottom: 20px;
            color: #f9fff0;
        }
        .hero-subtitle {
            font-size: 1.8rem;
            font-weight: 300;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            opacity: 0.9;
            color: #c9c9d1;
            margin-top: 20px;
        }

        /* Contact Info Section */
        .contact-section {
            padding: 80px 20px;
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }
        .contact-cards {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 50px;
        }
        .contact-card {
            background: rgba(16, 15, 74, 0.3);
            border: 1px solid rgba(201, 201, 209, 0.1);
            border-radius: 12px;
            padding: 30px 25px;
            width: 250px;
            transition: all 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-8px);
            border-color: rgba(201, 201, 209, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        .contact-card i {
            font-size: 2.5rem;
            color: #f9fff0;
            margin-bottom: 20px;
        }
        .contact-card h3 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #f9fff0;
        }
        .contact-card a,
        .contact-card p {
            color: #c9c9d1;
            text-decoration: none;
            font-size: 1.1rem;
            line-height: 1.5;
            opacity: 0.9;
            transition: opacity 0.3s;
        }
        .contact-card a:hover {
            opacity: 1;
            color: #f9fff0;
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

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 3rem; }
            .hero-subtitle { font-size: 1.3rem; }
            .contact-cards { gap: 20px; }
            footer {
                flex-direction: column;
                gap: 15px;
            }
        }
        @media (max-width: 480px) {
            .hero-title { font-size: 2.2rem; }
            .contact-card { width: 100%; max-width: 300px; }
        }
        .main-content{
            background: linear-gradient(to bottom, #000, #0708249c, #0f0f1f, #100f4a98);
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="contact-hero">
    <div class="contact-hero-bg"></div>
    <div class="contact-hero-content">
        <h1 class="hero-title">Contact Us</h1>
        <div class="hero-subtitle">Get in touch with the AFLMUN'25 Team</div>
    </div>
</section>

<div class="main-content">
    <div class="contact-section">
        <div class="contact-cards">
            <!-- Instagram -->
            <div class="contact-card">
                <i class="fab fa-instagram"></i>
                <h3>Instagram</h3>
                <a href="https://www.instagram.com/aflmun25" target="_blank">@aflmun</a>
            </div>

            <!-- Email -->
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <a href="mailto:info@aflmun.org">info@aflmun.org</a>
            </div>

            <!-- Website -->
            <div class="contact-card">
                <i class="fas fa-globe"></i>
                <h3>Website</h3>
                <a href="https://www.aflmun.org" target="_blank">www.aflmun.org</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>