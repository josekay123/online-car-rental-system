<?php 
session_start();
include 'header.php'; 
?>

<!-- Hero Section -->
<section class="relative min-h-[90vh] flex items-center pt-20">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 to-slate-900/60 z-10"></div>
        <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=2000" 
             alt="Luxury Car Background" class="w-full h-full object-cover">
    </div>

    <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-3xl opacity-0 animate-[fadeIn_0.8s_ease-out_forwards]">
            <div class="inline-flex items-center rounded-full border border-indigo-400/30 bg-indigo-500/10 px-4 py-1.5 text-sm font-medium text-indigo-300 mb-6 backdrop-blur-md">
                <i class="fas fa-star mr-2 text-indigo-400"></i> #1 Premium Car Rental Service
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-white tracking-tight leading-tight mb-6">
                Drive the <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">Extraordinary</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-300 mb-10 max-w-2xl leading-relaxed">
                Experience the thrill of the world's most exclusive vehicles. 
                From Italian supercars to German luxury sedans, your dream ride awaits.
                Instant booking. Zero paperwork hassles.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="catalog.php" class="inline-flex items-center justify-center h-14 px-8 text-lg bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-medium transition-colors">
                    Browse Collection <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="#how-it-works" class="inline-flex items-center justify-center h-14 px-8 text-lg border border-slate-600 text-white hover:bg-white/10 rounded-full font-medium transition-colors backdrop-blur-sm">
                    How it Works
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="how-it-works" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Why Choose LuxeDrive?</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">
                We provide more than just a car; we provide a seamless, premium experience designed for those who appreciate excellence.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-6 text-indigo-600">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Premium Insurance</h3>
                <p class="text-slate-500 leading-relaxed">Drive with peace of mind. All our rentals include comprehensive insurance coverage.</p>
            </div>
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-6 text-indigo-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">24/7 Support</h3>
                <p class="text-slate-500 leading-relaxed">Our dedicated concierge team is available around the clock to assist with any request.</p>
            </div>
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-6 text-indigo-600">
                    <i class="fas fa-star text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Top Condition</h3>
                <p class="text-slate-500 leading-relaxed">Every vehicle is meticulously maintained and detailed before every single rental.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 bg-slate-900 relative overflow-hidden">
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-indigo-600 rounded-full opacity-20 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-violet-600 rounded-full opacity-20 blur-3xl"></div>
    
    <div class="relative max-w-4xl mx-auto text-center px-4">
        <h2 class="text-4xl font-bold text-white mb-6">Ready to hit the road?</h2>
        <p class="text-slate-300 mb-10 text-lg">
            Join thousands of satisfied customers who have elevated their travel experience with LuxeDrive.
        </p>
        <a href="catalog.php" class="inline-block h-16 px-10 pt-4 text-xl bg-white text-slate-900 hover:bg-slate-100 hover:scale-105 transition-all rounded-full font-bold shadow-2xl shadow-indigo-900/50">
            Find Your Car Now
        </a>
    </div>
</section>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include 'footer.php'; ?>
