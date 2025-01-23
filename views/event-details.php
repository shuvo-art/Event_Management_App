<?php
require_once '../config/config.php';
require_once '../includes/utils.php';
require_once '../includes/event.php';

$db = (new Database())->getConnection();
$event = new Event($db);

if (!isset($_GET['id'])) {
    Utils::redirect('/');
}

$event_id = (int)$_GET['id'];
$event_details = $event->getById($event_id);

$page_title = "Event Details";
include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1><?php echo htmlspecialchars($event_details['title']); ?></h1>
        <p><?php echo htmlspecialchars($event_details['description']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($event_details['date']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($event_details['location']); ?></p>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
