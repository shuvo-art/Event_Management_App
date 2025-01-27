<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/utils.php';
require_once '../includes/event.php';

Utils::requireLogin();

$page_title = "Dashboard";
$db = (new Database())->getConnection();
$event = new Event($db);

// Get current page and limit from GET parameters, set defaults if not provided
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : 10;

// Get search, sort, and order parameters from GET
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort'] ?? 'date';
$sortOrder = $_GET['order'] ?? 'ASC';

// Fetch events based on pagination and filters
$events = $event->getAll($page, $limit, $search, $sortBy, $sortOrder);

// Fetch users for admin view
$stmt = $db->prepare("SELECT id, name, email, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="text-center">Dashboard</h2>
        <p class="text-center">Manage your events here.</p>
        <div class="text-end mb-3">
            <a href="event-create.php" class="btn btn-success">Create New Event</a>
        </div>

        <div class="mb-3">
            <input type="text" id="globalSearch" class="form-control" placeholder="Search events or attendees...">
        </div>
        <div id="searchResults"></div>

        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" id="filterForm">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
            <div class="col-md-3">
                <select name="sort" id="sort" class="form-select" form="filterForm">
                    <option value="date" <?php echo $sortBy === 'date' ? 'selected' : ''; ?>>Sort by Date</option>
                    <option value="title" <?php echo $sortBy === 'title' ? 'selected' : ''; ?>>Sort by Title</option>
                    <option value="location" <?php echo $sortBy === 'location' ? 'selected' : ''; ?>>Sort by Location</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="order" id="order" class="form-select" form="filterForm">
                    <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
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
                                <?php if (Utils::isAdmin()): ?>
                                    <a href="export-attendees.php?event_id=<?php echo $event['id']; ?>" class="btn btn-secondary btn-sm">Export Attendees</a>
                                <?php endif; ?>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-success btn-sm">
                                    <?php echo $event['registration_count'] >= $event['capacity'] ? 'Full' : 'Register'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-primary" <?php echo $page <= 1 ? 'disabled' : ''; ?> onclick="navigatePage(<?php echo $page - 1; ?>)">Previous</button>
            <span>Page <?php echo $page; ?></span>
            <button class="btn btn-primary" <?php echo count($events) < $limit ? 'disabled' : ''; ?> onclick="navigatePage(<?php echo $page + 1; ?>)">Next</button>
        </div>

        <!-- User Management Section for Admins -->
        <?php if (Utils::isAdmin()): ?>
            <h3 class="mt-5">User Management</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'user'): ?>
                                        <form method="POST" action="set-admin.php" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-warning btn-sm">Promote to Admin</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-success">Admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../templates/footer.php'; ?>

<script>
    function navigatePage(page) {
        const form = document.getElementById('filterForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'page';
        input.value = page;
        form.appendChild(input);
        form.submit();
    }
</script>
