<?php
require_once '../config/database.php';
require_once '../includes/event.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();
$event = new Event($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['term'])) {
    $term = $_GET['term'];

    try {
        $results = $event->search($term);
        Utils::jsonResponse(['status' => 'success', 'data' => $results]);
    } catch (Exception $e) {
        Utils::jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
    }
} else {
    Utils::jsonResponse(['status' => 'error', 'message' => 'Invalid request'], 400);
}
