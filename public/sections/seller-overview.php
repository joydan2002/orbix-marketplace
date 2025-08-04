<?php
/**
 * Seller Overview Dashboard
 * Main dashboard with real data from database
 */

// Load real data from database
require_once 'seller-data-loader.php';
?>

<!-- Dashboard Overview -->
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="bg-white rounded-2xl p-8 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Welcome back, <?= htmlspecialchars($sellerProfile['first_name']) ?>! 👋
                </h1>
                <p class="text-gray-600">Here's what's happening with your business today</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Total Balance</div>
                <div class="text-3xl font-bold text-green-600">$<?= number_format($availableBalance, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid md:grid-cols-4 gap-6">
        <!-- Total Earnings -->
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">$<?= number_format($totalEarnings, 2) ?></div>
                    <div class="text-sm text-gray-600">Total Earnings</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-money-dollar-circle-line text-xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600">↗ 12%</span>
                <span class="text-gray-500 ml-2">vs last month</span>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900"><?= $totalOrders ?></div>
                    <div class="text-sm text-gray-600">Total Orders</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-shopping-cart-line text-xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600">↗ 8%</span>
                <span class="text-gray-500 ml-2">vs last month</span>
            </div>
        </div>

        <!-- Products -->
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900"><?= $totalTemplates + $totalServices ?></div>
                    <div class="text-sm text-gray-600">Active Products</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-store-line text-xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-purple-600"><?= $templateStats['active'] ?> templates</span>
                <span class="text-gray-400 mx-1">•</span>
                <span class="text-purple-600"><?= $serviceStats['active'] ?> services</span>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900"><?= number_format($reviewStats['average_rating'], 1) ?></div>
                    <div class="text-sm text-gray-600">Average Rating</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ri-star-fill text-xl text-yellow-500"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-yellow-600"><?= $totalReviews ?> reviews</span>
                <span class="text-gray-500 ml-2">total</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Earnings Chart -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Earnings Overview</h3>
                <select class="text-sm border border-gray-200 rounded-lg px-3 py-2">
                    <option>Last 7 months</option>
                    <option>Last 6 months</option>
                    <option>Last 3 months</option>
                </select>
            </div>
            
            <div class="h-64">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                <a href="#" onclick="loadSection('orders')" class="text-sm text-primary hover:text-primary/80">View All</a>
            </div>
            
            <div class="space-y-4">
                <?php if (empty($recentActivity)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="ri-inbox-line text-3xl mb-2"></i>
                    <p>No recent activity</p>
                </div>
                <?php else: ?>
                    <?php foreach (array_slice($recentActivity, 0, 6) as $activity): ?>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center <?= $activity['type'] === 'order' ? 'bg-blue-100 text-blue-600' : 'bg-yellow-100 text-yellow-600' ?>">
                                <i class="ri-<?= $activity['type'] === 'order' ? 'shopping-cart' : 'star' ?>-line"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($activity['title']) ?></div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($activity['description']) ?></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <?php if ($activity['amount']): ?>
                            <div class="font-semibold text-green-600">+$<?= number_format($activity['amount'], 2) ?></div>
                            <?php endif; ?>
                            <div class="text-xs text-gray-500"><?= date('M j, g:i A', strtotime($activity['date'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Messages -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <button onclick="showAddProductModal()" class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <i class="ri-add-line text-primary"></i>
                    </div>
                    <div>
                        <div class="font-medium">Add New Product</div>
                        <div class="text-sm text-gray-500">Upload template or create service</div>
                    </div>
                </button>
                
                <button onclick="loadSection('analytics')" class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="ri-bar-chart-line text-blue-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">View Analytics</div>
                        <div class="text-sm text-gray-500">Track performance metrics</div>
                    </div>
                </button>
                
                <button onclick="loadSection('earnings')" class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="ri-money-dollar-circle-line text-green-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">Withdraw Earnings</div>
                        <div class="text-sm text-gray-500">Available: $<?= number_format($availableBalance, 2) ?></div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Messages</h3>
                <div class="flex items-center space-x-3">
                    <?php if ($unreadMessages > 0): ?>
                    <span class="bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs font-medium">
                        <?= $unreadMessages ?> unread
                    </span>
                    <?php endif; ?>
                    <a href="#" onclick="loadSection('messages')" class="text-sm text-primary hover:text-primary/80">View All</a>
                </div>
            </div>
            
            <div class="space-y-4">
                <?php if (empty($sellerMessages)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="ri-message-line text-3xl mb-2"></i>
                    <p>No messages yet</p>
                </div>
                <?php else: ?>
                    <?php foreach (array_slice($sellerMessages, 0, 4) as $message): ?>
                    <div class="flex items-start space-x-4 p-4 hover:bg-gray-50 rounded-lg cursor-pointer <?= !$message['is_read'] ? 'bg-blue-50 border-l-4 border-blue-400' : '' ?>">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-medium text-gray-600">
                                <?= strtoupper(substr($message['first_name'], 0, 1) . substr($message['last_name'], 0, 1)) ?>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="font-medium text-gray-900">
                                    <?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= date('M j, g:i A', strtotime($message['created_at'])) ?>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-gray-800 mb-1">
                                <?= htmlspecialchars($message['subject']) ?>
                            </div>
                            <div class="text-sm text-gray-600 line-clamp-2">
                                <?= htmlspecialchars($message['message']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Charts JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Earnings Chart
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const monthlyData = <?= json_encode(array_values($monthlyEarnings)) ?>;
    const monthlyLabels = <?= json_encode(array_map(function($month) { return date('M Y', strtotime($month . '-01')); }, array_keys($monthlyEarnings))) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Earnings',
                data: monthlyData,
                borderColor: '#FF5F1F',
                backgroundColor: 'rgba(255, 95, 31, 0.1)',
                borderWidth: 3,
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
        });
});
</script>