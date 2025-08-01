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

<!-- Filters Section -->
<section class="py-8 bg-white/50 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Categories -->
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-secondary">Categories:</span>
                <div class="flex items-center space-x-2" id="category-filters">
                    <button class="px-4 py-2 bg-primary text-white !rounded-button text-sm font-medium whitespace-nowrap" data-category="">All Templates</button>
                    <?php foreach ($categories as $category): ?>
                    <button class="px-4 py-2 bg-white/80 text-secondary !rounded-button text-sm font-medium hover:bg-primary hover:text-white transition-colors whitespace-nowrap" data-category="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Sort -->
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-secondary">Sort by:</span>
                <div class="relative">
                    <button class="flex items-center space-x-2 px-4 py-2 bg-white/80 !rounded-button text-sm font-medium hover:bg-white transition-colors whitespace-nowrap">
                        <span>Popular</span>
                        <div class="w-4 h-4 flex items-center justify-center">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Templates Grid -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:col-span-1">
                <div class="glass-effect rounded-2xl p-6 sticky top-32">
                    <h3 class="font-semibold text-secondary mb-6">Filters</h3>
                    
                    <!-- Price Range -->
                    <div class="mb-6">
                        <h4 class="font-medium text-secondary mb-3">Price Range</h4>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">Free</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">$1 - $25</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">$26 - $50</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">$51 - $100</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Technology -->
                    <div class="mb-6">
                        <h4 class="font-medium text-secondary mb-3">Technology</h4>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">HTML/CSS</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">React</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">Vue.js</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">Figma</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">WordPress</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Rating -->
                    <div>
                        <h4 class="font-medium text-secondary mb-3">Rating</h4>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <div class="flex items-center space-x-1">
                                    <div class="flex">
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                    </div>
                                    <span class="text-sm text-gray-600">(5.0)</span>
                                </div>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300">
                                <div class="flex items-center space-x-1">
                                    <div class="flex">
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <i class="ri-star-line text-gray-300 text-sm"></i>
                                    </div>
                                    <span class="text-sm text-gray-600">(4.0+)</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Templates Grid -->
            <div class="lg:col-span-3">
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6" id="templates-grid">
                    <!-- Templates will be loaded here via JavaScript -->
                    <div class="col-span-full text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        <p class="mt-2 text-gray-600">Loading templates...</p>
                    </div>
                </div>
                
                <!-- Load More Button - Positioned within templates column -->
                <div class="text-center mt-12" style="width: 100%; display: flex; justify-content: center;">
                    <button id="load-more-btn" class="bg-white border border-gray-200 text-secondary px-8 py-3 !rounded-button font-medium hover:bg-gray-50 transition-colors whitespace-nowrap" style="display: none;">
                        Load More Templates
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/service-cards.php'; ?>

<!-- CTA Section -->
<section class="py-20 relative overflow-hidden" style="background-image: url('https://readdy.ai/api/search-image?query=abstract%20technology%20background%20futuristic%20design%20neon%20orange%20accents%20geometric%20patterns%20modern%20digital%20art%20clean%20professional&width=1920&height=600&seq=cta1&orientation=landscape'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-secondary/80"></div>
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">Ready to Start Selling?</h2>
        <p class="text-xl text-gray-200 mb-8">Join thousands of designers earning passive income by selling their templates on Orbix Market</p>
        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="#" class="bg-primary text-white px-8 py-4 !rounded-button text-lg font-medium hover:bg-primary/90 transition-colors neon-glow whitespace-nowrap">Become a Seller</a>
            <button class="bg-white/10 text-white px-8 py-4 !rounded-button text-lg font-medium hover:bg-white/20 transition-colors backdrop-blur-sm whitespace-nowrap">Learn More</button>
        </div>
    </div>
</section>

<!-- JavaScript for Dynamic Content Loading -->
<script>
// Global variables
let currentCategory = '';
let currentOffset = 0;
const templatesPerPage = 6;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadHeroTemplates();
    loadMainTemplates();
    setupCategoryFilters();
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

// Load main templates grid
async function loadMainTemplates(reset = true) {
    if (reset) {
        currentOffset = 0;
        document.getElementById('templates-grid').innerHTML = `
            <div class="col-span-full text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-gray-600">Loading templates...</p>
            </div>
        `;
    }
    
    try {
        const url = `api.php?action=templates&limit=${templatesPerPage}&offset=${currentOffset}${currentCategory ? '&category=' + currentCategory : ''}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            if (reset) {
                renderTemplates(result.data);
            } else {
                appendTemplates(result.data);
            }
            
            // Show/hide load more button
            const loadMoreBtn = document.getElementById('load-more-btn');
            if (result.data.length === templatesPerPage) {
                loadMoreBtn.style.display = 'block';
                currentOffset += templatesPerPage;
            } else {
                loadMoreBtn.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error loading templates:', error);
        document.getElementById('templates-grid').innerHTML = `
            <div class="col-span-full text-center py-8">
                <p class="text-red-600">Error loading templates. Please try again later.</p>
            </div>
        `;
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
                <div class="template-card rounded-xl p-4 hover-scale">
                    <img src="${template.preview_image}" alt="${template.title}" class="w-full h-24 object-cover rounded-lg">
                    <div class="mt-2">
                        <h4 class="font-semibold text-sm text-secondary">${template.title}</h4>
                        <p class="text-xs text-gray-500">$${template.price}</p>
                    </div>
                </div>
            `).join('')}
        </div>
        <div class="space-y-4 mt-8">
            ${rightColumn.map(template => `
                <div class="template-card rounded-xl p-4 hover-scale">
                    <img src="${template.preview_image}" alt="${template.title}" class="w-full h-24 object-cover rounded-lg">
                    <div class="mt-2">
                        <h4 class="font-semibold text-sm text-secondary">${template.title}</h4>
                        <p class="text-xs text-gray-500">$${template.price}</p>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Render templates in main grid
function renderTemplates(templates) {
    const container = document.getElementById('templates-grid');
    if (!templates.length) {
        container.innerHTML = `
            <div class="col-span-full text-center py-8">
                <p class="text-gray-600">No templates found.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = templates.map(template => createTemplateCard(template)).join('');
}

// Append templates to existing grid
function appendTemplates(templates) {
    const container = document.getElementById('templates-grid');
    container.insertAdjacentHTML('beforeend', templates.map(template => createTemplateCard(template)).join(''));
}

// Create template card HTML
function createTemplateCard(template) {
    const techColors = {
        'React': 'bg-primary',
        'Vue.js': 'bg-green-500',
        'HTML': 'bg-blue-500',
        'Figma': 'bg-purple-500',
        'WordPress': 'bg-orange-500'
    };
    
    const stars = Math.floor(template.rating);
    const starHtml = Array.from({length: 5}, (_, i) => 
        i < stars ? '<i class="ri-star-fill text-yellow-400 text-sm"></i>' : '<i class="ri-star-line text-gray-300 text-sm"></i>'
    ).join('');
    
    // Truncate description to ensure consistent height
    const maxDescLength = 80;
    const truncatedDesc = template.description.length > maxDescLength 
        ? template.description.substring(0, maxDescLength) + '...'
        : template.description;
    
    return `
        <div class="template-card rounded-2xl overflow-hidden h-full flex flex-col">
            <div class="relative">
                <img src="${template.preview_image}" alt="${template.title}" class="w-full h-48 object-cover object-top">
                <div class="absolute top-3 right-3">
                    <button class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                        <i class="ri-heart-line text-gray-600"></i>
                    </button>
                </div>
                <div class="absolute bottom-3 left-3">
                    <span class="px-2 py-1 ${techColors[template.technology] || 'bg-gray-500'} text-white text-xs rounded-button font-medium">${template.technology}</span>
                </div>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <!-- Title and Price - Fixed height -->
                <div class="flex items-start justify-between mb-3 min-h-[3rem]">
                    <h3 class="font-semibold text-secondary leading-tight pr-2 flex-1">${template.title}</h3>
                    <span class="text-xl font-bold text-primary whitespace-nowrap">$${template.price}</span>
                </div>
                
                <!-- Description - Fixed height -->
                <div class="mb-4 min-h-[3rem] flex items-start">
                    <p class="text-sm text-gray-600 leading-relaxed">${truncatedDesc}</p>
                </div>
                
                <!-- Spacer to push bottom content down -->
                <div class="flex-1"></div>
                
                <!-- Author and Rating - Fixed position from bottom -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <img src="${template.profile_image}" alt="${template.seller_name}" class="w-6 h-6 rounded-full object-cover">
                        <span class="text-sm text-gray-600 truncate max-w-[8rem]">${template.seller_name}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="flex">${starHtml}</div>
                        <span class="text-sm text-gray-600 whitespace-nowrap">${template.rating} (${template.reviews_count})</span>
                    </div>
                </div>
                
                <!-- Buttons - Fixed position at bottom -->
                <div class="flex space-x-2">
                    <button class="flex-1 bg-primary text-white py-2 px-4 !rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Add to Cart</button>
                    <button class="px-4 py-2 border border-gray-200 !rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">Preview</button>
                </div>
            </div>
        </div>
    `;
}

// Setup category filters
function setupCategoryFilters() {
    const filterButtons = document.querySelectorAll('#category-filters button');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active state
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-white/80', 'text-secondary');
            });
            this.classList.remove('bg-white/80', 'text-secondary');
            this.classList.add('bg-primary', 'text-white');
            
            // Update current category and reload
            currentCategory = this.dataset.category;
            loadMainTemplates(true);
        });
    });
    
    // Load more button
    document.getElementById('load-more-btn').addEventListener('click', function() {
        loadMainTemplates(false);
    });
}
</script>

<?php include '../includes/footer.php'; ?>