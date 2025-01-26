<?php
require_once '../config/config.php';
require_once '../config/database.php'; // Include the Database class
require_once '../includes/utils.php';
require_once '../includes/event.php';
require_once '../includes/registration.php';

Utils::requireLogin();

$db = (new Database())->getConnection(); // Instantiate the Database class
$event = new Event($db);
$registration = new Registration($db);

if (!isset($_GET['id'])) {
    Utils::redirect('/');
}

$event_id = (int)$_GET['id'];
$event_details = $event->getById($event_id);

$page_title = "Event Details";
include '../templates/header.php';

// Handle registration
$error_message = "";
$success_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $registration->register($event_id, $_SESSION['user_id']);
        $success_message = "You have successfully registered for this event!";
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<div class="row">
    <div class="col-12">
        <h1><?php echo htmlspecialchars($event_details['title']); ?></h1>
        <p><?php echo htmlspecialchars($event_details['description']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($event_details['date']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($event_details['location']); ?></p>
        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($event_details['capacity']); ?></p>
        <p><strong>Registered Attendees:</strong> <?php echo htmlspecialchars($event_details['registration_count']); ?></p>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($event_details['registration_count'] < $event_details['capacity']): ?>
            <?php if (!$registration->isUserRegistered($event_id, $_SESSION['user_id'])): ?>
                <form method="POST">
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            <?php else: ?>
                <p class="text-success">You are already registered for this event.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-danger">This event is fully booked.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
