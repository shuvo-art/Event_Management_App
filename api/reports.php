<?php
require_once '../config/database.php';
require_once '../includes/reports.php';

header('Content-Type: application/json');
$db = (new Database())->getConnection();
$reports = new Reports($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
    $reports->generateAttendeeList($event_id);
} else {
    echo json_encode(['message' => 'Invalid request']);
}
