<?php
include '../includes/config.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $subject = $conn->real_escape_string(trim($_POST['subject']));
    $message = $conn->real_escape_string(trim($_POST['message']));
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all required fields";
    } else {
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            $success = "Thank you! We'll get back to you within 24 hours.";
            $_POST = array(); // Clear form
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | LuxeDrive Premium Car Rentals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>

    <div class="bg-gradient-to-br from-slate-50 to-slate-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">Get in Touch</h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Contact Information -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Address Card -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-map-marker-alt text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Visit Us</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">
                            123 Luxury Drive<br>
                            Miami Beach, FL 33139<br>
                            United States
                        </p>
                    </div>

                    <!-- Phone Card -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-phone text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Call Us</h3>
                        <p class="text-slate-600 text-sm mb-2">
                            <strong>Main:</strong> +1 (305) 555-0123
                        </p>
                        <p class="text-slate-600 text-sm">
                            <strong>24/7 Support:</strong> +1 (305) 555-0124
                        </p>
                    </div>

                    <!-- Email Card -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-envelope text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Email Us</h3>
                        <p class="text-slate-600 text-sm mb-2">
                            <strong>General:</strong><br>
                            <a href="mailto:info@luxedrive.com" class="text-indigo-600 hover:text-indigo-700">info@luxedrive.com</a>
                        </p>
                        <p class="text-slate-600 text-sm">
                            <strong>Support:</strong><br>
                            <a href="mailto:support@luxedrive.com" class="text-indigo-600 hover:text-indigo-700">support@luxedrive.com</a>
                        </p>
                    </div>

                    <!-- Hours Card -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-clock text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Business Hours</h3>
                        <div class="text-slate-600 text-sm space-y-1">
                            <p><strong>Mon - Fri:</strong> 8:00 AM - 8:00 PM</p>
                            <p><strong>Saturday:</strong> 9:00 AM - 6:00 PM</p>
                            <p><strong>Sunday:</strong> 10:00 AM - 4:00 PM</p>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="bg-gradient-to-br from-indigo-600 to-violet-600 rounded-2xl p-6 text-white">
                        <h3 class="text-lg font-bold mb-4">Follow Us</h3>
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">Send us a Message</h2>
                        
                        <?php if ($success): ?>
                            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo $success; ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                <span><?php echo $error; ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="space-y-5">
                            <div class="grid md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        required
                                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors"
                                        placeholder="John Doe"
                                        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        required
                                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors"
                                        placeholder="you@example.com"
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                    >
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input 
                                        type="tel" 
                                        name="phone"
                                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors"
                                        placeholder="+1 (305) 555-0000"
                                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        Subject
                                    </label>
                                    <select 
                                        name="subject"
                                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors"
                                    >
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Rental Question">Rental Question</option>
                                        <option value="Support">Support</option>
                                        <option value="Partnership">Partnership</option>
                                        <option value="Feedback">Feedback</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="message" 
                                    required
                                    rows="6"
                                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-colors resize-none"
                                    placeholder="Tell us how we can help you..."
                                ><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>

                            <button 
                                type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors shadow-lg shadow-indigo-200 hover:shadow-indigo-300 flex items-center justify-center gap-2"
                            >
                                <i class="fas fa-paper-plane"></i>
                                Send Message
                            </button>
                        </form>
                    </div>

                    <!-- Map -->
                    <div class="mt-8 bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3592.455326394067!2d-80.13481492464795!3d25.790654677358467!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88d9b4a5c3e5e0e5%3A0x5c0c0c0c0c0c0c0c!2sMiami%20Beach%2C%20FL!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy"
                            class="w-full"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
