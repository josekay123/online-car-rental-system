</main>
<footer class="bg-white border-t border-slate-200 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Company Info -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="bg-slate-900 p-1.5 rounded-lg text-white">
                        <i class="fas fa-car h-5 w-5"></i>
                    </div>
                    <span class="text-lg font-bold text-slate-900">LuxeDrive</span>
                </div>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Premium car rental service offering luxury vehicles for discerning customers. Experience the road like never before.
                </p>
                <div class="flex gap-4 pt-2">
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">
                        <i class="fab fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">
                        <i class="fab fa-linkedin-in text-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-bold text-slate-900 mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="index.php" class="text-slate-500 hover:text-indigo-600 transition-colors text-sm">Home</a></li>
                    <li><a href="catalog.php" class="text-slate-500 hover:text-indigo-600 transition-colors text-sm">Browse Cars</a></li>
                    <li><a href="contact.php" class="text-slate-500 hover:text-indigo-600 transition-colors text-sm">Contact Us</a></li>
                    <li><a href="my_rentals.php" class="text-slate-500 hover:text-indigo-600 transition-colors text-sm">My Rentals</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="font-bold text-slate-900 mb-4">Contact Info</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-indigo-500 mt-0.5"></i>
                        <span class="text-sm text-slate-500">123 Luxury Drive, Miami Beach, FL 33139</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-indigo-500"></i>
                        <span class="text-sm text-slate-500">+1 (305) 555-0123</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-indigo-500"></i>
                        <span class="text-sm text-slate-500">info@luxedrive.com</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-clock text-indigo-500"></i>
                        <span class="text-sm text-slate-500">24/7 Customer Support</span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h4 class="font-bold text-slate-900 mb-4">Stay Updated</h4>
                <p class="text-slate-500 text-sm mb-4">Subscribe to our newsletter for exclusive offers and updates.</p>
                <form class="space-y-3">
                    <input type="email" placeholder="Your email address" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-4 rounded-lg w-full transition-colors">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="pt-8 border-t border-slate-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-sm text-slate-500">
                    &copy; <?php echo date('Y'); ?> LuxeDrive Inc. All rights reserved.
                </div>
                
                <div class="flex flex-wrap gap-6">
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors text-sm">Privacy Policy</a>
                    <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors text-sm">Terms of Service</a>
                    <a href="contact.php" class="text-slate-400 hover:text-indigo-600 transition-colors text-sm">Contact</a>
                    
                    <!-- Admin Panel Link -->
                    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <a href="admin/index.php" class="text-purple-600 hover:text-purple-700 transition-colors font-semibold text-sm">
                            <i class="fas fa-user-shield"></i> Admin Panel
                        </a>
                    <?php else: ?>
                        <a href="admin_login.php" class="text-slate-400 hover:text-indigo-600 transition-colors text-sm">
                            <i class="fas fa-user-shield"></i> Admin Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Legal Notice -->
            <div class="mt-6 text-center text-xs text-slate-400 space-y-1">
                <p>All vehicles are fully insured. Driver must be 25+ with valid license.</p>
                <p>LuxeDrive is a registered trademark. All car makes and models are property of their respective owners.</p>
                <p class="mt-2">
                    <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                    SSL Secured • PCI Compliant • Verified Business
                </p>
            </div>
            
            <!-- Payment Methods -->
            <div class="mt-6 flex justify-center gap-4">
                <div class="text-slate-400 text-sm font-medium">We Accept:</div>
                <div class="flex gap-2">
                    <i class="fab fa-cc-visa text-2xl text-slate-400"></i>
                    <i class="fab fa-cc-mastercard text-2xl text-slate-400"></i>
                    <i class="fab fa-cc-amex text-2xl text-slate-400"></i>
                    <i class="fab fa-cc-paypal text-2xl text-slate-400"></i>
                    <i class="fab fa-cc-apple-pay text-2xl text-slate-400"></i>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button onclick="scrollToTop()" id="backToTop" 
        class="fixed bottom-8 right-8 w-12 h-12 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 transition-all opacity-0 invisible transform translate-y-4 duration-300">
    <i class="fas fa-chevron-up"></i>
</button>

<script>
// Back to Top functionality
const backToTopBtn = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTopBtn.classList.remove('opacity-0', 'invisible', 'translate-y-4');
        backToTopBtn.classList.add('opacity-100', 'visible', 'translate-y-0');
    } else {
        backToTopBtn.classList.remove('opacity-100', 'visible', 'translate-y-0');
        backToTopBtn.classList.add('opacity-0', 'invisible', 'translate-y-4');
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Form validation for newsletter
const newsletterForm = document.querySelector('footer form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        if (emailInput.value && emailInput.checkValidity()) {
            alert('Thank you for subscribing to our newsletter!');
            emailInput.value = '';
        } else {
            alert('Please enter a valid email address.');
        }
    });
}
</script>

</body>
</html>
