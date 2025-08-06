<?php
/**
 * Services Marketplace Page
 * Displays all available website-related services with filtering and search
 */

// Start session first - quan trọng để header hiển thị đúng auth status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/service-manager.php';
require_once __DIR__ . '/../config/cloudinary-config.php'; // Add Cloudinary support

// Get database connection
try {
    $db = DatabaseConfig::getConnection();
    $serviceManager = new ServiceManager($db);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Handle AJAX requests
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    // Get filter parameters
    $filters = [];
    $sort = $_GET['sort'] ?? 'popular';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    // Process filters
    if (!empty($_GET['category']) && $_GET['category'] !== 'all') {
        $filters['category'] = $_GET['category'];
    }
    
    if (!empty($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }
    
    if (!empty($_GET['min_price'])) {
        $filters['min_price'] = floatval($_GET['min_price']);
    }
    
    if (!empty($_GET['max_price'])) {
        $filters['max_price'] = floatval($_GET['max_price']);
    }
    
    if (!empty($_GET['delivery_time'])) {
        $filters['delivery_time'] = is_array($_GET['delivery_time']) ? $_GET['delivery_time'] : [$_GET['delivery_time']];
    }
    
    if (!empty($_GET['min_rating'])) {
        $filters['min_rating'] = floatval($_GET['min_rating']);
    }
    
    if (!empty($_GET['featured'])) {
        $filters['featured'] = true;
    }
    
    // Get services and total count from database
    $services = $serviceManager->getServices($filters, $sort, $limit, $offset);
    $totalCount = $serviceManager->getTotalCount($filters);
    $totalPages = ceil($totalCount / $limit);
    
    echo json_encode([
        'services' => $services,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
            'per_page' => $limit
        ]
    ]);
    exit;
}

// Get initial data for page load - only from database
$services = $serviceManager->getServices([], 'popular', 12);
$totalCount = $serviceManager->getTotalCount();
$categories = $serviceManager->getCategories();
$deliveryTimes = $serviceManager->getDeliveryTimeFilters();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Services - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }
        
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
        }
        
        .service-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .service-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            object-position: center;
        }
        
        .service-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .service-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            min-height: 3rem;
        }
        
        .service-title {
            font-weight: 600;
            color: #1f2937;
            line-height: 1.25;
            flex: 1;
            margin-right: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .service-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #FF5F1F;
            white-space: nowrap;
        }
        
        .service-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1rem;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            min-height: 4.2rem;
        }
        
        .service-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            min-height: 1.5rem;
        }
        
        .service-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
        }
        
        .filter-sidebar {
            max-height: calc(100vh - 8rem);
            overflow-y: auto;
        }
        
        .custom-checkbox {
            appearance: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid #d1d5db;
            border-radius: 0.25rem;
            background: white;
            cursor: pointer;
            position: relative;
        }
        
        .custom-checkbox:checked {
            background: #FF5F1F;
            border-color: #FF5F1F;
        }
        
        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: -2px;
            left: 1px;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .price-range-slider {
            -webkit-appearance: none;
            appearance: none;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            outline: none;
        }
        
        .price-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #FF5F1F;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .price-range-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #FF5F1F;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }
        
        /* List View Styles */
        .services-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .service-card-list {
            display: flex;
            flex-direction: row;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            height: auto;
        }
        
        .service-card-list:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .service-card-list .service-image {
            width: 280px;
            height: 180px;
            object-fit: cover;
            object-position: center;
            flex-shrink: 0;
        }
        
        .service-card-list .service-content {
            flex: 1;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .service-card-list .service-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .service-card-list .service-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
            display: block;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .service-card-list .service-description {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .service-card-list .service-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #FF5F1F;
        }
        
        .service-card-list .service-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-top: 1rem;
        }
        
        .service-card-list .service-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        /* View Toggle Button States */
        .view-btn-active {
            background: #FF5F1F !important;
            color: white !important;
        }
        
        .view-btn-inactive {
            background: rgba(255, 255, 255, 0.8) !important;
            color: #6b7280 !important;
        }
        
        .view-btn-inactive:hover {
            background: rgba(229, 231, 235, 1) !important;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1F',
                        secondary: '#1f2937'
                    },
                    borderRadius: {
                        'button': '8px'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'pacifico': ['Pacifico', 'serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter gradient-bg min-h-screen">
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Breadcrumb -->
    <section class="pt-24 pb-6">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center space-x-2 text-sm">
                <a href="../public/index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a>
                <div class="w-4 h-4 flex items-center justify-center">
                    <i class="ri-arrow-right-s-line text-gray-400"></i>
                </div>
                <span class="text-secondary font-medium">Services</span>
            </div>
        </div>
    </section>

    <!-- Page Header -->
    <section class="pb-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-bold text-secondary mb-4">Website Services</h1>
                    <p class="text-lg text-gray-600">Professional web services from expert freelancers and agencies</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-white/50 rounded-full px-4 py-2 backdrop-blur-sm">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-search-line text-gray-500"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search website services..." class="ml-2 bg-transparent border-none outline-none text-sm w-64">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Pills -->
    <section class="pb-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-wrap items-center gap-3" id="categoryPills">
                <?php foreach ($categories as $index => $category): ?>
                <button class="px-6 py-2 <?= $index === 0 ? 'bg-primary text-white' : 'bg-white/80 text-secondary hover:bg-primary hover:text-white' ?> rounded-button text-sm font-medium transition-colors" 
                        data-category="<?= htmlspecialchars($category['slug']) ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="pb-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1">
                    <div class="glass-effect rounded-2xl p-6 sticky top-32 filter-sidebar">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-semibold text-secondary">Filters</h3>
                            <button class="text-primary text-sm font-medium hover:underline" onclick="clearAllFilters()">Clear All</button>
                        </div>
                        
                        <!-- Categories -->
                        <div class="mb-8">
                            <h4 class="font-medium text-secondary mb-4">Service Categories</h4>
                            <div class="space-y-3">
                                <?php foreach ($categories as $category): ?>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="category" value="<?= htmlspecialchars($category['slug']) ?>" class="custom-checkbox" <?= $category['slug'] === 'all' ? 'checked' : '' ?>>
                                    <span class="text-sm text-gray-600"><?= htmlspecialchars($category['name']) ?></span>
                                    <span class="text-xs text-gray-400 ml-auto">(<?= $category['service_count'] ?>)</span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-8">
                            <h4 class="font-medium text-secondary mb-4">Budget Range</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>$0</span>
                                    <span>$2000</span>
                                </div>
                                <input type="range" min="0" max="2000" value="1000" class="w-full price-range-slider">
                                <div class="flex items-center space-x-2">
                                    <input type="number" id="minPrice" placeholder="Min" class="w-20 px-2 py-1 border border-gray-200 rounded-button text-sm">
                                    <span class="text-gray-400">-</span>
                                    <input type="number" id="maxPrice" placeholder="Max" class="w-20 px-2 py-1 border border-gray-200 rounded-button text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Delivery Time -->
                        <div class="mb-8">
                            <h4 class="font-medium text-secondary mb-4">Delivery Time</h4>
                            <div class="space-y-3">
                                <?php foreach ($deliveryTimes as $delivery): ?>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="delivery_time" value="<?= htmlspecialchars($delivery['delivery_time']) ?>" class="custom-checkbox">
                                    <span class="text-sm text-gray-600"><?= htmlspecialchars($delivery['delivery_time']) ?></span>
                                    <span class="text-xs text-gray-400 ml-auto">(<?= $delivery['count'] ?>)</span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Rating -->
                        <div class="mb-8">
                            <h4 class="font-medium text-secondary mb-4">Seller Rating</h4>
                            <div class="space-y-3">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="rating" value="5" class="custom-checkbox">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex">
                                            <?php for($i = 0; $i < 5; $i++): ?>
                                            <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-sm text-gray-600">(5.0)</span>
                                    </div>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="rating" value="4" class="custom-checkbox">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex">
                                            <?php for($i = 0; $i < 4; $i++): ?>
                                            <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                            <?php endfor; ?>
                                            <i class="ri-star-line text-gray-300 text-sm"></i>
                                        </div>
                                        <span class="text-sm text-gray-600">(4.0+)</span>
                                    </div>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="rating" value="3" class="custom-checkbox">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex">
                                            <?php for($i = 0; $i < 3; $i++): ?>
                                            <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                            <?php endfor; ?>
                                            <?php for($i = 0; $i < 2; $i++): ?>
                                            <i class="ri-star-line text-gray-300 text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-sm text-gray-600">(3.0+)</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Services Grid -->
                <div class="lg:col-span-3">
                    <!-- Top Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600" id="resultsCount">Showing <?= count($services) ?> services</span>
                            <div class="flex items-center space-x-2">
                                <button id="gridViewBtn" class="w-8 h-8 flex items-center justify-center bg-primary text-white rounded-button">
                                    <i class="ri-grid-line text-sm"></i>
                                </button>
                                <button id="listViewBtn" class="w-8 h-8 flex items-center justify-center bg-white/80 text-gray-600 rounded-button hover:bg-gray-100 transition-colors">
                                    <i class="ri-list-unordered text-sm"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-secondary">Sort by:</span>
                            <div class="relative">
                                <select id="sortSelect" class="px-4 py-2 bg-white/80 rounded-button text-sm font-medium hover:bg-white transition-colors border-none outline-none cursor-pointer">
                                    <option value="popular">Most Popular</option>
                                    <option value="newest">Newest First</option>
                                    <option value="price-low">Price: Low to High</option>
                                    <option value="price-high">Price: High to Low</option>
                                    <option value="rating">Highest Rated</option>
                                    <option value="delivery">Fastest Delivery</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Services Grid -->
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6" id="servicesGrid">
                        <?php foreach ($services as $service): ?>
                        <div class="service-card rounded-2xl overflow-hidden">
                            <div class="relative">
                                <img src="<?= getOptimizedImageUrl($service['preview_image'], 'thumb') ?>" 
                                     alt="<?= htmlspecialchars($service['title']) ?>" 
                                     class="service-image"
                                     loading="lazy"
                                     onerror="this.src='../assets/images/default-service.jpg'">
                                <div class="absolute top-3 right-3">
                                    <button class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <i class="ri-heart-line text-gray-600"></i>
                                    </button>
                                </div>
                                <?php if ($service['is_featured']): ?>
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-button font-medium">Featured</span>
                                </div>
                                <?php endif; ?>
                                <div class="absolute bottom-3 left-3 flex space-x-2">
                                    <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-button font-medium"><?= htmlspecialchars($service['delivery_time']) ?></span>
                                </div>
                            </div>
                            <div class="service-content">
                                <div class="service-header">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <img src="<?= getOptimizedImageUrl($service['profile_image'], 'avatar_small') ?>" 
                                                 alt="<?= htmlspecialchars($service['seller_name']) ?>" 
                                                 class="w-6 h-6 rounded-full object-cover"
                                                 onerror="this.src='../assets/images/default-avatar.png'">
                                            <span class="text-sm text-gray-600"><?= htmlspecialchars($service['seller_name']) ?></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-gray-500">Starting at</div>
                                        <span class="text-xl font-bold text-primary service-price">$<?= number_format($service['price'], 0) ?></span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-secondary service-title"><?= htmlspecialchars($service['title']) ?></h3>
                                    <p class="text-sm text-gray-600 service-description"><?= htmlspecialchars($service['description']) ?></p>
                                </div>
                                <div class="service-meta">
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <span class="text-sm text-gray-600"><?= number_format($service['avg_rating'], 1) ?> (<?= $service['review_count'] ?>)</span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <i class="ri-shopping-bag-line mr-1"></i><?= $service['orders_count'] ?> orders
                                    </div>
                                </div>
                                <div class="service-actions">
                                    <button onclick="handleOrderService(<?= $service['id'] ?>, '<?= addslashes($service['title']) ?>', <?= $service['price'] ?>, '<?= addslashes(getOptimizedImageUrl($service['preview_image'], 'thumb')) ?>', '<?= addslashes($service['seller_name']) ?>')" 
                                            class="flex-1 bg-primary text-white py-2 px-4 rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                                        <i class="ri-shopping-cart-line mr-1"></i>Order Now
                                    </button>
                                    <button onclick="window.location.href='service-detail.php?id=<?= $service['id'] ?>'" class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="flex items-center justify-center space-x-2 mt-12" id="paginationContainer">
                        <!-- Pagination will be dynamically updated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Global variables
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;
        let currentView = 'grid';

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            initializeFilters();
            initializeSearch();
            initializeCategoryPills();
            initializeViewToggle();
            
            // Initialize pagination with current data
            const totalCount = <?= isset($totalCount) ? $totalCount : count($services) ?>;
            const totalPages = Math.ceil(totalCount / 12);
            updatePagination({
                current_page: 1,
                total_pages: totalPages,
                total_count: totalCount,
                per_page: 12
            });
        });

        // Initialize view toggle functionality
        function initializeViewToggle() {
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            
            if (gridViewBtn && listViewBtn) {
                gridViewBtn.addEventListener('click', function() {
                    if (currentView !== 'grid') {
                        switchToGridView();
                    }
                });
                
                listViewBtn.addEventListener('click', function() {
                    if (currentView !== 'list') {
                        switchToListView();
                    }
                });
            }
        }

        // Switch to grid view
        function switchToGridView() {
            currentView = 'grid';
            
            // Update button states
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            
            gridViewBtn.classList.remove('view-btn-inactive');
            gridViewBtn.classList.add('view-btn-active');
            
            listViewBtn.classList.remove('view-btn-active');
            listViewBtn.classList.add('view-btn-inactive');
            
            // Update grid container classes
            const grid = document.getElementById('servicesGrid');
            if (grid) {
                grid.className = 'grid md:grid-cols-2 xl:grid-cols-3 gap-6';
                
                // Re-render services in grid view
                const services = getCurrentServices();
                if (services.length > 0) {
                    updateServicesGrid(services);
                }
            }
        }

        // Switch to list view
        function switchToListView() {
            currentView = 'list';
            
            // Update button states
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            
            gridViewBtn.classList.remove('view-btn-active');
            gridViewBtn.classList.add('view-btn-inactive');
            
            listViewBtn.classList.remove('view-btn-inactive');
            listViewBtn.classList.add('view-btn-active');
            
            // Update grid container classes
            const grid = document.getElementById('servicesGrid');
            if (grid) {
                grid.className = 'services-list';
                
                // Re-render services in list view
                const services = getCurrentServices();
                if (services.length > 0) {
                    updateServicesListView(services);
                }
            }
        }

        // Get current services data (for view switching)
        function getCurrentServices() {
            // Trigger a fresh filter application to get current data and switch view
            applyFilters();
            return [];
        }

        // Update services grid
        function updateServicesGrid(services) {
            const grid = document.getElementById('servicesGrid');
            if (!grid) return;
            
            if (services.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-500">No services found matching your criteria.</p></div>';
                return;
            }
            
            if (currentView === 'list') {
                updateServicesListView(services);
                return;
            }
            
            grid.innerHTML = services.map(service => `
                <div class="service-card rounded-2xl overflow-hidden">
                    <div class="relative">
                        <img src="${getOptimizedImageUrlJS(service.preview_image, 'thumb')}" 
                             alt="${escapeHtml(service.title)}" 
                             class="service-image"
                             loading="lazy"
                             onerror="this.src='../assets/images/default-service.jpg'">
                        <div class="absolute top-3 right-3">
                            <button class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                        ${service.is_featured ? '<div class="absolute top-3 left-3"><span class="px-2 py-1 bg-red-500 text-white text-xs rounded-button font-medium">Featured</span></div>' : ''}
                        <div class="absolute bottom-3 left-3 flex space-x-2">
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-button font-medium">${escapeHtml(service.delivery_time)}</span>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="service-header">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <img src="${getOptimizedImageUrlJS(service.profile_image, 'avatar_small')}" 
                                         alt="${escapeHtml(service.seller_name)}" 
                                         class="w-6 h-6 rounded-full object-cover"
                                         onerror="this.src='../assets/images/default-avatar.png'">
                                    <span class="text-sm text-gray-600">${escapeHtml(service.seller_name)}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">Starting at</div>
                                <span class="text-xl font-bold text-primary service-price">$${service.price}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-secondary service-title">${escapeHtml(service.title)}</h3>
                            <p class="text-sm text-gray-600 service-description">${escapeHtml(service.description)}</p>
                        </div>
                        <div class="service-meta">
                            <div class="flex items-center space-x-1">
                                <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                <span class="text-sm text-gray-600">${service.avg_rating} (${service.review_count})</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="ri-shopping-bag-line mr-1"></i>${service.orders_count} orders
                            </div>
                        </div>
                        <div class="service-actions">
                            <button onclick="handleOrderService(${service.id}, '${addslashes(service.title)}', ${service.price}, '${addslashes(getOptimizedImageUrlJS(service.preview_image, 'thumb'))}', '${addslashes(service.seller_name)}')" 
                                    class="flex-1 bg-primary text-white py-2 px-4 rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                                <i class="ri-shopping-cart-line mr-1"></i>Order Now
                            </button>
                            <button class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Update services in list view
        function updateServicesListView(services) {
            const grid = document.getElementById('servicesGrid');
            if (!grid) return;
            
            if (services.length === 0) {
                grid.innerHTML = '<div class="text-center py-12"><p class="text-gray-500">No services found matching your criteria.</p></div>';
                return;
            }
            
            grid.innerHTML = services.map(service => `
                <div class="service-card-list">
                    <div class="relative">
                        <img src="${getOptimizedImageUrlJS(service.preview_image, 'thumb')}" 
                             alt="${escapeHtml(service.title)}" 
                             class="service-image"
                             loading="lazy"
                             onerror="this.src='../assets/images/default-service.jpg'">
                        <div class="absolute top-3 right-3">
                            <button class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                        ${service.is_featured ? '<div class="absolute top-3 left-3"><span class="px-2 py-1 bg-red-500 text-white text-xs rounded-button font-medium">Featured</span></div>' : ''}
                        <div class="absolute bottom-3 left-3 flex space-x-2">
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-button font-medium">${escapeHtml(service.delivery_time)}</span>
                        </div>
                    </div>
                    <div class="service-content">
                        <div class="service-header">
                            <div class="flex-1">
                                <h3 class="service-title">${escapeHtml(service.title)}</h3>
                                <div class="flex items-center space-x-2 mb-3">
                                    <img src="${getOptimizedImageUrlJS(service.profile_image, 'avatar_small')}" 
                                         alt="${escapeHtml(service.seller_name)}" 
                                         class="w-6 h-6 rounded-full object-cover"
                                         onerror="this.src='../assets/images/default-avatar.png'">
                                    <span class="text-sm text-gray-600">${escapeHtml(service.seller_name)}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500 mb-1">Starting at</div>
                                <div class="service-price mb-2">$${service.price}</div>
                                <div class="flex items-center space-x-1 justify-end mb-2">
                                    <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                    <span class="text-sm text-gray-600">${service.avg_rating} (${service.review_count})</span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="ri-shopping-bag-line mr-1"></i>${service.orders_count} orders
                                </div>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="service-description">${escapeHtml(service.description)}</p>
                        </div>
                        <div class="service-actions">
                            <button onclick="handleOrderService(${service.id}, '${addslashes(service.title)}', ${service.price}, '${addslashes(getOptimizedImageUrlJS(service.preview_image, 'thumb'))}', '${addslashes(service.seller_name)}')" 
                                    class="bg-primary text-white py-2 px-6 rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                                <i class="ri-shopping-cart-line mr-2"></i>Order Now
                            </button>
                            <button class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
                                <i class="ri-eye-line mr-2"></i>Preview
                            </button>
                            <button class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
                                <i class="ri-information-line mr-2"></i>Details
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Initialize category pills
        function initializeCategoryPills() {
            const categoryButtons = document.querySelectorAll('#categoryPills button');
            
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Update active state for pills
                    categoryButtons.forEach(btn => {
                        btn.classList.remove('bg-primary', 'text-white');
                        btn.classList.add('bg-white/80', 'text-secondary');
                    });
                    
                    this.classList.remove('bg-white/80', 'text-secondary');
                    this.classList.add('bg-primary', 'text-white');
                    
                    // Sync with sidebar radio buttons
                    const category = this.getAttribute('data-category');
                    const correspondingRadio = document.querySelector(`input[name="category"][value="${category}"]`);
                    if (correspondingRadio) {
                        correspondingRadio.checked = true;
                    }
                    
                    // Reset to page 1 when category changes 
                    currentPage = 1;
                    
                    // Apply filter
                    applyFilters();
                });
            });
        }

        // Sync category pills with radio buttons
        function syncCategoryPills(selectedCategory) {
            const categoryButtons = document.querySelectorAll('#categoryPills button');
            
            categoryButtons.forEach(btn => {
                const category = btn.getAttribute('data-category');
                if (category === selectedCategory) {
                    btn.classList.remove('bg-white/80', 'text-secondary');
                    btn.classList.add('bg-primary', 'text-white');
                } else {
                    btn.classList.remove('bg-primary', 'text-white');
                    btn.classList.add('bg-white/80', 'text-secondary');
                }
            });
        }

        // Initialize search
        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });
        }

        // Initialize filters
        function initializeFilters() {
            // Category filters - sync with pills
            const categoryInputs = document.querySelectorAll('input[name="category"]');
            categoryInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Sync category pills with radio button selection
                    syncCategoryPills(this.value);
                    
                    // Reset to page 1 when category changes
                    currentPage = 1;
                    
                    applyFilters();
                });
            });

            // Delivery time filters
            const deliveryInputs = document.querySelectorAll('input[name="delivery_time"]');
            deliveryInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Reset to page 1 when filters change
                    currentPage = 1;
                    applyFilters();
                });
            });

            // Rating filters
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Reset to page 1 when filters change
                    currentPage = 1;
                    applyFilters();
                });
            });

            // Price inputs with debounce
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            
            let priceTimeout;
            const handlePriceChange = () => {
                clearTimeout(priceTimeout);
                priceTimeout = setTimeout(() => {
                    currentPage = 1;
                    applyFilters();
                }, 800); // Debounce price changes
            };
            
            if (minPrice) {
                minPrice.addEventListener('input', handlePriceChange);
                minPrice.addEventListener('change', handlePriceChange);
            }
            if (maxPrice) {
                maxPrice.addEventListener('input', handlePriceChange);
                maxPrice.addEventListener('change', handlePriceChange);
            }

            // Sort dropdown
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    // Don't reset page for sort changes, just re-sort current results
                    applyFilters();
                });
            }

            // Price range slider
            const priceSlider = document.querySelector('.price-range-slider');
            if (priceSlider) {
                priceSlider.addEventListener('input', function() {
                    const maxPrice = document.getElementById('maxPrice');
                    if (maxPrice) {
                        maxPrice.value = this.value;
                        handlePriceChange();
                    }
                });
            }
        }

        // Apply filters function - update to use services endpoint
        function applyFilters() {
            if (isLoading) return;
            
            isLoading = true;
            showLoading();
            
            // Collect filter data
            const filters = collectFilters();
            
            // Make AJAX request to services endpoint
            fetch('services.php?ajax=1&' + new URLSearchParams(filters))
                .then(response => response.json())
                .then(data => {
                    updateServicesGrid(data.services);
                    updatePagination(data.pagination);
                    updateResultsCount(data.pagination.total_count);
                })
                .catch(error => {
                    console.error('Error loading services:', error);
                })
                .finally(() => {
                    isLoading = false;
                    hideLoading();
                });
        }

        // Collect filter data
        function collectFilters() {
            const filters = {};
            
            // Search
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value.trim()) {
                filters.search = searchInput.value.trim();
            }
            
            // Category - Get from both category pills and sidebar radio buttons
            let selectedCategory = null;
            
            // First check category pills
            const activeCategory = document.querySelector('#categoryPills button.bg-primary');
            if (activeCategory) {
                selectedCategory = activeCategory.getAttribute('data-category');
            }
            
            // Then check sidebar radio buttons (they override pills)
            const checkedCategoryRadio = document.querySelector('input[name="category"]:checked');
            if (checkedCategoryRadio) {
                selectedCategory = checkedCategoryRadio.value;
            }
            
            if (selectedCategory && selectedCategory !== 'all') {
                filters.category = selectedCategory;
            }
            
            // Delivery time - Multiple checkboxes
            const checkedDelivery = document.querySelectorAll('input[name="delivery_time"]:checked');
            if (checkedDelivery.length > 0) {
                filters.delivery_time = Array.from(checkedDelivery).map(input => input.value);
            }
            
            // Technology - Multiple checkboxes (if available)
            const checkedTech = document.querySelectorAll('input[name="technology"]:checked');
            if (checkedTech.length > 0) {
                filters.technology = Array.from(checkedTech).map(input => input.value);
            }
            
            // Price range
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            if (minPrice && minPrice.value && !isNaN(minPrice.value)) {
                filters.min_price = parseFloat(minPrice.value);
            }
            if (maxPrice && maxPrice.value && !isNaN(maxPrice.value)) {
                filters.max_price = parseFloat(maxPrice.value);
            }
            
            // Rating
            const checkedRating = document.querySelector('input[name="rating"]:checked');
            if (checkedRating) {
                filters.min_rating = parseFloat(checkedRating.value);
            }
            
            // Sort
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                filters.sort = sortSelect.value;
            }
            
            // Page
            filters.page = currentPage;
            
            console.log('Collected filters:', filters); // Debug log
            return filters;
        }

        // Update pagination
        function updatePagination(pagination) {
            currentPage = pagination.current_page;
            totalPages = pagination.total_pages;
            
            const container = document.getElementById('paginationContainer');
            if (!container || totalPages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let paginationHTML = '';
            
            // Previous button
            if (currentPage > 1) {
                paginationHTML += `
                    <button onclick="changePage(${currentPage - 1})" class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors">
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                `;
            }
            
            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                paginationHTML += `
                    <button onclick="changePage(1)" class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors">1</button>
                `;
                if (startPage > 2) {
                    paginationHTML += '<span class="px-2 text-gray-400">...</span>';
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    paginationHTML += `
                        <button class="w-10 h-10 flex items-center justify-center bg-primary text-white rounded-button text-sm font-medium">${i}</button>
                    `;
                } else {
                    paginationHTML += `
                        <button onclick="changePage(${i})" class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors">${i}</button>
                    `;
                }
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHTML += '<span class="px-2 text-gray-400">...</span>';
                }
                paginationHTML += `
                    <button onclick="changePage(${totalPages})" class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors">${totalPages}</button>
                `;
            }
            
            // Next button
            if (currentPage < totalPages) {
                paginationHTML += `
                    <button onclick="changePage(${currentPage + 1})" class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors">
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                `;
            }
            
            container.innerHTML = paginationHTML;
        }
        
        // Change page function
        function changePage(page) {
            if (page === currentPage || isLoading) return;
            
            currentPage = page;
            applyFilters();
            
            // Scroll to top of services grid
            const grid = document.getElementById('servicesGrid');
            if (grid) {
                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Update results count for services
        function updateResultsCount(totalCount) {
            const resultsCount = document.getElementById('resultsCount');
            if (resultsCount) {
                resultsCount.textContent = `Showing ${totalCount} services`;
            }
        }

        // Show loading state
        function showLoading() {
            const grid = document.getElementById('servicesGrid');
            if (grid) {
                grid.style.opacity = '0.5';
            }
        }

        // Hide loading state
        function hideLoading() {
            const grid = document.getElementById('servicesGrid');
            if (grid) {
                grid.style.opacity = '1';
            }
        }

        // Handle Order Service function
        function handleOrderService(id, title, price, image, seller) {
            // Check if user is logged in
            if (typeof cart === 'undefined') {
                // If cart is not defined, user is not logged in
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Create service data object
            const serviceData = {
                id: id,
                title: title,
                price: price,
                image: image,
                seller: seller,
                type: 'service' // Distinguish from templates
            };
            
            // Add to cart using the global cart system
            cart.addItem(serviceData);
        }
        
        // Helper function for JavaScript addslashes equivalent
        function addslashes(str) {
            return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text ? text.toString().replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
        }
        
        // Clear all filters function
        function clearAllFilters() {
            // Reset category to "All Services"
            const allCategoryBtn = document.querySelector('#categoryPills button[data-category="all"]');
            if (allCategoryBtn) {
                allCategoryBtn.click();
            }
            
            // Clear search
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Reset all checkboxes and radios
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(input => {
                input.checked = false;
            });
            
            // Reset category to "all"
            const allCategoryRadio = document.querySelector('input[name="category"][value="all"]');
            if (allCategoryRadio) {
                allCategoryRadio.checked = true;
            }
            
            // Reset rating radio buttons
            const ratingRadios = document.querySelectorAll('input[name="rating"]');
            ratingRadios.forEach(input => {
                input.checked = false;
            });
            
            // Clear price inputs
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';
            
            // Reset price slider
            const priceSlider = document.querySelector('.price-range-slider');
            if (priceSlider) {
                priceSlider.value = 1000;
            }
            
            // Reset sort to popular
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.value = 'popular';
            }
            
            // Reset to page 1 and apply filters
            currentPage = 1;
            applyFilters();
        }

        // JavaScript function to generate optimized image URLs (equivalent to PHP function)
        function getOptimizedImageUrlJS(publicId, size = 'thumb') {
            if (!publicId) {
                // Return default image based on size
                const defaults = {
                    'thumb': '../assets/images/default-service.jpg',
                    'medium': '../assets/images/default-service.jpg', 
                    'avatar_small': '../assets/images/default-avatar.png',
                    'avatar_medium': '../assets/images/default-avatar.png',
                    'avatar_large': '../assets/images/default-avatar.png'
                };
                return defaults[size] || '../assets/images/default-service.jpg';
            }
            
            // If it's already a full URL, return as is
            if (publicId.startsWith('http')) {
                return publicId;
            }
            
            const cloudName = 'dpmwj7f9j';
            
            // Handle Cloudinary public IDs - add orbix/products/ prefix if needed
            let processedPublicId = publicId;
            if (!processedPublicId.includes('orbix/') && processedPublicId.startsWith('orbix_')) {
                processedPublicId = 'orbix/products/' + processedPublicId;
            }
            
            const transformations = {
                'thumb': 'w_300,h_200,c_fill,f_auto,q_auto',
                'medium': 'w_800,h_600,c_fill,f_auto,q_auto',
                'avatar_small': 'w_50,h_50,c_fill,f_auto,q_auto,r_max',
                'avatar_medium': 'w_100,h_100,c_fill,f_auto,q_auto,r_max',
                'avatar_large': 'w_200,h_200,c_fill,f_auto,q_auto,r_max'
            };
            
            const transformation = transformations[size] || transformations['thumb'];
            return `https://res.cloudinary.com/${cloudName}/image/upload/${transformation}/${processedPublicId}`;
        }
    </script>
</body>
</html>