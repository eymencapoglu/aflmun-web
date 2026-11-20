<?php
require_once 'config.php';

$id = $_GET['id'] ?? 1;
$committee_id = (int)$id;

$stmt = $pdo->prepare("SELECT * FROM committees WHERE id = ?");
$stmt->execute([$committee_id]);
$committee = $stmt->fetch();

if (!$committee) {
    header('Location: committees.php');
    exit;
}

// ---------------------------------------------------------
// KOMİTE YÖNETİM EKİBİ (ACADEMIC TEAM) TANIMLAMALARI
// ---------------------------------------------------------
$chairs = [];
$c_name = strtoupper($committee['name']); 

// Resimlerin ana yolu (Sunucuda bu klasör assets/images içinde olmalı)
$base_path = 'assets/images/aca_photos/';

// NOT: Dosya isimleri senin verdiğin listeye göre birebir ayarlandı.

if (strpos($c_name, 'NAC') !== false || strpos($c_name, 'NORTH ATLANTIC') !== false) {
    $chairs[] = [
        'name' => 'Elif Naz KURU', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'NAC/Elif Naz Kuru.jpeg' // Uzantı .jpeg
    ];
    $chairs[] = [
        'name' => 'Barbaros KARACA', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'NAC/Barbaros Karaca.jpg'
    ];
} 
elseif (strpos($c_name, 'LEGAL') !== false || strpos($c_name, 'GA6') !== false) {
    $chairs[] = [
        'name' => 'Ahmet Kağan TUNALI', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'LEGAL/Ahmet Kağan Tunalı.jpg'
    ];
    $chairs[] = [
        'name' => 'Toprak BOYRAZ', 
        'role' => 'Academic Assistant',
        'img'  => $base_path . 'LEGAL/toprak boyraz.jpg' // Küçük harfle yazılmış
    ];
} 
elseif (strpos($c_name, 'UNEP') !== false) {
    $chairs[] = [
        'name' => 'Devrim Deniz SAĞCAN', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'UNEP/Devrim Deniz Sağcan.jpeg' // Uzantı .jpeg
    ];
} 
elseif (strpos($c_name, 'SPECPOL') !== false || strpos($c_name, 'GA4') !== false) {
    $chairs[] = [
        'name' => 'Ahmet Erdem ÇAKMAK', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'SPECPOL/ahmeterdemcakmak.jpg'
    ];
    $chairs[] = [
        'name' => 'Neşet Erdem AĞIRMAN', 
        'role' => 'Academic Assistant',
        'img'  => $base_path . 'SPECPOL/Neşet Erdem Ağırman_.jpg' // Sonunda alt çizgi var
    ];
} 
elseif (strpos($c_name, 'JCC') !== false) {
    $chairs[] = [
        'name' => 'Ahmet Tarık CEYHAN', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'JCC/Ahmet Tarik CEYHAN.jpg' // Dosya adında 'Tarik' (noktasız)
    ];
    $chairs[] = [
        'name' => 'Mehmet Osman ÇEVLİK', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'JCC/MehmetOsmanÇevlik.jpg' // Boşluksuz yazılmış
    ];
    $chairs[] = [
        'name' => 'Vera Elvin TEPE', 
        'role' => 'Academic Assistant',
        'img'  => $base_path . 'JCC/Vera Elvin TEPE.jpg'
    ];
} 
elseif (strpos($c_name, 'SENATE') !== false || strpos($c_name, 'US') !== false) {
    $chairs[] = [
        'name' => 'Yağmur AKMAN', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'US-SENATE/Yağmur Akman.jpg' // Klasör US-SENATE
    ];
    $chairs[] = [
        'name' => 'Ömer KAHYA', 
        'role' => 'Under Secretary General',
        'img'  => $base_path . 'US-SENATE/Ömer Kahya.jpg'
    ];
} 
else {
    if (!empty($committee['chair_name'])) {
        $chairs[] = [
            'name' => $committee['chair_name'], 
            'role' => $committee['chair_title'] ?? 'Under Secretary General',
            'img'  => 'assets/images/logo.png' 
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($committee['name']) ?> - AFL-MUN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <style>
        /* --- GENEL AYARLAR --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #000;
            color: #e4e4e7;
            overflow-x: hidden;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            position: relative;
            height: 70vh;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-background {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover; background-position: center; background-repeat: no-repeat;
            filter: brightness(0.3); z-index: 0;
        }

        .hero-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.58), rgba(0, 0, 0, 0.51));
            z-index: 1;
        }

        .hero-content {
            position: relative; z-index: 2; text-align: center;
            padding: 0 20px; max-width: 900px;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-subtitle {
            font-size: 1.3rem; color: #a1a1aa; font-weight: 400;
            line-height: 1.6; margin-bottom: 25px;
        }
        .hero-title {
            font-weight: 800; font-size: 5rem; letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255,255,255,0.5);
            margin-bottom: 20px; color: #f9fff0;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- ANA İÇERİK --- */
        .main-content {
            background: linear-gradient(to bottom, #000, #070824, #0f0f1f, #100f4a);
            padding: 80px 0; min-height: 60vh;
        }

        .container {
            max-width: 1200px; margin: 0 auto; padding: 0 20px;
        }

        /* --- KART TASARIMI --- */
        .about-card {
            background: rgba(15, 15, 31, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 45px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            margin-bottom: 50px;
            display: block; /* Blok düzen */
        }

        .section-title {
            font-size: 2.2rem; font-weight: 700; color: #ffffff;
            margin-bottom: 25px; border-bottom: 2px solid rgba(255,255,255,0.1);
            padding-bottom: 15px; display: inline-block;
        }

        .about-text {
            font-size: 1.05rem; line-height: 1.9; color: #d4d4d8;
            margin-bottom: 30px; white-space: pre-line;
        }

        /* --- ACADEMIC TEAM GRID --- */
        .chairs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .chair-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .chair-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .chair-avatar-container {
            width: 120px; height: 120px; margin: 0 auto 20px auto;
            border-radius: 50%; padding: 3px;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.5));
            overflow: hidden;
        }

        .chair-avatar {
            width: 100%; height: 100%; border-radius: 50%;
            object-fit: cover; background-color: #0f0f1f;
        }

        .chair-name {
            font-size: 1.25rem; font-weight: 700; color: #fff;
            margin-bottom: 8px; letter-spacing: 0.5px;
        }

        .chair-role {
            font-size: 0.85rem; color: #a1a1aa; text-transform: uppercase;
            font-weight: 600; letter-spacing: 1px;
            background: rgba(255,255,255,0.05);
            padding: 5px 12px; border-radius: 20px; display: inline-block;
        }

        .download-btn {
            display: inline-flex; align-items: center; gap: 12px;
            background: rgba(255, 255, 255, 0.1); color: #e4e4e7;
            padding: 16px 32px; border-radius: 12px; text-decoration: none;
            font-weight: 600; font-size: 1rem; transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.15); margin-top: 20px;
        }
        .download-btn:hover {
            background: rgba(255, 255, 255, 0.15); transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); color: #fff;
        }

        /* --- MOBİL UYUMLULUK AYARLARI --- */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.8rem; }
            
            .about-card { 
                padding: 30px; 
                display: block !important; 
                width: 100% !important;
                margin-bottom: 40px !important;
            }
            .section-title { font-size: 1.8rem; }
            
            /* Chairs Grid Mobilde TEK SÜTUN (Flex Column) */
            .chairs-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 20px !important;
            }
            
            .chair-card {
                width: 100% !important;
                display: block !important;
                margin-bottom: 10px !important;
            }
            
            .download-btn { width: 100%; justify-content: center; }
            .container { display: block !important; }
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="hero-section">
    <div class="hero-background" style="background-image: url('<?= $committee['image_path'] ? 'assets/images/committees/' . basename($committee['image_path']) : 'assets/images/carousel/culture.jpeg' ?>');"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <p class="hero-subtitle">AFL Model United Nations</p>
        <h1 class="hero-title"><?= htmlspecialchars($committee['name']) ?></h1>
        <p class="hero-subtitle">Building tomorrow's global leaders through diplomacy</p>
    </div>
</section>

<section class="main-content">
    <div class="container">
        
        <div class="about-card">
            <h2 class="section-title">About the Committee</h2>
            <div class="about-text">
                <?= nl2br(htmlspecialchars($committee['details'])) ?>
            </div>
            
            <?php if ($committee['guide_pdf']): ?>
            <a href="<?= $committee['guide_pdf'] ?>" class="download-btn" target="_blank">
                <i class="fas fa-file-download"></i>
                Download Committee Guide
            </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($chairs)): ?>
        <div class="about-card">
            <h2 class="section-title">Academic Team</h2>
            <div class="chairs-grid">
                <?php foreach ($chairs as $chair): ?>
                <div class="chair-card">
                    <div class="chair-avatar-container">
                        <img src="<?= htmlspecialchars($chair['img'] ?? 'assets/images/logo.png') ?>" alt="<?= htmlspecialchars($chair['name']) ?>" class="chair-avatar">
                    </div>
                    <div class="chair-name"><?= htmlspecialchars($chair['name']) ?></div>
                    <div class="chair-role"><?= htmlspecialchars($chair['role']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Scroll reveal ve smooth scroll scriptleri
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) { target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.about-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'all 0.6s ease-out';
    observer.observe(el);
});
</script>

</body>
</html>