<?php
require_once '../config/database.php';
require_once '../includes/event.php';
require_once '../includes/registration.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();
$event = new Event($db);
$registration = new Registration($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['term'])) {
    $term = $_GET['term'];

    try {
        $eventResults = $event->search($term);
        $attendeeResults = $registration->searchAttendees($term); // Add attendee search logic
        Utils::jsonResponse([
            'status' => 'success',
            'events' => $eventResults,
            'attendees' => $attendeeResults
        ]);
    } catch (Exception $e) {
        Utils::jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
    }
} else {
    Utils::jsonResponse(['status' => 'error', 'message' => 'Invalid request'], 400);
}
?>
