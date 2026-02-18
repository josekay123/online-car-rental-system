<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

// Get all customers
$result = $conn->query("
    SELECT u.*, 
           COUNT(r.id) as total_bookings,
           SUM(r.total_price) as total_spent,
           MAX(r.created_at) as last_booking
    FROM users u
    LEFT JOIN rentals r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="index.php" class="text-purple-600 hover:text-purple-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-lg font-bold text-gray-900">Manage Customers</h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="bookings.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-calendar mr-1"></i>Bookings
                    </a>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Customer Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $stats = $conn->query("
                SELECT 
                    (SELECT COUNT(*) FROM users) as total_customers,
                    (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_customers,
                    (SELECT COUNT(DISTINCT user_id) FROM rentals) as customers_with_bookings,
                    (SELECT AVG(total_price) FROM rentals) as avg_booking_value
            ")->fetch_assoc();
            ?>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_customers']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">New (30 days)</p>
                        <p class="text-2xl font-bold text-green-600">+<?php echo $stats['new_customers']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white
