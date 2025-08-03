<?php
/**
 * Seller Overview Dashboard
 * Main dashboard with key metrics and quick actions
 */
?>

<!-- Dashboard Header -->
<div class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">
                Welcome back, <?= htmlspecialchars($userData['first_name']) ?>! 👋
            </h1>
            <p class="text-gray-600">Here's what's happening with your business today</p>
        </div>
        <div class="mt-4 lg:mt-0 flex items-center space-x-4">
            <button onclick="showAddProductModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                <i class="ri-add-line mr-2"></i>Add Product
            </button>
            <button onclick="exportData()" 
                    class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors flex items-center">
                <i class="ri-download-line mr-2"></i>Export
            </button>
        </div>
    </div>
</div>

<!-- Key Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Earnings -->
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                <i class="ri-money-dollar-circle-line text-white text-xl"></i>
            </div>
            <span class="text-green-600 text-sm font-medium flex items-center">
                <i class="ri-arrow-up-line mr-1"></i>+12.5%
            </span>
        </div>
        <div class="mb-2">
            <div class="text-2xl font-bold text-gray-900">$<?= number_format($sellerStats['total_earnings'], 2) ?></div>
            <div class="text-sm text-gray-600">Total Earnings</div>
        </div>
        <div class="text-xs text-gray-500">+$<?= number_format($sellerStats['total_earnings'] * 0.125, 2) ?> this month</div>
    </div>

    <!-- Total Sales -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                <i class="ri-shopping-cart-line text-white text-xl"></i>
            </div>
            <span class="text-blue-600 text-sm font-medium flex items-center">
                <i class="ri-arrow-up-line mr-1"></i>+8.2%
            </span>
        </div>
        <div class="mb-2">
            <div class="text-2xl font-bold text-gray-900"><?= number_format($sellerStats['total_orders']) ?></div>
            <div class="text-sm text-gray-600">Total Sales</div>
        </div>
        <div class="text-xs text-gray-500">+<?= number_format($sellerStats['total_orders'] * 0.082) ?> this month</div>
    </div>

    <!-- Active Products -->
    <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-2xl p-6 border border-purple-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                <i class="ri-grid-line text-white text-xl"></i>
            </div>
            <span class="text-purple-600 text-sm font-medium flex items-center">
                <i class="ri-arrow-up-line mr-1"></i>+3
            </span>
        </div>
        <div class="mb-2">
            <div class="text-2xl font-bold text-gray-900"><?= $sellerStats['total_products'] ?></div>
            <div class="text-sm text-gray-600">Active Products</div>
        </div>
        <div class="text-xs text-gray-500"><?= $sellerStats['approved_templates'] + $sellerStats['active_services'] ?> approved</div>
    </div>

    <!-- Average Rating -->
    <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                <i class="ri-star-line text-white text-xl"></i>
            </div>
            <span class="text-orange-600 text-sm font-medium flex items-center">
                <i class="ri-arrow-up-line mr-1"></i>+0.2
            </span>
        </div>
        <div class="mb-2">
            <div class="text-2xl font-bold text-gray-900"><?= number_format($sellerStats['avg_rating'], 1) ?></div>
            <div class="text-sm text-gray-600">Average Rating</div>
        </div>
        <div class="text-xs text-gray-500">From <?= $sellerStats['total_orders'] ?> reviews</div>
    </div>
</div>

<!-- Charts and Analytics Row -->
<div class="grid lg:grid-cols-3 gap-8 mb-8">
    <!-- Earnings Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-secondary">Earnings Overview</h3>
                <p class="text-sm text-gray-600">Monthly earnings for the past 12 months</p>
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-1 text-sm font-medium text-primary bg-primary/10 rounded-lg">12M</button>
                <button class="px-3 py-1 text-sm font-medium text-gray-600 hover:text-primary">6M</button>
                <button class="px-3 py-1 text-sm font-medium text-gray-600 hover:text-primary">3M</button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-lg font-bold text-secondary mb-6">Performance</h3>
        
        <div class="space-y-6">
            <!-- Conversion Rate -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Conversion Rate</span>
                    <span class="text-sm font-bold text-gray-900">3.2%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 32%"></div>
                </div>
            </div>

            <!-- View to Sale -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">View to Sale</span>
                    <span class="text-sm font-bold text-gray-900">1:15</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 67%"></div>
                </div>
            </div>

            <!-- Customer Satisfaction -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Satisfaction</span>
                    <span class="text-sm font-bold text-gray-900">98%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-500 h-2 rounded-full" style="width: 98%"></div>
                </div>
            </div>

            <!-- Repeat Customers -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Repeat Customers</span>
                    <span class="text-sm font-bold text-gray-900">45%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-500 h-2 rounded-full" style="width: 45%"></div>
                </div>
            </div>
        </div>

        <!-- Goal Progress -->
        <div class="mt-8 p-4 bg-gradient-to-r from-primary/5 to-accent/5 rounded-xl">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Monthly Goal</span>
                <span class="text-sm font-bold text-primary">$<?= number_format($sellerStats['total_earnings'] * 1.25) ?></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                <div class="bg-gradient-to-r from-primary to-accent h-3 rounded-full" style="width: 80%"></div>
            </div>
            <p class="text-xs text-gray-600">80% of monthly goal achieved</p>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid lg:grid-cols-2 gap-8 mb-8">
    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-secondary">Recent Orders</h3>
            <a href="?section=orders" class="text-primary hover:text-primary/80 text-sm font-medium">View All</a>
        </div>
        
        <?php if (empty($recentOrders)): ?>
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-shopping-cart-line text-2xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-600 mb-2">No Orders Yet</h4>
                <p class="text-gray-500 mb-4">Your recent orders will appear here</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mr-4">
                        <i class="ri-<?= $order['type'] === 'template' ? 'layout-grid' : 'tools' ?>-line text-primary"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-secondary mb-1">
                            <?= htmlspecialchars($order['product_name']) ?>
                        </h4>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span class="flex items-center">
                                <i class="ri-calendar-line mr-1"></i>
                                <?= date('M j, Y', strtotime($order['created_at'])) ?>
                            </span>
                            <span class="flex items-center">
                                <i class="ri-shopping-bag-line mr-1"></i>
                                <?= $order['quantity'] ?> <?= $order['type'] === 'template' ? 'downloads' : 'orders' ?>
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-primary">$<?= number_format($order['price'] * $order['quantity']) ?></div>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <?= $order['status'] ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-secondary">Top Performing Products</h3>
            <a href="?section=products" class="text-primary hover:text-primary/80 text-sm font-medium">View All</a>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-product-hunt-line text-2xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-600 mb-2">No Products Yet</h4>
                <p class="text-gray-500 mb-4">Upload your first product to get started</p>
                <button onclick="showAddProductModal()" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                    Add Product
                </button>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php 
                // Sort products by performance (downloads/orders * price)
                $topProducts = array_slice($products, 0, 5);
                foreach ($topProducts as $product): 
                    $sales = $product['type'] === 'template' ? $product['downloads_count'] : $product['orders_count'];
                    $revenue = $sales * $product['price'];
                ?>
                <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                    <img src="<?= htmlspecialchars($product['preview_image']) ?>" 
                         alt="<?= htmlspecialchars($product['title']) ?>" 
                         class="w-12 h-12 rounded-lg object-cover mr-4">
                    <div class="flex-1">
                        <h4 class="font-semibold text-secondary mb-1">
                            <?= htmlspecialchars($product['title']) ?>
                        </h4>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span class="flex items-center">
                                <i class="ri-download-line mr-1"></i>
                                <?= $sales ?> sales
                            </span>
                            <span class="flex items-center">
                                <i class="ri-star-line mr-1"></i>
                                <?= number_format($product['rating'], 1) ?>
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-primary">$<?= number_format($revenue) ?></div>
                        <div class="text-sm text-gray-600">Revenue</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions Grid -->
<div class="bg-white rounded-2xl p-6 shadow-sm border">
    <h3 class="text-lg font-bold text-secondary mb-6">Quick Actions</h3>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
        <button onclick="showAddProductModal()" 
                class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl hover:shadow-md transition-all duration-200 text-left group border border-blue-100">
            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="ri-add-line text-white text-xl"></i>
            </div>
            <h4 class="font-semibold text-secondary mb-2">Add Product</h4>
            <p class="text-sm text-gray-600">Upload a new template or service</p>
        </button>

        <button onclick="window.location.href='?section=analytics'" 
                class="p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl hover:shadow-md transition-all duration-200 text-left group border border-green-100">
            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="ri-line-chart-line text-white text-xl"></i>
            </div>
            <h4 class="font-semibold text-secondary mb-2">View Analytics</h4>
            <p class="text-sm text-gray-600">Check your performance metrics</p>
        </button>

        <button onclick="showPromoteModal()" 
                class="p-6 bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl hover:shadow-md transition-all duration-200 text-left group border border-purple-100">
            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="ri-megaphone-line text-white text-xl"></i>
            </div>
            <h4 class="font-semibold text-secondary mb-2">Promote Products</h4>
            <p class="text-sm text-gray-600">Boost your sales with ads</p>
        </button>

        <button onclick="window.location.href='?section=settings'" 
                class="p-6 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl hover:shadow-md transition-all duration-200 text-left group border border-orange-100">
            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="ri-settings-3-line text-white text-xl"></i>
            </div>
            <h4 class="font-semibold text-secondary mb-2">Settings</h4>
            <p class="text-sm text-gray-600">Manage your account settings</p>
        </button>
    </div>
</div>

<!-- Chart JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Earnings Chart
    const ctx = document.getElementById('earningsChart').getContext('2d');
    
    const monthlyData = <?= json_encode($monthlyEarnings ?? []) ?>;
    const labels = monthlyData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
    });
    const earnings = monthlyData.map(item => parseFloat(item.earnings || 0));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Earnings',
                data: earnings.length ? earnings : [0, 0, 0, 0, 0, 0],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#667eea',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Earnings: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#6b7280',
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});

// Export data function
function exportData() {
    const data = {
        earnings: <?= json_encode($sellerStats) ?>,
        orders: <?= json_encode($recentOrders) ?>,
        products: <?= json_encode($products) ?>
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'seller-data-' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    showSuccessToast('Data exported successfully!');
}
</script>