<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/utils.php';

Utils::requireAdmin();

$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT id, name, email, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "User Management";
include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="text-center">User Management</h2>
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
    </div>
</div>

<?php include '../templates/footer.php'; ?>
