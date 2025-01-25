<?php
require_once '../config/config.php'; // Include config file
require_once '../config/database.php'; // Include database file to load the Database class
require_once '../includes/utils.php'; // Include utility functions
require_once '../includes/auth.php'; // Include Auth class

// Initialize the database and Auth class
$db = (new Database())->getConnection();
$auth = new Auth($db);

$page_title = "Register";
include '../templates/header.php';

// Handle form submission
$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        // Attempt to register the user
        $auth->register($name, $email, $password);
        $success_message = "Registration successful! You can now <a href='" . SITE_URL . "/views/login.php'>login</a>.";
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="text-center">Register</h1>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback">Please enter your name.</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
