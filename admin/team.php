<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

include 'db.php';

$message = '';

// Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $title = trim($_POST['title']);
    $visible = isset($_POST['is_visible']) ? 1 : 0;

    // Fotoğraf yükleme
    $photo_path = '';
    if (!empty($_FILES['photo_file']['name'])) {
        $upload_dir = '../assets/images/team/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_ext = strtolower(pathinfo($_FILES['photo_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_ext, $allowed) && $_FILES['photo_file']['error'] === 0) {
            $file_name = 'team_' . time() . '.' . $file_ext;
            $photo_path = 'assets/images/team/' . $file_name;
            move_uploaded_file($_FILES['photo_file']['tmp_name'], '../' . $photo_path);
        }
    } elseif (!empty($_POST['photo_url'])) {
        $photo_path = trim($_POST['photo_url']);
    }

    if ($name && $title && $photo_path) {
        $stmt = $pdo->prepare("INSERT INTO team_members (name, photo, title, is_visible) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $photo_path, $title, $visible]);
        header('Location: team.php?success=added');
        exit;
    } else {
        $message = "All fields are required and image must be valid (JPG/PNG).";
    }
}

// Silme
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM team_members WHERE id = ?")->execute([$id]);
    header('Location: team.php?success=deleted');
    exit;
}

// Toggle visibility
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("SELECT is_visible FROM team_members WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    $new = $current ? 0 : 1;
    $pdo->prepare("UPDATE team_members SET is_visible = ? WHERE id = ?")->execute([$new, $id]);
    header('Location: team.php');
    exit;
}

$members = $pdo->query("SELECT * FROM team_members ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Members - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background:#0f0f1f; color:#c9c9d1; padding-top: 70px; /* Navbar yüksekliği kadar */ }
        
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

        .btn {
            background: #8b5cf6;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn:hover {
            background: #7c3aed;
            transform: translateY(-2px);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
        }

        .alert-success {
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.3);
            color: #6ee7b7;
        }

        .alert-error {
            background: rgba(220,38,38,0.15);
            border: 1px solid rgba(220,38,38,0.3);
            color: #fca5a5;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table-container {
            background: rgba(16,15,74,0.3);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
        }

        table { width: 100%; border-collapse: collapse; }
        thead { background: rgba(139,92,246,0.1); }
        th {
            padding: 16px;
            text-align: left;
            color: #f9fff0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        td { padding: 16px; border-top: 1px solid rgba(201,201,209,0.05); }
        tbody tr:hover { background: rgba(139,92,246,0.05); }

        .member-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid rgba(139,92,246,0.3);
        }

        .status-yes { color: #10b981; }
        .status-no { color: #dc2626; }

        .action-buttons { display: flex; gap: 10px; }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }

        .form-section {
            background: rgba(16,15,74,0.3);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
        }

        .form-section h3 {
            color: #f9fff0;
            margin-bottom: 20px;
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

        .form-group input[type="text"],
        .form-group input[type="file"] {
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .checkbox-group input {
            accent-color: #8b5cf6;
        }

        small.hint {
            display: block;
            color: #a1a1aa;
            font-size: 0.85rem;
            margin-top: 6px;
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
            <li><a href="team.php" class="active"><i class="fas fa-user-friends"></i> Team Members</a></li>
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
            <h1><i class="fas fa-user-friends"></i> Team Members</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <span>
                    <?php 
                    if ($_GET['success'] === 'deleted') echo "Team member deleted successfully!";
                    if ($_GET['success'] === 'added') echo "Team member added successfully!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h3><i class="fas fa-plus-circle"></i> Add New Member</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Upload Photo *</label>
                    <input type="file" name="photo_file" accept="image/*" required>
                    <small class="hint">Accepted formats: JPG, JPEG, PNG, WEBP</small>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="is_visible" id="is_visible" checked>
                    <label for="is_visible">Visible on site</label>
                </div>
                <br>
                <button type="submit" name="add" class="btn">
                    <i class="fas fa-user-plus"></i> Add Member
                </button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Title</th>
                        <th>Visible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px; color:#a1a1aa;">
                            <i class="fas fa-users" style="font-size:2rem; margin-bottom:10px;"></i><br>
                            No team members yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($members as $m): ?>
                    <tr>
                        <td>
                            <img src="../<?= htmlspecialchars($m['photo']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="member-photo">
                        </td>
                        <td><?= htmlspecialchars($m['name']) ?></td>
                        <td><?= htmlspecialchars($m['title']) ?></td>
                        <td>
                            <?php if ($m['is_visible']): ?>
                                <span class="status-yes"><i class="fas fa-eye"></i> Yes</span>
                            <?php else: ?>
                                <span class="status-no"><i class="fas fa-eye-slash"></i> No</span>
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <a href="?toggle=<?= $m['id'] ?>" class="btn btn-success btn-sm">
                                <?= $m['is_visible'] ? 'Hide' : 'Show' ?>
                            </a>
                            <a href="?delete=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this member?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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

        // Menü dışına tıklanırsa kapanır (mobilde)
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>