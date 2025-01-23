<?php
require_once '../config/config.php';
require_once '../includes/utils.php';

$page_title = "Login";
include '../templates/header.php';
?>

<form method="POST" action="authenticate.php" class="needs-validation" novalidate>
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
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<?php include '../templates/footer.php'; ?>
