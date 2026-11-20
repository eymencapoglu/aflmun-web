<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

include 'db.php';

// Get all committees
$committees = $pdo->query("SELECT * FROM committees ORDER BY created_at DESC")->fetchAll();

// Add new committee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_committee'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $details = $_POST['details'];
    $chair_name = $_POST['chair_name'] ?? '';
    $chair_title = $_POST['chair_title'] ?? 'Committee Chair';
    
    $image_path = '';
    if ($_FILES['image']['error'] === 0) {
        $upload_dir = '../assets/images/committees/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = 'committee_' . time() . '.' . $image_ext;
        $image_path = 'assets/images/committees/' . $image_name;
        
        move_uploaded_file($_FILES['image']['tmp_name'], '../' . $image_path);
    }
    
    $chair_photo = '';
    if ($_FILES['chair_photo']['error'] === 0) {
        $upload_dir = '../assets/images/chairs/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $photo_ext = pathinfo($_FILES['chair_photo']['name'], PATHINFO_EXTENSION);
        $photo_name = 'chair_' . time() . '.' . $photo_ext;
        $chair_photo = 'assets/images/chairs/' . $photo_name;
        
        move_uploaded_file($_FILES['chair_photo']['tmp_name'], '../' . $chair_photo);
    }
    
    $guide_pdf = '';
    if ($_FILES['guide_pdf']['error'] === 0) {
        $upload_dir = '../assets/guides/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $pdf_name = 'guide_' . time() . '.pdf';
        $guide_pdf = 'assets/guides/' . $pdf_name;
        
        move_uploaded_file($_FILES['guide_pdf']['tmp_name'], '../' . $guide_pdf);
    }
    
    $stmt = $pdo->prepare("INSERT INTO committees (name, description, image_path, details, guide_pdf, chair_name, chair_title, chair_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $image_path, $details, $guide_pdf, $chair_name, $chair_title, $chair_photo]);
    
    header('Location: committees.php?success=added');
    exit;
}

// Update committee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_committee'])) {
    $id = $_POST['committee_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $details = $_POST['details'];
    $chair_name = $_POST['chair_name'] ?? '';
    $chair_title = $_POST['chair_title'] ?? 'Committee Chair';
    
    // Get current data
    $stmt = $pdo->prepare("SELECT * FROM committees WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    
    $image_path = $current['image_path'];
    $chair_photo = $current['chair_photo'];
    $guide_pdf = $current['guide_pdf'];
    
    if ($_FILES['image']['error'] === 0) {
        $upload_dir = '../assets/images/committees/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = 'committee_' . time() . '.' . $image_ext;
        $image_path = 'assets/images/committees/' . $image_name;
        
        move_uploaded_file($_FILES['image']['tmp_name'], '../' . $image_path);
    }
    
    if ($_FILES['chair_photo']['error'] === 0) {
        $upload_dir = '../assets/images/chairs/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $photo_ext = pathinfo($_FILES['chair_photo']['name'], PATHINFO_EXTENSION);
        $photo_name = 'chair_' . time() . '.' . $photo_ext;
        $chair_photo = 'assets/images/chairs/' . $photo_name;
        
        move_uploaded_file($_FILES['chair_photo']['tmp_name'], '../' . $chair_photo);
    }
    
    if ($_FILES['guide_pdf']['error'] === 0) {
        $upload_dir = '../assets/guides/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $pdf_name = 'guide_' . time() . '.pdf';
        $guide_pdf = 'assets/guides/' . $pdf_name;
        
        move_uploaded_file($_FILES['guide_pdf']['tmp_name'], '../' . $guide_pdf);
    }
    
    $stmt = $pdo->prepare("UPDATE committees SET name = ?, description = ?, image_path = ?, details = ?, guide_pdf = ?, chair_name = ?, chair_title = ?, chair_photo = ? WHERE id = ?");
    $stmt->execute([$name, $description, $image_path, $details, $guide_pdf, $chair_name, $chair_title, $chair_photo, $id]);
    
    header('Location: committees.php?success=updated');
    exit;
}

// Delete committee
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM committees WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: committees.php?success=deleted');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Committee Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #0f0f1f;
            color: #c9c9d1;
            padding-top: 70px;
        }

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
            background: rgba(201, 201, 209, 0.1);
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
            max-width: 1400px;
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

        /* Alert */
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
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Table */
        .table-container {
            background: rgba(16, 15, 74, 0.3);
            border: 1px solid rgba(201, 201, 209, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(139, 92, 246, 0.1);
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
            border-top: 1px solid rgba(201, 201, 209, 0.05);
        }

        tbody tr {
            transition: all 0.2s;
        }

        tbody tr:hover {
            background: rgba(139, 92, 246, 0.05);
        }

        .committee-thumb {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .chair-thumb {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(139, 92, 246, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
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
            border: 1px solid rgba(139, 92, 246, 0.2);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 24px 30px;
            border-bottom: 1px solid rgba(201, 201, 209, 0.1);
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
            max-height: 70vh;
            overflow-y: auto;
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
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: rgba(16, 15, 74, 0.4);
            border: 1px solid rgba(201, 201, 209, 0.1);
            border-radius: 8px;
            color: #c9c9d1;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: rgba(139, 92, 246, 0.5);
            background: rgba(16, 15, 74, 0.6);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group small {
            display: block;
            margin-top: 6px;
            color: #a1a1aa;
            font-size: 0.85rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid rgba(201, 201, 209, 0.1);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .preview-container {
            margin-top: 10px;
        }

        .preview-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid rgba(139, 92, 246, 0.2);
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
            <li><a href="committees.php" class="active"><i class="fas fa-users"></i> Committees</a></li>
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
            <h1><i class="fas fa-users"></i> Committee Management</h1>
            <button class="btn" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Committee
            </button>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>
                    <?php 
                    if ($_GET['success'] == 'added') echo "Committee added successfully!";
                    if ($_GET['success'] == 'updated') echo "Committee updated successfully!";
                    if ($_GET['success'] == 'deleted') echo "Committee deleted successfully!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Chair</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($committees)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #a1a1aa;">
                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                            No committees yet. Add your first committee!
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($committees as $committee): ?>
                    <tr>
                        <td>
                            <?php if ($committee['image_path']): ?>
                            <img src="../<?= $committee['image_path'] ?>" alt="<?= $committee['name'] ?>" class="committee-thumb">
                            <?php else: ?>
                            <div style="width: 80px; height: 60px; background: rgba(139, 92, 246, 0.1); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: #8b5cf6;"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong style="color: #f9fff0; display: block; margin-bottom: 4px;"><?= htmlspecialchars($committee['name']) ?></strong>
                            <small style="color: #a1a1aa;">ID: <?= $committee['id'] ?></small>
                        </td>
                        <td>
                            <?php if (!empty($committee['chair_name'])): ?>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if (!empty($committee['chair_photo'])): ?>
                                <img src="../<?= $committee['chair_photo'] ?>" alt="Chair" class="chair-thumb">
                                <?php endif; ?>
                                <div>
                                    <div style="color: #f9fff0; font-size: 0.9rem;"><?= htmlspecialchars($committee['chair_name']) ?></div>
                                    <div style="color: #a1a1aa; font-size: 0.8rem;"><?= htmlspecialchars($committee['chair_title']) ?></div>
                                </div>
                            </div>
                            <?php else: ?>
                            <span style="color: #a1a1aa; font-size: 0.9rem;">No chair assigned</span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width: 300px;">
                            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #c9c9d1;">
                                <?= htmlspecialchars(substr($committee['description'], 0, 80)) ?>...
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-success" onclick='openEditModal(<?= json_encode($committee) ?>)'>
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger" onclick="confirmDelete(<?= $committee['id'] ?>, '<?= htmlspecialchars($committee['name']) ?>')">
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

    <!-- Add/Edit Committee Modal -->
    <div id="committeeModal" class="modal">
        <div class="modal-content">
            <form id="committeeForm" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h2 id="modalTitle">Add New Committee</h2>
                    <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" name="committee_id" id="committee_id">
                    
                    <div class="form-group">
                        <label>Committee Name *</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Short Description *</label>
                        <textarea name="description" id="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Detailed Information *</label>
                        <textarea name="details" id="details" style="min-height: 200px;" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Committee Image</label>
                        <input type="file" name="image" id="image" accept="image/*">
                        <div id="currentImage" class="preview-container"></div>
                        <small>Upload a high-quality image for the committee</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Chair Name</label>
                            <input type="text" name="chair_name" id="chair_name" placeholder="e.g., John Doe">
                        </div>
                        
                        <div class="form-group">
                            <label>Chair Title</label>
                            <input type="text" name="chair_title" id="chair_title" value="Committee Chair" placeholder="e.g., Committee Chair">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Chair Photo</label>
                        <input type="file" name="chair_photo" id="chair_photo" accept="image/*">
                        <div id="currentChairPhoto" class="preview-container"></div>
                        <small>If not provided, AFL MUN logo will be displayed</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Guide PDF (Optional)</label>
                        <input type="file" name="guide_pdf" id="guide_pdf" accept=".pdf">
                        <div id="currentPdf"></div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_committee" id="submitBtn" class="btn">
                        <i class="fas fa-save"></i> Save Committee
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('committeeModal').classList.add('show');
            document.getElementById('modalTitle').textContent = 'Add New Committee';
            document.getElementById('committeeForm').reset();
            document.getElementById('committee_id').value = '';
            document.getElementById('currentImage').innerHTML = '';
            document.getElementById('currentChairPhoto').innerHTML = '';
            document.getElementById('currentPdf').innerHTML = '';
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Save Committee';
            document.getElementById('submitBtn').name = 'add_committee';
        }

        function openEditModal(committee) {
            document.getElementById('committeeModal').classList.add('show');
            document.getElementById('modalTitle').textContent = 'Edit Committee';
            document.getElementById('committee_id').value = committee.id;
            document.getElementById('name').value = committee.name;
            document.getElementById('description').value = committee.description;
            document.getElementById('details').value = committee.details;
            document.getElementById('chair_name').value = committee.chair_name || '';
            document.getElementById('chair_title').value = committee.chair_title || 'Committee Chair';
            
            // Show current image
            if (committee.image_path) {
                document.getElementById('currentImage').innerHTML = `
                    <div style="margin-top: 10px;">
                        <small style="color: #a1a1aa;">Current image:</small><br>
                        <img src="../${committee.image_path}" class="preview-image" style="margin-top: 8px;">
                    </div>
                `;
            }
            
            // Show current chair photo
            if (committee.chair_photo) {
                document.getElementById('currentChairPhoto').innerHTML = `
                    <div style="margin-top: 10px;">
                        <small style="color: #a1a1aa;">Current photo:</small><br>
                        <img src="../${committee.chair_photo}" class="preview-image" style="margin-top: 8px;">
                    </div>
                `;
            }
            
            // Show current PDF
            if (committee.guide_pdf) {
                document.getElementById('currentPdf').innerHTML = `
                    <small style="color: #10b981; display: block; margin-top: 8px;">
                        <i class="fas fa-file-pdf"></i> Current: ${committee.guide_pdf.split('/').pop()}
                    </small>
                `;
            }
            
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update Committee';
            document.getElementById('submitBtn').name = 'update_committee';
        }

        function closeModal() {
            document.getElementById('committeeModal').classList.remove('show');
        }

        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete "${name}"?\n\nThis action cannot be undone.`)) {
                window.location.href = `committees.php?delete=${id}`;
            }
        }

        // Hamburger Menu Toggle
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('committeeModal');
            if (event.target == modal) {
                closeModal();
            }
        };

        // Menü dışına tıklanırsa kapanır
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });

        // Auto-hide success message
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