<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/utils.php';
require_once '../includes/event.php';

Utils::requireLogin();

$page_title = "Dashboard";
$db = (new Database())->getConnection();
$event = new Event($db);

$events = $event->getAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="text-center">Dashboard</h2>
        <p class="text-center">Manage your events here.</p>
        <div class="text-end mb-3">
            <a href="event-create.php" class="btn btn-success">Create New Event</a>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo htmlspecialchars($event['description']); ?></td>
                        <td><?php echo htmlspecialchars($event['date']); ?></td>
                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                        <td><?php echo htmlspecialchars($event['capacity']); ?></td>
                        <td>
                            <a href="event-edit.php?id=<?php echo $event['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="event-delete.php?id=<?php echo $event['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            <a href="export-attendees.php?event_id=<?php echo $event['id']; ?>" class="btn btn-secondary btn-sm">Export Attendees</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
