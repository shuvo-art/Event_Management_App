<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/utils.php';
require_once '../includes/event.php';

Utils::requireLogin();

$db = (new Database())->getConnection();
$event = new Event($db);

// Get event ID from query parameters
$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    Utils::redirect('/views/dashboard.php?error=Event ID is required');
}

try {
    // Attempt to delete the event
    $event_details = $event->getById($event_id);
    if (!$event_details) {
        throw new Exception('Event not found');
    }

    $event->delete($event_id);

    // Redirect to dashboard with success message
    Utils::redirect('/views/dashboard.php?success=Event deleted successfully');
} catch (Exception $e) {
    // Redirect to dashboard with error message
    Utils::redirect('/views/dashboard.php?error=' . urlencode($e->getMessage()));
}
