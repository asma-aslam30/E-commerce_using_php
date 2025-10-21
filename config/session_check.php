<?php
session_start();
 
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) == 'dashboard.php') {
    header("Location: Login.php");
    exit();
}
 
if (isset($_SESSION['user_id']) && in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'Registration.php'])) {
    header("Location: dashboard.php");
    exit();
}
?>




