<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

include 'db.php';

$stmt = $pdo->query("SELECT event_date FROM countdown WHERE id = 1");
$countdown = $stmt->fetch();
$current_date = $countdown ? $countdown['event_date'] : '2025-12-12 09:00:00';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_date = $_POST['event_date'] ?? '';
    if ($new_date) {
        $stmt = $pdo->prepare("UPDATE countdown SET event_date = ? WHERE id = 1");
        $stmt->execute([$new_date]);
        $message = "Countdown updated successfully!";
        $current_date = $new_date;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Countdown - Admin Panel</title>
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
            max-width: 700px;
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

        .card {
            background: rgba(16,15,74,0.3);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 12px;
            padding: 30px;
        }

        .card h2 {
            color: #f9fff0;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #f9fff0;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background: rgba(16,15,74,0.4);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 8px;
            color: #c9c9d1;
        }

        .form-group input:focus {
            outline: none;
            border-color: rgba(139,92,246,0.5);
        }

        .btn {
            background: #8b5cf6;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background: #7c3aed;
        }

        .alert-success {
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.3);
            color: #6ee7b7;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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

            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Responsive Top Navbar -->
    <nav class="top-navbar">
        <div class="logo">AFL MUN</div>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
            <li><a href="committees.php"><i class="fas fa-users"></i> Committees</a></li>
            <li><a href="apply_cards.php"><i class="fas fa-file-signature"></i> Apply Cards</a></li>
            <li><a href="team.php"><i class="fas fa-user-friends"></i> Team Members</a></li>
            <li><a href="countdown.php" class="active"><i class="fas fa-clock"></i> Countdown</a></li>
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
            <h1><i class="fas fa-clock"></i> Countdown Settings</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2><i class="fas fa-edit"></i> Set Event Date & Time</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="event_date">Event Date and Time:</label>
                    <input type="datetime-local" 
                           id="event_date" 
                           name="event_date" 
                           value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($current_date))) ?>"
                           required>
                </div>
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Update Countdown
                </button>
            </form>
        </div>
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