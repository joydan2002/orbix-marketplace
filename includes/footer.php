<!-- Footer -->
<footer class="bg-secondary text-white py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Company -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">O</span>
                    </div>
                    <span class="font-pacifico text-2xl">Orbix Market</span>
                </div>
                <p class="text-gray-400 mb-6">The premier marketplace for premium website templates, digital assets, and professional web services. Trusted by thousands of customers worldwide.</p>
                <div class="flex items-center space-x-4">
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="ri-twitter-line"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="ri-facebook-line"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="ri-instagram-line"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                        <i class="ri-linkedin-line"></i>
                    </a>
                </div>
            </div>
            
            <!-- Products -->
            <div>
                <h3 class="font-semibold mb-6">Products</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Website Templates</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">UI Kits</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Landing Pages</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Admin Dashboards</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">E-commerce Templates</a></li>
                </ul>
            </div>
            
            <!-- Services -->
            <div>
                <h3 class="font-semibold mb-6">Services</h3>
                <ul class="space-y-3">
                    <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Website Design</a></li>
                    <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Domain Registration</a></li>
                    <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Hosting & Cloud</a></li>
                    <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">SSL & Security</a></li>
                    <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Technical Support</a></li>
                    <li><a href="#services" class="text-gray-400 hover:text-white transition-colors">Website Maintenance</a></li>
                </ul>
            </div>
            
            <!-- Support -->
            <div>
                <h3 class="font-semibold mb-6">Support</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Seller Guidelines</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
                
        <!-- Bottom -->
        <div class="border-t border-gray-700 pt-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <p class="text-gray-400 mb-4 md:mb-0">© 2025 Orbix Market. All rights reserved.</p>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <i class="ri-visa-fill text-2xl"></i>
                        <i class="ri-mastercard-fill text-2xl"></i>
                        <i class="ri-paypal-fill text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- AI Mascot -->
<div class="ai-mascot">
    <button class="w-16 h-16 bg-primary rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all neon-glow">
        <i class="ri-robot-line text-white text-2xl"></i>
    </button>
</div>

<!-- Scripts - Exactly matching original file -->
<script id="header-interactions">
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const templatesDropdown = document.getElementById('templates-dropdown');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.1)';
        }
    });
    
    if (templatesDropdown) {
        const dropdownContent = templatesDropdown.querySelector('div[class*="absolute"]');
        
        templatesDropdown.addEventListener('mouseenter', () => {
            dropdownContent.style.display = 'block';
            requestAnimationFrame(() => {
                dropdownContent.style.opacity = '1';
                dropdownContent.style.visibility = 'visible';
                dropdownContent.style.transform = 'translateY(0)';
            });
        });
        
        templatesDropdown.addEventListener('mouseleave', () => {
            dropdownContent.style.opacity = '0';
            dropdownContent.style.visibility = 'hidden';
            dropdownContent.style.transform = 'translateY(2px)';
        });
    }
});
</script>

<script id="template-interactions">
document.addEventListener('DOMContentLoaded', function() {
    const heartButtons = document.querySelectorAll('[class*="ri-heart"]');
    heartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.classList.contains('ri-heart-line')) {
                this.classList.remove('ri-heart-line');
                this.classList.add('ri-heart-fill');
                this.style.color = '#ef4444';
            } else {
                this.classList.remove('ri-heart-fill');
                this.classList.add('ri-heart-line');
                this.style.color = '';
            }
        });
    });
});
</script>

<script id="filter-interactions">
document.addEventListener('DOMContentLoaded', function() {
    const categoryButtons = document.querySelectorAll('button[class*="px-4 py-2"]');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            categoryButtons.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-white/80', 'text-secondary');
            });
            
            this.classList.remove('bg-white/80', 'text-secondary');
            this.classList.add('bg-primary', 'text-white');
        });
    });
});
</script>

<script id="ai-mascot-interaction">
document.addEventListener('DOMContentLoaded', function() {
    const mascot = document.querySelector('.ai-mascot button');
    if (mascot) {
        mascot.addEventListener('click', function() {
            alert('Hi! I\'m your AI assistant. How can I help you find the perfect template today?');
        });
    }
});
</script>

</body>
</html>