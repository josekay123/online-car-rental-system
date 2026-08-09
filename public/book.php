<?php
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    die("Car not found");
}

$features = json_decode($car['features'], true);

// Check if user already has a pending/active booking for this car
$user_id = $_SESSION['user_id'];
$check_booking = $conn->prepare("SELECT id, status, start_date, end_date, total_price, created_at, payment_status FROM rentals WHERE user_id = ? AND car_id = ? AND status IN ('pending', 'active', 'completed') ORDER BY created_at DESC LIMIT 1");
$check_booking->bind_param("ii", $user_id, $id);
$check_booking->execute();
$existing_booking = $check_booking->get_result()->fetch_assoc();

// Handle cancellation
if (isset($_GET['cancel']) && isset($_GET['rental_id'])) {
    $rental_id = (int)$_GET['rental_id'];
    $cancel_stmt = $conn->prepare("UPDATE rentals SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
    $cancel_stmt->bind_param("ii", $rental_id, $user_id);
    if ($cancel_stmt->execute() && $cancel_stmt->affected_rows > 0) {
        header("Location: book.php?id=$id&cancelled=1");
        exit();
    }
}

$cancelled = isset($_GET['cancelled']) ? true : false;
$payment_success = isset($_GET['payment_success']) ? true : false;
$payment_error = isset($_GET['payment_error']) ? true : false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $card_number = str_replace(' ', '', $_POST['card_number']);
    $card_holder = $_POST['card_holder'];
    $expiry = $_POST['expiry'];
    $cvv = $_POST['cvv'];
    
    // Validate dates
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days = $start->diff($end)->days + 1;
    
    if ($days < 1) $days = 1;
    $total = $days * $car['price_per_day'];
    
    // Validate credit card (simplified)
    $errors = [];
    
    if (strlen($card_number) != 16 || !is_numeric($card_number)) {
        $errors[] = "Invalid card number. Must be 16 digits.";
    }
    
    if (empty($card_holder)) {
        $errors[] = "Card holder name is required.";
    }
    
    if (!preg_match('/^\d{2}\/\d{2}$/', $expiry)) {
        $errors[] = "Invalid expiry date format (MM/YY).";
    }
    
    if (strlen($cvv) != 3 || !is_numeric($cvv)) {
        $errors[] = "Invalid CVV. Must be 3 digits.";
    }
    
    if (empty($errors)) {
        // Generate transaction ID
        $transaction_id = 'TXN' . time() . rand(1000, 9999);
        $card_last_four = substr($card_number, -4);
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert rental
            $stmt = $conn->prepare("INSERT INTO rentals (user_id, car_id, start_date, end_date, total_price, payment_method, payment_status, transaction_id, amount_paid, status) VALUES (?, ?, ?, ?, ?, 'credit_card', 'paid', ?, ?, 'pending')");
            $stmt->bind_param("iissdsd", $_SESSION['user_id'], $id, $start_date, $end_date, $total, $transaction_id, $total);
            
            if ($stmt->execute()) {
                $rental_id = $stmt->insert_id;
                
                // Insert payment record
                $payment_stmt = $conn->prepare("INSERT INTO payments (rental_id, user_id, amount, payment_method, transaction_id, payment_status, payment_date, card_last_four) VALUES (?, ?, ?, 'credit_card', ?, 'paid', NOW(), ?)");
                $payment_stmt->bind_param("iidss", $rental_id, $_SESSION['user_id'], $total, $transaction_id, $card_last_four);
                $payment_stmt->execute();
                
                $conn->commit();
                
                // Send confirmation email (optional)
                // sendBookingConfirmation($_SESSION['email'], $rental_id, $car, $total, $start_date, $end_date);
                
                header("Location: book.php?id=$id&booked=1&payment=success&rental_id=$rental_id");
                exit();
            } else {
                throw new Exception("Failed to create booking.");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Payment failed: " . $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

$just_booked = isset($_GET['booked']) ? true : false;

// Check if admin has approved any of user's bookings recently
$check_approvals = $conn->prepare("
    SELECT r.id, r.status, r.updated_at, c.make, c.model 
    FROM rentals r 
    JOIN cars c ON r.car_id = c.id 
    WHERE r.user_id = ? 
    AND r.status IN ('active', 'approved') 
    AND r.updated_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ORDER BY r.updated_at DESC 
    LIMIT 3
");
$check_approvals->bind_param("i", $user_id);
$check_approvals->execute();
$recent_approvals = $check_approvals->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="catalog.php" class="inline-flex items-center text-slate-500 hover:text-indigo-600 mb-6">
        <i class="fas fa-chevron-left mr-2"></i> Back to Catalog
    </a>

    <!-- Approval Notifications -->
    <?php if (!empty($recent_approvals)): ?>
        <div class="mb-6 space-y-3">
            <?php foreach($recent_approvals as $approval): ?>
                <?php if($approval['status'] == 'active'): ?>
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-emerald-900">Booking Approved! ✅</h4>
                                    <p class="text-emerald-700 text-sm">
                                        Your <?php echo $approval['make'] . ' ' . $approval['model']; ?> rental has been approved by admin.
                                    </p>
                                    <p class="text-xs text-emerald-600 mt-1">
                                        <i class="far fa-clock mr-1"></i>
                                        Approved <?php echo date('h:i A', strtotime($approval['updated_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <a href="my_rentals.php" class="text-emerald-600 hover:text-emerald-800 font-medium text-sm">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Left Column: Images -->
        <div class="space-y-6">
            <div class="aspect-[16/10] rounded-3xl overflow-hidden bg-slate-100 shadow-xl shadow-slate-200/50">
                <img src="<?php echo $car['image_url']; ?>" alt="<?php echo $car['make']; ?>" class="w-full h-full object-cover">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <i class="fas fa-shield-alt text-indigo-600 text-2xl mb-3"></i>
                    <h3 class="font-semibold text-slate-900">Premium Insurance</h3>
                    <p class="text-sm text-slate-500 mt-1">Full coverage included in your rental price.</p>
                </div>
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <i class="fas fa-check-circle text-indigo-600 text-2xl mb-3"></i>
                    <h3 class="font-semibold text-slate-900">Guaranteed Model</h3>
                    <p class="text-sm text-slate-500 mt-1">You get exactly the car you see here.</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Details & Booking -->
        <div class="flex flex-col h-full">
            <div class="mb-6">
                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-bold border-none">
                    <?php echo $car['category']; ?>
                </span>
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mt-4 mb-2">
                    <?php echo $car['make'] . ' ' . $car['model']; ?>
                </h1>
                <div class="flex items-center gap-2 text-slate-500 text-lg mb-4">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Available in Miami, FL</span>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    <?php echo $car['description']; ?>
                </p>
            </div>

            <!-- Car Specifications -->
            <div class="grid grid-cols-2 gap-3 mb-8">
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-medium">Model Year</p>
                        <p class="font-semibold text-slate-900"><?php echo $car['year']; ?></p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-cog text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-medium">Transmission</p>
                        <p class="font-semibold text-slate-900"><?php echo $car['transmission']; ?></p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tachometer-alt text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-medium">0-60 MPH</p>
                        <p class="font-semibold text-slate-900"><?php echo $car['acceleration']; ?></p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bolt text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-medium">Power</p>
                        <p class="font-semibold text-slate-900"><?php echo $car['horsepower']; ?></p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-gas-pump text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-medium">Fuel Type</p>
                        <p class="font-semibold text-slate-900"><?php echo $car['fuel_type']; ?></p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-medium">Seats</p>
                        <p class="font-semibold text-slate-900"><?php echo $car['seats']; ?> Seats</p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-3">Features</h3>
                <div class="flex flex-wrap gap-2">
                    <?php if($features): foreach($features as $feature): ?>
                        <span class="px-3 py-1.5 border border-slate-200 text-slate-600 bg-slate-50 rounded-full text-sm font-medium">
                            <i class="fas fa-check text-indigo-600 mr-1"></i>
                            <?php echo $feature; ?>
                        </span>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- Booking Card -->
            <div class="mt-auto bg-white border border-slate-200 rounded-2xl p-6 shadow-lg">
                <?php if ($cancelled): ?>
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Booking cancelled successfully!</span>
                </div>
                <?php endif; ?>

                <?php if ($payment_success): ?>
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-green-900">Payment Successful!</h4>
                            <p class="text-green-700 text-sm">Your booking has been confirmed.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($payment_error): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Payment failed. Please try again.
                </div>
                <?php endif; ?>

                <?php if ($just_booked): ?>
                <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-blue-900 text-lg mb-2">Booking & Payment Complete! 🎉</h4>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-blue-700">
                                    <i class="fas fa-credit-card text-blue-500"></i>
                                    <span class="font-medium">Payment Status: </span>
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">Paid</span>
                                </div>
                                <div class="flex items-center gap-2 text-blue-700">
                                    <i class="fas fa-clock text-blue-500"></i>
                                    <span class="font-medium">Booking Status: </span>
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">Pending Approval</span>
                                </div>
                                <p class="text-blue-600 text-sm">
                                    Your payment has been processed successfully and your booking is awaiting admin approval.
                                </p>
                                <?php if (isset($_GET['rental_id'])): ?>
                                <div class="bg-white border border-blue-100 rounded-lg p-4 mt-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm text-slate-600">Booking Reference</p>
                                            <p class="font-mono font-bold text-slate-900">#<?php echo str_pad($_GET['rental_id'], 6, '0', STR_PAD_LEFT); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-slate-600">Amount Paid</p>
                                            <p class="font-bold text-green-600">$<?php echo number_format($car['price_per_day'] * $days, 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="flex gap-3 pt-3">
                                    <a href="my_rentals.php" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-medium text-center transition-colors">
                                        <i class="fas fa-list mr-2"></i> View My Rentals
                                    </a>
                                    <a href="catalog.php" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-800 py-3 rounded-xl font-medium text-center transition-colors">
                                        <i class="fas fa-car mr-2"></i> Browse More Cars
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if ($existing_booking): ?>
                <!-- Show existing booking -->
                <div class="mb-6">
                    <?php 
                    $status_colors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'cancelled' => 'bg-red-100 text-red-800 border-red-200'
                    ];
                    $status_color = $status_colors[$existing_booking['status']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                    $status_icons = [
                        'pending' => 'fas fa-clock',
                        'active' => 'fas fa-check-circle',
                        'completed' => 'fas fa-flag-checkered',
                        'cancelled' => 'fas fa-times-circle'
                    ];
                    $status_icon = $status_icons[$existing_booking['status']] ?? 'fas fa-clock';
                    
                    $payment_colors = [
                        'paid' => 'bg-green-100 text-green-800 border-green-200',
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'failed' => 'bg-red-100 text-red-800 border-red-200'
                    ];
                    $payment_color = $payment_colors[$existing_booking['payment_status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                    ?>
                    
                    <div class="border rounded-xl p-5 mb-4 <?php echo str_replace('bg-', 'border-', explode(' ', $status_color)[0]) . ' ' . str_replace('bg-', 'bg-', explode(' ', $status_color)[0]) . '10'; ?>">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full <?php echo str_replace('text-', 'bg-', explode(' ', $status_color)[1]) . '20'; ?> flex items-center justify-center">
                                    <i class="<?php echo $status_icon . ' ' . explode(' ', $status_color)[1]; ?> text-lg"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-lg">Your Booking Status</h4>
                                        <span class="text-sm text-slate-500">
                                            Created <?php echo date('M d, Y', strtotime($existing_booking['created_at'])); ?>
                                        </span>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="px-3 py-1 rounded-full text-sm font-bold <?php echo $status_color; ?> border">
                                            <?php echo ucfirst($existing_booking['status']); ?>
                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $payment_color; ?> border">
                                            <?php echo ucfirst($existing_booking['payment_status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <!-- Booking Details -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-white rounded-lg p-3 border">
                                            <p class="text-xs text-slate-500 mb-1">Rental Period</p>
                                            <p class="font-semibold text-slate-900">
                                                <?php 
                                                $start = new DateTime($existing_booking['start_date']);
                                                $end = new DateTime($existing_booking['end_date']);
                                                echo $start->format('M d') . ' - ' . $end->format('M d, Y');
                                                ?>
                                            </p>
                                            <p class="text-xs text-slate-500 mt-1">
                                                <?php 
                                                $days = $start->diff($end)->days + 1;
                                                echo $days . ' day' . ($days > 1 ? 's' : '');
                                                ?>
                                            </p>
                                        </div>
                                        
                                        <div class="bg-white rounded-lg p-3 border">
                                            <p class="text-xs text-slate-500 mb-1">Total Price</p>
                                            <p class="font-semibold text-indigo-600 text-xl">
                                                $<?php echo number_format($existing_booking['total_price'], 2); ?>
                                            </p>
                                            <p class="text-xs text-slate-500 mt-1">
                                                $<?php echo $car['price_per_day']; ?>/day
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment Status -->
                                    <?php if ($existing_booking['payment_status'] == 'paid'): ?>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-credit-card text-green-600"></i>
                                            <div>
                                                <p class="font-medium text-green-800">Payment Confirmed</p>
                                                <p class="text-sm text-green-700">Your payment has been processed successfully.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php elseif ($existing_booking['payment_status'] == 'pending'): ?>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-exclamation-circle text-yellow-600"></i>
                                            <div>
                                                <p class="font-medium text-yellow-800">Payment Pending</p>
                                                <p class="text-sm text-yellow-700">Please complete your payment to confirm booking.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Status Message -->
                                    <?php if ($existing_booking['status'] == 'pending'): ?>
                                    <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                                            <div>
                                                <p class="font-medium text-yellow-800 mb-1">Awaiting Admin Approval</p>
                                                <p class="text-sm text-yellow-700">
                                                    Your booking is in the queue for approval. Our team will review it shortly.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php elseif ($existing_booking['status'] == 'active'): ?>
                                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-lg p-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                                            <div>
                                                <p class="font-bold text-emerald-900 mb-1">Booking Approved! 🎉</p>
                                                <p class="text-sm text-emerald-800">
                                                    Your rental has been confirmed. Get ready to drive your <?php echo $car['make'] . ' ' . $car['model']; ?>!
                                                </p>
                                                <p class="text-xs text-emerald-700 mt-2">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Please bring your ID and driver's license for pickup.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Actions -->
                                    <div class="flex gap-3 pt-2">
                                        <a href="my_rentals.php" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-medium text-center transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-list"></i> View All Rentals
                                        </a>
                                        <?php if ($existing_booking['status'] == 'pending'): ?>
                                        <a href="?id=<?php echo $id; ?>&cancel=1&rental_id=<?php echo $existing_booking['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to cancel this reservation? This action cannot be undone.')"
                                           class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-medium text-center transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-times"></i> Cancel Booking
                                        </a>
                                        <?php elseif ($existing_booking['status'] == 'active'): ?>
                                        <a href="#" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-medium text-center transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-file-contract"></i> View Rental Agreement
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Show booking form with credit card payment -->
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <span class="text-3xl font-bold text-slate-900">$<?php echo $car['price_per_day']; ?></span>
                        <span class="text-slate-500"> / day</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500 mb-1">Total Price</div>
                        <div class="text-xl font-bold text-indigo-600" id="totalPriceDisplay">$0.00</div>
                    </div>
                </div>

                <form method="POST" id="bookingForm" class="space-y-6">
                    <!-- Date Selection -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-900 mb-3">Select Rental Dates</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Pick-up Date</label>
                                <input type="date" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>"
                                       class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Return Date</label>
                                <input type="date" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>"
                                       class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Credit Card Payment -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-900 mb-3">Credit Card Payment</h4>
                        
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                                    <i class="fab fa-cc-mastercard text-2xl text-red-600"></i>
                                    <i class="fab fa-cc-amex text-2xl text-blue-800"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-700">Secure Payment</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Card Number -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Card Number</label>
                                <div class="relative">
                                    <i class="fas fa-credit-card absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" name="card_number" required 
                                           placeholder="1234 5678 9012 3456"
                                           class="w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors"
                                           oninput="formatCardNumber(this)">
                                </div>
                            </div>

                            <!-- Card Holder -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Card Holder Name</label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" name="card_holder" required 
                                           placeholder="JOHN DOE"
                                           class="w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors">
                                </div>
                            </div>

                            <!-- Expiry & CVV -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Expiry Date</label>
                                    <input type="text" name="expiry" required 
                                           placeholder="MM/YY"
                                           class="w-full px-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors"
                                           oninput="formatExpiry(this)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">CVV</label>
                                    <div class="relative">
                                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                        <input type="text" name="cvv" required 
                                               placeholder="123"
                                               class="w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors"
                                               maxlength="3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-shield-alt text-green-600 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-green-800">Your Payment is Secure</p>
                                <p class="text-xs text-green-600 mt-1">
                                    We use SSL encryption to protect your card details. Your information is never stored on our servers.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Test Card Info -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-yellow-800 mb-2">For Testing Only:</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-yellow-700">
                            <div>Card: 4242 4242 4242 4242</div>
                            <div>Expiry: 12/30</div>
                            <div>CVV: 123</div>
                        </div>
                    </div>

                    <button type="submit" class="w-full h-14 text-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200/50 transition-all hover:shadow-xl hover:shadow-indigo-300/50">
                        <i class="fas fa-lock mr-2"></i> Book Now & Pay Securely
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const pricePerDay = <?php echo $car['price_per_day']; ?>;
const startDateInput = document.getElementById('start_date');
const endDateInput = document.getElementById('end_date');
const totalDisplay = document.getElementById('totalPriceDisplay');

// Format card number with spaces
function formatCardNumber(input) {
    let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let formatted = '';
    for (let i = 0; i < value.length && i < 16; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += value[i];
    }
    input.value = formatted;
}

// Format expiry date
function formatExpiry(input) {
    let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    if (value.length >= 2) {
        input.value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
}

// Calculate total price
function calculateTotal() {
    if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
        const start = new Date(startDateInput.value);
        const end = new Date(endDateInput.value);
        
        // Ensure end date is not before start date
        if (end < start) {
            endDateInput.value = startDateInput.value;
            calculateTotal();
            return;
        }
        
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        if (diffDays > 0) {
            const total = diffDays * pricePerDay;
            totalDisplay.innerText = '$' + total.toFixed(2);
        } else {
            totalDisplay.innerText = '$0.00';
        }
    }
}

// Set minimum end date based on start date
if (startDateInput && endDateInput) {
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
        calculateTotal();
    });
    
    endDateInput.addEventListener('change', calculateTotal);
}

// Auto-refresh page if there's a pending booking
<?php if ($existing_booking && $existing_booking['status'] == 'pending'): ?>
setTimeout(function() {
    window.location.reload();
}, 30000);
<?php endif; ?>

// Form validation
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const cardNumber = document.querySelector('input[name="card_number"]').value.replace(/\s/g, '');
    const expiry = document.querySelector('input[name="expiry"]').value;
    const cvv = document.querySelector('input[name="cvv"]').value;
    
    // Validate card number
    if (cardNumber.length !== 16) {
        e.preventDefault();
        alert('Please enter a valid 16-digit card number.');
        return false;
    }
    
    // Validate expiry date
    if (!/^\d{2}\/\d{2}$/.test(expiry)) {
        e.preventDefault();
        alert('Please enter expiry date in MM/YY format.');
        return false;
    }
    
    // Validate CVV
    if (cvv.length !== 3 || !/^\d{3}$/.test(cvv)) {
        e.preventDefault();
        alert('Please enter a valid 3-digit CVV.');
        return false;
    }
    
    // Validate dates
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    if (endDate < startDate) {
        e.preventDefault();
        alert('Return date cannot be before pick-up date.');
        return false;
    }
    
    // Confirm booking
    const total = totalDisplay.innerText;
    if (!confirm(`Confirm booking and pay ${total}?`)) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php include '../includes/footer.php'; ?>
