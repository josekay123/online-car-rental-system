<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxeDrive - Premium Car Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 selection:bg-indigo-100 selection:text-indigo-900">
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <a href="index.php" class="flex items-center gap-2 group">
                <div class="bg-indigo-600 p-2 rounded-xl text-white group-hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-car h-6 w-6"></i>
                </div>
                <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">
                    LuxeDrive
                </span>
            </a>
            <div class="hidden md:flex items-center gap-8">
                <a href="index.php" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Home</a>
                <a href="catalog.php" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Browse Cars</a>
                <a href="contact.php" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Contact</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="my_rentals.php" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">My Rentals</a>
                    <div class="flex items-center gap-4 ml-4">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-full">
                            <i class="fas fa-user-circle text-slate-500"></i>
                            <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                        </div>
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <a href="admin/index.php" class="text-purple-600 hover:text-purple-700 text-sm font-medium" title="Admin Panel">
                                <i class="fas fa-user-shield"></i> Admin Panel
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" class="text-red-600 text-sm font-medium hover:text-red-700" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-4">
                        <a href="login.php" class="text-slate-600 hover:text-indigo-600 text-sm font-medium transition-colors">Sign In</a>
                        <a href="register.php" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full px-6 py-2.5 text-sm font-medium transition-all shadow-lg shadow-indigo-200 hover:shadow-indigo-300">
                            Get Started
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="pt-20 pb-16 min-h-[calc(100vh-4rem)]">
