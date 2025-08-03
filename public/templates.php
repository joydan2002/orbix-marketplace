<?php
/**
 * Templates Gallery Page
 * Displays all available website templates with filtering and search
 */

// Start session first - quan trọng để header hiển thị đúng auth status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/template-manager.php';

// Get database connection
try {
    $db = DatabaseConfig::getConnection();
    $templateManager = new TemplateManager($db);
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
    
    if (!empty($_GET['technology'])) {
        $filters['technology'] = is_array($_GET['technology']) ? $_GET['technology'] : [$_GET['technology']];
    }
    
    if (!empty($_GET['min_rating'])) {
        $filters['min_rating'] = floatval($_GET['min_rating']);
    }
    
    if (!empty($_GET['featured'])) {
        $filters['featured'] = true;
    }
    
    // Get templates and total count from database
    $templates = $templateManager->getTemplates($filters, $sort, $limit, $offset);
    $totalCount = $templateManager->getTotalCount($filters);
    $totalPages = ceil($totalCount / $limit);
    
    echo json_encode([
        'templates' => $templates,
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
$templates = $templateManager->getTemplates([], 'popular', 12);
$totalCount = $templateManager->getTotalCount();
$categories = $templateManager->getCategories();
$technologies = $templateManager->getTechnologyFilters();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Templates - Orbix Market</title>
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
        
        .template-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            height: 100%; /* Make all cards same height */
            display: flex;
            flex-direction: column;
        }
        
        .template-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .template-image {
            width: 100%;
            height: 200px; /* Fixed height for all images */
            object-fit: cover;
            object-position: top;
        }
        
        .template-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .template-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            min-height: 3rem; /* Reserve space for title */
        }
        
        .template-title {
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
        
        .template-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #FF5F1F;
            white-space: nowrap;
        }
        
        .template-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1rem;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            min-height: 4.2rem; /* 3 lines * 1.4 line-height */
        }
        
        .template-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            min-height: 1.5rem;
        }
        
        .template-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto; /* Push to bottom */
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
        .templates-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .template-card-list {
            display: flex;
            flex-direction: row;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            height: auto;
        }
        
        .template-card-list:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .template-card-list .template-image {
            width: 280px;
            height: 180px;
            object-fit: cover;
            object-position: top;
            flex-shrink: 0;
        }
        
        .template-card-list .template-content {
            flex: 1;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .template-card-list .template-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .template-card-list .template-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
            display: block;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .template-card-list .template-description {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .template-card-list .template-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #FF5F1F;
        }
        
        .template-card-list .template-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-top: 1rem;
        }
        
        .template-card-list .template-meta {
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
                <span class="text-secondary font-medium">Templates</span>
            </div>
        </div>
    </section>

    <!-- Page Header -->
    <section class="pb-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-bold text-secondary mb-4">Website Templates</h1>
                    <p class="text-lg text-gray-600">Discover premium website templates for every industry and purpose</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center bg-white/50 rounded-full px-4 py-2 backdrop-blur-sm">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-search-line text-gray-500"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search website templates..." class="ml-2 bg-transparent border-none outline-none text-sm w-64">
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
                            <h4 class="font-medium text-secondary mb-4">Categories</h4>
                            <div class="space-y-3">
                                <?php foreach ($categories as $category): ?>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="category" value="<?= htmlspecialchars($category['slug']) ?>" class="custom-checkbox" <?= $category['slug'] === 'all' ? 'checked' : '' ?>>
                                    <span class="text-sm text-gray-600"><?= htmlspecialchars($category['name']) ?></span>
                                    <span class="text-xs text-gray-400 ml-auto">(<?= $category['template_count'] ?>)</span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-8">
                            <h4 class="font-medium text-secondary mb-4">Price Range</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span>$0</span>
                                    <span>$500</span>
                                </div>
                                <input type="range" min="0" max="500" value="250" class="w-full price-range-slider">
                                <div class="flex items-center space-x-2">
                                    <input type="number" id="minPrice" placeholder="Min" class="w-20 px-2 py-1 border border-gray-200 rounded-button text-sm">
                                    <span class="text-gray-400">-</span>
                                    <input type="number" id="maxPrice" placeholder="Max" class="w-20 px-2 py-1 border border-gray-200 rounded-button text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Technology Stack -->
                        <div class="mb-8">
                            <h4 class="font-medium text-secondary mb-4">Technology Stack</h4>
                            <div class="space-y-3">
                                <?php foreach ($technologies as $tech): ?>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="technology" value="<?= htmlspecialchars($tech['technology']) ?>" class="custom-checkbox">
                                    <span class="text-sm text-gray-600"><?= htmlspecialchars($tech['technology']) ?></span>
                                    <span class="text-xs text-gray-400 ml-auto">(<?= $tech['count'] ?>)</span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Rating -->
                        <div class="mb-8">
                            <h4 class="font-medium text-secondary mb-4">Minimum Rating</h4>
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
                
                <!-- Templates Grid -->
                <div class="lg:col-span-3">
                    <!-- Top Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600" id="resultsCount">Showing <?= count($templates) ?> templates</span>
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
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Templates Grid -->
                    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6" id="templatesGrid">
                        <?php foreach ($templates as $template): ?>
                        <div class="template-card rounded-2xl overflow-hidden">
                            <div class="relative">
                                <img src="<?= htmlspecialchars($template['preview_image']) ?>" alt="<?= htmlspecialchars($template['title']) ?>" class="template-image">
                                <div class="absolute top-3 right-3">
                                    <button class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <i class="ri-heart-line text-gray-600"></i>
                                    </button>
                                </div>
                                <?php if ($template['is_featured']): ?>
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-button font-medium">Best Seller</span>
                                </div>
                                <?php endif; ?>
                                <div class="absolute bottom-3 left-3 flex space-x-2">
                                    <span class="px-2 py-1 bg-primary text-white text-xs rounded-button font-medium"><?= htmlspecialchars($template['technology']) ?></span>
                                </div>
                            </div>
                            <div class="template-content">
                                <div class="template-header">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <img src="<?= htmlspecialchars($template['profile_image']) ?>" alt="<?= htmlspecialchars($template['seller_name']) ?>" class="w-6 h-6 rounded-full object-cover">
                                            <span class="text-sm text-gray-600"><?= htmlspecialchars($template['seller_name']) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xl font-bold text-primary template-price">$<?= number_format($template['price'], 0) ?></span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-secondary template-title"><?= htmlspecialchars($template['title']) ?></h3>
                                    <p class="text-sm text-gray-600 template-description"><?= htmlspecialchars($template['description']) ?></p>
                                </div>
                                <div class="template-meta">
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                        <span class="text-sm text-gray-600"><?= number_format($template['avg_rating'], 1) ?> (<?= $template['review_count'] ?>)</span>
                                    </div>
                                </div>
                                <div class="template-actions">
                                    <button onclick="handleAddToCart(<?= $template['id'] ?>, '<?= addslashes($template['title']) ?>', <?= $template['price'] ?>, '<?= addslashes($template['preview_image']) ?>', '<?= addslashes($template['seller_name']) ?>')" 
                                            class="flex-1 bg-primary text-white py-2 px-4 rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                                        <i class="ri-shopping-cart-line mr-1"></i>Add to Cart
                                    </button>
                                    <button onclick="window.location.href='template-detail.php?id=<?= $template['id'] ?>'" class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
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
        let currentView = 'grid'; // Track current view mode

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            initializeFilters();
            initializeSearch();
            initializeCategoryPills();
            initializeViewToggle(); // Add view toggle initialization
            
            // Initialize pagination with current data
            const totalCount = <?= isset($totalCount) ? $totalCount : count($templates) ?>;
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
            const grid = document.getElementById('templatesGrid');
            if (grid) {
                grid.className = 'grid md:grid-cols-2 xl:grid-cols-3 gap-6';
                
                // Re-render templates in grid view
                const templates = getCurrentTemplates();
                if (templates.length > 0) {
                    updateTemplatesGrid(templates);
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
            const grid = document.getElementById('templatesGrid');
            if (grid) {
                grid.className = 'templates-list';
                
                // Re-render templates in list view
                const templates = getCurrentTemplates();
                if (templates.length > 0) {
                    updateTemplatesListView(templates);
                }
            }
        }

        // Get current templates data (for view switching)
        function getCurrentTemplates() {
            // Trigger a fresh filter application to get current data and switch view
            applyFilters();
            return [];
        }

        // Update templates grid
        function updateTemplatesGrid(templates) {
            const grid = document.getElementById('templatesGrid');
            if (!grid) return;
            
            if (templates.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-500">No templates found matching your criteria.</p></div>';
                return;
            }
            
            if (currentView === 'list') {
                updateTemplatesListView(templates);
                return;
            }
            
            grid.innerHTML = templates.map(template => `
                <div class="template-card rounded-2xl overflow-hidden">
                    <div class="relative">
                        <img src="${escapeHtml(template.preview_image)}" alt="${escapeHtml(template.title)}" class="template-image">
                        <div class="absolute top-3 right-3">
                            <button class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                        ${template.is_featured ? '<div class="absolute top-3 left-3"><span class="px-2 py-1 bg-red-500 text-white text-xs rounded-button font-medium">Best Seller</span></div>' : ''}
                        <div class="absolute bottom-3 left-3 flex space-x-2">
                            <span class="px-2 py-1 bg-primary text-white text-xs rounded-button font-medium">${escapeHtml(template.technology)}</span>
                        </div>
                    </div>
                    <div class="template-content">
                        <div class="template-header">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <img src="${escapeHtml(template.profile_image)}" alt="${escapeHtml(template.seller_name)}" class="w-6 h-6 rounded-full object-cover">
                                    <span class="text-sm text-gray-600">${escapeHtml(template.seller_name)}</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-xl font-bold text-primary template-price">$${template.price}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-secondary template-title">${escapeHtml(template.title)}</h3>
                            <p class="text-sm text-gray-600 template-description">${escapeHtml(template.description)}</p>
                        </div>
                        <div class="template-meta">
                            <div class="flex items-center space-x-1">
                                <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                <span class="text-sm text-gray-600">${template.avg_rating} (${template.review_count})</span>
                            </div>
                        </div>
                        <div class="template-actions">
                            <button onclick="handleAddToCart(${template.id}, '${addslashes(template.title)}', ${template.price}, '${addslashes(template.preview_image)}', '${addslashes(template.seller_name)}')" 
                                    class="flex-1 bg-primary text-white py-2 px-4 rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                                <i class="ri-shopping-cart-line mr-1"></i>Add to Cart
                            </button>
                            <button onclick="window.location.href='template-detail.php?id=${template.id}'" class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Update templates in list view
        function updateTemplatesListView(templates) {
            const grid = document.getElementById('templatesGrid');
            if (!grid) return;
            
            if (templates.length === 0) {
                grid.innerHTML = '<div class="text-center py-12"><p class="text-gray-500">No templates found matching your criteria.</p></div>';
                return;
            }
            
            grid.innerHTML = templates.map(template => `
                <div class="template-card-list">
                    <div class="relative">
                        <img src="${escapeHtml(template.preview_image)}" alt="${escapeHtml(template.title)}" class="template-image">
                        <div class="absolute top-3 right-3">
                            <button class="w-8 h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                        ${template.is_featured ? '<div class="absolute top-3 left-3"><span class="px-2 py-1 bg-red-500 text-white text-xs rounded-button font-medium">Best Seller</span></div>' : ''}
                        <div class="absolute bottom-3 left-3 flex space-x-2">
                            <span class="px-2 py-1 bg-primary text-white text-xs rounded-button font-medium">${escapeHtml(template.technology)}</span>
                        </div>
                    </div>
                    <div class="template-content">
                        <div class="template-header">
                            <div class="flex-1">
                                <h3 class="template-title">${escapeHtml(template.title)}</h3>
                                <div class="flex items-center space-x-2 mb-3">
                                    <img src="${escapeHtml(template.profile_image)}" alt="${escapeHtml(template.seller_name)}" class="w-6 h-6 rounded-full object-cover">
                                    <span class="text-sm text-gray-600">${escapeHtml(template.seller_name)}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="template-price mb-2">$${template.price}</div>
                                <div class="flex items-center space-x-1 justify-end">
                                    <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                    <span class="text-sm text-gray-600">${template.avg_rating} (${template.review_count})</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="template-description">${escapeHtml(template.description)}</p>
                        </div>
                        <div class="template-actions">
                            <button onclick="handleAddToCart(${template.id}, '${addslashes(template.title)}', ${template.price}, '${addslashes(template.preview_image)}', '${addslashes(template.seller_name)}')" 
                                    class="bg-primary text-white py-2 px-6 rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                                <i class="ri-shopping-cart-line mr-2"></i>Add to Cart
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

            // Technology filters
            const techInputs = document.querySelectorAll('input[name="technology"]');
            techInputs.forEach(input => {
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

        // Apply filters function
        function applyFilters() {
            if (isLoading) return;
            
            isLoading = true;
            showLoading();
            
            // Collect filter data
            const filters = collectFilters();
            
            // Make AJAX request
            fetch('templates.php?ajax=1&' + new URLSearchParams(filters))
                .then(response => response.json())
                .then(data => {
                    updateTemplatesGrid(data.templates);
                    updatePagination(data.pagination);
                    updateResultsCount(data.pagination.total_count);
                })
                .catch(error => {
                    console.error('Error loading templates:', error);
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
            
            // Technology - Multiple checkboxes
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
            
            // Scroll to top of templates grid
            const grid = document.getElementById('templatesGrid');
            if (grid) {
                grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Update results count
        function updateResultsCount(totalCount) {
            const resultsCount = document.getElementById('resultsCount');
            if (resultsCount) {
                resultsCount.textContent = `Showing ${totalCount} templates`;
            }
        }

        // Show loading state
        function showLoading() {
            const grid = document.getElementById('templatesGrid');
            if (grid) {
                grid.style.opacity = '0.5';
            }
        }

        // Hide loading state
        function hideLoading() {
            const grid = document.getElementById('templatesGrid');
            if (grid) {
                grid.style.opacity = '1';
            }
        }

        // Clear all filters
        function clearAllFilters() {
            // Reset category to "All Templates"
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
            const checkboxes = document.querySelectorAll('input[type="checkbox"], input[type="radio"]:not([name="category"])');
            checkboxes.forEach(input => {
                input.checked = false;
            });
            
            // Reset category to "all"
            const allCategoryRadio = document.querySelector('input[name="category"][value="all"]');
            if (allCategoryRadio) {
                allCategoryRadio.checked = true;
            }
            
            // Clear price inputs
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';
            
            // Reset sort to popular
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.value = 'popular';
            }
            
            // Apply filters (will show all templates)
            applyFilters();
        }

        // Utility function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Handle Add to Cart function
        function handleAddToCart(id, title, price, image, seller) {
            // Check if user is logged in (cart system is only available for logged in users)
            if (typeof cart === 'undefined') {
                // If cart is not defined, user is not logged in
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Create template data object
            const templateData = {
                id: id,
                title: title,
                price: price,
                image: image,
                seller: seller
            };
            
            // Add to cart using the global cart system
            cart.addItem(templateData);
        }
        
        // Helper function for JavaScript addslashes equivalent
        function addslashes(str) {
            return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
        }
    </script>
</body>
</html>