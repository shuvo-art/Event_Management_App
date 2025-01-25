<?php
require_once '../config/config.php';
require_once '../config/database.php'; // Include database connection
require_once '../includes/utils.php'; // Include utility functions
require_once '../includes/auth.php'; // Include Auth class

// Initialize the database and Auth class
$db = (new Database())->getConnection();
$auth = new Auth($db);

$page_title = "Login";
include '../templates/header.php';

// Handle form submission
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        // Attempt to log the user in
        if ($auth->login($email, $password)) {
            // Redirect to dashboard or home page after successful login
            Utils::redirect('/views/dashboard.php');
        } else {
            $error_message = "Invalid email or password. Please try again.";
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="text-center">Login</h1>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
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
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
