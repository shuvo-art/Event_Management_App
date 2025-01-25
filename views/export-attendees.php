<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/utils.php';
require_once '../includes/reports.php';

Utils::requireAdmin();

$db = (new Database())->getConnection();
$reports = new Reports($db);

$event_id = $_GET['event_id'] ?? null;

if ($event_id) {
    $reports->generateAttendeeList($event_id);
} else {
    Utils::redirect('dashboard.php');
}
?>
