<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/utils.php';

Utils::requireAdmin();

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];

    try {
        $stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $stmt->execute([$user_id]);

        Utils::redirect('/views/dashboard.php?success=User promoted to admin successfully.');
    } catch (Exception $e) {
        Utils::redirect('/views/dashboard.php?error=' . urlencode($e->getMessage()));
    }
}

Utils::redirect('/views/dashboard.php?error=Invalid request.');
?>
