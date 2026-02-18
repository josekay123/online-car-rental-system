<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $conn->query("UPDATE rentals SET status = 'active', updated_at = NOW() WHERE id = $id");
        header("Location: bookings.php?msg=approved");
        exit();
    } elseif ($action == 'cancel') {
        $conn->query("UPDATE rentals SET status = 'cancelled', updated_at = NOW() WHERE id = $id");
        header("Location: bookings.php?msg=cancelled");
        exit();
    } elseif ($action == 'delete') {
        $conn->query("DELETE FROM rentals WHERE id = $id");
        header("Location: bookings.php?msg=deleted");
        exit();
    }
}

// Get status filter
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query
$sql = "SELECT r.*, u.full_name, u.email, u.phone, u.address, u.city, u.state, u.license_number, 
               u.date_of_birth, c.make, c.model, c.image_url, c.price_per_day 
        FROM rentals r 
        JOIN users u ON r.user_id = u.id 
        JOIN cars c ON r.car_id = c.id";

if ($status !== 'all') {
    $sql .= " WHERE r.status = '$status'";
}

$sql .= " ORDER BY r.created_at DESC";

$result = $conn->query($sql);

// Get revenue data
$revenue_query = $conn->query("
    SELECT 
        COALESCE(SUM(CASE WHEN status IN ('active', 'approved') AND payment_status = 'paid' THEN total_price ELSE 0 END), 0) as active_revenue,
        COALESCE(SUM(CASE WHEN status = 'completed' AND payment_status = 'paid' THEN total_price ELSE 0 END), 0) as completed_revenue,
        COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_price ELSE 0 END), 0) as total_revenue
    FROM rentals
");

$revenue = $revenue_query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Simple Header -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="index.php" class="text-purple-600 hover:text-purple-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-lg font-bold text-gray-900">Manage Bookings</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-user-circle mr-1"></i><?php echo $_SESSION['admin_name']; ?>
                    </span>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Customer Details Modal -->
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Customer Details</h3>
                <button onclick="closeModal('customerModal')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-6" id="customerDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
    <!-- Success Messages -->
    <?php if (isset($_GET['msg'])): ?>
        <?php
        $messages = [
            'approved' => ['Booking approved successfully!', 'bg-green-100 text-green-800 border-green-200'],
            'cancelled' => ['Booking cancelled successfully!', 'bg-yellow-100 text-yellow-800 border-yellow-200'],
            'deleted' => ['Booking deleted successfully!', 'bg-red-100 text-red-800 border-red-200']
        ];
        if (isset($messages[$_GET['msg']])): ?>
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="p-4 rounded-lg border <?php echo $messages[$_GET['msg']][1]; ?>">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $messages[$_GET['msg']][0]; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Status Filters -->
        <div class="mb-6">
            <div class="flex flex-wrap gap-2 bg-white p-3 rounded-lg shadow-sm">
                <a href="?status=all" 
                   class="px-4 py-2 rounded-lg <?php echo $status == 'all' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    All Bookings
                </a>
                <a href="?status=pending" 
                   class="px-4 py-2 rounded-lg <?php echo $status == 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-clock mr-2"></i>Pending
                </a>
                <a href="?status=active" 
                   class="px-4 py-2 rounded-lg <?php echo $status == 'active' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-check-circle mr-2"></i>Active
                </a>
                <a href="?status=completed" 
                   class="px-4 py-2 rounded-lg <?php echo $status == 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-flag-checkered mr-2"></i>Completed
                </a>
                <a href="?status=cancelled" 
                   class="px-4 py-2 rounded-lg <?php echo $status == 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-times-circle mr-2"></i>Cancelled
                </a>
            </div>
        </div>

        <!-- Revenue Summary -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h3 class="font-bold text-gray-900 mb-6 text-lg">Revenue Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Active Rentals Revenue -->
                <div class="border border-green-200 rounded-lg p-5 bg-gradient-to-br from-green-50 to-emerald-50">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-sm font-medium text-green-800 mb-1">Active Rentals Revenue</p>
                            <p class="text-2xl font-bold text-green-900">$<?php echo number_format($revenue['active_revenue'], 2); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-car text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-green-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Currently rented & paid
                    </p>
                </div>
                
                <!-- Completed Rentals Revenue -->
                <div class="border border-blue-200 rounded-lg p-5 bg-gradient-to-br from-blue-50 to-indigo-50">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-sm font-medium text-blue-800 mb-1">Completed Rentals Revenue</p>
                            <p class="text-2xl font-bold text-blue-900">$<?php echo number_format($revenue['completed_revenue'], 2); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-flag-checkered text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-blue-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Finished & paid rentals
                    </p>
                </div>
                
                <!-- Total Revenue -->
                <div class="border border-purple-200 rounded-lg p-5 bg-gradient-to-br from-purple-50 to-violet-50">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-sm font-medium text-purple-800 mb-1">Total Revenue</p>
                            <p class="text-2xl font-bold text-purple-900">$<?php echo number_format($revenue['total_revenue'], 2); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-purple-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        All confirmed payments
                    </p>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Booking ID</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Customer</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Car</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Dates</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Total</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($booking = $result->fetch_assoc()): ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50" id="booking-<?php echo $booking['id']; ?>">
                                    <td class="p-4">
                                        <span class="font-mono text-sm font-bold text-gray-900">#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                        </p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-900"><?php echo $booking['full_name']; ?></p>
                                        <p class="text-sm text-gray-500"><?php echo $booking['email']; ?></p>
                                        <button onclick="showCustomerDetails(<?php echo htmlspecialchars(json_encode($booking)); ?>)" 
                                                class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                                            <i class="fas fa-eye mr-1"></i>View Details
                                        </button>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-medium text-gray-900"><?php echo $booking['make'] . ' ' . $booking['model']; ?></p>
                                        <p class="text-sm text-gray-500">$<?php echo $booking['price_per_day']; ?>/day</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="text-gray-900 font-medium">
                                            <?php echo date('M d, Y', strtotime($booking['start_date'])); ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            to <?php echo date('M d, Y', strtotime($booking['end_date'])); ?>
                                        </p>
                                        <?php
                                        $start = new DateTime($booking['start_date']);
                                        $end = new DateTime($booking['end_date']);
                                        $days = $start->diff($end)->days + 1;
                                        ?>
                                        <p class="text-xs text-gray-400"><?php echo $days; ?> day<?php echo $days > 1 ? 's' : ''; ?></p>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-gray-900 text-lg">$<?php echo number_format($booking['total_price'], 2); ?></p>
                                        <p class="text-xs text-gray-500">$<?php echo $booking['price_per_day']; ?> × <?php echo $days; ?> days</p>
                                    </td>
                                    <td class="p-4">
                                        <?php
                                        $status_color = match($booking['status']) {
                                            'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                            'active' => 'bg-green-100 text-green-800 border border-green-200',
                                            'completed' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                            'cancelled' => 'bg-red-100 text-red-800 border border-red-200',
                                            default => 'bg-gray-100 text-gray-800 border border-gray-200'
                                        };
                                        $status_icon = match($booking['status']) {
                                            'pending' => 'fas fa-clock',
                                            'active' => 'fas fa-check-circle',
                                            'completed' => 'fas fa-flag-checkered',
                                            'cancelled' => 'fas fa-times-circle',
                                            default => 'fas fa-circle'
                                        };
                                        ?>
                                        <span class="px-3 py-1.5 rounded-full text-xs font-bold <?php echo $status_color; ?> inline-flex items-center gap-1">
                                            <i class="<?php echo $status_icon; ?> text-xs"></i>
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-col gap-2">
                                            <!-- View Details Button -->
                                            <button onclick="showBookingDetails(<?php echo htmlspecialchars(json_encode($booking)); ?>)" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-medium inline-flex items-center justify-center">
                                                <i class="fas fa-eye mr-2"></i>View
                                            </button>
                                            
                                            <!-- Action Buttons -->
                                            <div class="flex gap-1">
                                                <?php if ($booking['status'] == 'pending'): ?>
                                                    <a href="?action=approve&id=<?php echo $booking['id']; ?>" 
                                                       onclick="return confirm('Approve this booking?')"
                                                       class="flex-1 bg-green-600 hover:bg-green-700 text-white px-2 py-1.5 rounded text-xs font-medium inline-flex items-center justify-center">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="?action=cancel&id=<?php echo $booking['id']; ?>" 
                                                       onclick="return confirm('Cancel this booking?')"
                                                       class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-2 py-1.5 rounded text-xs font-medium inline-flex items-center justify-center">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php elseif ($booking['status'] == 'active'): ?>
                                                    <a href="?action=cancel&id=<?php echo $booking['id']; ?>" 
                                                       onclick="return confirm('Cancel this active booking?')"
                                                       class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white px-2 py-1.5 rounded text-xs font-medium inline-flex items-center justify-center">
                                                        Cancel
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <!-- Delete Button (always visible) -->
                                                <a href="?action=delete&id=<?php echo $booking['id']; ?>" 
                                                   onclick="return confirm('Delete this booking permanently? This cannot be undone.')"
                                                   class="flex-1 bg-red-600 hover:bg-red-700 text-white px-2 py-1.5 rounded text-xs font-medium inline-flex items-center justify-center">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="p-12 text-center text-gray-500">
                                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                                        <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900 mb-2">No bookings found</p>
                                    <p class="text-gray-600">
                                        <?php if ($status !== 'all'): ?>
                                            No <?php echo $status; ?> bookings in the system
                                        <?php else: ?>
                                            No bookings have been made yet
                                        <?php endif; ?>
                                    </p>
                                    <a href="?status=all" class="inline-block mt-4 text-purple-600 hover:text-purple-800">
                                        <i class="fas fa-sync-alt mr-2"></i>Show All Bookings
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <h3 class="font-bold text-gray-900 mb-4 text-lg">Booking Statistics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php
                $stats_query = $conn->query("SELECT status, COUNT(*) as count FROM rentals GROUP BY status");
                $all_stats = ['pending' => 0, 'active' => 0, 'completed' => 0, 'cancelled' => 0];
                $total = 0;
                while ($row = $stats_query->fetch_assoc()) {
                    $all_stats[$row['status']] = $row['count'];
                    $total += $row['count'];
                }
                ?>
                <div class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-700"><?php echo $all_stats['pending']; ?></p>
                    <p class="text-sm font-medium text-yellow-800">Pending</p>
                    <p class="text-xs text-yellow-600 mt-1">Awaiting approval</p>
                </div>
                <div class="text-center p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-2xl font-bold text-green-700"><?php echo $all_stats['active']; ?></p>
                    <p class="text-sm font-medium text-green-800">Active</p>
                    <p class="text-xs text-green-600 mt-1">Currently rented</p>
                </div>
                <div class="text-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-2xl font-bold text-blue-700"><?php echo $all_stats['completed']; ?></p>
                    <p class="text-sm font-medium text-blue-800">Completed</p>
                    <p class="text-xs text-blue-600 mt-1">Finished rentals</p>
                </div>
                <div class="text-center p-4 bg-gray-50 border border-gray-300 rounded-lg">
                    <p class="text-2xl font-bold text-gray-700"><?php echo $total; ?></p>
                    <p class="text-sm font-medium text-gray-800">Total</p>
                    <p class="text-xs text-gray-600 mt-1">All bookings</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
                <a href="?status=pending" class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium inline-flex items-center">
                    <i class="fas fa-clock text-yellow-600 mr-2"></i>Review Pending
                </a>
                <a href="?status=active" class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium inline-flex items-center">
                    <i class="fas fa-car text-green-600 mr-2"></i>Active Rentals
                </a>
                <a href="../index.php" class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium inline-flex items-center">
                    <i class="fas fa-globe text-blue-600 mr-2"></i>View Website
                </a>
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Customer Details</h3>
                <button onclick="closeModal('customerModal')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-6" id="customerDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Booking Details</h3>
                <button onclick="closeModal('bookingModal')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-6" id="bookingDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function showCustomerDetails(customer) {
            const content = `
                <div class="space-y-6">
                    <!-- Personal Info -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-lg">Personal Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Full Name</p>
                                <p class="font-medium text-gray-900">${customer.full_name}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">${customer.email}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium text-gray-900">${customer.phone || 'Not provided'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date of Birth</p>
                                <p class="font-medium text-gray-900">${customer.date_of_birth ? new Date(customer.date_of_birth).toLocaleDateString() : 'Not provided'}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Driver's License</p>
                                <p class="font-medium text-gray-900 font-mono">${customer.license_number || 'Not provided'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-lg">Address</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-900">${customer.address || 'Not provided'}</p>
                            <p class="text-gray-600">${customer.city || ''} ${customer.state || ''}</p>
                        </div>
                    </div>

                    <!-- Contact Actions -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <a href="mailto:${customer.email}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-center">
                            <i class="fas fa-envelope mr-2"></i>Email Customer
                        </a>
                        <a href="tel:${customer.phone}" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-center ${!customer.phone ? 'opacity-50 cursor-not-allowed' : ''}">
                            <i class="fas fa-phone mr-2"></i>Call Customer
                        </a>
                    </div>
                </div>
            `;
            document.getElementById('customerDetailsContent').innerHTML = content;
            document.getElementById('customerModal').style.display = 'block';
        }

        function showBookingDetails(booking) {
            const start = new Date(booking.start_date);
            const end = new Date(booking.end_date);
            const days = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
            
            const content = `
                <div class="space-y-6">
                    <!-- Booking Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">Booking ID</p>
                                <p class="font-bold text-gray-900 text-lg">#${String(booking.id).padStart(6, '0')}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="font-bold text-gray-900 text-2xl">$${parseFloat(booking.total_price).toFixed(2)}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Car Details -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3">Car Details</h4>
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                <img src="${booking.image_url}" alt="${booking.make}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">${booking.make} ${booking.model}</p>
                                <p class="text-gray-600">$${booking.price_per_day} per day</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Period -->
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3">Rental Period</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 border border-gray-200 rounded-lg">
                                <p class="text-sm text-gray-500">Pick-up Date</p>
                                <p class="font-medium text-gray-900">${start.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                            <div class="p-3 border border-gray-200 rounded-lg">
                                <p class="text-sm text-gray-500">Return Date</p>
                                <p class="font-medium text-gray-900">${end.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                        </div>
                        <p class="text-center text-gray-600 mt-2">${days} day${days > 1 ? 's' : ''} rental</p>
                    </div>

                    <!-- Status & Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Booking Status</p>
                            <p class="font-bold text-gray-900 capitalize">${booking.status}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Created On</p>
                            <p class="font-medium text-gray-900">${new Date(booking.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="font-bold text-gray-900 mb-3">Price Breakdown</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Daily Rate</span>
                                <span class="font-medium">$${booking.price_per_day} × ${days} days</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-2">
                                <span class="text-lg font-bold text-gray-900">Total Amount</span>
                                <span class="text-lg font-bold text-gray-900">$${parseFloat(booking.total_price).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('bookingDetailsContent').innerHTML = content;
            document.getElementById('bookingModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['customerModal', 'bookingModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
