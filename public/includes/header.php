<?php
/**
 * Header Include File
 * Contains the HTML header section with navigation
 */

// Include database config only if DatabaseConfig class doesn't exist yet
if (!class_exists('DatabaseConfig')) {
    require_once __DIR__ . '/../config/database.php';
}

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
    
    <!-- Toast Notification System -->
    <script src="assets/js/toast.js"></script>
    <link rel="stylesheet" href="assets/css/header.css">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <!-- Logo Section -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Animated Hamburger Logo (Mobile Only) -->
                <div id="logoHamburger" class="logo-hamburger">
                    <!-- Logo Text -->
                    <span class="logo-text">O</span>
                    
                    <!-- Hamburger Lines -->
                    <div class="hamburger-lines">
                        <div class="hamburger-line"></div>
                        <div class="hamburger-line"></div>
                        <div class="hamburger-line"></div>
                    </div>
                </div>
                
                <!-- Mobile Site Name (Mobile Only) -->
                <span class="mobile-site-name" style="display: none;">Orbix Market</span>
                
                <!-- Original Logo (Desktop Only) -->
                <div class="original-logo flex items-center space-x-2 sm:space-x-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg sm:text-xl">O</span>
                    </div>
                    <span class="font-pacifico text-xl sm:text-2xl lg:text-3xl text-secondary">Orbix Market</span>
                </div>
            </div>
            
            <!-- Desktop Navigation -->
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
                <a href="seller-channel.php" class="text-secondary hover:text-primary transition-colors font-medium">Seller Channel</a>
                <a href="support.php" class="text-secondary hover:text-primary transition-colors font-medium">Support</a>
            </nav>
            
            <!-- Actions -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <?php if ($isLoggedIn): ?>
                    <!-- Shopping Cart -->
                    <div class="relative">
                        <button id="cartButton" class="relative p-2 rounded-xl hover:bg-white/10 transition-colors group">
                            <i class="ri-shopping-cart-line text-lg sm:text-xl text-secondary group-hover:text-primary transition-colors"></i>
                            <!-- Cart Badge -->
                            <span id="cartBadge" class="absolute -top-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center transform scale-0 transition-transform duration-200">
                                0
                            </span>
                        </button>
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
                        <button id="userDropdownButton" class="flex items-center space-x-2 sm:space-x-3 p-1 sm:p-2 rounded-xl hover:bg-white/10 transition-colors">
                            <!-- Avatar -->
                            <div class="relative avatar-ring">
                                <?php if (!empty($userData['profile_image'])): ?>
                                    <img src="<?= htmlspecialchars($userData['profile_image']) ?>" 
                                         alt="<?= htmlspecialchars($userData['first_name']) ?>" 
                                         class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-white/20">
                                <?php else: ?>
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-primary to-primary/80 rounded-full flex items-center justify-center border-2 border-white/20">
                                        <span class="text-white font-semibold text-xs sm:text-sm">
                                            <?= strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1)) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- User Info (Hidden on small mobile) -->
                            <div class="hidden sm:block text-left">
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
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div id="mobileMenu" class="mobile-menu">
    <div class="mobile-menu-content">
        <!-- Navigation Links -->
        <nav class="mobile-nav">
            <a href="index.php" class="mobile-menu-item">
                <i class="ri-home-line"></i>
                <span>Home</span>
            </a>
            <a href="services.php" class="mobile-menu-item">
                <i class="ri-service-line"></i>
                <span>Services</span>
            </a>
            <a href="templates.php" class="mobile-menu-item">
                <i class="ri-layout-line"></i>
                <span>Templates</span>
            </a>
            <a href="support.php" class="mobile-menu-item">
                <i class="ri-customer-service-line"></i>
                <span>Support</span>
            </a>
        </nav>

        <!-- User Section -->
        <div class="mobile-user-section">
            <?php if ($isLoggedIn && $userData): ?>
                <div class="mobile-user-info">
                    <img src="<?php echo htmlspecialchars($userData['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" 
                         alt="Avatar" class="mobile-avatar" onerror="this.src='assets/images/default-avatar.png'">
                    <div class="mobile-user-details">
                        <span class="mobile-username"><?php echo htmlspecialchars($userData['username'] ?? $userData['first_name'] ?? 'User'); ?></span>
                        <span class="mobile-user-role"><?php echo $userData['user_type'] === 'seller' ? 'Seller' : 'Customer'; ?></span>
                    </div>
                </div>
                <div class="mobile-user-actions">
                    <a href="profile.php" class="mobile-menu-item">
                        <i class="ri-user-line"></i>
                        <span>Profile</span>
                    </a>
                    <?php if ($userData['user_type'] === 'seller'): ?>
                        <a href="seller-channel.php" class="mobile-menu-item">
                            <i class="ri-store-line"></i>
                            <span>Seller Dashboard</span>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="mobile-menu-item text-red-500">
                        <i class="ri-logout-line"></i>
                        <span>Logout</span>
                    </a>
                </div>
            <?php else: ?>
                <div class="mobile-auth-actions">
                    <a href="auth.php" class="mobile-menu-item">
                        <i class="ri-login-line"></i>
                        <span>Login</span>
                    </a>
                    <a href="auth.php?mode=register" class="mobile-menu-item">
                        <i class="ri-user-add-line"></i>
                        <span>Register</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cart Section -->
        <div class="mobile-cart-section">
            <a href="cart.php" class="mobile-menu-item">
                <i class="ri-shopping-cart-line"></i>
                <span>Cart</span>
                <?php 
                // Get cart count if logged in
                $cartCount = 0;
                if ($isLoggedIn && $userData) {
                    try {
                        $cartStmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
                        $cartStmt->execute([$userData['id']]);
                        $cartCount = $cartStmt->fetchColumn();
                    } catch (Exception $e) {
                        $cartCount = 0;
                    }
                }
                if ($cartCount > 0): ?>
                    <span class="mobile-cart-badge"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript for Header Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Enhanced dropdown functionality
        setupDropdowns();
        
        // Mobile menu toggle
        setupMobileMenu();
        
        // Smooth animations
        setupAnimations();
    } catch (error) {
        console.error('Error initializing header interactions:', error);
    }
});

function setupDropdowns() {
    try {
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
            
            // Event listeners cho container (chỉ desktop)
            if (window.innerWidth >= 1024) {
                userDropdownContainer.addEventListener('mouseenter', showDropdown);
                userDropdownContainer.addEventListener('mouseleave', hideDropdown);
                
                // Event listeners cho dropdown menu để không bị đóng khi hover vào menu
                userMenu.addEventListener('mouseenter', () => {
                    clearTimeout(userHideTimeout);
                });
                
                userMenu.addEventListener('mouseleave', hideDropdown);
            }
            
            // Click để toggle (cho mobile/touch devices)
            userDropdownButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('User dropdown button clicked');
                
                const isShown = userMenu.classList.contains('show');
                
                if (isShown) {
                    userMenu.classList.remove('show');
                } else {
                    userMenu.classList.add('show');
                }
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
    } catch (error) {
        console.error('Error in setupDropdowns:', error);
    }
}

function setupMobileMenu() {
    try {
        const logoHamburger = document.getElementById('logoHamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        
        console.log('Mobile menu elements:', {
            hamburger: logoHamburger,
            menu: mobileMenu
        });
        
        if (logoHamburger && mobileMenu) {
            let currentState = 0; // 0: logo, 1: hamburger
            let animationInterval;
            
            // Auto animation every 3 seconds (switch between logo and hamburger)
            function startAutoAnimation() {
                animationInterval = setInterval(() => {
                    if (!mobileMenu.classList.contains('show')) {
                        currentState = currentState === 0 ? 1 : 0;
                        updateHamburgerState();
                    }
                }, 3000);
            }
            
            function stopAutoAnimation() {
                if (animationInterval) {
                    clearInterval(animationInterval);
                    animationInterval = null;
                }
            }
            
            function updateHamburgerState() {
                // Remove all states
                logoHamburger.classList.remove('hamburger-mode', 'menu-open');
                
                if (currentState === 1) {
                    // Show hamburger lines
                    logoHamburger.classList.add('hamburger-mode');
                }
                // currentState === 0 shows logo by default
            }
            
            // Start auto animation
            startAutoAnimation();
            
            // Toggle mobile menu on click
            logoHamburger.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isShown = mobileMenu.classList.contains('show');
                
                if (isShown) {
                    // Hide menu
                    mobileMenu.classList.remove('show');
                    // Reset to logo state and restart animation
                    currentState = 0;
                    logoHamburger.classList.remove('hamburger-mode', 'menu-open');
                    startAutoAnimation();
                } else {
                    // Show menu
                    mobileMenu.classList.add('show');
                    // Stop animation and set to X state
                    stopAutoAnimation();
                    logoHamburger.classList.remove('hamburger-mode');
                    logoHamburger.classList.add('menu-open');
                }
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!logoHamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                    mobileMenu.classList.remove('show');
                    // Reset to logo state and restart animation
                    currentState = 0;
                    logoHamburger.classList.remove('hamburger-mode', 'menu-open');
                    startAutoAnimation();
                }
            });
            
            // Close menu when clicking on menu items
            const mobileMenuItems = mobileMenu.querySelectorAll('.mobile-menu-item');
            mobileMenuItems.forEach(item => {
                item.addEventListener('click', () => {
                    mobileMenu.classList.remove('show');
                    // Reset to logo state and restart animation
                    currentState = 0;
                    logoHamburger.classList.remove('hamburger-mode', 'menu-open');
                    startAutoAnimation();
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    mobileMenu.classList.remove('show');
                    // Reset to logo state and restart animation
                    currentState = 0;
                    logoHamburger.classList.remove('hamburger-mode', 'menu-open');
                    startAutoAnimation();
                    
                    // Hide dropdowns on desktop
                    const userMenu = document.querySelector('.user-dropdown');
                    const cartDropdown = document.getElementById('cartDropdown');
                    
                    if (userMenu) userMenu.classList.remove('show');
                    if (cartDropdown) {
                        cartDropdown.style.opacity = '0';
                        cartDropdown.style.visibility = 'hidden';
                        cartDropdown.style.transform = 'translateY(10px)';
                    }
                }
            });
        } else {
            console.error('Mobile menu elements not found:', {
                hamburger: !!logoHamburger,
                menu: !!mobileMenu
            });
        }
    } catch (error) {
        console.error('Error in setupMobileMenu:', error);
    }
}

function setupAnimations() {
    try {
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
    } catch (error) {
        console.error('Error in setupAnimations:', error);
    }
}

// Enhanced notification system for auth status
<?php if ($isLoggedIn): ?>
try {
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
            try {
                await this.loadCartFromServer();
                this.setupEventListeners();
            } catch (error) {
                console.error('Error initializing shopping cart:', error);
            }
        }
        
        setupEventListeners() {
            try {
                // Cart button click
                const cartButton = document.getElementById('cartButton');
                const cartDropdown = document.getElementById('cartDropdown');
                
                if (cartButton && cartDropdown) {
                    cartButton.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        try {
                            // Refresh cart data khi mở dropdown
                            await this.loadCartFromServer();
                            this.toggleCartDropdown();
                        } catch (error) {
                            console.error('Error handling cart button click:', error);
                        }
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
            } catch (error) {
                console.error('Error setting up cart event listeners:', error);
            }
        }
        
        async loadCartFromServer() {
            try {
                const response = await fetch('/api/cart.php?action=get');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.items = data.items.map(item => ({
                        id: item.id, // Now using normalized id field
                        title: item.title,
                        price: parseFloat(item.price),
                        image: item.preview_image,
                        seller: item.seller_name,
                        addedAt: item.added_at,
                        type: item.type // template or service
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
                
                const response = await fetch('/api/cart.php?action=add', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
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
        
        async removeItem(itemId, itemType = 'template') {
            try {
                const formData = new FormData();
                
                // Send appropriate ID field based on item type
                if (itemType === 'service') {
                    formData.append('service_id', itemId);
                } else {
                    formData.append('template_id', itemId);
                }
                
                const response = await fetch('/api/cart.php?action=remove', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
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
                const response = await fetch('/api/cart.php?action=clear', {
                    method: 'POST'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
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
            try {
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
                                <img src="${this.getOptimizedImageUrl(item.image, 'thumb')}" alt="${this.escapeHtml(item.title)}" class="w-12 h-12 rounded-lg object-cover" onerror="this.src='assets/images/default-service.jpg'">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm text-gray-900 truncate">${this.escapeHtml(item.title)}</h4>
                                    <p class="text-xs text-gray-500">by ${this.escapeHtml(item.seller)}</p>
                                    <p class="text-sm font-semibold text-primary">$${parseFloat(item.price).toFixed(2)}</p>
                                    <span class="text-xs text-gray-400 capitalize">${item.type || 'template'}</span>
                                </div>
                                <button onclick="cart.removeItem('${item.id}', '${item.type || 'template'}')" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
                                    <i class="ri-close-line text-sm"></i>
                                </button>
                            </div>
                        `).join('');
                    }
                }
            } catch (error) {
                console.error('Error updating cart UI:', error);
            }
        }
        
        toggleCartDropdown() {
            try {
                const cartDropdown = document.getElementById('cartDropdown');
                if (cartDropdown) {
                    const isVisible = cartDropdown.style.opacity === '1';
                    if (isVisible) {
                        this.closeCartDropdown();
                    } else {
                        this.openCartDropdown();
                    }
                }
            } catch (error) {
                console.error('Error toggling cart dropdown:', error);
            }
        }
        
        openCartDropdown() {
            try {
                const cartDropdown = document.getElementById('cartDropdown');
                const isMobile = window.innerWidth < 1024;
                
                if (cartDropdown) {
                    cartDropdown.style.opacity = '1';
                    cartDropdown.style.visibility = 'visible';
                    cartDropdown.style.transform = isMobile ? 'translateX(-50%) translateY(0)' : 'translateY(0)';
                }
            } catch (error) {
                console.error('Error opening cart dropdown:', error);
            }
        }
        
        closeCartDropdown() {
            try {
                const cartDropdown = document.getElementById('cartDropdown');
                const isMobile = window.innerWidth < 1024;
                
                if (cartDropdown) {
                    cartDropdown.style.opacity = '0';
                    cartDropdown.style.visibility = 'hidden';
                    cartDropdown.style.transform = isMobile ? 'translateX(-50%) translateY(10px)' : 'translateY(10px)';
                }
            } catch (error) {
                console.error('Error closing cart dropdown:', error);
            }
        }
        
        highlightCartButton() {
            try {
                const cartButton = document.getElementById('cartButton');
                if (cartButton) {
                    cartButton.style.transform = 'scale(1.1)';
                    cartButton.style.background = 'rgba(255, 95, 31, 0.1)';
                    
                    setTimeout(() => {
                        cartButton.style.transform = 'scale(1)';
                        cartButton.style.background = '';
                    }, 300);
                }
            } catch (error) {
                console.error('Error highlighting cart button:', error);
            }
        }
        
        showNotification(message, type = 'info') {
            try {
                // Use existing toast system if available
                if (window.toast) {
                    window.toast[type](message, {
                        duration: 3000,
                        position: 'top-right'
                    });
                } else {
                    // Fallback to toast
                    showError(message);
                }
            } catch (error) {
                console.error('Error showing notification:', error);
            }
        }
        
        escapeHtml(text) {
            try {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            } catch (error) {
                console.error('Error escaping HTML:', error);
                return String(text).replace(/[&<>"']/g, '');
            }
        }
        
        getOptimizedImageUrl(publicId, size = 'thumb') {
            try {
                if (!publicId) {
                    // Return default image based on size
                    const defaults = {
                        'thumb': 'assets/images/default-service.jpg',
                        'medium': 'assets/images/default-service.jpg', 
                        'avatar_small': 'assets/images/default-avatar.png',
                        'avatar_medium': 'assets/images/default-avatar.png',
                        'avatar_large': 'assets/images/default-avatar.png'
                    };
                    return defaults[size] || 'assets/images/default-service.jpg';
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
            } catch (error) {
                console.error('Error generating optimized image URL:', error);
                return 'assets/images/default-service.jpg';
            }
        }
    }

    // Initialize shopping cart
    const cart = new ShoppingCart();

    // Global function to add items to cart (can be called from template cards)
    window.addToCart = function(templateData) {
        try {
            cart.addItem(templateData);
        } catch (error) {
            console.error('Error in global addToCart function:', error);
        }
    };

    // Global functions for cart buttons
    window.viewCart = function() {
        try {
            // Close the cart dropdown first
            cart.closeCartDropdown();
            
            // Navigate to cart page - we'll create this page
            window.location.href = 'cart.php';
        } catch (error) {
            console.error('Error in viewCart function:', error);
        }
    };

    window.proceedToCheckout = function() {
        try {
            // Check if cart has items
            if (cart.items.length === 0) {
                if (window.toast) {
                    window.toast.warning('Your cart is empty. Add some templates first!', {
                        duration: 3000,
                        position: 'top-right'
                    });
                } else {
                    showWarning('Your cart is empty. Add some templates first!');
                }
                return;
            }
            
            // Close the cart dropdown first
            cart.closeCartDropdown();
            
            // Navigate to checkout page - we'll create this page
            window.location.href = 'checkout.php';
        } catch (error) {
            console.error('Error in proceedToCheckout function:', error);
        }
    };
} catch (error) {
    console.error('Error initializing cart system:', error);
}
<?php endif; ?>
</script>