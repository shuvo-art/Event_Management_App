<?php
// Configure session settings before starting the session
ini_set('session.gc_maxlifetime', 1800);

session_start();

define('BASE_PATH', dirname(__DIR__));
define('SITE_URL', 'http://localhost/event-management');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'event_management');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('UTC');
?>
