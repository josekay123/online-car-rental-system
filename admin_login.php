<?php
session_start();

// Simple admin credentials
$admin_username = 'admin';
$admin_password = 'admin123';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['is_admin'] = 1;
        $_SESSION['admin_name'] = 'Administrator';
        header("Location: admin/index.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | LuxeDrive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-2xl mb-4">
                <i class="fas fa-user-shield text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Admin Login</h2>
            <p class="text-gray-600 mt-2">Manage bookings and users</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <?php if($error): ?>
            <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none"
                           placeholder="Enter username">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none"
                           placeholder="Enter password">
                </div>
                
                <button type="submit" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 rounded-lg transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login to Admin
                </button>
            </form>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-sm font-medium text-yellow-800">Test Credentials:</p>
                    <p class="text-sm text-yellow-700 mt-1">Username: <span class="font-mono">admin</span></p>
                    <p class="text-sm text-yellow-700">Password: <span class="font-mono">admin123</span></p>
                </div>
                
                <div class="flex gap-3">
                    <a href="index.php" class="flex-1 text-center text-gray-600 hover:text-gray-800 py-2">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="login.php" class="flex-1 text-center text-gray-600 hover:text-gray-800 py-2">
                        <i class="fas fa-user mr-2"></i>User Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
