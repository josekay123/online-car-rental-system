<?php
session_start();

// Unset all admin session variables
unset($_SESSION['admin_logged_in']);
unset($_SESSION['is_admin']);
unset($_SESSION['admin_name']);

// Destroy the session
session_destroy();

// Redirect to admin login page
header("Location: ../admin_login.php");
exit();
?>
