<?php
// InfinityFree Database Configuration
$host = "sql102.infinityfree.com";
$username = "if0_40656079";
$password = "4qBxCs3fEpjN9p";
$dbname = "if0_40656079_car_rental_db";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Start session
session_start();

// Admin credentials (change these!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Change this!

// Helper function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>
