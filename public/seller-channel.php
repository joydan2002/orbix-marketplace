<?php
/**
 * Professional Seller Channel Platform
 * Complete marketplace for templates and services with analytics and payment system
 * Similar to Shopify functionality
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to check if user is already logged in
session_start();

// Check if user is logged in and is a seller - if so, show dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'seller') {
    // User is logged in as seller, show dashboard
    require_once '../config/database.php';
    require_once '../includes/header.php';
    
    // Get seller data
    $seller_id = $_SESSION['user_id'];
    $seller_name = $_SESSION['first_name'] ?? 'Seller';
    
    ?>
    <style>
        .dashboard-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .content-area {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .nav-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .nav-item:hover, .nav-item.active {
            background-color: #FF5F1F;
            color: white;
        }
    </style>
    
    <div class="dashboard-container pt-20">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <div class="sidebar w-64 p-6">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Xin chào, <?php echo htmlspecialchars($seller_name); ?>!</h2>
                    <p class="text-gray-600">Seller Dashboard</p>
                </div>
                
                <nav class="space-y-2">
                    <div class="nav-item active rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('overview')">
                        <i class="ri-dashboard-line text-xl"></i>
                        <span>Tổng quan</span>
                    </div>
                    <div class="nav-item rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('products')">
                        <i class="ri-store-line text-xl"></i>
                        <span>Sản phẩm</span>
                    </div>
                    <div class="nav-item rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('orders')">
                        <i class="ri-shopping-cart-line text-xl"></i>
                        <span>Đơn hàng</span>
                    </div>
                    <div class="nav-item rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('analytics')">
                        <i class="ri-bar-chart-line text-xl"></i>
                        <span>Thống kê</span>
                    </div>
                    <div class="nav-item rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('earnings')">
                        <i class="ri-money-dollar-circle-line text-xl"></i>
                        <span>Thu nhập</span>
                    </div>
                    <div class="nav-item rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('messages')">
                        <i class="ri-message-line text-xl"></i>
                        <span>Tin nhắn</span>
                    </div>
                    <div class="nav-item rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('reviews')">
                        <i class="ri-star-line text-xl"></i>
                        <span>Đánh giá</span>
                    </div>
                    <div class="nav-item rounded-lg px-4 py-3 flex items-center space-x-3" onclick="loadSection('settings')">
                        <i class="ri-settings-line text-xl"></i>
                        <span>Cài đặt</span>
                    </div>
                </nav>
                
                <div class="mt-8 pt-8 border-t">
                    <a href="logout.php" class="flex items-center space-x-3 text-red-600 hover:text-red-700 px-4 py-3 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="ri-logout-box-line text-xl"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="flex-1 p-6">
                <div class="content-area rounded-2xl p-8 min-h-full">
                    <div id="dashboard-content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Load dashboard sections
        function loadSection(section) {
            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.closest('.nav-item').classList.add('active');
            
            // Load section content
            const content = document.getElementById('dashboard-content');
            
            // Show loading
            content.innerHTML = '<div class="text-center py-12"><i class="ri-loader-line text-4xl text-primary animate-spin"></i><p class="mt-4 text-gray-600">Đang tải...</p></div>';
            
            // Load section via AJAX
            fetch(`sections/seller-${section}.php`)
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(error => {
                    content.innerHTML = '<div class="text-center py-12 text-red-600"><i class="ri-error-warning-line text-4xl"></i><p class="mt-4">Có lỗi xảy ra khi tải nội dung</p></div>';
                    console.error('Error loading section:', error);
                });
        }
        
        // Load overview by default
        document.addEventListener('DOMContentLoaded', function() {
            loadSection('overview');
        });
    </script>
    
    <?php
    require_once '../includes/footer.php';
    exit(); // Exit here to prevent showing the intro page
}

// If not a seller or not logged in, show the intro page
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in, can still view the seller channel page
    // but won't see personalized content
}

// Optional: If you want to restrict access only to sellers and logged-in users
// Uncomment the lines below:
/*
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] !== 'seller') {
    // User is logged in but not a seller, redirect to main page with error
    header('Location: index.php?error=access_denied');
    exit();
}
*/

try {
    require_once '../config/database.php';
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if config files exist before requiring them
if (file_exists('../config/service-manager.php')) {
    require_once '../config/service-manager.php';
}

if (file_exists('../config/seller-manager.php')) {
    require_once '../config/seller-manager.php';
}

// Get top sellers data for Success Stories section
$topSellers = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->prepare("
            SELECT 
                u.id,
                u.first_name,
                u.last_name,
                u.profile_image,
                COALESCE(COUNT(DISTINCT s.id), 0) + COALESCE(COUNT(DISTINCT t.id), 0) as total_products,
                COALESCE(SUM(CASE WHEN so.status = 'completed' THEN 1 ELSE 0 END), 0) + 
                COALESCE(SUM(CASE WHEN to_orders.status = 'completed' THEN 1 ELSE 0 END), 0) as total_sales,
                COALESCE(AVG(sr.rating), 0) as avg_rating,
                COALESCE(SUM(CASE WHEN so.status = 'completed' THEN s.price ELSE 0 END), 0) + 
                COALESCE(SUM(CASE WHEN to_orders.status = 'completed' THEN t.price ELSE 0 END), 0) as total_earnings
            FROM users u
            LEFT JOIN services s ON u.id = s.seller_id AND s.status = 'active'
            LEFT JOIN templates t ON u.id = t.seller_id AND t.status = 'active'
            LEFT JOIN service_orders so ON s.id = so.service_id
            LEFT JOIN template_orders to_orders ON t.id = to_orders.template_id
            LEFT JOIN service_reviews sr ON s.id = sr.service_id
            WHERE u.user_type = 'seller' AND u.status = 'active'
            GROUP BY u.id, u.first_name, u.last_name, u.profile_image
            HAVING total_products > 0
            ORDER BY total_earnings DESC, total_sales DESC
            LIMIT 8
        ");
        $stmt->execute();
        $topSellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Database query failed: " . $e->getMessage());
}

// If no real sellers or database error, create demo data
if (empty($topSellers)) {
    $topSellers = [
        [
            'id' => 1,
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'profile_image' => null,
            'total_products' => 45,
            'total_sales' => 1250,
            'avg_rating' => 4.9,
            'total_earnings' => 28500
        ],
        [
            'id' => 2,
            'first_name' => 'Mike',
            'last_name' => 'Chen',
            'profile_image' => null,
            'total_products' => 32,
            'total_sales' => 890,
            'avg_rating' => 4.8,
            'total_earnings' => 21200
        ],
        [
            'id' => 3,
            'first_name' => 'Emma',
            'last_name' => 'Davis',
            'profile_image' => null,
            'total_products' => 28,
            'total_sales' => 675,
            'avg_rating' => 4.7,
            'total_earnings' => 15800
        ],
        [
            'id' => 4,
            'first_name' => 'Alex',
            'last_name' => 'Rodriguez',
            'profile_image' => null,
            'total_products' => 38,
            'total_sales' => 920,
            'avg_rating' => 4.9,
            'total_earnings' => 19600
        ]
    ];
}

// Include the common header
require_once '../includes/header.php';
?>

<!-- Seller Channel Specific Styles -->
<style>
    .seller-hero-gradient {
        background: linear-gradient(135deg, #FF5F1F 0%, #FF8C42 50%, #FFB366 100%);
    }
    
    .success-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .success-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    .feature-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 95, 31, 0.1);
    }
    
    .feature-card:hover {
        border-color: rgba(255, 95, 31, 0.3);
        box-shadow: 0 10px 30px rgba(255, 95, 31, 0.1);
        transform: translateY(-4px);
    }
    
    .category-card {
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }
    
    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 1);
    }
    
    .step-number {
        background: linear-gradient(135deg, #FF5F1F 0%, #FF8C42 100%);
        box-shadow: 0 8px 25px rgba(255, 95, 31, 0.3);
    }
    
    .floating-animation {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
    }
</style>

<!-- Main Content -->
<main class="pt-20">
    <!-- Hero Section -->
    <section class="seller-hero-gradient text-white py-24 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-32 h-32 bg-white rounded-full floating-animation"></div>
            <div class="absolute top-40 right-32 w-20 h-20 bg-white rounded-full floating-animation" style="animation-delay: -2s;"></div>
            <div class="absolute bottom-32 left-1/3 w-24 h-24 bg-white rounded-full floating-animation" style="animation-delay: -4s;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <div class="mb-8">
                <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-6 py-2 rounded-full text-sm font-semibold mb-6">
                    🚀 Join 50,000+ Successful Sellers
                </span>
            </div>
            
            <h1 class="text-6xl md:text-7xl font-bold mb-8 leading-tight">
                Turn Your <span class="text-yellow-300">Creativity</span><br>
                Into <span class="text-yellow-300">Cash</span>
            </h1>
            
            <p class="text-xl md:text-2xl mb-12 opacity-90 max-w-3xl mx-auto leading-relaxed">
                Join the world's fastest-growing marketplace for digital templates and services. 
                Start earning from your skills today with zero upfront costs.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="auth.php?mode=signup&type=seller" 
                   class="bg-white text-primary px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center gap-3">
                    <i class="ri-rocket-line text-xl"></i>
                    Start Selling Now
                </a>
                <a href="#how-it-works" 
                   class="border-2 border-white text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-primary transition-all duration-300 transform hover:scale-105">
                    Learn How It Works
                </a>
            </div>
            
            <!-- Stats -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold mb-2">50K+</div>
                    <div class="text-white/80 text-sm md:text-base">Active Sellers</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold mb-2">$2M+</div>
                    <div class="text-white/80 text-sm md:text-base">Total Earnings</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold mb-2">95%</div>
                    <div class="text-white/80 text-sm md:text-base">Keep Earnings</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold mb-2">24/7</div>
                    <div class="text-white/80 text-sm md:text-base">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Orbix Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <span class="inline-block bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    Why Choose Us?
                </span>
                <h2 class="text-5xl font-bold text-secondary mb-6">Everything You Need to Succeed</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    We provide all the tools, support, and opportunities you need to build a thriving online business
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-2xl p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-money-dollar-circle-line text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-secondary">Keep 95% of Earnings</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Industry-leading commission rates mean more money in your pocket. No hidden fees or surprise charges.
                    </p>
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <span class="inline-block bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">
                            Only 5% Platform Fee
                        </span>
                    </div>
                </div>

                <div class="feature-card bg-white rounded-2xl p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-global-line text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-secondary">Global Marketplace</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Reach millions of customers worldwide. Our platform is available in 40+ countries and 15 languages.
                    </p>
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <span class="inline-block bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">
                            40+ Countries
                        </span>
                    </div>
                </div>

                <div class="feature-card bg-white rounded-2xl p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-tools-line text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-secondary">Powerful Analytics</h3>
                    <p class="text-gray-600 text-lg leading-relaxed">
                        Track your performance, understand your customers, and optimize your offerings with detailed insights.
                    </p>
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <span class="inline-block bg-purple-100 text-purple-700 px-4 py-2 rounded-full text-sm font-semibold">
                            Real-time Data
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <span class="inline-block bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    Success Stories
                </span>
                <h2 class="text-5xl font-bold text-secondary mb-6">Meet Our Top Performers</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Join thousands of successful sellers who are already building their dream businesses on Orbix
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach($topSellers as $seller): ?>
                <div class="success-card rounded-2xl p-8 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary to-accent rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-white font-bold text-2xl">
                            <?php echo strtoupper(substr($seller['first_name'], 0, 1) . substr($seller['last_name'], 0, 1)); ?>
                        </span>
                    </div>
                    
                    <h3 class="font-bold text-xl mb-3 text-secondary">
                        <?php echo htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']); ?>
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="font-bold text-lg text-primary"><?php echo number_format($seller['total_products']); ?></div>
                            <div class="text-xs text-gray-600">Products</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="font-bold text-lg text-primary"><?php echo number_format($seller['total_sales']); ?></div>
                            <div class="text-xs text-gray-600">Sales</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-2xl font-bold text-green-600 mb-1">
                            $<?php echo number_format($seller['total_earnings']); ?>
                        </div>
                        <div class="text-sm text-gray-600">Total Earned</div>
                    </div>
                    
                    <div class="flex items-center justify-center">
                        <div class="flex text-yellow-400 mr-2">
                            <?php for($i = 0; $i < 5; $i++): ?>
                                <i class="ri-star-<?php echo $i < floor($seller['avg_rating']) ? 'fill' : 'line'; ?> text-lg"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="text-sm font-semibold text-gray-700"><?php echo number_format($seller['avg_rating'], 1); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <span class="inline-block bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    Getting Started
                </span>
                <h2 class="text-5xl font-bold text-secondary mb-6">Start Selling in 3 Simple Steps</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Our streamlined process gets you up and running quickly, so you can focus on what you do best
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="step-number w-24 h-24 text-white rounded-full flex items-center justify-center mx-auto mb-8 text-3xl font-bold">1</div>
                    <h3 class="text-2xl font-bold mb-6 text-secondary">Create Your Profile</h3>
                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                        Sign up as a seller and build your professional profile. Showcase your skills, experience, and portfolio to attract customers.
                    </p>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-600">
                            <i class="ri-time-line"></i>
                            <span>Takes 5 minutes</span>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="step-number w-24 h-24 text-white rounded-full flex items-center justify-center mx-auto mb-8 text-3xl font-bold">2</div>
                    <h3 class="text-2xl font-bold mb-6 text-secondary">List Your Products</h3>
                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                        Upload your templates or create service offerings. Use our tools to create compelling descriptions and set competitive prices.
                    </p>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-600">
                            <i class="ri-upload-line"></i>
                            <span>Easy drag & drop</span>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="step-number w-24 h-24 text-white rounded-full flex items-center justify-center mx-auto mb-8 text-3xl font-bold">3</div>
                    <h3 class="text-2xl font-bold mb-6 text-secondary">Start Earning</h3>
                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                        Receive orders, deliver quality work, and build your reputation. Watch your earnings grow as you help customers achieve their goals.
                    </p>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-600">
                            <i class="ri-money-dollar-circle-line"></i>
                            <span>Weekly payouts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <span class="inline-block bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    Categories
                </span>
                <h2 class="text-5xl font-bold text-secondary mb-6">What Can You Sell?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Explore the diverse categories where you can showcase your talents and build your business
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="category-card rounded-2xl p-8 text-center cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-code-line text-3xl text-white"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-secondary">Web Templates</h3>
                    <p class="text-gray-600 mb-4">HTML, CSS, JavaScript templates for modern websites</p>
                    <div class="text-sm text-primary font-semibold">High Demand 🔥</div>
                </div>

                <div class="category-card rounded-2xl p-8 text-center cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-smartphone-line text-3xl text-white"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-secondary">Mobile Apps</h3>
                    <p class="text-gray-600 mb-4">iOS and Android app templates and UI kits</p>
                    <div class="text-sm text-primary font-semibold">Growing Fast 📈</div>
                </div>

                <div class="category-card rounded-2xl p-8 text-center cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-palette-line text-3xl text-white"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-secondary">Design Services</h3>
                    <p class="text-gray-600 mb-4">Logo design, branding, and UI/UX services</p>
                    <div class="text-sm text-primary font-semibold">Always Needed ⭐</div>
                </div>

                <div class="category-card rounded-2xl p-8 text-center cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="ri-megaphone-line text-3xl text-white"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-secondary">Digital Marketing</h3>
                    <p class="text-gray-600 mb-4">SEO, social media, and content marketing</p>
                    <div class="text-sm text-primary font-semibold">Top Earner 💰</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-24 bg-secondary text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px); background-size: 50px 50px;"></div>
        </div>
        
        <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
            <div class="mb-8">
                <span class="inline-block bg-primary/20 backdrop-blur-sm text-white px-6 py-3 rounded-full text-sm font-semibold mb-8">
                    🎯 Limited Time: No Setup Fees
                </span>
            </div>
            
            <h2 class="text-5xl md:text-6xl font-bold mb-8 leading-tight">
                Ready to Transform Your<br>
                <span class="text-primary">Skills Into Success?</span>
            </h2>
            
            <p class="text-xl md:text-2xl mb-12 opacity-90 max-w-4xl mx-auto leading-relaxed">
                Join over 50,000 entrepreneurs who have already built thriving businesses on Orbix. 
                Your journey to financial freedom starts with a single click.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mb-12">
                <a href="auth.php?mode=signup&type=seller" 
                   class="bg-primary hover:bg-primary/90 text-white px-12 py-5 rounded-xl font-bold text-xl transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center gap-3">
                    <i class="ri-rocket-line text-2xl"></i>
                    Start Your Journey Today
                </a>
                <a href="auth.php" 
                   class="text-white hover:text-primary transition-colors text-lg font-semibold flex items-center gap-2">
                    Already have an account? Sign in
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
            
            <!-- Trust Indicators -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto text-center">
                <div>
                    <div class="text-2xl mb-2">🛡️</div>
                    <div class="text-sm opacity-80">Secure Payments</div>
                </div>
                <div>
                    <div class="text-2xl mb-2">⚡</div>
                    <div class="text-sm opacity-80">Instant Setup</div>
                </div>
                <div>
                    <div class="text-2xl mb-2">💬</div>
                    <div class="text-sm opacity-80">24/7 Support</div>
                </div>
                <div>
                    <div class="text-2xl mb-2">🌟</div>
                    <div class="text-sm opacity-80">No Hidden Fees</div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>

<!-- Enhanced JavaScript for interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all feature cards and success cards
    document.querySelectorAll('.feature-card, .success-card, .category-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>

</body>
</html>