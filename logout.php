<?php
require_once 'config/config.php';
require_once 'includes/utils.php';

// Destroy the session
session_start();
session_unset();
session_destroy();

// Redirect to the login page
Utils::redirect('/views/login.php');
