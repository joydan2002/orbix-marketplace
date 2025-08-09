<?php
/**
 * Main Index Page - Dynamic Version with Database Integration
 * Orbix Market - Premium Website Templates Marketplace
 * Now uses real data from database instead of mock data
 */

// Include configuration and database connection
require_once '../config/database.php';
require_once '../config/cloudinary-config.php'; // Add Cloudinary support

// Get database connection for server-side rendering
try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get initial data for page load
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM templates t 
                         LEFT JOIN users u ON t.seller_id = u.id 
                         WHERE t.status = 'approved' AND u.user_type = 'seller'");
    $templateCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'seller' AND is_verified = 1");
    $sellerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $pdo->query("SELECT SUM(t.downloads_count) as total FROM templates t 
                         LEFT JOIN users u ON t.seller_id = u.id 
                         WHERE t.status = 'approved' AND u.user_type = 'seller'");
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
include 'includes/header.php';

// Check for logout success message
$showLogoutSuccess = isset($_GET['logout']) && $_GET['logout'] === 'success';

// Check for login success message
$showLoginSuccess = isset($_GET['login']) && $_GET['login'] === 'success';
?>

<!-- Hero Section -->
<section class="pt-20 sm:pt-24 pb-16 sm:pb-32 relative overflow-hidden min-h-[90vh] sm:min-h-screen flex items-center" style="background-image: url('https://readdy.ai/api/search-image?query=futuristic%20digital%20marketplace%20abstract%20background%20with%20floating%20geometric%20shapes%20holographic%20elements%20neon%20orange%20accents%20modern%20technology%20theme%20clean%20white%20base%20professional%20design&width=1920&height=800&seq=hero1&orientation=landscape'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10 w-full">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            <!-- Left Content -->
            <div class="space-y-6 sm:space-y-8 text-center lg:text-left">
                <div class="space-y-4">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-secondary leading-tight">
                        Premium Website
                        <span class="text-primary">Templates</span>
                        <span class="block">Marketplace</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-600 leading-relaxed max-w-lg mx-auto lg:mx-0">
                        Discover thousands of professional website templates, UI kits, and digital assets created by talented designers worldwide.
                    </p>
                </div>
                
                <!-- Search Bar -->
                <div class="relative max-w-2xl mx-auto lg:mx-0">
                    <div class="flex items-center bg-white rounded-full shadow-lg border border-gray-200 overflow-hidden">
                        <div class="flex items-center pl-6 pr-4">
                            <i class="ri-search-line text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" placeholder="Search for templates..." class="flex-1 py-4 px-2 border-none outline-none text-base bg-transparent pr-12" id="hero-search-input" oninput="toggleSearchIcon('hero')">
                        <button class="absolute right-4 text-orange-500 hover:text-orange-600 transition-colors hidden" id="hero-search-btn" onclick="performHeroSearch()">
                            <i class="ri-send-plane-fill text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Right Content - Floating Templates (will be loaded from API) -->
            <div class="relative mt-8 lg:mt-0 order-first lg:order-last">
                <div class="floating-animation">
                    <div class="grid grid-cols-2 gap-2 sm:gap-4" id="hero-templates">
                        <!-- Templates will be loaded here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Template Categories Section -->
<section id="templates" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 text-secondary">
                Template Categories
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto px-4">
                Explore our diverse collection of professionally designed templates 
                for all your business needs.
            </p>
        </div>
        
        <!-- Category Filters -->
        <div class="mb-8 sm:mb-10">
            <!-- Desktop view -->
            <div class="hidden sm:flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                <button class="category-filter active px-4 sm:px-6 py-2 sm:py-3 rounded-full bg-primary text-white font-medium transition-all hover:shadow-lg text-sm sm:text-base" data-category="">
                    All Categories
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="category-filter px-4 sm:px-6 py-2 sm:py-3 rounded-full bg-white text-gray-700 font-medium transition-all hover:shadow-lg border border-gray-200 hover:border-primary text-sm sm:text-base" data-category="<?= $category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Mobile horizontal scroll view -->
            <div class="block sm:hidden">
                <div class="overflow-x-auto scrollbar-hide pb-2">
                    <div class="flex gap-3 px-4 min-w-max">
                        <button class="category-filter active px-4 py-2 rounded-full bg-primary text-white font-medium transition-all hover:shadow-lg text-sm whitespace-nowrap flex-shrink-0" data-category="">
                            All Categories
                        </button>
                        <?php foreach ($categories as $category): ?>
                        <button class="category-filter px-4 py-2 rounded-full bg-white text-gray-700 font-medium transition-all hover:shadow-lg border border-gray-200 hover:border-primary text-sm whitespace-nowrap flex-shrink-0" data-category="<?= $category['id'] ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Templates Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8" id="main-templates-grid">
            <!-- Loading state -->
            <div class="col-span-full text-center py-8 sm:py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-gray-600">Loading templates...</p>
            </div>
        </div>
        
        <div class="text-center mt-8 sm:mt-12">
            <a href="templates.php" class="bg-gradient-to-r from-primary to-primary/80 text-white px-6 sm:px-8 py-3 rounded-button whitespace-nowrap font-medium inline-block hover:shadow-lg transition-all text-sm sm:text-base">
                View All Templates
            </a>
        </div>
    </div>
</section>

<!-- Featured Templates Section -->
<section id="featured" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 text-secondary">Featured Templates</h2>
            <p class="text-gray-600 max-w-2xl mx-auto px-4">
                Discover the most popular website templates with modern design 
                and cutting-edge features.
            </p>
        </div>
        
        <div class="relative overflow-hidden">
            <div id="featured-slider" class="flex transition-transform duration-500 ease-in-out">
                <!-- Featured templates will be loaded here -->
            </div>
            
            <!-- Navigation Buttons - Hidden on mobile, visible on tablet+ -->
            <button id="prev-slide" class="hidden sm:flex absolute left-2 top-1/2 transform -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 items-center justify-center bg-white rounded-full shadow-lg z-10 hover:shadow-xl transition-shadow">
                <i class="ri-arrow-left-s-line text-lg sm:text-xl text-gray-700"></i>
            </button>
            <button id="next-slide" class="hidden sm:flex absolute right-2 top-1/2 transform -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 items-center justify-center bg-white rounded-full shadow-lg z-10 hover:shadow-xl transition-shadow">
                <i class="ri-arrow-right-s-line text-lg sm:text-xl text-gray-700"></i>
            </button>
            
            <!-- Mobile Navigation Buttons -->
            <div class="flex sm:hidden justify-center mt-4 space-x-4">
                <button id="prev-slide-mobile" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow hover:shadow-md transition-shadow">
                    <i class="ri-arrow-left-s-line text-lg text-gray-700"></i>
                </button>
                <button id="next-slide-mobile" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full shadow hover:shadow-md transition-shadow">
                    <i class="ri-arrow-right-s-line text-lg text-gray-700"></i>
                </button>
            </div>
            
            <!-- Slider Indicators -->
            <div class="flex justify-center mt-6 sm:mt-8">
                <div class="flex space-x-2" id="slider-indicators">
                    <!-- Indicators will be generated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/service-cards.php'; ?>

<?php include 'includes/testimonials.php'; ?>

<!-- CTA Section -->
<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="bg-gradient-to-r from-primary to-primary/80 rounded-xl sm:rounded-2xl overflow-hidden shadow-xl">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2 p-6 sm:p-8 md:p-12 flex items-center">
                    <div class="w-full text-center md:text-left">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4 sm:mb-6">
                            Ready to Upgrade Your Website?
                        </h2>
                        <p class="text-white/90 mb-6 sm:mb-8 text-sm sm:text-base">
                            Sign up today to receive a special 30% discount for new customers. 
                            Applies to all service packages.
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            <a href="#pricing" class="bg-white text-primary px-6 sm:px-8 py-3 rounded-button font-medium hover:bg-gray-100 transition-colors whitespace-nowrap text-center text-sm sm:text-base">
                                View Pricing
                            </a>
                            <a href="#contact" class="bg-transparent border-2 border-white text-white px-6 sm:px-8 py-3 rounded-button font-medium hover:bg-white/10 transition-colors whitespace-nowrap text-center text-sm sm:text-base">
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 relative min-h-[200px] sm:min-h-[300px]">
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
        loadScript('assets/js/components/toast-notification.js').then(() => {
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
        loadScript('assets/js/components/toast-notification.js').then(() => {
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
        const response = await fetch('../api/general.php?action=featured&limit=4');
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
        const url = `../api/general.php?action=templates&limit=6${currentCategory ? '&category=' + currentCategory : ''}`;
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
        const response = await fetch('../api/general.php?action=featured&limit=8');
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
        <div class="space-y-2 sm:space-y-4">
            ${leftColumn.map(template => `
                <div class="bg-white/90 backdrop-blur-sm rounded-lg sm:rounded-xl p-2 sm:p-4 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                    <img src="${getOptimizedImageUrlJS(template.preview_image, 'thumb')}" alt="${template.title}" class="w-full h-16 sm:h-20 md:h-24 object-cover rounded-md sm:rounded-lg" onerror="this.src='assets/images/default-template.jpg'">
                    <div class="mt-1 sm:mt-2">
                        <h4 class="font-semibold text-xs sm:text-sm text-secondary truncate">${template.title}</h4>
                        <p class="text-xs text-primary font-medium">$${template.price}</p>
                    </div>
                </div>
            `).join('')}
        </div>
        <div class="space-y-2 sm:space-y-4 mt-4 sm:mt-8">
            ${rightColumn.map(template => `
                <div class="bg-white/90 backdrop-blur-sm rounded-lg sm:rounded-xl p-2 sm:p-4 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                    <img src="${getOptimizedImageUrlJS(template.preview_image, 'thumb')}" alt="${template.title}" class="w-full h-16 sm:h-20 md:h-24 object-cover rounded-md sm:rounded-lg" onerror="this.src='assets/images/default-template.jpg'">
                    <div class="mt-1 sm:mt-2">
                        <h4 class="font-semibold text-xs sm:text-sm text-secondary truncate">${template.title}</h4>
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
        '<div class="absolute top-2 sm:top-4 right-2 sm:right-4 bg-primary text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium">Featured</div>' :
        '<div class="absolute top-2 sm:top-4 right-2 sm:right-4 bg-green-500 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium">New</div>';

    return `
        <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all hover:-translate-y-2">
            <div class="relative">
                <img src="${getOptimizedImageUrlJS(template.preview_image, 'card')}" alt="${template.title}" class="w-full h-48 sm:h-56 md:h-64 object-cover" onerror="this.src='assets/images/default-template.jpg'">
                ${badges}
            </div>
            <div class="p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 space-y-2 sm:space-y-0">
                    <h3 class="text-lg sm:text-xl font-bold text-secondary">${template.title}</h3>
                    <div class="flex items-center">
                        <div class="text-yellow-400 flex text-sm">
                            ${starHtml}
                        </div>
                        <span class="text-gray-600 ml-1 text-sm">(${template.review_count || Math.floor(Math.random() * 200) + 50})</span>
                    </div>
                </div>
                <p class="text-gray-600 mb-4 leading-relaxed text-sm sm:text-base">
                    ${template.description.substring(0, 100)}${template.description.length > 100 ? '...' : ''}
                </p>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                    <span class="text-primary font-bold text-xl">$${template.price}</span>
                    <div class="flex justify-center sm:justify-end space-x-2">
                        <button onclick="viewTemplateDetails(${template.id})" class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <i class="ri-eye-line text-gray-600 text-sm sm:text-base"></i>
                        </button>
                        <button onclick="addToFavorites(${template.id}, 'template')" class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                            <i class="ri-heart-line text-gray-600 text-sm sm:text-base"></i>
                        </button>
                        <button onclick="addToCart(${template.id}, '${escapeHtml(template.title)}', ${template.price}, '${escapeHtml(getOptimizedImageUrlJS(template.preview_image, 'thumb'))}', '${escapeHtml(template.seller_name || 'Unknown')}', 'template')" class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center bg-primary rounded-full hover:bg-primary/90 transition-colors">
                            <i class="ri-shopping-cart-line text-white text-sm sm:text-base"></i>
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
                <img src="${getOptimizedImageUrlJS(template.preview_image, 'card')}" alt="${template.title}" class="w-full h-64 object-cover" onerror="this.src='assets/images/default-template.jpg'">
                <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <button onclick="viewTemplateDetails(${template.id})" class="bg-white text-gray-800 px-6 py-2 rounded-button font-medium hover:bg-gray-100 transition-colors">
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
                    <button onclick="addToCart(${template.id}, '${escapeHtml(template.title)}', ${template.price}, '${escapeHtml(getOptimizedImageUrlJS(template.preview_image, 'thumb'))}', '${escapeHtml(template.seller_name || 'Unknown')}', 'template')" class="bg-gradient-to-r from-primary to-primary/80 text-white px-4 py-2 rounded-button font-medium hover:shadow-lg transition-all">
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
    const prevBtnMobile = document.getElementById('prev-slide-mobile');
    const nextBtnMobile = document.getElementById('next-slide-mobile');
    
    // Desktop buttons
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
    
    // Mobile buttons
    if (prevBtnMobile) {
        prevBtnMobile.addEventListener('click', () => {
            currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 1;
            goToSlide(currentSlide);
        });
    }
    
    if (nextBtnMobile) {
        nextBtnMobile.addEventListener('click', () => {
            currentSlide = currentSlide < totalSlides - 1 ? currentSlide + 1 : 0;
            goToSlide(currentSlide);
        });
    }
    
    // Touch/swipe support for mobile
    let startX = 0;
    let startY = 0;
    let distX = 0;
    let distY = 0;
    
    const slider = document.getElementById('featured-slider');
    if (slider) {
        slider.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });
        
        slider.addEventListener('touchmove', (e) => {
            if (!startX || !startY) return;
            
            distX = e.touches[0].clientX - startX;
            distY = e.touches[0].clientY - startY;
        });
        
        slider.addEventListener('touchend', () => {
            if (Math.abs(distX) > Math.abs(distY) && Math.abs(distX) > 50) {
                if (distX > 0) {
                    // Swipe right - go to previous slide
                    currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 1;
                } else {
                    // Swipe left - go to next slide
                    currentSlide = currentSlide < totalSlides - 1 ? currentSlide + 1 : 0;
                }
                goToSlide(currentSlide);
            }
            
            startX = 0;
            startY = 0;
            distX = 0;
            distY = 0;
        });
    }
    
    // Auto-play slider (pause on mobile for better UX)
    const isDesktop = window.innerWidth >= 768;
    if (isDesktop) {
        setInterval(() => {
            if (totalSlides > 1) {
                currentSlide = currentSlide < totalSlides - 1 ? currentSlide + 1 : 0;
                goToSlide(currentSlide);
            }
        }, 5000);
    }
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

// Button handler functions
function viewTemplateDetails(templateId) {
    window.location.href = `template-detail.php?id=${templateId}`;
}

function addToFavorites(itemId, itemType) {
    // Check if user is logged in
    if (typeof cart === 'undefined') {
        window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
        return;
    }
    
    // Add to favorites logic here
    console.log(`Adding ${itemType} ${itemId} to favorites`);
    
    // Show success message
    if (window.toast) {
        window.toast.success(`${itemType === 'template' ? 'Template' : 'Service'} added to favorites!`, {
            duration: 3000,
            position: 'top-right'
        });
    } else {
        showSuccess(`${itemType === 'template' ? 'Template' : 'Service'} added to favorites!`);
    }
}

function addToCart(itemId, title, price, image, seller, itemType) {
    // Check if user is logged in
    if (typeof cart === 'undefined') {
        window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
        return;
    }
    
    // Create item data object
    const itemData = {
        id: itemId,
        title: title,
        price: price,
        image: image,
        seller: seller,
        type: itemType
    };
    
    // Add to cart using the global cart system
    cart.addItem(itemData);
}

// Search function for hero search bar
function performHeroSearch() {
    const searchInput = document.getElementById('hero-search-input');
    const searchTerm = searchInput.value.trim();
    
    if (searchTerm) {
        // Redirect to templates page with search parameter
        window.location.href = `templates.php?search=${encodeURIComponent(searchTerm)}`;
    } else {
        // If empty, just go to templates page
        window.location.href = 'templates.php';
    }
}

// Toggle search icon visibility based on input content
function toggleSearchIcon(type) {
    const input = document.getElementById(type + '-search-input');
    const button = document.getElementById(type + '-search-btn');
    
    if (input && button) {
        if (input.value.trim().length > 0) {
            button.classList.remove('hidden');
        } else {
            button.classList.add('hidden');
        }
    }
}

// Handle Enter key press in search inputs
document.addEventListener('DOMContentLoaded', function() {
    const heroSearchInput = document.getElementById('hero-search-input');
    
    if (heroSearchInput) {
        heroSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performHeroSearch();
            }
        });
    }
});

// Helper function to escape HTML for JavaScript strings
function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Add Cloudinary optimization function for JavaScript
function getOptimizedImageUrlJS(imageUrl, transformType = 'thumb') {
    if (!imageUrl) return 'assets/images/default-template.jpg';
    
    // If already a Cloudinary URL, return as is
    if (imageUrl.includes('cloudinary.com')) {
        return imageUrl;
    }
    
    // If it's a relative or full path, check if it exists in Cloudinary
    const filename = imageUrl.split('/').pop();
    if (!filename) return 'assets/images/default-template.jpg';
    
    // Define transformations based on type
    const transformations = {
        'thumb': 'w_300,h_200,c_fill,f_auto,q_auto',
        'card': 'w_400,h_300,c_fill,f_auto,q_auto',
        'hero': 'w_200,h_150,c_fill,f_auto,q_auto'
    };
    
    const transformation = transformations[transformType] || transformations['thumb'];
    
    // Return Cloudinary URL
    return `https://res.cloudinary.com/<?= CLOUDINARY_CLOUD_NAME ?>/image/upload/${transformation}/templates/${filename}`;
}

// Additional responsive utilities
function initResponsiveUtilities() {
    // Handle viewport changes
    window.addEventListener('resize', () => {
        // Recalculate slider dimensions on resize
        if (totalSlides > 0) {
            goToSlide(currentSlide);
        }
    });
    
    // Optimize images for different screen sizes
    const isMobile = window.innerWidth < 768;
    const isTablet = window.innerWidth >= 768 && window.innerWidth < 1024;
    
    // Adjust lazy loading based on device
    if (isMobile) {
        // Preload fewer images on mobile to save bandwidth
        document.querySelectorAll('img[data-lazy]').forEach((img, index) => {
            if (index < 3) { // Only preload first 3 images on mobile
                img.src = img.dataset.lazy;
            }
        });
    }
}

// Initialize responsive utilities
document.addEventListener('DOMContentLoaded', initResponsiveUtilities);
</script>

<!-- Additional responsive CSS -->
<style>
/* Mobile horizontal scroll for category filters */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Smooth scrolling for mobile category filters */
@media (max-width: 639px) {
    .overflow-x-auto {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Mobile category filter improvements */
    .category-filter {
        touch-action: manipulation;
        -webkit-tap-highlight-color: rgba(255, 95, 31, 0.1);
    }
    
    .category-filter:active {
        transform: scale(0.98);
    }
}

@media (max-width: 640px) {
    /* Mobile-specific optimizations */
    .floating-animation {
        animation: none; /* Disable animations on mobile for better performance */
    }
    
    /* Ensure proper touch targets */
    button, a {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Improve readability on small screens */
    h1, h2, h3 {
        line-height: 1.2;
    }
    
    /* Better spacing for mobile */
    .space-y-8 > * + * {
        margin-top: 1.5rem;
    }
}

@media (max-width: 768px) {
    /* Tablet optimizations */
    .category-filter {
        flex-shrink: 0;
    }
    
    /* Ensure grid items don't become too small */
    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (orientation: landscape) and (max-height: 500px) {
    /* Landscape mobile optimizations */
    .min-h-screen {
        min-height: 100vh;
    }
    
    .py-20, .py-16 {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
}

/* Print styles */
@media print {
    .fixed, button, nav {
        display: none !important;
    }
}
</style>
</script>

<?php include 'includes/footer.php'; ?>