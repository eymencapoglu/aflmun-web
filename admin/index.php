<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

// Ayarlardan show_executive değerini al
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'show_executive'");
$stmt->execute();
$show_executive = $stmt->fetchColumn() ?? '1';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AFL MUN Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background:#0f0f1f; color:#c9c9d1; padding-top: 70px; }

        /* Top Responsive Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #04033b;
            padding: 0 20px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .logo {
            color: #f9fff0;
            font-size: 1.4rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 15px;
        }

        .nav-links a {
            color: #c9c9d1;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background: rgba(201,201,209,0.1);
            color: #f9fff0;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #c9c9d1;
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .main-content {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header h1 {
            color: #f9fff0;
            font-size: 2rem;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .menu-card {
            background: rgba(16,15,74,0.3);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            border-color: rgba(139,92,246,0.4);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .menu-card i {
            font-size: 2.2rem;
            color: #8b5cf6;
            margin-bottom: 15px;
        }

        .menu-card h3 {
            color: #f9fff0;
            margin-bottom: 10px;
        }

        .menu-card p {
            font-size: 0.95rem;
            opacity: 0.8;
        }

        .setting-section {
            background: rgba(16,15,74,0.3);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
        }

        .setting-section h3 {
            color: #f9fff0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        form.settings-form {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .radio-group {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        input[type="radio"] {
            accent-color: #8b5cf6;
        }

        button.save-btn {
            background: #8b5cf6;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        button.save-btn:hover {
            background: #7c3aed;
        }

        .logout-link {
            display: inline-block;
            margin-top: 30px;
            color: #dc2626;
            text-decoration: underline;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }

            .nav-links {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 250px;
                height: calc(100vh - 60px);
                background: #04033b;
                flex-direction: column;
                padding: 20px;
                gap: 10px;
                transition: 0.3s;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }

            .nav-links.active {
                left: 0;
            }

            .hamburger {
                display: flex;
            }

            .hamburger.active span:nth-child(1) {
                transform: rotate(-45deg) translate(-5px, 6px);
            }

            .hamburger.active span:nth-child(2) {
                opacity: 0;
            }

            .hamburger.active span:nth-child(3) {
                transform: rotate(45deg) translate(-5px, -6px);
            }

            .main-content {
                padding: 20px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Responsive Top Navbar -->
    <nav class="top-navbar">
        <div class="logo">AFL MUN</div>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="committees.php"><i class="fas fa-users"></i> Committees</a></li>
            <li><a href="apply_cards.php"><i class="fas fa-file-signature"></i> Apply Cards</a></li>
            <li><a href="team.php"><i class="fas fa-user-friends"></i> Team Members</a></li>
            <li><a href="countdown.php"><i class="fas fa-clock"></i> Countdown</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-eye"></i> View Site</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        </div>

        <div class="menu-grid">
            <a href="committees.php" class="menu-card">
                <i class="fas fa-users"></i>
                <h3>Committees</h3>
                <p>Manage MUN committees</p>
            </a>
            <a href="apply_cards.php" class="menu-card">
                <i class="fas fa-file-signature"></i>
                <h3>Apply Cards</h3>
                <p>Configure application types</p>
            </a>
            <a href="team.php" class="menu-card">
                <i class="fas fa-user-friends"></i>
                <h3>Team Members</h3>
                <p>Edit executive board</p>
            </a>
            <a href="countdown.php" class="menu-card">
                <i class="fas fa-clock"></i>
                <h3>Countdown</h3>
                <p>Set event date & time</p>
            </a>
        </div>

        <div class="setting-section">
            <h3><i class="fas fa-cog"></i> General Settings</h3>
            <form method="POST" action="toggle_executive.php" class="settings-form">
                <div class="radio-group">
                    <label>
                        <input type="radio" name="show" value="1" <?= $show_executive == '1' ? 'checked' : '' ?>> Show Executive Menu
                    </label>
                    <label>
                        <input type="radio" name="show" value="0" <?= $show_executive == '0' ? 'checked' : '' ?>> Hide Executive Menu
                    </label>
                </div>
                <button type="submit" class="save-btn">Save Settings</button>
            </form>
        </div>

        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <script>
        // Hamburger Menu Toggle
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        // Menü dışına tıklanırsa kapanır
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>