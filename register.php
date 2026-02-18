<?php
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: catalog.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    
    // Separated address components
    $building_number = $conn->real_escape_string(trim($_POST['building_number']));
    $street_name = $conn->real_escape_string(trim($_POST['street_name']));
    $apartment = $conn->real_escape_string(trim($_POST['apartment']));
    
    $city = $conn->real_escape_string(trim($_POST['city']));
    $state = $conn->real_escape_string(trim($_POST['state']));
    $zip_code = $conn->real_escape_string(trim($_POST['zip_code']));
    $license_number = $conn->real_escape_string(trim($_POST['license_number']));
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($phone) || 
        empty($building_number) || empty($street_name) || empty($license_number)) {
        $error = "Please fill in all required fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email already registered. Please login instead.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $is_admin = 0; // Default to regular user
            
            // Combine address components for single address field (backward compatibility)
            $full_address = $building_number . ' ' . $street_name;
            if (!empty($apartment)) {
                $full_address .= ', ' . $apartment;
            }
            
            $insert_sql = "INSERT INTO users (full_name, email, password, phone, address, city, state, zip_code, license_number, date_of_birth, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssssssssssi", $full_name, $email, $hashed_password, $phone, $full_address, $city, $state, $zip_code, $license_number, $date_of_birth, $is_admin);
            
            if ($insert_stmt->execute()) {
                $_SESSION['user_id'] = $insert_stmt->insert_id;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                $_SESSION['is_admin'] = 0;
                
                header("Location: catalog.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl mb-4">
                <i class="fas fa-car text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-900">Create Account</h2>
            <p class="text-slate-500 mt-2">Join LuxeDrive - Complete your profile to get started</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
            <?php if ($error): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">
                        <i class="fas fa-user text-indigo-600 mr-2"></i>Personal Information
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="full_name" required
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="John Doe"
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="you@example.com"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" required
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="+1 (305) 555-0000"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Date of Birth <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_of_birth" required
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>"
                                   value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Driver's License Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="license_number" required
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="D1234567"
                                   value="<?php echo isset($_POST['license_number']) ? htmlspecialchars($_POST['license_number']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">
                        <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>Address Information
                    </h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Building/House Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="building_number" required
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="123"
                                   value="<?php echo isset($_POST['building_number']) ? htmlspecialchars($_POST['building_number']) : ''; ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Street Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="street_name" required
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="Main Street"
                                   value="<?php echo isset($_POST['street_name']) ? htmlspecialchars($_POST['street_name']) : ''; ?>">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Apartment/Unit/Suite (Optional)
                            </label>
                            <input type="text" name="apartment"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="Apt 4B"
                                   value="<?php echo isset($_POST['apartment']) ? htmlspecialchars($_POST['apartment']) : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">City</label>
                            <input type="text" name="city"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="Miami"
                                   value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">State</label>
                            <input type="text" name="state"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="FL"
                                   value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">ZIP Code</label>
                            <input type="text" name="zip_code"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="33139"
                                   value="<?php echo isset($_POST['zip_code']) ? htmlspecialchars($_POST['zip_code']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Account Security -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4 pb-2 border-b border-slate-200">
                        <i class="fas fa-lock text-indigo-600 mr-2"></i>Account Security
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" required minlength="6"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="••••••••">
                            <p class="text-xs text-slate-500 mt-1">Minimum 6 characters</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="confirm_password" required minlength="6"
                                   class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex items-start">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded mt-1">
                    <label for="terms" class="ml-2 block text-sm text-slate-700">
                        I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> 
                        and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg transition-colors shadow-lg shadow-indigo-200 hover:shadow-indigo-300 flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-slate-500">Already have an account?</span>
                </div>
            </div>

            <a href="login.php" 
               class="block w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-900 font-medium py-3 px-4 rounded-lg transition-colors">
                Sign In Instead
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
