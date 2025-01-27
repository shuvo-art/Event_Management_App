<?php
require_once '../config/database.php';
require_once '../includes/registration.php';
require_once '../includes/utils.php';

session_start();
header('Content-Type: application/json');

$db = (new Database())->getConnection();
$registration = new Registration($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['event_id'], $data['csrf_token']) && Utils::isLoggedIn()) {
        if (hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
            $event_id = (int)$data['event_id'];
            $user_id = $_SESSION['user_id'];

            try {
                $registration->register($event_id, $user_id);
                Utils::jsonResponse(['status' => 'success', 'message' => 'Successfully registered for the event']);
            } catch (Exception $e) {
                Utils::jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
            }
        } else {
            Utils::jsonResponse(['status' => 'error', 'message' => 'CSRF validation failed.'], 400);
        }
    } else {
        Utils::jsonResponse(['status' => 'error', 'message' => 'Invalid request or user not logged in'], 400);
    }
} else {
    Utils::jsonResponse(['status' => 'error', 'message' => 'Invalid request'], 400);
}
?>
