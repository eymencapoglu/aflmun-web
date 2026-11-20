<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show'])) {
    $value = $_POST['show'] === '1' ? '1' : '0';
    $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('show_executive', ?)");
    $stmt->execute([$value]);
}

header('Location: index.php');
exit;
?>