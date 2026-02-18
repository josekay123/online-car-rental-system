<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle booking cancellation
$success_msg = '';
$error_msg = '';

if (isset($_GET['cancel']) && isset($_GET['id'])) {
    $rental_id = (int)$_GET['id'];
    // Only allow cancellation of pending bookings
    $sql = "UPDATE rentals SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $rental_id, $user_id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $success_msg = 'Booking cancelled successfully!';
    } else {
        $error_msg = 'Cannot cancel this booking. Only pending bookings can be cancelled.';
    }
}

$sql = "SELECT rentals.*, cars.make, cars.model, cars.image_url 
        FROM rentals 
        JOIN cars ON rentals.car_id = cars.id 
        WHERE rentals.user_id = ? 
        ORDER BY rentals.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'header.php'; ?>

<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">My Rentals</h1>
                <p class="text-slate-500 mt-1">Manage your upcoming and past trips.</p>
            </div>
            <a href="catalog.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Book New Car
            </a>
        </div>

        <?php if ($success_msg): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $success_msg; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error_msg; ?></span>
            </div>
        <?php endif; ?>

        <div class="space-y-4">
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <?php 
                        $statusClass = '';
                        $statusIcon = '';
                        $statusText = '';
                        switch($row['status']) {
                            case 'pending': 
                                $statusClass = 'bg-yellow-100 text-yellow-700 border-yellow-200'; 
                                $statusIcon = 'fa-clock';
                                $statusText = 'Pending Approval';
                                break;
                            case 'active': 
                                $statusClass = 'bg-green-100 text-green-700 border-green-200'; 
                                $statusIcon = 'fa-check-circle';
                                $statusText = 'Approved';
                                break;
                            case 'completed': 
                                $statusClass = 'bg-slate-100 text-slate-700 border-slate-200'; 
                                $statusIcon = 'fa-flag-checkered';
                                $statusText = 'Completed';
                                break;
                            case 'cancelled': 
                                $statusClass = 'bg-red-100 text-red-700 border-red-200'; 
                                $statusIcon = 'fa-times-circle';
                                $statusText = 'Cancelled';
                                break;
                            default: 
                                $statusClass = 'bg-indigo-100 text-indigo-700 border-indigo-200';
                                $statusIcon = 'fa-car';
                                $statusText = ucfirst($row['status']);
                        }
                        
                        $start = new DateTime($row['start_date']);
                        $end = new DateTime($row['end_date']);
                    ?>
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Car Image -->
                            <div class="w-full md:w-48 h-32 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="<?php echo $row['image_url']; ?>" alt="Car" class="w-full h-full object-cover">
                            </div>

                            <!-- Rental Details -->
                            <div class="flex-grow flex flex-col justify-between">
                                <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900">
                                            <?php echo $row['make'] . ' ' . $row['model']; ?>
                                        </h3>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-slate-500">
                                            <div class="flex items-center gap-1.5">
                                                <i class="far fa-calendar-alt"></i>
                                                <span>
                                                    <?php echo $start->format('M d'); ?> - <?php echo $end->format('M d, Y'); ?>
                                                </span>
                                            </div>
                                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                            <span class="font-medium text-slate-900">
                                                $<?php echo $row['total_price']; ?> Total
                                            </span>
                                        </div>
                                        <?php if ($row['admin_notes']): ?>
                                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <p class="text-xs text-blue-600 font-semibold mb-1">Admin Note:</p>
                                            <p class="text-sm text-blue-800"><?php echo $row['admin_notes']; ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="<?php echo $statusClass; ?> border px-4 py-2 rounded-full text-xs font-bold uppercase flex items-center gap-1.5">
                                        <i class="fas <?php echo $statusIcon; ?>"></i>
                                        <?php echo $statusText; ?>
                                    </span>
                                </div>

                                <div class="mt-6 flex justify-end gap-3">
                                    <a href="book.php?id=<?php echo $row['car_id']; ?>" class="text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                                        Rent Again <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                    
                                    <?php if ($row['status'] == 'pending'): ?>
                                    <a href="?cancel=1&id=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to cancel this booking?')"
                                       class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                                        <i class="fas fa-times mr-2"></i> Cancel Booking
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-24 bg-white rounded-3xl border border-slate-200 border-dashed">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 mb-4">
                        <i class="fas fa-car h-8 w-8 text-indigo-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">No rentals yet</h3>
                    <p class="text-slate-500 mt-2 mb-6">You haven't booked any cars yet. Start your journey today.</p>
                    <a href="catalog.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Browse Cars
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
