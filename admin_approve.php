<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $booking_id = (int)$_GET['id'];
    
    // Update booking status to active
    $sql = "UPDATE rentals SET status = 'active', updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        // Success - redirect back with success message
        header("Location: bookings.php?approved=1&id=" . $booking_id);
    } else {
        // Error
        header("Location: bookings.php?error=1");
    }
    exit();
}

// If no ID, redirect to bookings
header("Location: bookings.php");
?>
