<?php
require_once 'config.php';

$early_applications_open = true;
$applications = $pdo->query("SELECT * FROM applications WHERE is_active = 1 ORDER BY sort_order, id")->fetchAll();
$show_success = isset($_GET['applied']);
$success_role = $_GET['role'] ?? '';
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
        }
        body {
            background: #000;
            color: #c9c9d1;
            overflow-x: hidden;
        }
        .hero {
            height: 90vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('/assets/images/carousel/culture.jpg') center/cover;
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
        .main-content {
            background: linear-gradient(to bottom, #000, #0708249c, #0f0f1f, #100f4a98);
            padding: 60px 20px;
        }
        .section-title {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 50px;
            color: #f9fff0;
            text-shadow: 0 0 10px rgba(255,255,255,0.2);
        }
        .fees-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .fee-card {
            background: rgba(15, 15, 43, 0.7);
            border: 1px solid rgba(100, 120, 180, 0.2);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .fee-card:hover {
            transform: translateY(-8px);
            border-color: rgba(120, 140, 200, 0.3);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 12px;
        }
        .overlay-content {
            color: #f9fff0;
            text-align: center;
        }
        .overlay-content i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #a1a1aa;
        }
        .main-message {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .sub-message {
            font-size: 0.95rem;
            opacity: 0.8;
        }
        .fee-title {
            font-size: 1.6rem;
            color: #f9fff0;
            margin-bottom: 15px;
        }
        .fee-amount {
            font-size: 2.2rem;
            font-weight: 700;
            color: #a0d2eb;
            margin: 15px 0;
        }
        .fee-details {
            margin: 20px 0;
            text-align: left;
        }
        .fee-detail {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .fee-detail i {
            color: #10b981;
            margin-top: 4px;
        }
        .hero-title {
            font-weight: 800;
            font-size: 6rem;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255,255,255,0.5);
            margin-bottom: 20px;
            color: #f9fff0;
        }
        .apply-btn {
            display: inline-block;
            background: #13133a;
            color: #f9fff0;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
            border: 1px solid rgba(100, 120, 180, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .apply-btn:hover {
            background: #1a1a4a;
            border-color: rgba(130, 150, 210, 0.4);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }
        .muted {
            color: #a1a1aa;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto 40px;
            font-size: 1.1rem;
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

        @media (max-width: 768px) {
            .hero h1 { font-size: 2.2rem; }
            .hero-subtitle { font-size: 1.1rem; }
            .section-title { font-size: 1.8rem; }
            .fee-amount { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Apply to AFLMUN'25</h1>
        <div class="hero-subtitle">Choose your role and join the conference</div>
    </div>
</section>

<div class="main-content">
    <h2 class="section-title">
        <?= $early_applications_open ? 'ALL APPLICATIONS ARE CLOSED' : 'APPLICATIONS ARE CLOSED' ?>
    </h2>

    <?php if ($show_success): ?>
    <div class="alert-success">
        <strong>Thank you!</strong><br>
        <span><?= htmlspecialchars(ucfirst($success_role)) ?> application has been received. We will contact you shortly.</span>
    </div>
    <?php endif; ?>

    <div class="fees-container">
        <?php if (empty($applications)): ?>
            <p style="text-align:center; color:#a1a1aa; grid-column: 1 / -1;">No application types available at the moment.</p>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
            <div class="fee-card">
                <?php if (!$early_applications_open): ?>
                <div class="card-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-lock"></i>
                        <div class="main-message">Applications are closed</div>
                        <div class="sub-message">Check back soon!</div>
                    </div>
                </div>
                <?php endif; ?>

                <h3 class="fee-title"><?= htmlspecialchars($app['title']) ?></h3>
<div class="fee-amount">
    <?php if ($app['fee'] === '0.00'): ?>
        Free
    <?php else: ?>
        <?= htmlspecialchars($app['fee']) ?>₺
    <?php endif; ?>
</div>

                <div class="fee-details">
                    <?php
                    $items = json_decode($app['description'], true);
                    if ($items && is_array($items)) {
                        foreach ($items as $item) {
                            echo '<div class="fee-detail"> > <span>' . htmlspecialchars($item) . '</span></div>';
                        }
                    } else {
                        foreach (explode("\n", trim($app['description'])) as $line) {
                            if (!empty($line)) {
                                echo '<div class="fee-detail"> > <span>' . htmlspecialchars($line) . '</span></div>';
                            }
                        }
                    }
                    ?>
                </div>

                <?php if ($early_applications_open): ?>
                    <a href="<?= htmlspecialchars($app['apply_link']) ?>" target="_blank" class="apply-btn">Apply NOW</a>
                    <?php if (!empty($app['accommodation_note'])): ?>
                        <div class="muted">Accommodation Fee: <?= htmlspecialchars($app['accommodation_note']) ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>