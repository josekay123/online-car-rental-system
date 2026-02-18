<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

// Connect to database
include '../config.php';

// Get statistics
$stats = [
    'pending' => 0,
    'active' => 0,
    'total' => 0
];

$result = $conn->query("SELECT status, COUNT(*) as count FROM rentals GROUP BY status");
while ($row = $result->fetch_assoc()) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}

// Get recent bookings
$recent_bookings = $conn->query("
    SELECT r.*, u.full_name, c.make, c.model 
    FROM rentals r 
    JOIN users u ON r.user_id = u.id 
    JOIN cars c ON r.car_id = c.id 
    ORDER BY r.created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | LuxeDrive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Admin Header -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-600 p-2 rounded-lg text-white">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Admin Dashboard</h1>
                        <p class="text-sm text-gray-600">Welcome, <?php echo $_SESSION['admin_name']; ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="../index.php" class="text-gray-600 hover:text-gray-800 text-sm">
                        <i class="fas fa-globe mr-1"></i>View Site
                    </a>
                    <a href="logout.php" class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Pending Bookings</h3>
                        <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo $stats['pending']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <a href="bookings.php?status=pending" class="text-sm text-yellow-600 hover:text-yellow-700 mt-4 block">
                    View pending bookings →
                </a>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Active Rentals</h3>
                        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $stats['active']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <a href="bookings.php?status=active" class="text-sm text-green-600 hover:text-green-700 mt-4 block">
                    View active rentals →
                </a>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Total Bookings</h3>
                        <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $stats['total']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <a href="bookings.php" class="text-sm text-blue-600 hover:text-blue-700 mt-4 block">
                    View all bookings →
                </a>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Recent Bookings</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Customer</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Car</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Dates</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Total</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_bookings->num_rows > 0): ?>
                            <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="p-4">
                                        <p class="font-medium text-gray-900"><?php echo $booking['full_name']; ?></p>
                                        <p class="text-sm text-gray-500">Booking #<?php echo $booking['id']; ?></p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-900"><?php echo $booking['make'] . ' ' . $booking['model']; ?></p>
                                    </td>
                                    <td class="p-4">
                                        <p class="text-gray-900"><?php echo date('M d', strtotime($booking['start_date'])); ?> - <?php echo date('M d, Y', strtotime($booking['end_date'])); ?></p>
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">$<?php echo number_format($booking['total_price'], 2); ?></td>
                                    <td class="p-4">
                                        <?php
                                        $status_color = match($booking['status']) {
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'active' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $status_color; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <?php if ($booking['status'] == 'pending'): ?>
                                            <a href="approve.php?id=<?php echo $booking['id']; ?>" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                                                <i class="fas fa-check mr-2"></i>Approve
                                            </a>
                                        <?php elseif ($booking['status'] == 'active'): ?>
                                            <a href="bookings.php" class="text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-3 block text-gray-300"></i>
                                    No bookings found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200 text-center">
                <a href="bookings.php" class="text-purple-600 hover:text-purple-700 font-medium">
                    View All Bookings <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="bookings.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-purple-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Manage All Bookings</h3>
                        <p class="text-gray-600 text-sm mt-1">View, approve, and manage all rental bookings</p>
                    </div>
                </div>
            </a>
            
            <a href="#" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Manage Users</h3>
                        <p class="text-gray-600 text-sm mt-1">View and manage registered users</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
