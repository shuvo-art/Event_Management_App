<?php
require_once '../config/database.php';
require_once '../includes/registration.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();
$registration = new Registration($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
    try {
        $attendees = $registration->getAttendees($event_id);
        Utils::jsonResponse(['status' => 'success', 'data' => $attendees]);
    } catch (Exception $e) {
        Utils::jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
    }
} else {
    Utils::jsonResponse(['status' => 'error', 'message' => 'Invalid request'], 400);
}
