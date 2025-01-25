<?php
require_once '../config/database.php';
require_once '../includes/event.php';
require_once '../includes/utils.php';

header('Content-Type: application/json');

$db = (new Database())->getConnection();
$event = new Event($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    try {
        $events = $event->getAll($page, $limit, $search);
        Utils::jsonResponse(['status' => 'success', 'data' => $events]);
    } catch (Exception $e) {
        Utils::jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
    }
} else {
    Utils::jsonResponse(['status' => 'error', 'message' => 'Invalid request'], 400);
}
?>
