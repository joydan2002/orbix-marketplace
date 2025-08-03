<?php
/**
 * Header Include File
 * Contains the HTML header section with navigation
 */

require_once __DIR__ . '/../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and get user data
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$userData = null;

if ($isLoggedIn) {
    try {
        $pdo = DatabaseConfig::getConnection();
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, username, profile_image, user_type, email_verified FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If user not found, clear session
        if (!$userData) {
            session_destroy();
            $isLoggedIn = false;
        }
    } catch (Exception $e) {
        // If error, treat as not logged in
        $isLoggedIn = false;
        $userData = null;
    }
}

$config = DatabaseConfig::getAppConfig();

// Get categories and technologies for dropdown
try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get categories with template count
    $stmt = $pdo->query("SELECT c.name, c.slug, COUNT(t.id) as template_count 
                        FROM categories c 
                        LEFT JOIN templates t ON c.id = t.category_id AND t.status = 'approved'
                        WHERE c.is_active = 1 
                        GROUP BY c.id, c.name, c.slug
                        ORDER BY template_count DESC, c.name");
    $headerCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get popular technologies
    $stmt = $pdo->query("SELECT technology, COUNT(*) as count 
                        FROM templates 
                        WHERE status = 'approved' AND technology IS NOT NULL 
                        GROUP BY technology 
                        ORDER BY count DESC 
                        LIMIT 6");
    $headerTechnologies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Fallback to empty arrays if database error
    $headerCategories = [];
    $headerTechnologies = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['name']; ?> - Premium Website Templates Marketplace</title>
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
        .neon-glow {
            box-shadow: 0 0 20px rgba(255, 95, 31, 0.3);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
        .template-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .template-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .ai-mascot {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* User Dropdown Styles - Thay đổi về background trắng */
        .user-dropdown {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px) scale(0.95);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            /* Background trắng như các dropdown khác */
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 
                0 20px 40px -12px rgba(0, 0, 0, 0.15),
                0 4px 16px -4px rgba(0, 0, 0, 0.1);
        }
        
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        
        /* Templates Dropdown Styles - Tương tự User Dropdown */
        .templates-dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px) translateX(-50%) scale(0.95);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 
                0 20px 40px -12px rgba(0, 0, 0, 0.15),
                0 4px 16px -4px rgba(0, 0, 0, 0.1);
        }
        
        .templates-dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) translateX(-50%) scale(1);
        }
        
        /* Template dropdown hover effects */
        .templates-dropdown-container:hover .templates-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) translateX(-50%) scale(1);
        }
        
        .dropdown-header {
            background: rgba(248, 250, 252, 0.8);
            border-radius: 16px 16px 0 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            position: relative;
        }
        
        .menu-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 14px;
            position: relative;
            overflow: hidden;
        }
        
        .menu-item:hover {
            background: rgba(255, 95, 31, 0.15);
            backdrop-filter: blur(20px);
            transform: translateX(6px) translateY(-2px);
            box-shadow: 
                0 8px 20px -8px rgba(255, 95, 31, 0.3),
                0 0 0 1px rgba(255, 95, 31, 0.2);
        }
        
        /* Fixed Avatar Container */
        .dropdown-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            border: 3px solid rgba(255, 95, 31, 0.4);
            box-shadow: 0 4px 15px rgba(255, 95, 31, 0.2);
        }
        
        .dropdown-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .dropdown-avatar-fallback {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #FF5F1F 0%, #FF8C42 50%, #FFB366 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        /* User Info Container - Màu text phù hợp với brand */
        .user-info-container {
            flex: 1;
            min-width: 0;
            margin-left: 16px;
        }
        
        .user-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
            text-shadow: 0 2px 4px rgba(255, 255, 255, 0.8);
        }
        
        .user-email {
            font-size: 0.85rem;
            color: #4b5563;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 8px;
            max-width: 180px;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.6);
        }
        
        .glass-badge {
            background: rgba(255, 95, 31, 0.2);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 95, 31, 0.4);
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
            color: #1f2937;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
            box-shadow: 0 2px 8px rgba(255, 95, 31, 0.2);
        }
        
        .glass-badge.badge-success {
            background: rgba(34, 197, 94, 0.2);
            border-color: rgba(34, 197, 94, 0.4);
            color: #1f2937;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
        }
        
        /* Menu item text với màu brand phù hợp */
        .menu-title {
            font-weight: 700;
            color: #1f2937;
            font-size: 0.95rem;
            text-shadow: 0 1px 3px rgba(255, 255, 255, 0.8);
        }
        
        .menu-subtitle {
            color: #6b7280;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.6);
        }
        
        .menu-item:hover .menu-title {
            color: #FF5F1F;
            text-shadow: 0 2px 4px rgba(255, 255, 255, 0.9);
        }
        
        .menu-item:hover .menu-subtitle {
            color: #1f2937;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.7);
        }
        
        /* Icon containers với màu brand */
        .icon-container {
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Red text cho Sign Out với contrast tốt */
        .text-red-600 {
            color: #dc2626 !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 3px rgba(255, 255, 255, 0.8) !important;
        }
        
        .text-red-400 {
            color: #f87171 !important;
            font-weight: 500 !important;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.6) !important;
        }
        
        /* Arrow icons với màu primary khi hover */
        .menu-item:hover .ri-arrow-right-s-line {
            color: #FF5F1F !important;
        }
        
        /* Divider với gradient subtle */
        .dropdown-divider {
            background: linear-gradient(to right, 
                transparent, 
                rgba(255, 95, 31, 0.2) 20%, 
                rgba(255, 95, 31, 0.3) 50%, 
                rgba(255, 95, 31, 0.2) 80%, 
                transparent
            );
            height: 1px;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1D',
                        secondary: '#1f2937'
                    },
                    borderRadius: {
                        'none': '0px',
                        'sm': '4px',
                        DEFAULT: '8px',
                        'md': '12px',
                        'lg': '16px',
                        'xl': '20px',
                        '2xl': '24px',
                        '3xl': '32px',
                        'full': '9999px',
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
<header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md" style="background: rgba(255, 255, 255, 0.1);">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">O</span>
                </div>
                <span class="font-pacifico text-3xl text-secondary">Orbix Market</span>
            </div>
            
            <!-- Navigation -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="index.php" class="text-secondary hover:text-primary transition-colors font-medium">Home</a>
                
                <!-- Templates Dropdown - Dynamic with wider layout -->
                <div id="templates-dropdown" class="relative templates-dropdown-container">
                    <a href="templates.php" class="text-secondary hover:text-primary transition-colors font-medium flex items-center">
                        Templates 
                        <div class="w-4 h-4 flex items-center justify-center ml-1">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </a>
                    
                    <!-- Expanded Dropdown Content -->
                    <div class="templates-dropdown-menu absolute top-full left-1/2 transform -translate-x-1/2 mt-2 w-[600px] bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible translate-y-2 transition-all duration-300" style="display: none;">
                        <div class="p-8">
                            <div class="grid grid-cols-2 gap-8">
                                <!-- Categories Section -->
                                <div>
                                    <h3 class="font-semibold text-secondary mb-4 flex items-center">
                                        <i class="ri-folder-line mr-2 text-primary"></i>
                                        Categories
                                    </h3>
                                    <div class="space-y-2">
                                        <a href="templates.php" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <span class="text-gray-600 group-hover:text-primary">All Templates</span>
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                                                <?= array_sum(array_column($headerCategories, 'template_count')) ?>
                                            </span>
                                        </a>
                                        <?php foreach ($headerCategories as $category): ?>
                                        <a href="templates.php?category=<?= urlencode($category['slug']) ?>" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <span class="text-gray-600 group-hover:text-primary"><?= htmlspecialchars($category['name']) ?></span>
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full"><?= $category['template_count'] ?></span>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Technologies Section -->
                                <div>
                                    <h3 class="font-semibold text-secondary mb-4 flex items-center">
                                        <i class="ri-code-line mr-2 text-primary"></i>
                                        Technologies
                                    </h3>
                                    <div class="space-y-2">
                                        <?php foreach ($headerTechnologies as $tech): ?>
                                        <a href="templates.php?technology=<?= urlencode($tech['technology']) ?>" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <span class="text-gray-600 group-hover:text-primary"><?= htmlspecialchars($tech['technology']) ?></span>
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full"><?= $tech['count'] ?></span>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="mt-6 pt-4 border-t border-gray-100">
                                        <div class="flex space-x-2">
                                            <a href="templates.php?featured=1" class="flex-1 bg-primary text-white text-center py-2 px-4 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                                Featured
                                            </a>
                                            <a href="templates.php?sort=newest" class="flex-1 bg-gray-100 text-gray-600 text-center py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                                New
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="services.php" class="text-secondary hover:text-primary transition-colors font-medium">Services</a>
                <a href="#" class="text-secondary hover:text-primary transition-colors font-medium">Seller Channel</a>
                <a href="#" class="text-secondary hover:text-primary transition-colors font-medium">Support</a>
            </nav>
            
            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <?php if ($isLoggedIn): ?>
                    <!-- Shopping Cart -->
                    <div class="relative">
                        <button id="cartButton" class="relative p-2 rounded-xl hover:bg-white/10 transition-colors group">
                            <i class="ri-shopping-cart-line text-xl text-secondary group-hover:text-primary transition-colors"></i>
                            <!-- Cart Badge -->
                            <span id="cartBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center transform scale-0 transition-transform duration-200">
                                0
                            </span>
                        </button>
                        
                        <!-- Cart Dropdown -->
                        <div id="cartDropdown" class="absolute top-full right-0 mt-3 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible translate-y-2 transition-all duration-300 z-50">
                            <div class="p-6">
                                <!-- Cart Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-lg text-secondary">Shopping Cart</h3>
                                    <button id="clearCartBtn" class="text-sm text-gray-500 hover:text-red-500 transition-colors">
                                        Clear All
                                    </button>
                                </div>
                                
                                <!-- Cart Items Container -->
                                <div id="cartItems" class="space-y-3 max-h-64 overflow-y-auto">
                                    <!-- Empty Cart Message -->
                                    <div id="emptyCartMessage" class="text-center py-8">
                                        <i class="ri-shopping-cart-line text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">Your cart is empty</p>
                                        <p class="text-sm text-gray-400 mt-1">Add some templates to get started!</p>
                                    </div>
                                </div>
                                
                                <!-- Cart Footer -->
                                <div id="cartFooter" class="mt-6 pt-4 border-t border-gray-100" style="display: none;">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="font-semibold text-secondary">Total:</span>
                                        <span id="cartTotal" class="font-bold text-xl text-primary">$0</span>
                                    </div>
                                    <div class="flex space-x-3">
                                        <button onclick="viewCart()" class="flex-1 bg-gray-100 text-gray-600 py-2 px-4 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                            View Cart
                                        </button>
                                        <button onclick="proceedToCheckout()" class="flex-1 bg-primary text-white py-2 px-4 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                            Checkout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu Dropdown -->
                    <div id="userDropdownContainer" class="relative group">
                        <!-- User Avatar Button -->
                        <button id="userDropdownButton" class="flex items-center space-x-3 p-2 rounded-xl hover:bg-white/10 transition-colors">
                            <!-- Avatar -->
                            <div class="relative avatar-ring">
                                <?php if (!empty($userData['profile_image'])): ?>
                                    <img src="<?= htmlspecialchars($userData['profile_image']) ?>" 
                                         alt="<?= htmlspecialchars($userData['first_name']) ?>" 
                                         class="w-10 h-10 rounded-full object-cover border-2 border-white/20">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/80 rounded-full flex items-center justify-center border-2 border-white/20">
                                        <span class="text-white font-semibold text-sm">
                                            <?= strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1)) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                

                            </div>
                            
                            <!-- User Info (Hidden on mobile) -->
                            <div class="hidden md:block text-left">
                                <div class="text-sm font-semibold text-secondary">
                                    <?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?>
                                </div>
                                <div class="text-xs text-gray-500 capitalize flex items-center">
                                    <?= htmlspecialchars($userData['user_type']) ?>
                                    <?php if ($userData['email_verified']): ?>
                                        <i class="ri-verified-badge-fill text-blue-500 ml-1"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Dropdown Arrow -->
                            <div class="w-4 h-4 flex items-center justify-center">
                                <i class="ri-arrow-down-s-line text-gray-500 group-hover:text-secondary transition-colors"></i>
                            </div>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute top-full right-0 mt-3 w-80 rounded-2xl shadow-2xl z-50 user-dropdown">
                            <div class="p-6">
                                <!-- User Info Header -->
                                <div class="dropdown-header flex items-center pb-6 mb-5">
                                    <div class="dropdown-avatar">
                                        <?php if (!empty($userData['profile_image'])): ?>
                                            <img src="<?= htmlspecialchars($userData['profile_image']) ?>" 
                                                 alt="<?= htmlspecialchars($userData['first_name']) ?>" />
                                        <?php else: ?>
                                            <div class="dropdown-avatar-fallback">
                                                <?= strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        

                                    </div>
                                    
                                    <div class="user-info-container">
                                        <div class="user-name">
                                            <?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?>
                                        </div>
                                        <div class="user-email">
                                            <?= htmlspecialchars($userData['email']) ?>
                                        </div>
                                        <div class="user-badges">
                                            <span class="glass-badge badge-primary">
                                                <?= htmlspecialchars(ucfirst($userData['user_type'])) ?>
                                            </span>
                                            <?php if ($userData['email_verified']): ?>
                                                <span class="glass-badge badge-success">
                                                    <i class="ri-verified-badge-fill mr-1"></i>Verified
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Menu Items -->
                                <div class="space-y-1">
                                    <a href="profile.php" class="menu-item flex items-center space-x-4 px-4 py-3 group">
                                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center icon-container">
                                            <i class="ri-user-line text-blue-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="menu-title">Profile</div>
                                            <div class="text-xs menu-subtitle">Manage your account</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line text-gray-400 group-hover:text-primary transition-colors"></i>
                                    </a>
                                    
                                    <a href="templates.php" class="menu-item flex items-center space-x-4 px-4 py-3 group">
                                        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center icon-container">
                                            <i class="ri-layout-grid-line text-purple-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="menu-title">Browse Templates</div>
                                            <div class="text-xs menu-subtitle">Explore marketplace</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line text-gray-400 group-hover:text-primary transition-colors"></i>
                                    </a>
                                    
                                    <a href="#" class="menu-item flex items-center space-x-4 px-4 py-3 group">
                                        <div class="w-10 h-10 bg-pink-50 rounded-xl flex items-center justify-center icon-container">
                                            <i class="ri-heart-line text-pink-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="menu-title">My Favorites</div>
                                            <div class="text-xs menu-subtitle">Saved templates</div>
                                        </div>
                                        <span class="glass-badge">12</span>
                                    </a>
                                    
                                    <a href="#" class="menu-item flex items-center space-x-4 px-4 py-3 group">
                                        <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center icon-container">
                                            <i class="ri-download-line text-orange-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="menu-title">My Downloads</div>
                                            <div class="text-xs menu-subtitle">Purchase history</div>
                                        </div>
                                        <span class="glass-badge">5</span>
                                    </a>
                                    
                                    <?php if ($userData['user_type'] === 'seller'): ?>
                                    <a href="#" class="menu-item flex items-center space-x-4 px-4 py-3 group">
                                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center icon-container">
                                            <i class="ri-upload-line text-indigo-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="menu-title">Upload Template</div>
                                            <div class="text-xs menu-subtitle">Share your work</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line text-gray-400 group-hover:text-primary transition-colors"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Divider -->
                                <div class="my-4 dropdown-divider"></div>
                                
                                <!-- Footer Actions -->
                                <div class="space-y-1">
                                    <a href="#" class="menu-item flex items-center space-x-4 px-4 py-3 group">
                                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center icon-container">
                                            <i class="ri-settings-3-line text-gray-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="menu-title">Settings</div>
                                            <div class="text-xs menu-subtitle">Preferences & privacy</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line text-gray-400 group-hover:text-primary transition-colors"></i>
                                    </a>
                                    
                                    <a href="logout.php" class="menu-item flex items-center space-x-4 px-4 py-3 group">
                                        <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center icon-container">
                                            <i class="ri-logout-box-line text-red-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-red-600">Sign Out</div>
                                            <div class="text-xs text-red-400">End your session</div>
                                        </div>
                                        <i class="ri-arrow-right-s-line text-red-400 transition-colors"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="auth.php" class="hidden md:block text-secondary hover:text-primary transition-colors font-medium">Sign In</a>
                    <a href="auth.php?mode=signup" class="bg-primary text-white px-6 py-2 rounded-button font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Get Started</a>
                <?php endif; ?>
                
                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden w-8 h-8 flex items-center justify-center">
                    <i class="ri-menu-line text-secondary text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Enhanced JavaScript for Header Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced dropdown functionality
    setupDropdowns();
    
    // Mobile menu toggle
    setupMobileMenu();
    
    // Smooth animations
    setupAnimations();
});

function setupDropdowns() {
    // Templates dropdown - Sử dụng hiệu ứng mượt mà như user dropdown
    const templatesDropdown = document.querySelector('.templates-dropdown-container');
    const templatesMenu = templatesDropdown?.querySelector('.templates-dropdown-menu');
    
    if (templatesDropdown && templatesMenu) {
        let templatesShowTimeout, templatesHideTimeout;
        
        templatesDropdown.addEventListener('mouseenter', () => {
            clearTimeout(templatesHideTimeout);
            templatesShowTimeout = setTimeout(() => {
                templatesMenu.style.display = 'block';
                templatesMenu.classList.add('show');
            }, 100);
        });
        
        templatesDropdown.addEventListener('mouseleave', () => {
            clearTimeout(templatesShowTimeout);
            templatesHideTimeout = setTimeout(() => {
                templatesMenu.classList.remove('show');
                setTimeout(() => {
                    if (!templatesMenu.classList.contains('show')) {
                        templatesMenu.style.display = 'none';
                    }
                }, 400); // Match CSS transition duration
            }, 150);
        });
    }
    
    // User dropdown - Sử dụng ID cụ thể thay vì selector generic
    const userDropdownContainer = document.getElementById('userDropdownContainer');
    const userDropdownButton = document.getElementById('userDropdownButton');
    const userMenu = document.querySelector('.user-dropdown');
    
    console.log('User dropdown elements:', {
        container: userDropdownContainer,
        button: userDropdownButton,
        menu: userMenu
    });
    
    if (userDropdownContainer && userMenu && userDropdownButton) {
        let userShowTimeout, userHideTimeout;
        
        // Mouse enter trên user dropdown container và menu
        const showDropdown = () => {
            clearTimeout(userHideTimeout);
            userShowTimeout = setTimeout(() => {
                console.log('Showing user dropdown');
                userMenu.classList.add('show');
            }, 100);
        };
        
        const hideDropdown = () => {
            clearTimeout(userShowTimeout);
            userHideTimeout = setTimeout(() => {
                console.log('Hiding user dropdown');
                userMenu.classList.remove('show');
            }, 200);
        };
        
        // Event listeners cho container
        userDropdownContainer.addEventListener('mouseenter', showDropdown);
        userDropdownContainer.addEventListener('mouseleave', hideDropdown);
        
        // Event listeners cho dropdown menu để không bị đóng khi hover vào menu
        userMenu.addEventListener('mouseenter', () => {
            clearTimeout(userHideTimeout);
        });
        
        userMenu.addEventListener('mouseleave', hideDropdown);
        
        // Click để toggle (cho mobile/touch devices)
        userDropdownButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('User dropdown button clicked');
            userMenu.classList.toggle('show');
        });
        
        // Click bên ngoài để đóng
        document.addEventListener('click', (e) => {
            if (!userDropdownContainer.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.remove('show');
            }
        });
    } else {
        console.error('User dropdown elements not found:', {
            container: !!userDropdownContainer,
            button: !!userDropdownButton,
            menu: !!userMenu
        });
    }
}

function setupMobileMenu() {
    const mobileToggle = document.querySelector('button.lg\\:hidden');
    const nav = document.querySelector('nav.hidden.lg\\:flex');
    
    if (mobileToggle && nav) {
        mobileToggle.addEventListener('click', () => {
            nav.classList.toggle('hidden');
            nav.classList.toggle('flex');
            
            // Animate icon
            const icon = mobileToggle.querySelector('i');
            if (nav.classList.contains('hidden')) {
                icon.className = 'ri-menu-line text-secondary text-xl';
            } else {
                icon.className = 'ri-close-line text-secondary text-xl';
            }
        });
    }
}

function setupAnimations() {
    // Add smooth hover effects to menu items
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Avatar glow effect
    const avatars = document.querySelectorAll('.avatar-ring');
    
    avatars.forEach(avatar => {
        avatar.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 0 20px rgba(255, 95, 31, 0.4)';
        });
        
        avatar.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });
}

// Enhanced notification system for auth status
<?php if ($isLoggedIn): ?>
// User is logged in - show welcome message if first visit
if (sessionStorage.getItem('showWelcome') !== 'false') {
    setTimeout(() => {
        if (window.toast) {
            window.toast.success('Welcome back, <?= htmlspecialchars($userData['first_name']) ?>!', {
                duration: 3000,
                position: 'top-right'
            });
        }
        sessionStorage.setItem('showWelcome', 'false');
    }, 1000);
}

// Shopping Cart System - Database-based với real-time updates
class ShoppingCart {
    constructor() {
        this.items = [];
        this.init();
    }
    
    async init() {
        await this.loadCartFromServer();
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Cart button click
        const cartButton = document.getElementById('cartButton');
        const cartDropdown = document.getElementById('cartDropdown');
        
        if (cartButton && cartDropdown) {
            cartButton.addEventListener('click', async (e) => {
                e.stopPropagation();
                // Refresh cart data khi mở dropdown
                await this.loadCartFromServer();
                this.toggleCartDropdown();
            });
            
            // Close cart when clicking outside
            document.addEventListener('click', (e) => {
                if (!cartButton.contains(e.target) && !cartDropdown.contains(e.target)) {
                    this.closeCartDropdown();
                }
            });
        }
        
        // Clear cart button
        const clearCartBtn = document.getElementById('clearCartBtn');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', () => {
                this.clearCart();
            });
        }
    }
    
    async loadCartFromServer() {
        try {
            const response = await fetch('cart-api.php?action=get');
            const data = await response.json();
            
            if (data.success) {
                this.items = data.items.map(item => ({
                    id: item.template_id,
                    title: item.title,
                    price: parseFloat(item.price),
                    image: item.preview_image,
                    seller: item.seller_name,
                    addedAt: item.added_at
                }));
                this.updateCartUI();
            } else {
                console.error('Failed to load cart:', data.error);
                // Reset cart nếu không load được
                this.items = [];
                this.updateCartUI();
            }
        } catch (error) {
            console.error('Error loading cart from server:', error);
            this.items = [];
            this.updateCartUI();
        }
    }
    
    async addItem(item) {
        try {
            const formData = new FormData();
            formData.append('template_id', item.id);
            
            const response = await fetch('cart-api.php?action=add', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Refresh cart from server để đảm bảo đồng bộ
                await this.loadCartFromServer();
                // Xóa thông báo - chỉ highlight button
                this.highlightCartButton();
            } else {
                if (data.redirect) {
                    // User not logged in, redirect to auth
                    window.location.href = data.redirect + '?redirect=' + encodeURIComponent(window.location.href);
                    return;
                }
                
                // Xóa tất cả thông báo - chỉ highlight button cho biết đã click
                this.highlightCartButton();
            }
        } catch (error) {
            console.error('Error adding item to cart:', error);
            // Xóa thông báo lỗi - chỉ log console
        }
    }
    
    async removeItem(itemId) {
        try {
            const formData = new FormData();
            formData.append('template_id', itemId);
            
            const response = await fetch('cart-api.php?action=remove', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Refresh cart from server - xóa thông báo
                await this.loadCartFromServer();
            } else {
                // Xóa thông báo lỗi - chỉ log console
                console.error('Failed to remove item:', data.error);
            }
        } catch (error) {
            console.error('Error removing item from cart:', error);
            // Xóa thông báo lỗi - chỉ log console
        }
    }
    
    async clearCart() {
        try {
            const response = await fetch('cart-api.php?action=clear', {
                method: 'POST'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.items = [];
                this.updateCartUI();
                this.closeCartDropdown();
                // Xóa thông báo - chỉ cập nhật UI
            } else {
                // Xóa thông báo lỗi - chỉ log console
                console.error('Failed to clear cart:', data.error);
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            // Xóa thông báo lỗi - chỉ log console
        }
    }
    
    updateCartUI() {
        const cartBadge = document.getElementById('cartBadge');
        const cartItems = document.getElementById('cartItems');
        const cartFooter = document.getElementById('cartFooter');
        const cartTotal = document.getElementById('cartTotal');
        
        // Update badge
        if (cartBadge) {
            if (this.items.length > 0) {
                cartBadge.textContent = this.items.length;
                cartBadge.style.transform = 'scale(1)';
            } else {
                cartBadge.style.transform = 'scale(0)';
            }
        }
        
        // Update cart content
        if (cartItems && cartFooter && cartTotal) {
            if (this.items.length === 0) {
                cartFooter.style.display = 'none';
                cartItems.innerHTML = `
                    <div class="text-center py-8">
                        <i class="ri-shopping-cart-line text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Your cart is empty</p>
                        <p class="text-sm text-gray-400 mt-1">Add some templates to get started!</p>
                    </div>
                `;
            } else {
                cartFooter.style.display = 'block';
                
                // Calculate total
                const total = this.items.reduce((sum, item) => sum + parseFloat(item.price), 0);
                cartTotal.textContent = `$${total.toFixed(2)}`;
                
                // Render cart items
                cartItems.innerHTML = this.items.map(item => `
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <img src="${this.escapeHtml(item.image)}" alt="${this.escapeHtml(item.title)}" class="w-12 h-12 rounded-lg object-cover">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-sm text-gray-900 truncate">${this.escapeHtml(item.title)}</h4>
                            <p class="text-xs text-gray-500">by ${this.escapeHtml(item.seller)}</p>
                            <p class="text-sm font-semibold text-primary">$${parseFloat(item.price).toFixed(2)}</p>
                        </div>
                        <button onclick="cart.removeItem('${item.id}')" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
                            <i class="ri-close-line text-sm"></i>
                        </button>
                    </div>
                `).join('');
            }
        }
    }
    
    toggleCartDropdown() {
        const cartDropdown = document.getElementById('cartDropdown');
        if (cartDropdown) {
            const isVisible = cartDropdown.style.opacity === '1';
            if (isVisible) {
                this.closeCartDropdown();
            } else {
                this.openCartDropdown();
            }
        }
    }
    
    openCartDropdown() {
        const cartDropdown = document.getElementById('cartDropdown');
        if (cartDropdown) {
            cartDropdown.style.opacity = '1';
            cartDropdown.style.visibility = 'visible';
            cartDropdown.style.transform = 'translateY(0)';
        }
    }
    
    closeCartDropdown() {
        const cartDropdown = document.getElementById('cartDropdown');
        if (cartDropdown) {
            cartDropdown.style.opacity = '0';
            cartDropdown.style.visibility = 'hidden';
            cartDropdown.style.transform = 'translateY(10px)';
        }
    }
    
    highlightCartButton() {
        const cartButton = document.getElementById('cartButton');
        if (cartButton) {
            cartButton.style.transform = 'scale(1.1)';
            cartButton.style.background = 'rgba(255, 95, 31, 0.1)';
            
            setTimeout(() => {
                cartButton.style.transform = 'scale(1)';
                cartButton.style.background = '';
            }, 300);
        }
    }
    
    showNotification(message, type = 'info') {
        // Use existing toast system if available
        if (window.toast) {
            window.toast[type](message, {
                duration: 3000,
                position: 'top-right'
            });
        } else {
            // Fallback notification
            alert(message);
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize shopping cart
const cart = new ShoppingCart();

// Global function to add items to cart (can be called from template cards)
window.addToCart = function(templateData) {
    cart.addItem(templateData);
};

// Global functions for cart buttons
window.viewCart = function() {
    // Close the cart dropdown first
    cart.closeCartDropdown();
    
    // Navigate to cart page - we'll create this page
    window.location.href = 'cart.php';
};

window.proceedToCheckout = function() {
    // Check if cart has items
    if (cart.items.length === 0) {
        if (window.toast) {
            window.toast.warning('Your cart is empty. Add some templates first!', {
                duration: 3000,
                position: 'top-right'
            });
        } else {
            alert('Your cart is empty. Add some templates first!');
        }
        return;
    }
    
    // Close the cart dropdown first
    cart.closeCartDropdown();
    
    // Navigate to checkout page - we'll create this page
    window.location.href = 'checkout.php';
};
<?php endif; ?>
</script>