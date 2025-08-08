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
require_once __DIR__ . '/../config/cloudinary-config.php'; // Add Cloudinary support

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
    <link href="assets/css/template.css" rel="stylesheet">
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
    <section class="pb-6 sm:pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sm:gap-6">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-secondary mb-2 sm:mb-4 hero-description">Website Templates</h1>
                    <p class="text-base sm:text-lg text-gray-600">Discover premium website templates for every industry and purpose</p>
                </div>
                <div class="flex items-center header-actions space-x-2 sm:space-x-4">
                    <!-- Mobile Filter Button (positioned next to search) -->
                    <button id="mobileFilterDropdownToggle" class="lg:hidden flex items-center justify-center w-10 h-10 bg-white/50 rounded-full backdrop-blur-sm border border-gray-200 hover:bg-white/70 transition-colors">
                        <i class="ri-equalizer-line text-gray-600"></i>
                    </button>
                    
                    <div class="flex items-center bg-white/50 rounded-full px-3 sm:px-4 py-2 backdrop-blur-sm search-container">
                        <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                            <i class="ri-search-line text-gray-500"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search templates..." class="ml-2 bg-transparent border-none outline-none text-sm w-32 sm:w-48 lg:w-64">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Filter Dropdown -->
    <div id="mobileFilterDropdown" class="lg:hidden hidden fixed top-0 left-0 right-0 z-50 bg-white shadow-lg border-b border-gray-200 max-h-[80vh] overflow-y-auto">
        <div class="p-4">
            <!-- Filter Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-secondary">Filters</h3>
                <div class="flex items-center space-x-3">
                    <button onclick="clearAllFilters()" class="text-primary text-sm font-medium hover:underline">Clear All</button>
                    <button id="closeMobileFilterDropdown" class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Filter Content -->
            <div class="space-y-4">
                <!-- Categories -->
                <div>
                    <h4 class="font-medium text-secondary mb-3 text-sm">Template Categories</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <?php foreach ($categories as $category): ?>
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="radio" name="mobile_category" value="<?= htmlspecialchars($category['slug']) ?>" class="custom-checkbox text-primary" <?= $category['slug'] === 'all' ? 'checked' : '' ?>>
                            <span class="text-sm text-gray-600"><?= htmlspecialchars($category['name']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range -->
                <div>
                    <h4 class="font-medium text-secondary mb-3 text-sm">Price Range</h4>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <input type="number" id="mobileminPrice" placeholder="Min" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <span class="text-gray-400">-</span>
                        <div class="flex-1">
                            <input type="number" id="mobilemaxPrice" placeholder="Max" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <!-- Technology Stack -->
                <div>
                    <h4 class="font-medium text-secondary mb-3 text-sm">Technology Stack</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <?php foreach ($technologies as $tech): ?>
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" name="mobile_technology" value="<?= htmlspecialchars($tech['technology']) ?>" class="custom-checkbox text-primary">
                            <span class="text-sm text-gray-600"><?= htmlspecialchars($tech['technology']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Complexity -->
                <div>
                    <h4 class="font-medium text-secondary mb-3 text-sm">Complexity</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" name="mobile_complexity" value="basic" class="custom-checkbox text-primary">
                            <span class="text-sm text-gray-600">Basic</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" name="mobile_complexity" value="intermediate" class="custom-checkbox text-primary">
                            <span class="text-sm text-gray-600">Intermediate</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" name="mobile_complexity" value="advanced" class="custom-checkbox text-primary">
                            <span class="text-sm text-gray-600">Advanced</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" name="mobile_complexity" value="expert" class="custom-checkbox text-primary">
                            <span class="text-sm text-gray-600">Expert</span>
                        </label>
                    </div>
                </div>

                <!-- Rating -->
                <div>
                    <h4 class="font-medium text-secondary mb-3 text-sm">Minimum Rating</h4>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="radio" name="mobile_rating" value="4" class="custom-checkbox text-primary">
                            <div class="flex items-center space-x-1">
                                <?php for($i = 0; $i < 4; $i++): ?>
                                <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                <?php endfor; ?>
                                <i class="ri-star-line text-gray-300 text-sm"></i>
                                <span class="text-sm text-gray-600 ml-1">(4.0+)</span>
                            </div>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                            <input type="radio" name="mobile_rating" value="3" class="custom-checkbox text-primary">
                            <div class="flex items-center space-x-1">
                                <?php for($i = 0; $i < 3; $i++): ?>
                                <i class="ri-star-fill text-yellow-400 text-sm"></i>
                                <?php endfor; ?>
                                <?php for($i = 0; $i < 2; $i++): ?>
                                <i class="ri-star-line text-gray-300 text-sm"></i>
                                <?php endfor; ?>
                                <span class="text-sm text-gray-600 ml-1">(3.0+)</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Apply Filters Button -->
                <div class="pt-4 border-t border-gray-200">
                    <button id="applyMobileFilters" class="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Pills -->
    <section class="pb-6 sm:pb-8 hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto scrollbar-hide pb-2" id="categoryPills">
                <?php foreach ($categories as $index => $category): ?>
                <button class="px-4 sm:px-6 py-2 <?= $index === 0 ? 'bg-primary text-white' : 'bg-white/80 text-secondary hover:bg-primary hover:text-white' ?> rounded-button text-xs sm:text-sm font-medium transition-colors flex-shrink-0 whitespace-nowrap" 
                        data-category="<?= htmlspecialchars($category['slug']) ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="pb-12 sm:pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Sidebar Filters -->
                <div class="lg:col-span-1 order-2 lg:order-1">
                    <div class="hidden lg:block glass-effect rounded-2xl p-4 sm:p-6 lg:sticky lg:top-32 filter-sidebar">
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
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
                <div class="lg:col-span-3 order-1 lg:order-2">
                    <!-- Top Bar -->
                    <div class="flex flex-col gap-4 mb-6 sm:mb-8">
                        <!-- Results count (mobile: full width, desktop: left side) -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <span class="text-xs sm:text-sm text-gray-600" id="resultsCount">Showing <?= count($templates) ?> templates</span>
                            
                            <!-- Desktop: View buttons and sort in original layout -->
                            <div class="hidden sm:flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <button id="gridViewBtn" class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-primary text-white rounded-button">
                                        <i class="ri-grid-line text-xs sm:text-sm"></i>
                                    </button>
                                    <button id="listViewBtn" class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-white/80 text-gray-600 rounded-button hover:bg-gray-100 transition-colors">
                                        <i class="ri-list-unordered text-xs sm:text-sm"></i>
                                    </button>
                                </div>
                                <span class="text-xs sm:text-sm font-medium text-secondary">Sort by:</span>
                                <div class="relative">
                                    <select id="sortSelect" class="px-3 sm:px-4 py-2 bg-white/80 rounded-button text-xs sm:text-sm font-medium hover:bg-white transition-colors border-none outline-none cursor-pointer">
                                        <option value="popular">Most Popular</option>
                                        <option value="newest">Newest First</option>
                                        <option value="price-low">Price: Low to High</option>
                                        <option value="price-high">Price: High to Low</option>
                                        <option value="rating">Highest Rated</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile: View buttons and sort on same row -->
                        <div class="flex sm:hidden items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button id="gridViewBtnMobile" class="w-7 h-7 flex items-center justify-center bg-primary text-white rounded-button">
                                    <i class="ri-grid-line text-xs"></i>
                                </button>
                                <button id="listViewBtnMobile" class="w-7 h-7 flex items-center justify-center bg-white/80 text-gray-600 rounded-button hover:bg-gray-100 transition-colors">
                                    <i class="ri-list-unordered text-xs"></i>
                                </button>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-medium text-secondary">Sort by:</span>
                                <div class="relative">
                                    <select id="sortSelectMobile" class="px-3 py-1.5 bg-white/80 rounded-button text-xs font-medium hover:bg-white transition-colors border-none outline-none cursor-pointer">
                                        <option value="popular">Most Popular</option>
                                        <option value="newest">Newest First</option>
                                        <option value="price-low">Price: Low to High</option>
                                        <option value="price-high">Price: High to Low</option>
                                        <option value="rating">Highest Rated</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Templates Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6" id="templatesGrid">
                        <?php foreach ($templates as $template): ?>
                        <div class="template-card rounded-xl sm:rounded-2xl overflow-hidden">
                            <div class="relative">
                                <img src="<?= getOptimizedImageUrl($template['preview_image'], 'thumb') ?>" 
                                     alt="<?= htmlspecialchars($template['title']) ?>" 
                                     class="template-image"
                                     loading="lazy"
                                     onerror="this.src='assets/images/default-template.jpg'">
                                <div class="absolute top-2 sm:top-3 right-2 sm:right-3">
                                    <button class="w-6 h-6 sm:w-8 sm:h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                        <i class="ri-heart-line text-gray-600 text-xs sm:text-sm"></i>
                                    </button>
                                </div>
                                <?php if ($template['is_featured']): ?>
                                <div class="absolute top-2 sm:top-3 left-2 sm:left-3">
                                    <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-red-500 text-white text-xs rounded-button font-medium">Best Seller</span>
                                </div>
                                <?php endif; ?>
                                <div class="absolute bottom-2 sm:bottom-3 left-2 sm:left-3 flex space-x-1 sm:space-x-2">
                                    <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-primary text-white text-xs rounded-button font-medium"><?= htmlspecialchars($template['technology']) ?></span>
                                </div>
                            </div>
                            <div class="template-content">
                                <div class="template-header">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-1.5 sm:space-x-2">
                                            <img src="<?= getOptimizedImageUrl($template['profile_image'], 'avatar_small') ?>" 
                                                 alt="<?= htmlspecialchars($template['seller_name']) ?>" 
                                                 class="w-4 h-4 sm:w-6 sm:h-6 rounded-full object-cover flex-shrink-0"
                                                 onerror="this.src='assets/images/default-avatar.png'">
                                            <span class="text-xs sm:text-sm text-gray-600 truncate"><?= htmlspecialchars($template['seller_name']) ?></span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text-sm sm:text-xl font-bold text-primary template-price">$<?= number_format($template['price'], 0) ?></span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-secondary template-title"><?= htmlspecialchars($template['title']) ?></h3>
                                    <p class="text-xs sm:text-sm text-gray-600 template-description"><?= htmlspecialchars($template['description']) ?></p>
                                </div>
                                <div class="template-meta">
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-star-fill text-yellow-400 text-xs sm:text-sm"></i>
                                        <span class="text-xs sm:text-sm text-gray-600"><?= number_format($template['avg_rating'], 1) ?> (<?= $template['review_count'] ?>)</span>
                                    </div>
                                </div>
                                <div class="template-actions">
                                    <button onclick="handleAddToCart(<?= $template['id'] ?>, '<?= addslashes($template['title']) ?>', <?= $template['price'] ?>, '<?= addslashes(getOptimizedImageUrl($template['preview_image'], 'thumb')) ?>', '<?= addslashes($template['seller_name']) ?>')" 
                                            class="flex-1 bg-primary text-white py-1.5 sm:py-2 px-2 sm:px-4 rounded-button text-xs sm:text-sm font-medium hover:bg-primary/90 transition-colors">
                                        <i class="ri-shopping-cart-line mr-1"></i><span class="hidden sm:inline">Add to Cart</span><span class="sm:hidden">Add</span>
                                    </button>
                                    <button onclick="window.location.href='template-detail.php?id=<?= $template['id'] ?>'" class="px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-200 rounded-button text-xs sm:text-sm font-medium hover:bg-gray-50 transition-colors">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="flex items-center justify-center space-x-1 sm:space-x-2 mt-8 sm:mt-12" id="paginationContainer">
                        <!-- Pagination will be dynamically updated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Toast Notification System - Inline version to ensure availability
        function createToastContainer() {
            let container = document.querySelector('.toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container';
                container.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 10001;
                    max-width: 400px;
                    width: 100%;
                    pointer-events: none;
                `;
                document.body.appendChild(container);
            }
            return container;
        }

        function showToast(message, type = 'info', title = '', duration = 5000) {
            const container = createToastContainer();
            
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'i'
            };

            const titles = {
                success: title || 'Success',
                error: title || 'Error', 
                warning: title || 'Warning',
                info: title || 'Info'
            };

            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };

            const toast = document.createElement('div');
            toast.style.cssText = `
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                margin-bottom: 12px;
                padding: 16px 20px;
                border-left: 4px solid ${colors[type]};
                transform: translateX(450px);
                transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                pointer-events: auto;
                display: flex;
                align-items: center;
                gap: 12px;
                position: relative;
                overflow: hidden;
            `;

            toast.innerHTML = `
                <div style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; font-weight: bold; background: ${colors[type]}; color: white;">
                    ${icons[type]}
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; font-size: 14px; color: #1f2937; margin-bottom: 2px;">${titles[type]}</div>
                    <div style="font-size: 13px; color: #6b7280; line-height: 1.4;">${message}</div>
                </div>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 18px; padding: 0; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">×</button>
            `;

            container.appendChild(toast);

            // Trigger animation
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 10);

            // Auto remove
            if (duration > 0) {
                setTimeout(() => {
                    toast.style.transform = 'translateX(450px)';
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.parentElement.removeChild(toast);
                        }
                    }, 300);
                }, duration);
            }

            return toast;
        }

        // Define toast functions globally
        window.showSuccess = function(message, title = '', duration = 5000) {
            console.log('🎉 Success toast:', message);
            return showToast(message, 'success', title, duration);
        };

        window.showError = function(message, title = '', duration = 7000) {
            console.log('❌ Error toast:', message);
            return showToast(message, 'error', title, duration);
        };

        window.showWarning = function(message, title = '', duration = 6000) {
            console.log('⚠️ Warning toast:', message);
            return showToast(message, 'warning', title, duration);
        };

        window.showInfo = function(message, title = '', duration = 5000) {
            console.log('ℹ️ Info toast:', message);
            return showToast(message, 'info', title, duration);
        };

        // Global variables
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;
        let currentView = 'grid'; // Track current view mode

        // Fallback toast functions if main toast.js fails to load
        function ensureToastFunctions() {
            if (typeof showError === 'undefined') {
                window.showError = function(message) {
                    if (typeof toastManager !== 'undefined' && toastManager.error) {
                        toastManager.error(message);
                    } else {
                        alert('Error: ' + message);
                    }
                };
            }
            
            if (typeof showSuccess === 'undefined') {
                window.showSuccess = function(message) {
                    if (typeof toastManager !== 'undefined' && toastManager.success) {
                        toastManager.success(message);
                    } else {
                        alert('Success: ' + message);
                    }
                };
            }
            
            if (typeof showWarning === 'undefined') {
                window.showWarning = function(message) {
                    if (typeof toastManager !== 'undefined' && toastManager.warning) {
                        toastManager.warning(message);
                    } else {
                        alert('Warning: ' + message);
                    }
                };
            }
            
            if (typeof showInfo === 'undefined') {
                window.showInfo = function(message) {
                    if (typeof toastManager !== 'undefined' && toastManager.info) {
                        toastManager.info(message);
                    } else {
                        alert('Info: ' + message);
                    }
                };
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Read URL parameters and set initial values
            initializeFromURLParams();
            
            initializeFilters();
            initializeSearch();
            initializeCategoryPills();
            initializeViewToggle(); // Add view toggle initialization
            initializeMobileFilters(); // Add mobile filters initialization
            
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

        // Initialize from URL parameters
        function initializeFromURLParams() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Set search value if exists
            const searchParam = urlParams.get('search');
            if (searchParam) {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = searchParam;
                    // Trigger search immediately
                    setTimeout(() => {
                        applyFilters();
                    }, 100);
                }
            }
            
            // Set category if exists
            const categoryParam = urlParams.get('category');
            if (categoryParam) {
                const categoryInput = document.querySelector(`input[name="category"][value="${categoryParam}"]`);
                if (categoryInput) {
                    categoryInput.checked = true;
                    syncCategoryPills(categoryParam);
                }
            }
        }

        // Initialize view toggle functionality
        function initializeViewToggle() {
            // Desktop view buttons
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            
            // Mobile view buttons
            const gridViewBtnMobile = document.getElementById('gridViewBtnMobile');
            const listViewBtnMobile = document.getElementById('listViewBtnMobile');
            
            // Desktop grid view
            if (gridViewBtn) {
                gridViewBtn.addEventListener('click', function() {
                    if (currentView !== 'grid') {
                        switchToGridView();
                    }
                });
            }
            
            // Desktop list view
            if (listViewBtn) {
                listViewBtn.addEventListener('click', function() {
                    if (currentView !== 'list') {
                        switchToListView();
                    }
                });
            }
            
            // Mobile grid view
            if (gridViewBtnMobile) {
                gridViewBtnMobile.addEventListener('click', function() {
                    if (currentView !== 'grid') {
                        switchToGridView();
                    }
                });
            }
            
            // Mobile list view
            if (listViewBtnMobile) {
                listViewBtnMobile.addEventListener('click', function() {
                    if (currentView !== 'list') {
                        switchToListView();
                    }
                });
            }
        }

        // Switch to grid view
        function switchToGridView() {
            currentView = 'grid';
            
            // Update desktop button states
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            
            if (gridViewBtn && listViewBtn) {
                gridViewBtn.classList.remove('bg-white/80', 'text-gray-600');
                gridViewBtn.classList.add('bg-primary', 'text-white');
                
                listViewBtn.classList.remove('bg-primary', 'text-white');
                listViewBtn.classList.add('bg-white/80', 'text-gray-600');
            }
            
            // Update mobile button states
            const gridViewBtnMobile = document.getElementById('gridViewBtnMobile');
            const listViewBtnMobile = document.getElementById('listViewBtnMobile');
            
            if (gridViewBtnMobile && listViewBtnMobile) {
                gridViewBtnMobile.classList.remove('bg-white/80', 'text-gray-600');
                gridViewBtnMobile.classList.add('bg-primary', 'text-white');
                
                listViewBtnMobile.classList.remove('bg-primary', 'text-white');
                listViewBtnMobile.classList.add('bg-white/80', 'text-gray-600');
            }
            
            // Update grid container classes
            const grid = document.getElementById('templatesGrid');
            if (grid) {
                grid.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6';
                
                // Re-render templates in grid view
                applyFilters();
            }
        }

        // Switch to list view
        function switchToListView() {
            currentView = 'list';
            
            // Update button states for both desktop and mobile
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            const gridViewBtnMobile = document.getElementById('gridViewBtnMobile');
            const listViewBtnMobile = document.getElementById('listViewBtnMobile');
            
            // Update desktop buttons
            if (gridViewBtn && listViewBtn) {
                gridViewBtn.classList.remove('bg-primary', 'text-white');
                gridViewBtn.classList.add('bg-white/80', 'text-gray-600');
                
                listViewBtn.classList.remove('bg-white/80', 'text-gray-600');
                listViewBtn.classList.add('bg-primary', 'text-white');
            }
            
            // Update mobile buttons
            if (gridViewBtnMobile && listViewBtnMobile) {
                gridViewBtnMobile.classList.remove('bg-primary', 'text-white');
                gridViewBtnMobile.classList.add('bg-white/80', 'text-gray-600');
                
                listViewBtnMobile.classList.remove('bg-white/80', 'text-gray-600');
                listViewBtnMobile.classList.add('bg-primary', 'text-white');
            }
            
            // Update grid container classes
            const grid = document.getElementById('templatesGrid');
            if (grid) {
                grid.className = 'templates-list space-y-4 sm:space-y-6';
                
                // Re-render templates in list view
                applyFilters();
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
                <div class="template-card rounded-xl sm:rounded-2xl overflow-hidden">
                    <div class="relative">
                        <img src="${template.preview_image_url || 'assets/images/default-template.jpg'}" 
                             alt="${escapeHtml(template.title)}" 
                             class="template-image"
                             loading="lazy"
                             onerror="this.src='assets/images/default-template.jpg'">
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3">
                            <button class="w-6 h-6 sm:w-8 sm:h-8 bg-white/80 rounded-full flex items-center justify-center hover:bg-white transition-colors">
                                <i class="ri-heart-line text-gray-600 text-xs sm:text-sm"></i>
                            </button>
                        </div>
                        ${template.is_featured ? '<div class="absolute top-2 sm:top-3 left-2 sm:left-3"><span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-red-500 text-white text-xs rounded-button font-medium">Best Seller</span></div>' : ''}
                        <div class="absolute bottom-2 sm:bottom-3 left-2 sm:left-3 flex space-x-1 sm:space-x-2">
                            <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-primary text-white text-xs rounded-button font-medium">${escapeHtml(template.technology)}</span>
                        </div>
                    </div>
                    <div class="template-content">
                        <div class="template-header">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-1.5 sm:space-x-2">
                                    <img src="${template.seller_avatar_url || 'assets/images/default-avatar.png'}" 
                                         alt="${escapeHtml(template.seller_name)}" 
                                         class="w-4 h-4 sm:w-6 sm:h-6 rounded-full object-cover flex-shrink-0"
                                         onerror="this.src='assets/images/default-avatar.png'">
                                    <span class="text-xs sm:text-sm text-gray-600 truncate">${escapeHtml(template.seller_name)}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="text-sm sm:text-xl font-bold text-primary template-price">$${template.price}</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-secondary template-title">${escapeHtml(template.title)}</h3>
                            <p class="text-xs sm:text-sm text-gray-600 template-description">${escapeHtml(template.description)}</p>
                        </div>
                        <div class="template-meta">
                            <div class="flex items-center space-x-1">
                                <i class="ri-star-fill text-yellow-400 text-xs sm:text-sm"></i>
                                <span class="text-xs sm:text-sm text-gray-600">${template.avg_rating} (${template.review_count})</span>
                            </div>
                        </div>
                        <div class="template-actions">
                            <button onclick="handleAddToCart(${template.id}, '${addslashes(template.title)}', ${template.price}, '${addslashes(template.preview_image_url || 'assets/images/default-template.jpg')}', '${addslashes(template.seller_name)}')" 
                                    class="flex-1 bg-primary text-white py-1.5 sm:py-2 px-2 sm:px-4 rounded-button text-xs sm:text-sm font-medium hover:bg-primary/90 transition-colors">
                                <i class="ri-shopping-cart-line mr-1"></i><span class="hidden sm:inline">Add to Cart</span><span class="sm:hidden">Add</span>
                            </button>
                            <button onclick="window.location.href='template-detail.php?id=${template.id}'" class="px-2 sm:px-4 py-1.5 sm:py-2 border border-gray-200 rounded-button text-xs sm:text-sm font-medium hover:bg-gray-50 transition-colors">
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
                        <img src="${template.preview_image_url || 'assets/images/default-template.jpg'}" 
                             alt="${escapeHtml(template.title)}" 
                             class="template-image"
                             loading="lazy"
                             onerror="this.src='assets/images/default-template.jpg'">
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
                                    <img src="${template.seller_avatar_url || 'assets/images/default-avatar.png'}" 
                                         alt="${escapeHtml(template.seller_name)}" 
                                         class="w-6 h-6 rounded-full object-cover"
                                         onerror="this.src='assets/images/default-avatar.png'">
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
                            <button onclick="handleAddToCart(${template.id}, '${addslashes(template.title)}', ${template.price}, '${addslashes(template.preview_image_url || 'assets/images/default-template.jpg')}', '${addslashes(template.seller_name)}')" 
                                    class="bg-primary text-white py-2 px-6 rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                                <i class="ri-shopping-cart-line mr-2"></i>Add to Cart
                            </button>
                            <button onclick="window.location.href='template-detail.php?id=${template.id}'" class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
                                <i class="ri-eye-line mr-2"></i>Preview
                            </button>
                            <button onclick="window.location.href='template-detail.php?id=${template.id}'" class="px-4 py-2 border border-gray-200 rounded-button text-sm font-medium hover:bg-gray-50 transition-colors whitespace-nowrap">
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

            // Mobile sort dropdown
            const sortSelectMobile = document.getElementById('sortSelectMobile');
            if (sortSelectMobile) {
                sortSelectMobile.addEventListener('change', function() {
                    // Sync with desktop sort dropdown
                    if (sortSelect) {
                        sortSelect.value = this.value;
                    }
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
            const sortSelectMobile = document.getElementById('sortSelectMobile');
            if (sortSelect) {
                filters.sort = sortSelect.value;
            } else if (sortSelectMobile) {
                filters.sort = sortSelectMobile.value;
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
            // Check if user is logged in using PHP session
            const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
            
            if (!isLoggedIn) {
                // If user is not logged in, redirect to login
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            console.log('Adding template to cart:', {
                id: id,
                title: title,
                price: price,
                type: 'template'
            });
            
            // Send to cart API directly
            fetch('cart-api.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    template_id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge if element exists
                    const cartBadge = document.getElementById('cartBadge');
                    if (cartBadge && data.cart_count) {
                        cartBadge.textContent = data.cart_count;
                        cartBadge.style.transform = 'scale(1)';
                    }
                    
                    // Refresh cart dropdown if cart object exists
                    if (typeof cart !== 'undefined' && cart.loadCartFromServer) {
                        cart.loadCartFromServer();
                    }
                } else {
                    if (data.redirect) {
                        window.location.href = data.redirect + '?redirect=' + encodeURIComponent(window.location.href);
                    } else {
                        showError('Failed to add template to cart: ' + (data.error || 'Unknown error'));
                    }
                }
            })
            .catch(error => {
                console.error('Error adding template to cart:', error);
                showError('Error adding template to cart. Please try again.');
            });
        }
        
        // Helper function for JavaScript addslashes equivalent
        function addslashes(str) {
            return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
        }

        // Initialize mobile filters
        function initializeMobileFilters() {
            const mobileFilterDropdownToggle = document.getElementById('mobileFilterDropdownToggle');
            const mobileFilterDropdown = document.getElementById('mobileFilterDropdown');
            const closeMobileFilterDropdown = document.getElementById('closeMobileFilterDropdown');
            const applyMobileFilters = document.getElementById('applyMobileFilters');
            
            // Add flag to prevent race conditions
            let isAnimating = false;
            
            // Toggle dropdown
            if (mobileFilterDropdownToggle) {
                mobileFilterDropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent event bubbling
                    if (!isAnimating) {
                        toggleMobileFilterDropdown();
                    }
                });
            }
            
            // Close dropdown
            if (closeMobileFilterDropdown) {
                closeMobileFilterDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent event bubbling
                    closeMobileFilterDropdownFunc();
                });
            }
            
            // Apply filters
            if (applyMobileFilters) {
                applyMobileFilters.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Prevent event bubbling
                    applyMobileFiltersFunc();
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (mobileFilterDropdown && 
                    !mobileFilterDropdown.contains(e.target) && 
                    !mobileFilterDropdownToggle.contains(e.target) &&
                    !mobileDropdownAnimating) {
                    closeMobileFilterDropdownFunc();
                }
            });
            
            // Sync mobile filters with desktop filters
            initializeMobileFilterSync();
        }

        // Initialize mobile filter synchronization
        function initializeMobileFilterSync() {
            // Sync category changes
            const mobileCategoryRadios = document.querySelectorAll('input[name="mobile_category"]');
            mobileCategoryRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Sync to desktop
                    const desktopRadio = document.querySelector(`input[name="category"][value="${this.value}"]`);
                    if (desktopRadio) {
                        desktopRadio.checked = true;
                    }
                    // Sync category pills if they exist
                    if (typeof syncCategoryPills === 'function') {
                        syncCategoryPills(this.value);
                    }
                });
            });

            // Sync technology changes
            const mobileTechnologyCheckboxes = document.querySelectorAll('input[name="mobile_technology"]');
            mobileTechnologyCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const desktopCheckbox = document.querySelector(`input[name="technology"][value="${this.value}"]`);
                    if (desktopCheckbox) {
                        desktopCheckbox.checked = this.checked;
                    }
                });
            });

            // Sync rating changes
            const mobileRatingRadios = document.querySelectorAll('input[name="mobile_rating"]');
            mobileRatingRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const desktopRadio = document.querySelector(`input[name="rating"][value="${this.value}"]`);
                    if (desktopRadio) {
                        desktopRadio.checked = true;
                    }
                });
            });

            // Sync price inputs
            const mobileminPrice = document.getElementById('mobileminPrice');
            const mobilemaxPrice = document.getElementById('mobilemaxPrice');
            
            if (mobileminPrice) {
                mobileminPrice.addEventListener('input', function() {
                    const desktopMinPrice = document.getElementById('minPrice');
                    if (desktopMinPrice) {
                        desktopMinPrice.value = this.value;
                    }
                });
            }
            
            if (mobilemaxPrice) {
                mobilemaxPrice.addEventListener('input', function() {
                    const desktopMaxPrice = document.getElementById('maxPrice');
                    if (desktopMaxPrice) {
                        desktopMaxPrice.value = this.value;
                    }
                });
            }
        }

        // Toggle mobile filter dropdown
        function toggleMobileFilterDropdown() {
            const dropdown = document.getElementById('mobileFilterDropdown');
            if (dropdown) {
                if (dropdown.classList.contains('hidden')) {
                    openMobileFilterDropdown();
                } else {
                    closeMobileFilterDropdownFunc();
                }
            }
        }

        // Global animation flag
        let mobileDropdownAnimating = false;

        // Open mobile filter dropdown
        function openMobileFilterDropdown() {
            if (mobileDropdownAnimating) return;
            
            const dropdown = document.getElementById('mobileFilterDropdown');
            if (dropdown) {
                mobileDropdownAnimating = true;
                dropdown.classList.remove('hidden');
                // Force reflow to ensure hidden class is removed
                dropdown.offsetHeight;
                
                // Animate slide down
                dropdown.style.transform = 'translateY(-10px)';
                dropdown.style.opacity = '0';
                dropdown.style.transition = 'all 0.2s ease-out';
                
                // Use requestAnimationFrame for smoother animation
                requestAnimationFrame(() => {
                    dropdown.style.transform = 'translateY(0)';
                    dropdown.style.opacity = '1';
                });
                
                setTimeout(() => {
                    mobileDropdownAnimating = false;
                }, 200);
            }
        }

        // Close mobile filter dropdown
        function closeMobileFilterDropdownFunc() {
            if (mobileDropdownAnimating) return;
            
            const dropdown = document.getElementById('mobileFilterDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                mobileDropdownAnimating = true;
                dropdown.style.transition = 'all 0.2s ease-in';
                dropdown.style.transform = 'translateY(-10px)';
                dropdown.style.opacity = '0';
                
                setTimeout(() => {
                    if (dropdown && !dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('hidden');
                        dropdown.style.transform = '';
                        dropdown.style.opacity = '';
                        dropdown.style.transition = '';
                    }
                    mobileDropdownAnimating = false;
                }, 200);
            }
        }

        // Apply mobile filters
        function applyMobileFiltersFunc() {
            // Get mobile filter values
            const categoryFilter = document.querySelector('input[name="mobile_category"]:checked');
            const minPrice = document.getElementById('mobileminPrice').value;
            const maxPrice = document.getElementById('mobilemaxPrice').value;
            const technologyFilters = Array.from(document.querySelectorAll('input[name="mobile_technology"]:checked')).map(cb => cb.value);
            const ratingFilter = document.querySelector('input[name="mobile_rating"]:checked');
            
            // Apply filters to desktop version
            if (categoryFilter && categoryFilter.value) {
                const desktopCategoryInput = document.querySelector(`input[name="category"][value="${categoryFilter.value}"]`);
                if (desktopCategoryInput) {
                    desktopCategoryInput.checked = true;
                }
            }
            
            if (minPrice) {
                const desktopMinPrice = document.getElementById('minPrice');
                if (desktopMinPrice) desktopMinPrice.value = minPrice;
            }
            
            if (maxPrice) {
                const desktopMaxPrice = document.getElementById('maxPrice');
                if (desktopMaxPrice) desktopMaxPrice.value = maxPrice;
            }
            
            if (technologyFilters.length > 0) {
                // Clear existing technology filters
                document.querySelectorAll('input[name="technology"]').forEach(cb => cb.checked = false);
                // Apply selected filters
                technologyFilters.forEach(tech => {
                    const desktopTechInput = document.querySelector(`input[name="technology"][value="${tech}"]`);
                    if (desktopTechInput) desktopTechInput.checked = true;
                });
            }
            
            if (ratingFilter && ratingFilter.value) {
                const desktopRatingInput = document.querySelector(`input[name="rating"][value="${ratingFilter.value}"]`);
                if (desktopRatingInput) {
                    desktopRatingInput.checked = true;
                }
            }
            
            // Close mobile dropdown
            closeMobileFilterDropdownFunc();
            
            // Apply filters
            applyFilters();
        }

        function selectMobileCategory(categoryId, categoryName) {
            // Update selected category
            selectedCategory = categoryId;
            
            // Update button text
            const button = document.getElementById('mobileCategoryBtn');
            if (button) {
                button.innerHTML = `
                    <span>${categoryName}</span>
                    <i class="ri-arrow-down-s-line"></i>
                `;
            }
            
            // Close dropdown and apply filters
            closeMobileFilter();
            applyFilters();
        }

        function selectMobilePriceRange(minPrice, maxPrice, label) {
            // Update selected price range
            minPriceFilter = minPrice;
            maxPriceFilter = maxPrice;
            
            // Update button text
            const button = document.getElementById('mobilePriceBtn');
            if (button) {
                button.innerHTML = `
                    <span>${label}</span>
                    <i class="ri-arrow-down-s-line"></i>
                `;
            }
            
            // Close dropdown and apply filters
            closeMobileFilter();
            applyFilters();
        }

        function selectMobileComplexity(complexity) {
            // Update selected complexity
            selectedComplexity = complexity;
            
            // Update button text
            const button = document.getElementById('mobileComplexityBtn');
            if (button) {
                const displayText = complexity === 'all' ? 'All Levels' : complexity.charAt(0).toUpperCase() + complexity.slice(1);
                button.innerHTML = `
                    <span>${displayText}</span>
                    <i class="ri-arrow-down-s-line"></i>
                `;
            }
            
            // Close dropdown and apply filters
            closeMobileFilter();
            applyFilters();
        }

        function selectMobileRating(rating) {
            // Update selected rating
            selectedRating = rating;
            
            // Update button text
            const button = document.getElementById('mobileRatingBtn');
            if (button) {
                const displayText = rating === 'all' ? 'All Ratings' : `${rating}+ Stars`;
                button.innerHTML = `
                    <span>${displayText}</span>
                    <i class="ri-arrow-down-s-line"></i>
                `;
            }
            
            // Close dropdown and apply filters
            closeMobileFilter();
            applyFilters();
        }

        // Initialize mobile filters when DOM is ready - removed duplicate
    </script>
    
    <!-- Toast Notification System - Load at the end to ensure it's available -->
    <script src="assets/js/toast.js"></script>
    <script>
        // Debug: Check if toast functions are loaded
        console.log('Toast functions available:', {
            showError: typeof showError,
            showSuccess: typeof showSuccess,
            showInfo: typeof showInfo,
            showWarning: typeof showWarning,
            toastManager: typeof toastManager
        });
        
        // If not available, wait for DOM ready
        if (typeof showError === 'undefined') {
            document.addEventListener('DOMContentLoaded', function() {
                console.log('After DOM ready - Toast functions available:', {
                    showError: typeof showError,
                    showSuccess: typeof showSuccess,
                    toastManager: typeof toastManager
                });
            });
        }
    </script>
</body>
</html>