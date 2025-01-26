<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/utils.php';
require_once '../includes/event.php';

Utils::requireLogin();

$db = (new Database())->getConnection();
$event = new Event($db);

$page_title = "Edit Event";
include '../templates/header.php';

$error_message = "";

// Get event ID from query parameters
$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    Utils::redirect('/views/dashboard.php');
}

// Fetch event details
$event_details = $event->getById($event_id);
if (!$event_details) {
    Utils::redirect('/views/dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);

    try {
        // Validate input data
        if (empty($title) || empty($description) || empty($date) || empty($location) || $capacity <= 0) {
            throw new Exception("All fields are required, and capacity must be greater than zero.");
        }

        // Check date format
        $dateObject = DateTime::createFromFormat('Y-m-d\TH:i', $date);
        if (!$dateObject) {
            throw new Exception("Invalid date format. Please use the provided date picker.");
        }

        // Update the event
        $event->update($event_id, $title, $description, $dateObject->format('Y-m-d H:i:s'), $location, $capacity);

        // Redirect to dashboard with success message
        Utils::redirect('/views/dashboard.php?success=Event updated successfully');
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1>Edit Event</h1>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event_details['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($event_details['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="datetime-local" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d\TH:i', strtotime($event_details['date'])); ?>" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($event_details['location']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo htmlspecialchars($event_details['capacity']); ?>" required min="1">
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Event</button>
        </form>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
