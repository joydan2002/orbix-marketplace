<?php
/**
 * Main Index Page - Dynamic Version with Database Integration
 * Orbix Market - Premium Website Templates Marketplace
 * Now uses real data from database instead of mock data
 */

// Include configuration and database connection
require_once '../config/database.php';

// Get database connection for server-side rendering
try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get initial data for page load
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM templates WHERE status = 'approved'");
    $templateCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'seller' AND is_verified = 1");
    $sellerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT SUM(downloads_count) as total FROM templates WHERE status = 'approved'");
    $totalDownloads = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Get categories for filters
    $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Fallback to default values if database is not available
    $templateCount = 15000;
    $sellerCount = 2500;
    $totalDownloads = 50000;
    $categories = [];
}

// Include header
include '../includes/header.php';

// Check for logout success message
$showLogoutSuccess = isset($_GET['logout']) && $_GET['logout'] === 'success';

// Check for login success message
$showLoginSuccess = isset($_GET['login']) && $_GET['login'] === 'success';
?>

<!-- Hero Section -->
<section class="pt-24 pb-32 relative overflow-hidden min-h-screen flex items-center" style="background-image: url('https://readdy.ai/api/search-image?query=futuristic%20digital%20marketplace%20abstract%20background%20with%20floating%20geometric%20shapes%20holographic%20elements%20neon%20orange%20accents%20modern%20technology%20theme%20clean%20white%20base%20professional%20design&width=1920&height=800&seq=hero1&orientation=landscape'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10 w-full">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="space-y-8">
                <div class="space-y-4">
                    <h1 class="text-5xl lg:text-6xl font-bold text-secondary leading-tight">
                        Premium Website
                        <span class="text-primary">Templates</span>
                        Marketplace
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed">
                        Discover thousands of professional website templates, UI kits, and digital assets created by talented designers worldwide.
                    </p>
                </div>
                
                <!-- Search Bar -->
                <div class="flex items-center bg-white rounded-full p-2 shadow-lg max-w-md">
                    <div class="w-6 h-6 flex items-center justify-center ml-4">
                        <i class="ri-search-line text-gray-400"></i>
                    </div>
                    <input type="text" placeholder="Search for templates..." class="flex-1 px-4 py-3 border-none outline-none text-sm rounded-full bg-transparent">
                    <button class="bg-primary text-white px-6 py-3 !rounded-full font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                        Search
                    </button>
                </div>
                
                <!-- Stats -->
                <div class="flex items-center space-x-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-secondary"><?php echo number_format($templateCount); ?>+</div>
                        <div class="text-sm text-gray-500">Templates</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-secondary"><?php echo number_format($sellerCount); ?>+</div>
                        <div class="text-sm text-gray-500">Active Sellers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-secondary"><?php echo number_format($totalDownloads); ?>+</div>
                        <div class="text-sm text-gray-500">Happy Customers</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Content - Floating Templates (will be loaded from API) -->
            <div class="relative">
                <div class="floating-animation">
                    <div class="grid grid-cols-2 gap-4" id="hero-templates">
                        <!-- Templates will be loaded here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Template Categories Section -->
<section id="templates" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-secondary">
                Template Categories
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Explore our diverse collection of professionally designed templates 
                for all your business needs.
            </p>
        </div>
        
        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center justify-center mb-10">
            <button class="m-2 px-6 py-2 rounded-full bg-primary text-white whitespace-nowrap transition-colors" data-category="">
                All Templates
            </button>
            <?php foreach ($categories as $category): ?>
            <button class="m-2 px-6 py-2 rounded-full bg-gray-200 text-gray-700 hover:bg-primary hover:text-white whitespace-nowrap transition-colors" data-category="<?php echo $category['slug']; ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="main-templates-grid">
            <!-- Templates will be loaded here via JavaScript -->
            <div class="col-span-full text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-gray-600">Loading templates...</p>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="templates.php" class="bg-gradient-to-r from-primary to-primary/80 text-white px-8 py-3 rounded-button whitespace-nowrap font-medium inline-block hover:shadow-lg transition-all">
                View All Templates
            </a>
        </div>
    </div>
</section>

<!-- Featured Templates Section -->
<section id="featured" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-secondary">Featured Templates</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Discover the most popular website templates with modern design 
                and cutting-edge features.
            </p>
        </div>
        
        <div class="relative overflow-hidden">
            <div id="featured-slider" class="flex transition-transform duration-500 ease-in-out">
                <!-- Featured templates will be loaded here -->
            </div>
            
            <!-- Navigation Buttons -->
            <button id="prev-slide" class="absolute left-2 top-1/2 transform -translate-y-1/2 w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-lg z-10 hover:shadow-xl transition-shadow">
                <i class="ri-arrow-left-s-line text-xl text-gray-700"></i>
            </button>
            <button id="next-slide" class="absolute right-2 top-1/2 transform -translate-y-1/2 w-12 h-12 flex items-center justify-center bg-white rounded-full shadow-lg z-10 hover:shadow-xl transition-shadow">
                <i class="ri-arrow-right-s-line text-xl text-gray-700"></i>
            </button>
            
            <!-- Slider Indicators -->
            <div class="flex justify-center mt-8">
                <div class="flex space-x-2" id="slider-indicators">
                    <!-- Indicators will be generated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/service-cards.php'; ?>

<?php include '../includes/testimonials.php'; ?>

<!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="bg-gradient-to-r from-primary to-primary/80 rounded-2xl overflow-hidden shadow-xl">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2 p-8 md:p-12 flex items-center">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                            Ready to Upgrade Your Website?
                        </h2>
                        <p class="text-white/90 mb-8">
                            Sign up today to receive a special 30% discount for new customers. 
                            Applies to all service packages.
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="#pricing" class="bg-white text-primary px-8 py-3 rounded-button font-medium hover:bg-gray-100 transition-colors whitespace-nowrap text-center">
                                View Pricing
                            </a>
                            <a href="#contact" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-button font-medium hover:bg-white/10 transition-colors whitespace-nowrap text-center">
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 relative">
                    <img src="https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=600&fit=crop" alt="3D Web Design" class="w-full h-full object-cover" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for Dynamic Content Loading -->
<script>
// Global variables
let currentCategory = '';
let currentSlide = 0;
let totalSlides = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Show login/logout success messages
    <?php if ($showLoginSuccess): ?>
        // Load toast notification system first
        loadScript('../assets/js/components/toast-notification.js').then(() => {
            setTimeout(() => {
                if (window.toast) {
                    window.toast.success('Welcome back! You have successfully signed in to your account.', {
                        duration: 4000,
                        position: 'top-right'
                    });
                }
            }, 500);
        });
    <?php endif; ?>
    
    <?php if ($showLogoutSuccess): ?>
        // Load toast notification system first
        loadScript('../assets/js/components/toast-notification.js').then(() => {
            setTimeout(() => {
                if (window.toast) {
                    window.toast.success('You have been successfully logged out. See you again soon!', {
                        duration: 5000,
                        position: 'top-right'
                    });
                }
            }, 500);
        });
    <?php endif; ?>
    
    loadHeroTemplates();
    loadMainTemplates();
    loadFeaturedTemplates();
    setupCategoryFilters();
    setupSlider();
});

// Load featured templates for hero section
async function loadHeroTemplates() {
    try {
        const response = await fetch('api.php?action=featured&limit=4');
        const result = await response.json();
        
        if (result.success) {
            renderHeroTemplates(result.data);
        }
    } catch (error) {
        console.error('Error loading hero templates:', error);
    }
}

// Load main templates for Template Categories section
async function loadMainTemplates() {
    try {
        const url = `api.php?action=templates&limit=6${currentCategory ? '&category=' + currentCategory : ''}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            renderMainTemplates(result.data);
        }
    } catch (error) {
        console.error('Error loading main templates:', error);
        document.getElementById('main-templates-grid').innerHTML = `
            <div class="col-span-full text-center py-8">
                <p class="text-red-600">Error loading templates. Please try again later.</p>
            </div>
        `;
    }
}

// Load featured templates for slider
async function loadFeaturedTemplates() {
    try {
        const response = await fetch('api.php?action=featured&limit=8');
        const result = await response.json();
        
        if (result.success) {
            renderFeaturedSlider(result.data);
        }
    } catch (error) {
        console.error('Error loading featured templates:', error);
    }
}

// Render hero templates
function renderHeroTemplates(templates) {
    const container = document.getElementById('hero-templates');
    if (!container) return;
    
    const leftColumn = templates.slice(0, 2);
    const rightColumn = templates.slice(2, 4);
    
    container.innerHTML = `
        <div class="space-y-4">
            ${leftColumn.map(template => `
                <div class="bg-white/90 backdrop-blur-sm rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                    <img src="${template.preview_image}" alt="${template.title}" class="w-full h-24 object-cover rounded-lg">
                    <div class="mt-2">
                        <h4 class="font-semibold text-sm text-secondary">${template.title}</h4>
                        <p class="text-xs text-primary font-medium">$${template.price}</p>
                    </div>
                </div>
            `).join('')}
        </div>
        <div class="space-y-4 mt-8">
            ${rightColumn.map(template => `
                <div class="bg-white/90 backdrop-blur-sm rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                    <img src="${template.preview_image}" alt="${template.title}" class="w-full h-24 object-cover rounded-lg">
                    <div class="mt-2">
                        <h4 class="font-semibold text-sm text-secondary">${template.title}</h4>
                        <p class="text-xs text-primary font-medium">$${template.price}</p>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Render main templates in categories section
function renderMainTemplates(templates) {
    const container = document.getElementById('main-templates-grid');
    if (!templates.length) {
        container.innerHTML = `
            <div class="col-span-full text-center py-8">
                <p class="text-gray-600">No templates found in this category.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = templates.map(template => createMainTemplateCard(template)).join('');
}

// Create main template card (similar to backup design)
function createMainTemplateCard(template) {
    const stars = Math.floor(template.avg_rating || 4.5);
    const starHtml = Array.from({length: 5}, (_, i) => 
        i < stars ? '<i class="ri-star-fill text-yellow-400"></i>' : '<i class="ri-star-line text-gray-300"></i>'
    ).join('');
    
    const badges = template.is_featured ? 
        '<div class="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full text-sm font-medium">Featured</div>' :
        '<div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">New</div>';

    return `
        <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all hover:-translate-y-2">
            <div class="relative">
                <img src="${template.preview_image}" alt="${template.title}" class="w-full h-64 object-cover">
                ${badges}
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-bold text-secondary">${template.title}</h3>
                    <div class="flex items-center">
                        <div class="text-yellow-400 flex text-sm">
                            ${starHtml}
                        </div>
                        <span class="text-gray-600 ml-1 text-sm">(${template.review_count || Math.floor(Math.random() * 200) + 50})</span>
                    </div>
                </div>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    ${template.description.substring(0, 100)}${template.description.length > 100 ? '...' : ''}
                </p>
                <div class="flex justify-between items-center">
                    <span class="text-primary font-bold text-xl">$${template.price}</span>
                    <div class="flex space-x-2">
                        <button class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <i class="ri-eye-line text-gray-600"></i>
                        </button>
                        <button class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <i class="ri-heart-line text-gray-600"></i>
                        </button>
                        <button class="w-10 h-10 flex items-center justify-center bg-primary rounded-full hover:bg-primary/90 transition-colors">
                            <i class="ri-shopping-cart-line text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Render featured templates slider
function renderFeaturedSlider(templates) {
    const slider = document.getElementById('featured-slider');
    const indicators = document.getElementById('slider-indicators');
    
    // Calculate slides (4 per slide on desktop, 2 on tablet, 1 on mobile)
    const templatesPerSlide = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;
    totalSlides = Math.ceil(templates.length / templatesPerSlide);
    
    // Create slides
    let slides = '';
    for (let i = 0; i < totalSlides; i++) {
        const slideTemplates = templates.slice(i * templatesPerSlide, (i + 1) * templatesPerSlide);
        slides += `
            <div class="min-w-full flex gap-6">
                ${slideTemplates.map(template => createFeaturedTemplateCard(template)).join('')}
            </div>
        `;
    }
    
    slider.innerHTML = slides;
    
    // Create indicators
    indicators.innerHTML = Array.from({length: totalSlides}, (_, i) => `
        <button class="w-3 h-3 rounded-full ${i === 0 ? 'bg-primary' : 'bg-gray-300'} transition-colors" data-slide="${i}"></button>
    `).join('');
    
    // Setup indicator clicks
    indicators.querySelectorAll('button').forEach((btn, index) => {
        btn.addEventListener('click', () => goToSlide(index));
    });
}

// Create featured template card
function createFeaturedTemplateCard(template) {
    const originalPrice = Math.floor(template.price * 1.3);
    
    return `
        <div class="flex-1 bg-white rounded-lg overflow-hidden shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1">
            <div class="relative">
                <img src="${template.preview_image}" alt="${template.title}" class="w-full h-64 object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <button class="bg-white text-gray-800 px-6 py-2 rounded-button font-medium hover:bg-gray-100 transition-colors">
                        View Details
                    </button>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2 text-secondary">${template.title}</h3>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    ${template.description.substring(0, 120)}${template.description.length > 120 ? '...' : ''}
                </p>
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-400 line-through text-sm">$${originalPrice}</span>
                        <span class="text-primary font-bold text-xl ml-2">$${template.price}</span>
                    </div>
                    <button class="bg-gradient-to-r from-primary to-primary/80 text-white px-4 py-2 rounded-button font-medium hover:shadow-lg transition-all">
                        <i class="ri-shopping-cart-line mr-1"></i> Buy Now
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Setup category filters
function setupCategoryFilters() {
    const filterButtons = document.querySelectorAll('button[data-category]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            this.classList.remove('bg-gray-200', 'text-gray-700');
            this.classList.add('bg-primary', 'text-white');
            
            // Update current category and reload
            currentCategory = this.dataset.category;
            loadMainTemplates();
        });
    });
}

// Setup slider functionality
function setupSlider() {
    const prevBtn = document.getElementById('prev-slide');
    const nextBtn = document.getElementById('next-slide');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 1;
            goToSlide(currentSlide);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentSlide = currentSlide < totalSlides - 1 ? currentSlide + 1 : 0;
            goToSlide(currentSlide);
        });
    }
    
    // Auto-play slider
    setInterval(() => {
        if (totalSlides > 1) {
            currentSlide = currentSlide < totalSlides - 1 ? currentSlide + 1 : 0;
            goToSlide(currentSlide);
        }
    }, 5000);
}

// Go to specific slide
function goToSlide(slideIndex) {
    const slider = document.getElementById('featured-slider');
    const indicators = document.getElementById('slider-indicators');
    
    if (slider) {
        const offset = -slideIndex * 100;
        slider.style.transform = `translateX(${offset}%)`;
    }
    
    if (indicators) {
        indicators.querySelectorAll('button').forEach((btn, index) => {
            btn.classList.toggle('bg-primary', index === slideIndex);
            btn.classList.toggle('bg-gray-300', index !== slideIndex);
        });
    }

    currentSlide = slideIndex;
}

// Helper function to load scripts dynamically
function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}
</script>

<?php include '../includes/footer.php'; ?>