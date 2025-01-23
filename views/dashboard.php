<?php
require_once '../config/config.php';
require_once '../includes/utils.php';

Utils::requireLogin();

$page_title = "Dashboard";
include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2>Dashboard</h2>
        <p>Manage your events here.</p>
        <a href="events-list.php" class="btn btn-primary">View Events</a>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
