<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}
include 'db.php';

// Fetch all application types
$applications = $pdo->query("SELECT * FROM applications ORDER BY sort_order, id")->fetchAll();

// Add new
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_application'])) {
    $title = trim($_POST['title']);
    $short_name = trim($_POST['short_name'] ?? '');
    $fee = $_POST['fee'];
    $description = $_POST['description']; // Expect JSON or plain text
    $apply_link = trim($_POST['apply_link']);
    $accommodation_note = trim($_POST['accommodation_note'] ?? null);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    $stmt = $pdo->prepare("INSERT INTO applications (title, short_name, fee, description, apply_link, accommodation_note, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $short_name, $fee, $description, $apply_link, $accommodation_note, $is_active, $sort_order]);
    header('Location: apply_cards.php?success=added');
    exit;
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_application'])) {
    $id = $_POST['application_id'];
    $title = trim($_POST['title']);
    $short_name = trim($_POST['short_name'] ?? '');
    $fee = $_POST['fee'];
    $description = $_POST['description'];
    $apply_link = trim($_POST['apply_link']);
    $accommodation_note = trim($_POST['accommodation_note'] ?? null);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    $stmt = $pdo->prepare("UPDATE applications SET title = ?, short_name = ?, fee = ?, description = ?, apply_link = ?, accommodation_note = ?, is_active = ?, sort_order = ? WHERE id = ?");
    $stmt->execute([$title, $short_name, $fee, $description, $apply_link, $accommodation_note, $is_active, $sort_order, $id]);
    header('Location: apply_cards.php?success=updated');
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: apply_cards.php?success=deleted');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Management - Admin Panel</title>
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #f9fff0;
            font-size: 2rem;
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

        .btn-secondary {
            background: #6b7280;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-danger {
            background: #dc2626;
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-success {
            background: #10b981;
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .btn-success:hover {
            background: #059669;
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

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table-container {
            background: rgba(16,15,74,0.3);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(139,92,246,0.1);
        }

        th {
            padding: 16px;
            text-align: left;
            color: #f9fff0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-top: 1px solid rgba(201,201,209,0.05);
        }

        tbody tr:hover {
            background: rgba(139,92,246,0.05);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 1000;
            overflow-y: auto;
            padding: 20px;
        }

        .modal.show {
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .modal-content {
            background: #13132b;
            border-radius: 16px;
            width: 100%;
            max-width: 700px;
            margin: 40px auto;
            border: 1px solid rgba(139,92,246,0.2);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 24px 30px;
            border-bottom: 1px solid rgba(201,201,209,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            color: #f9fff0;
            font-size: 1.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            color: #c9c9d1;
            font-size: 28px;
            cursor: pointer;
            line-height: 1;
            transition: all 0.2s;
        }

        .close-btn:hover {
            color: #f9fff0;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #f9fff0;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(16,15,74,0.4);
            border: 1px solid rgba(201,201,209,0.1);
            border-radius: 8px;
            color: #c9c9d1;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: rgba(139,92,246,0.5);
            background: rgba(16,15,74,0.6);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid rgba(201,201,209,0.1);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
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

            .form-row {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .table-container {
                overflow-x: auto;
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
            <li><a href="apply_cards.php" class="active"><i class="fas fa-file-signature"></i> Apply Cards</a></li>
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
            <h1><i class="fas fa-file-signature"></i> Application Types</h1>
            <button class="btn" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Application Type
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>
                    <?php 
                    if ($_GET['success'] == 'added') echo "Application type added successfully!";
                    if ($_GET['success'] == 'updated') echo "Application type updated successfully!";
                    if ($_GET['success'] == 'deleted') echo "Application type deleted successfully!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Fee</th>
                        <th>Link</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:40px; color:#a1a1aa;">
                            <i class="fas fa-inbox" style="font-size:2rem; margin-bottom:10px;"></i>
                            No application types yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td>
                            <strong style="color:#f9fff0;"><?= htmlspecialchars($app['title']) ?></strong>
                            <br><small>ID: <?= $app['id'] ?></small>
                        </td>
                        <td><?= htmlspecialchars($app['fee']) ?>₺</td>
                        <td style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <a href="<?= htmlspecialchars($app['apply_link']) ?>" target="_blank" style="color:#8b5cf6;"><?= htmlspecialchars(parse_url($app['apply_link'], PHP_URL_HOST)) ?></a>
                        </td>
                        <td>
                            <i class="fas fa-<?= $app['is_active'] ? 'toggle-on' : 'toggle-off' ?>" style="color:<?= $app['is_active'] ? '#10b981' : '#dc2626' ?>;"></i>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-success" onclick='openEditModal(<?= json_encode($app, JSON_HEX_TAG | JSON_HEX_AMP) ?>)'>
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger" onclick="confirmDelete(<?= $app['id'] ?>, '<?= htmlspecialchars($app['title']) ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="applyModal" class="modal">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h2 id="modalTitle">Add Application Type</h2>
                    <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="application_id" id="application_id">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    <div class="form-group">
                        <label>Short Name (optional)</label>
                        <input type="text" name="short_name" id="short_name">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fee (₺) *</label>
                            <input type="number" step="0.01" name="fee" id="fee" required>
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description (One per line or JSON array)</label>
                        <textarea name="description" id="description" placeholder="e.g.&#10;Develop public speaking&#10;Enhance research skills"></textarea>
                        <small>Each line becomes a bullet point on the apply page.</small>
                    </div>
                    <div class="form-group">
                        <label>Apply Link (Full URL) *</label>
                        <input type="url" name="apply_link" id="apply_link" required>
                    </div>
                    <div class="form-group">
                        <label>Accommodation Note (optional)</label>
                        <input type="text" name="accommodation_note" id="accommodation_note" placeholder="e.g. Accommodation Fee: 200₺">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" id="is_active" checked> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_application" id="submitBtn" class="btn">
                        <i class="fas fa-save"></i> Save Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('applyModal').classList.add('show');
            document.getElementById('modalTitle').textContent = 'Add Application Type';
            document.querySelector('form').reset();
            document.getElementById('application_id').value = '';
            document.getElementById('is_active').checked = true;
            document.getElementById('submitBtn').name = 'add_application';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Save Application';
        }

        function openEditModal(app) {
            document.getElementById('applyModal').classList.add('show');
            document.getElementById('modalTitle').textContent = 'Edit Application Type';
            document.getElementById('application_id').value = app.id;
            document.getElementById('title').value = app.title || '';
            document.getElementById('short_name').value = app.short_name || '';
            document.getElementById('fee').value = app.fee || '';
            document.getElementById('description').value = app.description || '';
            document.getElementById('apply_link').value = app.apply_link || '';
            document.getElementById('accommodation_note').value = app.accommodation_note || '';
            document.getElementById('sort_order').value = app.sort_order || 0;
            document.getElementById('is_active').checked = app.is_active == '1';
            document.getElementById('submitBtn').name = 'update_application';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update Application';
        }

        function closeModal() {
            document.getElementById('applyModal').classList.remove('show');
        }

        function confirmDelete(id, name) {
            if (confirm(`Delete "${name}"? This cannot be undone.`)) {
                window.location.href = `apply_cards.php?delete=${id}`;
            }
        }

        // Hamburger Menu Toggle
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        window.onclick = function(e) {
            if (e.target.id === 'applyModal') closeModal();
        };

        // Menü dışına tıklanırsa kapanır
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });

        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.animation = 'slideDown 0.3s ease-out reverse';
                setTimeout(() => alert.remove(), 300);
            }
        }, 3000);
    </script>
</body>
</html>