<?php
session_start();
require_once '../config/database.php';
require_once '../config/seller-manager.php';

// Check if user is logged in and is a seller
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: auth.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Seller';

// Get database connection and initialize SellerManager
try {
    $pdo = DatabaseConfig::getConnection();
    $sellerManager = new SellerManager($pdo);
    
    // Get seller statistics
    $stats = $sellerManager->getSellerStats($user_id);
    $monthlyEarnings = $sellerManager->getMonthlyEarnings($user_id, 6); // Last 6 months
    $recentOrders = $sellerManager->getRecentOrders($user_id, 5);
} catch (Exception $e) {
    error_log("Profile page error: " . $e->getMessage());
    $stats = [
        'total_templates' => 0, 'approved_templates' => 0, 'pending_templates' => 0,
        'total_services' => 0, 'active_services' => 0,
        'total_products' => 0, 'total_earnings' => 0, 'total_orders' => 0,
        'template_downloads' => 0, 'service_orders' => 0, 'avg_rating' => 0,
        'template_views' => 0, 'service_views' => 0
    ];
    $monthlyEarnings = [];
    $recentOrders = [];
}

// Get current section from URL parameter
$current_section = $_GET['section'] ?? 'overview';

// Determine which section to show
$section = $_GET['section'] ?? 'overview';
$allowedSections = ['overview', 'products', 'services', 'orders', 'earnings', 'messages', 'reviews', 'analytics', 'settings'];

if (!in_array($section, $allowedSections)) {
    $section = 'overview';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B35',
                        secondary: '#2C3E50',
                        accent: '#F39C12'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="../public/index.php" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">O</span>
                        </div>
                        <span class="text-xl font-bold text-secondary">Orbix Market</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-primary">
                        <i class="ri-notification-line text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                    </button>
                    
                    <!-- Messages -->
                    <button class="relative p-2 text-gray-600 hover:text-primary">
                        <i class="ri-message-line text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 rounded-full text-xs text-white flex items-center justify-center">2</span>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                            <div class="w-8 h-8 bg-gradient-to-r from-primary to-accent rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-sm"><?= strtoupper(substr($userData['first_name'], 0, 1)) ?></span>
                            </div>
                            <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($userData['first_name']) ?></span>
                            <i class="ri-arrow-down-s-line text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="?section=settings" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="ri-settings-line mr-3"></i>Settings
                                </a>
                                <a href="../public/index.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="ri-home-line mr-3"></i>Marketplace
                                </a>
                                <hr class="my-2">
                                <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="ri-logout-line mr-3"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm border-r">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-12 h-12 bg-gradient-to-r from-primary to-accent rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold"><?= strtoupper(substr($userData['first_name'], 0, 1)) ?></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-secondary"><?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?></h3>
                        <p class="text-sm text-gray-600">Seller Dashboard</p>
                    </div>
                </div>

                <nav class="space-y-2">
                    <a href="?section=overview" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'overview' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-dashboard-line mr-3"></i>Overview
                    </a>
                    <a href="?section=products" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'products' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-grid-line mr-3"></i>My Products
                    </a>
                    <a href="?section=services" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'services' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-tools-line mr-3"></i>My Services
                    </a>
                    <a href="?section=orders" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'orders' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-shopping-bag-line mr-3"></i>Orders
                    </a>
                    <a href="?section=earnings" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'earnings' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-money-dollar-circle-line mr-3"></i>Earnings
                    </a>
                    <a href="?section=messages" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'messages' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-message-line mr-3"></i>Messages
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">2</span>
                    </a>
                    <a href="?section=reviews" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'reviews' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-star-line mr-3"></i>Reviews
                    </a>
                    <a href="?section=analytics" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'analytics' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-bar-chart-line mr-3"></i>Analytics
                    </a>
                    <a href="?section=settings" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl <?= $section === 'settings' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                        <i class="ri-settings-line mr-3"></i>Settings
                    </a>
                </nav>

                <!-- Quick Stats -->
                <div class="mt-8 p-4 bg-gradient-to-r from-primary/5 to-accent/5 rounded-xl">
                    <h4 class="font-semibold text-secondary mb-3">Quick Stats</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Earnings</span>
                            <span class="font-semibold text-primary">$<?= number_format($sellerStats['total_earnings']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Products</span>
                            <span class="font-semibold"><?= $sellerStats['total_products'] ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Rating</span>
                            <span class="font-semibold text-yellow-600"><?= number_format($sellerStats['avg_rating'], 1) ?>★</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <?php
                // Load the appropriate section
                $sectionFile = __DIR__ . "/sections/seller-{$section}.php";
                if (file_exists($sectionFile)) {
                    include $sectionFile;
                } else {
                    echo '<div class="text-center py-16">';
                    echo '<div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">';
                    echo '<i class="ri-file-line text-2xl text-gray-400"></i>';
                    echo '</div>';
                    echo '<h3 class="text-lg font-semibold text-gray-600 mb-2">Section Not Found</h3>';
                    echo '<p class="text-gray-500">The requested section is not available.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Include all modals -->
    <?php include 'modals/add-product-modal.php'; ?>
    <?php include 'modals/edit-product-modal.php'; ?>
    <?php include 'modals/order-details-modal.php'; ?>
    <?php include 'modals/message-modal.php'; ?>
    <?php include 'modals/notification-modal.php'; ?>
    <?php include 'modals/promote-modal.php'; ?>

    <!-- Scripts -->
    <script src="../assets/js/seller-dashboard.js"></script>
    <script src="../assets/js/components/toast-notification.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Initialize charts if on overview section
        <?php if ($section === 'overview'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Earnings Chart
            const ctx = document.getElementById('earningsChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Earnings',
                            data: [1200, 1900, 3000, 5000, 2000, 3000, 4500, 3200, 4800, 6200, 5800, 7200],
                            borderColor: '#FF6B35',
                            backgroundColor: 'rgba(255, 107, 53, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>