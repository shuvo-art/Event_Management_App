<?php
require_once 'config/database.php';

$db = new Database();
$connection = $db->getConnection();

if ($connection) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed.";
}
?>
