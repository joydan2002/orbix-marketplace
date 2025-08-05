<?php
/**
 * Seller Channel Page
 * Central hub for sellers to manage their business and for buyers to become sellers
 */

require_once 'config/database.php';
require_once 'includes/header.php';

// Get current page from URL parameter
$page = $_GET['page'] ?? 'overview';

// Check if user is logged in and get seller data
$sellerData = null;
$sellerStats = null;
$isSeller = false;

if ($isLoggedIn) {
    $isSeller = ($userData['user_type'] === 'seller');
    
    if ($isSeller) {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            // Get seller statistics
            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(t.id) as total_templates,
                    COUNT(CASE WHEN t.status = 'approved' THEN 1 END) as approved_templates,
                    COUNT(CASE WHEN t.status = 'pending' THEN 1 END) as pending_templates,
                    COALESCE(SUM(t.downloads_count), 0) as total_downloads,
                    COALESCE(AVG(t.rating), 0) as average_rating,
                    COALESCE(SUM(t.views_count), 0) as total_views
                FROM templates t 
                WHERE t.seller_id = ?
            ");
            $stmt->execute([$userData['id']]);
            $sellerStats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get recent templates
            $stmt = $pdo->prepare("
                SELECT id, title, preview_image, price, status, downloads_count, views_count, rating, created_at
                FROM templates 
                WHERE seller_id = ? 
                ORDER BY created_at DESC 
                LIMIT 6
            ");
            $stmt->execute([$userData['id']]);
            $recentTemplates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $sellerStats = [
                'total_templates' => 0,
                'approved_templates' => 0, 
                'pending_templates' => 0,
                'total_downloads' => 0,
                'average_rating' => 0,
                'total_views' => 0
            ];
            $recentTemplates = [];
        }
    }
}

// Get top sellers for showcase
try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->query("
        SELECT 
            u.id, u.first_name, u.last_name, u.profile_image,
            COUNT(t.id) as template_count,
            COALESCE(SUM(t.downloads_count), 0) as total_downloads,
            COALESCE(AVG(t.rating), 0) as avg_rating
        FROM users u
        JOIN templates t ON u.id = t.seller_id AND t.status = 'approved'
        WHERE u.user_type = 'seller'
        GROUP BY u.id
        ORDER BY total_downloads DESC, avg_rating DESC
        LIMIT 8
    ");
    $topSellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $topSellers = [];
}
?>

<!-- Main Content -->
<main class="pt-20">
    <?php if (!$isLoggedIn): ?>
        <!-- Not Logged In - Show Seller Channel Overview -->
        <section class="py-20 bg-gradient-to-br from-primary/5 via-white to-primary/10">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-primary/10 rounded-full mb-6">
                        <i class="ri-store-line text-3xl text-primary"></i>
                    </div>
                    <h1 class="text-5xl font-bold text-secondary mb-6">
                        Join Our <span class="text-primary">Seller Community</span>
                    </h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                        Turn your creative skills into income. Sell templates, earn money, and build your digital business with thousands of customers worldwide.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="auth.php?mode=signup&type=seller" class="bg-primary text-white px-8 py-4 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                            <i class="ri-rocket-line mr-2"></i>
                            Start Selling Today
                        </a>
                        <a href="auth.php" class="text-primary border-2 border-primary px-8 py-4 rounded-xl font-semibold hover:bg-primary/5 transition-colors">
                            Already a Seller? Sign In
                        </a>
                    </div>
                </div>
                
                <!-- Benefits Grid -->
                <div class="grid md:grid-cols-3 gap-8 mb-16">
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                            <i class="ri-money-dollar-circle-line text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-4">Earn Passive Income</h3>
                        <p class="text-gray-600">Upload once, earn forever. Get up to 70% commission on every sale with our competitive revenue sharing model.</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                            <i class="ri-global-line text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-4">Global Marketplace</h3>
                        <p class="text-gray-600">Reach customers worldwide with our international platform and built-in marketing tools.</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                            <i class="ri-tools-line text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-4">Seller Tools</h3>
                        <p class="text-gray-600">Advanced analytics, automated payments, and professional seller dashboard to manage your business.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Top Sellers Showcase -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-secondary mb-4">Meet Our Top Sellers</h2>
                    <p class="text-gray-600">Join successful creators who are building their digital business with us</p>
                </div>
                
                <div class="grid md:grid-cols-4 gap-6">
                    <?php foreach ($topSellers as $seller): ?>
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 text-center hover:shadow-lg transition-shadow">
                        <div class="relative inline-block mb-4">
                            <?php if ($seller['profile_image']): ?>
                                <img src="<?= htmlspecialchars($seller['profile_image']) ?>" 
                                     alt="<?= htmlspecialchars($seller['first_name']) ?>" 
                                     class="w-16 h-16 rounded-full object-cover mx-auto">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto">
                                    <span class="text-white font-bold">
                                        <?= strtoupper(substr($seller['first_name'], 0, 1) . substr($seller['last_name'], 0, 1)) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                                <i class="ri-check-line text-xs text-white"></i>
                            </div>
                        </div>
                        <h3 class="font-semibold text-secondary mb-2">
                            <?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?>
                        </h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div><?= $seller['template_count'] ?> Templates</div>
                            <div><?= number_format($seller['total_downloads']) ?> Downloads</div>
                            <div class="flex items-center justify-center">
                                <span class="text-yellow-500">★</span>
                                <span class="ml-1"><?= number_format($seller['avg_rating'], 1) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
    <?php elseif (!$isSeller): ?>
        <!-- Logged In But Not a Seller - Upgrade Account -->
        <section class="py-20 bg-gradient-to-br from-primary/5 via-white to-primary/10">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <div class="bg-white rounded-3xl p-12 shadow-xl">
                    <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-8">
                        <i class="ri-vip-crown-line text-4xl text-primary"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-secondary mb-6">
                        Upgrade to <span class="text-primary">Seller Account</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Hi <?= htmlspecialchars($userData['first_name']) ?>! Ready to start earning? Upgrade your account to seller status and begin monetizing your creative skills.
                    </p>
                    
                    <!-- Upgrade Benefits -->
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="text-left p-6 bg-green-50 rounded-xl">
                            <div class="flex items-center mb-3">
                                <i class="ri-check-double-line text-green-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-secondary">Instant Activation</h3>
                            </div>
                            <p class="text-gray-600 text-sm">Your seller account will be activated immediately after upgrade</p>
                        </div>
                        
                        <div class="text-left p-6 bg-blue-50 rounded-xl">
                            <div class="flex items-center mb-3">
                                <i class="ri-upload-cloud-line text-blue-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-secondary">Upload Templates</h3>
                            </div>
                            <p class="text-gray-600 text-sm">Start uploading and selling your templates right away</p>
                        </div>
                        
                        <div class="text-left p-6 bg-purple-50 rounded-xl">
                            <div class="flex items-center mb-3">
                                <i class="ri-line-chart-line text-purple-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-secondary">Analytics Dashboard</h3>
                            </div>
                            <p class="text-gray-600 text-sm">Track your sales, earnings, and performance metrics</p>
                        </div>
                        
                        <div class="text-left p-6 bg-orange-50 rounded-xl">
                            <div class="flex items-center mb-3">
                                <i class="ri-hand-coin-line text-orange-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-secondary">70% Commission</h3>
                            </div>
                            <p class="text-gray-600 text-sm">Keep 70% of every sale with our competitive rates</p>
                        </div>
                    </div>
                    
                    <button onclick="upgradeToSeller()" class="bg-primary text-white px-8 py-4 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center justify-center mx-auto">
                        <i class="ri-arrow-up-line mr-2"></i>
                        Upgrade to Seller Account
                    </button>
                </div>
            </div>
        </section>
        
    <?php else: ?>
        <!-- Seller Dashboard -->
        <section class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Dashboard Header -->
                <div class="bg-white rounded-2xl p-8 mb-8 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-secondary mb-2">
                                Welcome back, <?= htmlspecialchars($userData['first_name']) ?>!
                            </h1>
                            <p class="text-gray-600">Manage your seller account and track your performance</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button onclick="showUploadModal()" class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                                <i class="ri-upload-line mr-2"></i>
                                Upload Template
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="grid md:grid-cols-6 gap-6">
                        <div class="bg-blue-50 rounded-xl p-6 text-center">
                            <div class="text-2xl font-bold text-blue-600 mb-1">
                                <?= $sellerStats['total_templates'] ?>
                            </div>
                            <div class="text-sm text-gray-600">Total Templates</div>
                        </div>
                        
                        <div class="bg-green-50 rounded-xl p-6 text-center">
                            <div class="text-2xl font-bold text-green-600 mb-1">
                                <?= $sellerStats['approved_templates'] ?>
                            </div>
                            <div class="text-sm text-gray-600">Approved</div>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-xl p-6 text-center">
                            <div class="text-2xl font-bold text-yellow-600 mb-1">
                                <?= $sellerStats['pending_templates'] ?>
                            </div>
                            <div class="text-sm text-gray-600">Pending</div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-xl p-6 text-center">
                            <div class="text-2xl font-bold text-purple-600 mb-1">
                                <?= number_format($sellerStats['total_downloads']) ?>
                            </div>
                            <div class="text-sm text-gray-600">Downloads</div>
                        </div>
                        
                        <div class="bg-pink-50 rounded-xl p-6 text-center">
                            <div class="text-2xl font-bold text-pink-600 mb-1">
                                <?= number_format($sellerStats['total_views']) ?>
                            </div>
                            <div class="text-sm text-gray-600">Views</div>
                        </div>
                        
                        <div class="bg-orange-50 rounded-xl p-6 text-center">
                            <div class="text-2xl font-bold text-orange-600 mb-1">
                                <?= number_format($sellerStats['average_rating'], 1) ?>★
                            </div>
                            <div class="text-sm text-gray-600">Avg Rating</div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Tabs -->
                <div class="bg-white rounded-2xl mb-8 shadow-sm">
                    <div class="flex border-b border-gray-100 overflow-x-auto">
                        <button onclick="switchTab('overview')" class="seller-tab px-6 py-4 font-semibold text-primary border-b-2 border-primary whitespace-nowrap" data-tab="overview">
                            <i class="ri-dashboard-line mr-2"></i>Overview
                        </button>
                        <button onclick="switchTab('templates')" class="seller-tab px-6 py-4 font-semibold text-gray-600 hover:text-primary transition-colors whitespace-nowrap" data-tab="templates">
                            <i class="ri-layout-grid-line mr-2"></i>My Templates
                        </button>
                        <button onclick="switchTab('analytics')" class="seller-tab px-6 py-4 font-semibold text-gray-600 hover:text-primary transition-colors whitespace-nowrap" data-tab="analytics">
                            <i class="ri-line-chart-line mr-2"></i>Analytics
                        </button>
                        <button onclick="switchTab('earnings')" class="seller-tab px-6 py-4 font-semibold text-gray-600 hover:text-primary transition-colors whitespace-nowrap" data-tab="earnings">
                            <i class="ri-money-dollar-circle-line mr-2"></i>Earnings
                        </button>
                        <button onclick="switchTab('settings')" class="seller-tab px-6 py-4 font-semibold text-gray-600 hover:text-primary transition-colors whitespace-nowrap" data-tab="settings">
                            <i class="ri-settings-3-line mr-2"></i>Settings
                        </button>
                    </div>
                    
                    <!-- Tab Content -->
                    <div class="p-8">
                        <!-- Overview Tab -->
                        <div id="overview-content" class="tab-content">
                            <div class="grid lg:grid-cols-3 gap-8">
                                <!-- Recent Templates -->
                                <div class="lg:col-span-2">
                                    <h3 class="text-xl font-bold text-secondary mb-6">Recent Templates</h3>
                                    <?php if (empty($recentTemplates)): ?>
                                        <div class="text-center py-12 bg-gray-50 rounded-xl">
                                            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="ri-image-line text-2xl text-gray-400"></i>
                                            </div>
                                            <h4 class="text-lg font-semibold text-gray-600 mb-2">No Templates Yet</h4>
                                            <p class="text-gray-500 mb-4">Upload your first template to get started</p>
                                            <button onclick="showUploadModal()" class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                                                Upload Template
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="space-y-4">
                                            <?php foreach ($recentTemplates as $template): ?>
                                            <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                                <img src="<?= htmlspecialchars($template['preview_image']) ?>" 
                                                     alt="<?= htmlspecialchars($template['title']) ?>" 
                                                     class="w-16 h-16 rounded-lg object-cover">
                                                <div class="flex-1 ml-4">
                                                    <h4 class="font-semibold text-secondary mb-1">
                                                        <?= htmlspecialchars($template['title']) ?>
                                                    </h4>
                                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                        <span class="flex items-center">
                                                            <i class="ri-download-line mr-1"></i>
                                                            <?= $template['downloads_count'] ?>
                                                        </span>
                                                        <span class="flex items-center">
                                                            <i class="ri-eye-line mr-1"></i>
                                                            <?= $template['views_count'] ?>
                                                        </span>
                                                        <span class="flex items-center">
                                                            <i class="ri-star-line mr-1"></i>
                                                            <?= number_format($template['rating'], 1) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-lg font-bold text-primary mb-1">$<?= $template['price'] ?></div>
                                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                                        <?php 
                                                        switch($template['status']) {
                                                            case 'approved': echo 'bg-green-100 text-green-800'; break;
                                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                            case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                                            default: echo 'bg-gray-100 text-gray-800';
                                                        }
                                                        ?>">
                                                        <?= ucfirst($template['status']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div>
                                    <h3 class="text-xl font-bold text-secondary mb-6">Quick Actions</h3>
                                    <div class="space-y-4">
                                        <button onclick="showUploadModal()" class="w-full bg-primary text-white p-4 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center justify-center">
                                            <i class="ri-upload-cloud-line mr-2"></i>
                                            Upload New Template
                                        </button>
                                        
                                        <button onclick="switchTab('analytics')" class="w-full bg-blue-50 text-blue-600 p-4 rounded-xl font-semibold hover:bg-blue-100 transition-colors flex items-center justify-center">
                                            <i class="ri-bar-chart-line mr-2"></i>
                                            View Analytics
                                        </button>
                                        
                                        <button onclick="switchTab('earnings')" class="w-full bg-green-50 text-green-600 p-4 rounded-xl font-semibold hover:bg-green-100 transition-colors flex items-center justify-center">
                                            <i class="ri-wallet-line mr-2"></i>
                                            Check Earnings
                                        </button>
                                    </div>
                                    
                                    <!-- Seller Tips -->
                                    <div class="mt-8 p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl">
                                        <h4 class="font-bold text-secondary mb-3">💡 Seller Tips</h4>
                                        <ul class="space-y-2 text-sm text-gray-600">
                                            <li>• Use high-quality preview images</li>
                                            <li>• Write detailed descriptions</li>
                                            <li>• Add relevant tags for better discovery</li>
                                            <li>• Respond to customer feedback</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other tab contents will be loaded via JavaScript -->
                        <div id="templates-content" class="tab-content hidden">
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-layout-grid-line text-2xl text-primary"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-secondary mb-2">Templates Management</h3>
                                <p class="text-gray-600 mb-4">This section is under development</p>
                                <button onclick="showUploadModal()" class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                                    Upload Template
                                </button>
                            </div>
                        </div>
                        
                        <div id="analytics-content" class="tab-content hidden">
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-line-chart-line text-2xl text-blue-600"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-secondary mb-2">Analytics Dashboard</h3>
                                <p class="text-gray-600">Detailed analytics coming soon</p>
                            </div>
                        </div>
                        
                        <div id="earnings-content" class="tab-content hidden">
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-money-dollar-circle-line text-2xl text-green-600"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-secondary mb-2">Earnings Overview</h3>
                                <p class="text-gray-600">Earnings tracking coming soon</p>
                            </div>
                        </div>
                        
                        <div id="settings-content" class="tab-content hidden">
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-settings-3-line text-2xl text-gray-600"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-secondary mb-2">Seller Settings</h3>
                                <p class="text-gray-600">Settings panel coming soon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>

<!-- Upload Template Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-secondary">Upload New Template</h2>
                    <button onclick="hideUploadModal()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                        <i class="ri-close-line text-xl text-gray-600"></i>
                    </button>
                </div>
                
                <div class="text-center py-12 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-upload-cloud-line text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary mb-2">Template Upload System</h3>
                    <p class="text-gray-600 mb-4">Upload functionality will be implemented soon</p>
                    <div class="text-sm text-gray-500">
                        Coming features:<br>
                        • Drag & drop file upload<br>
                        • Template preview generator<br>
                        • SEO optimization tools<br>
                        • Automated quality checks
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.seller-tab').forEach(tab => {
        tab.classList.remove('text-primary', 'border-b-2', 'border-primary');
        tab.classList.add('text-gray-600');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active class to selected tab
    const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
    activeTab.classList.remove('text-gray-600');
    activeTab.classList.add('text-primary', 'border-b-2', 'border-primary');
}

// Modal functions
function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Upgrade to seller function
function upgradeToSeller() {
    if (confirm('Are you sure you want to upgrade to a seller account? This action cannot be undone.')) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i>Upgrading...';
        button.disabled = true;
        
        // Make API request
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'upgrade_to_seller'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload page
                if (window.toast) {
                    window.toast.success('Successfully upgraded to seller account!', {
                        duration: 3000,
                        position: 'top-right'
                    });
                }
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.error || 'Upgrade failed');
            }
        })
        .catch(error => {
            console.error('Upgrade error:', error);
            if (window.toast) {
                window.toast.error('Failed to upgrade account. Please try again.', {
                    duration: 5000,
                    position: 'top-right'
                });
            } else {
                alert('Failed to upgrade account. Please try again.');
            }
            
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

// Close modal when clicking outside
document.getElementById('uploadModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideUploadModal();
    }
});

// Handle escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideUploadModal();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>