<?php
/**
 * User Profile Page - Universal for all user types
 * Modern glassmorphism design matching auth page style
 */

session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$user_id = $_SESSION['user_id'];

// Get database connection and user data
try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get user information with stats
    $stmt = $pdo->prepare("
        SELECT u.*, 
               COALESCE(COUNT(DISTINCT CASE WHEN u.user_type = 'seller' THEN t.id END), 0) as total_templates,
               COALESCE(COUNT(DISTINCT CASE WHEN u.user_type = 'seller' THEN s.id END), 0) as total_services,
               COALESCE(COUNT(DISTINCT o.id), 0) as total_orders,
               COALESCE(SUM(CASE WHEN t.status = 'approved' AND u.user_type = 'seller' THEN t.downloads_count ELSE 0 END), 0) as total_downloads,
               COALESCE(AVG(CASE WHEN r.rating > 0 AND u.user_type = 'seller' THEN r.rating END), 0) as avg_rating,
               COALESCE(COUNT(DISTINCT r.id), 0) as total_reviews,
               COALESCE(COUNT(DISTINCT f.id), 0) as total_favorites
        FROM users u
        LEFT JOIN templates t ON u.id = t.seller_id
        LEFT JOIN services s ON u.id = s.seller_id  
        LEFT JOIN orders o ON u.id = o.user_id
        LEFT JOIN reviews r ON t.id = r.template_id
        LEFT JOIN favorites f ON u.id = f.user_id
        WHERE u.id = ?
        GROUP BY u.id
    ");
    $stmt->execute([$user_id]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userData) {
        header('Location: auth.php');
        exit();
    }
    
    // Ensure all stats have default values (safety net)
    $userData['total_templates'] = $userData['total_templates'] ?? 0;
    $userData['total_services'] = $userData['total_services'] ?? 0;
    $userData['total_orders'] = $userData['total_orders'] ?? 0;
    $userData['total_downloads'] = $userData['total_downloads'] ?? 0;
    $userData['avg_rating'] = $userData['avg_rating'] ?? 0;
    $userData['total_reviews'] = $userData['total_reviews'] ?? 0;
    $userData['total_favorites'] = $userData['total_favorites'] ?? 0;
    
    // Helper function to safely format numbers
    function safeNumberFormat($value, $decimals = 0) {
        return number_format(floatval($value ?? 0), $decimals);
    }
    
    // Get recent activity based on user type
    $recentActivity = [];
    if ($userData['user_type'] === 'seller') {
        // Get recent orders for seller
        $stmt = $pdo->prepare("
            SELECT 'order' as type, o.order_number as title, o.total_amount as amount, 
                   o.status, o.created_at, CONCAT(u.first_name, ' ', u.last_name) as customer_name
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN templates t ON oi.template_id = t.id
            JOIN users u ON o.user_id = u.id
            WHERE t.seller_id = ?
            ORDER BY o.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Get recent orders for buyer
        $stmt = $pdo->prepare("
            SELECT 'purchase' as type, t.title, o.total_amount as amount,
                   o.status, o.created_at, o.order_number
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN templates t ON oi.template_id = t.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (Exception $e) {
    error_log("Profile page error: " . $e->getMessage());
    // Fallback data
    $userData = [
        'first_name' => 'User',
        'last_name' => '',
        'email' => $_SESSION['user_email'] ?? '',
        'user_type' => $_SESSION['user_type'] ?? 'buyer',
        'total_templates' => 0,
        'total_services' => 0,
        'total_orders' => 0,
        'total_downloads' => 0,
        'avg_rating' => 0,
        'total_reviews' => 0,
        'total_favorites' => 0
    ];
    $recentActivity = [];
}

// Include header
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/profile.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1D',
                        secondary: '#1f2937'
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

<body class="font-inter gradient-bg">
    <!-- Background decorative elements - same as auth page -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="floating-animation absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="floating-animation absolute top-40 right-32 w-24 h-24 bg-white/20 rounded-full blur-lg" style="animation-delay: -2s;"></div>
        <div class="floating-animation absolute bottom-32 left-40 w-40 h-40 bg-white/5 rounded-full blur-2xl" style="animation-delay: -4s;"></div>
        <div class="floating-animation absolute bottom-20 right-20 w-28 h-28 bg-white/15 rounded-full blur-xl" style="animation-delay: -6s;"></div>
    </div>

    <div class="main-container relative z-10">
        <div class="max-w-6xl mx-auto px-6">
            
            <!-- Profile Header Card -->
            <div class="glass-card rounded-3xl p-8 mb-8">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-8">
                    <!-- Profile Avatar -->
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full overflow-hidden avatar-glow border-4 border-white/30">
                            <?php if (!empty($userData['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($userData['profile_image']) ?>" 
                                     alt="Profile" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-primary to-orange-600 flex items-center justify-center">
                                    <span class="text-4xl font-bold text-white text-shadow">
                                        <?= strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1)) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- User Type Badge -->
                        <div class="absolute -bottom-2 -right-2">
                            <div class="bg-white text-primary px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                <?= ucfirst($userData['user_type']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Info -->
                    <div class="flex-1 text-center lg:text-left">
                        <h1 class="text-4xl lg:text-5xl font-bold text-white mb-3 text-shadow">
                            <?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?>
                        </h1>
                        <p class="text-white/90 text-lg mb-6 text-shadow">
                            <?= htmlspecialchars($userData['email']) ?>
                        </p>
                        
                        <!-- Status Badges -->
                        <div class="flex flex-wrap justify-center lg:justify-start gap-3 mb-8">
                            <span class="status-badge bg-white/20 text-white px-4 py-2 rounded-full text-sm font-medium">
                                <i class="ri-calendar-line mr-2"></i>
                                Joined <?= date('M Y', strtotime($userData['created_at'] ?? 'now')) ?>
                            </span>
                            
                            <?php if ($userData['email_verified']): ?>
                            <span class="status-badge bg-green-500/20 text-green-100 px-4 py-2 rounded-full text-sm font-medium">
                                <i class="ri-verified-badge-fill mr-2"></i>
                                Verified
                            </span>
                            <?php endif; ?>
                            
                            <?php if ($userData['user_type'] === 'seller' && $userData['avg_rating'] > 0): ?>
                            <span class="status-badge bg-yellow-500/20 text-yellow-100 px-4 py-2 rounded-full text-sm font-medium">
                                <i class="ri-star-fill mr-2"></i>
                                <?= safeNumberFormat($userData['avg_rating'], 1) ?> Rating
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                            <button class="bg-white text-primary px-6 py-3 rounded-xl font-semibold">
                                <i class="ri-settings-line mr-2"></i>
                                Edit Profile
                            </button>
                            
                            <?php if ($userData['user_type'] === 'buyer'): ?>
                            <a href="seller-channel.php" class="glass-effect text-white px-6 py-3 rounded-xl font-medium inline-flex items-center">
                                <i class="ri-store-line mr-2"></i>
                                Become a Seller
                            </a>
                            <?php else: ?>
                            <a href="seller-channel.php" class="glass-effect text-white px-6 py-3 rounded-xl font-medium inline-flex items-center">
                                <i class="ri-dashboard-line mr-2"></i>
                                Seller Dashboard
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <?php if ($userData['user_type'] === 'seller'): ?>
                <!-- Seller Stats -->
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-layout-grid-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_templates']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Templates</div>
                </div>
                
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-tools-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_services']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Services</div>
                </div>
                
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-download-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_downloads']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Downloads</div>
                </div>
                
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-star-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_reviews']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Reviews</div>
                </div>
                <?php else: ?>
                <!-- Buyer Stats -->
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-shopping-bag-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_orders']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Orders</div>
                </div>
                
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-heart-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_favorites']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Favorites</div>
                </div>
                
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-download-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_downloads']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Downloads</div>
                </div>
                
                <div class="glass-card-light rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-star-line text-2xl text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2 text-shadow"><?= safeNumberFormat($userData['total_reviews']) ?></div>
                    <div class="text-white/80 text-sm font-medium">Reviews Given</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Content Grid -->
            <div class="grid lg:grid-cols-3 gap-8 mb-8">
                <!-- Recent Activity -->
                <div class="lg:col-span-2">
                    <div class="glass-card rounded-3xl p-8">
                        <h2 class="text-2xl font-bold text-white mb-8 flex items-center text-shadow">
                            <i class="ri-time-line mr-3"></i>
                            Recent Activity
                        </h2>
                        
                        <div class="space-y-4 custom-scrollbar max-h-96 overflow-y-auto">
                            <?php if (!empty($recentActivity)): ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                <div class="glass-effect rounded-xl p-5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                                <i class="ri-<?= $activity['type'] === 'order' ? 'shopping-bag' : 'download' ?>-line text-white"></i>
                                            </div>
                                            <div>
                                                <div class="text-white font-semibold text-shadow">
                                                    <?php if ($userData['user_type'] === 'seller'): ?>
                                                        New order: <?= htmlspecialchars($activity['title'] ?? 'Order') ?>
                                                    <?php else: ?>
                                                        Purchase: <?= htmlspecialchars($activity['title'] ?? 'Template') ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-white/70 text-sm">
                                                    <?= date('M j, Y', strtotime($activity['created_at'])) ?>
                                                    <?php if ($userData['user_type'] === 'seller' && isset($activity['customer_name'])): ?>
                                                        • <?= htmlspecialchars($activity['customer_name']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-white font-bold text-shadow">
                                                $<?= safeNumberFormat($activity['amount'] ?? 0, 2) ?>
                                            </div>
                                            <span class="status-badge <?= $activity['status'] === 'completed' ? 'status-completed' : 'status-pending' ?> px-3 py-1 rounded-full text-xs font-medium">
                                                <?= ucfirst($activity['status'] ?? 'pending') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="ri-inbox-line text-2xl text-white"></i>
                                    </div>
                                    <p class="text-white/80 text-lg text-shadow">No recent activity</p>
                                    <p class="text-white/60 text-sm mt-2">Your activity will appear here</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Sidebar -->
                <div class="space-y-6">
                    <!-- Account Settings -->
                    <div class="glass-card-light rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center text-shadow">
                            <i class="ri-settings-line mr-2"></i>
                            Account Settings
                        </h3>
                        
                        <div class="space-y-3">
                            <button class="w-full glass-effect text-white p-3 rounded-xl font-medium text-left flex items-center">
                                <i class="ri-user-line mr-3"></i>
                                Edit Profile
                            </button>
                            <button class="w-full glass-effect text-white p-3 rounded-xl font-medium text-left flex items-center">
                                <i class="ri-lock-line mr-3"></i>
                                Change Password
                            </button>
                            <button class="w-full glass-effect text-white p-3 rounded-xl font-medium text-left flex items-center">
                                <i class="ri-notification-line mr-3"></i>
                                Notifications
                            </button>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="glass-card-light rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center text-shadow">
                            <i class="ri-rocket-line mr-2"></i>
                            Quick Actions
                        </h3>
                        
                        <div class="space-y-3">
                            <?php if ($userData['user_type'] === 'seller'): ?>
                            <a href="seller-channel.php" class="block w-full glass-effect text-white p-3 rounded-xl font-medium text-left">
                                <i class="ri-dashboard-line mr-3"></i>
                                Seller Dashboard
                            </a>
                            <a href="#" class="block w-full glass-effect text-white p-3 rounded-xl font-medium text-left">
                                <i class="ri-add-line mr-3"></i>
                                Add New Product
                            </a>
                            <?php else: ?>
                            <a href="templates.php" class="block w-full glass-effect text-white p-3 rounded-xl font-medium text-left">
                                <i class="ri-search-line mr-3"></i>
                                Browse Templates
                            </a>
                            <a href="services.php" class="block w-full glass-effect text-white p-3 rounded-xl font-medium text-left">
                                <i class="ri-tools-line mr-3"></i>
                                Browse Services
                            </a>
                            <?php endif; ?>
                            
                            <a href="support.php" class="block w-full glass-effect text-white p-3 rounded-xl font-medium text-left">
                                <i class="ri-customer-service-line mr-3"></i>
                                Get Support
                            </a>
                        </div>
                    </div>

                    <!-- Account Stats Summary -->
                    <div class="glass-card-light rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center text-shadow">
                            <i class="ri-bar-chart-line mr-2"></i>
                            Account Summary
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-white/80">Member since</span>
                                <span class="text-white font-semibold text-shadow">
                                    <?= date('M Y', strtotime($userData['created_at'] ?? 'now')) ?>
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-white/80">Account type</span>
                                <span class="text-white font-semibold text-shadow capitalize">
                                    <?= $userData['user_type'] ?>
                                </span>
                            </div>
                            
                            <?php if ($userData['user_type'] === 'seller'): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-white/80">Products</span>
                                <span class="text-white font-semibold text-shadow">
                                    <?= intval($userData['total_templates'] ?? 0) + intval($userData['total_services'] ?? 0) ?>
                                </span>
                            </div>
                            <?php else: ?>
                            <div class="flex justify-between items-center">
                                <span class="text-white/80">Purchases</span>
                                <span class="text-white font-semibold text-shadow">
                                    <?= $userData['total_orders'] ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-white/80">Status</span>
                                <span class="text-green-300 font-semibold text-shadow">
                                    <?= $userData['email_verified'] ? 'Verified' : 'Pending' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>