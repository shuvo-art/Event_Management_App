<?php
require_once '../config/config.php';

$page_title = "Register";
include '../templates/header.php';
?>

<form method="POST" action="register-user.php" class="needs-validation" novalidate>
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
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php include '../templates/footer.php'; ?>
