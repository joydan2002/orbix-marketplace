<?php
/**
 * Seller Analytics Section
 * Detailed analytics and reporting for sellers
 */
?>

<!-- Analytics Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Analytics & Insights</h1>
            <p class="text-gray-600">Track your performance and grow your business</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-2">
                <select id="dateRange" class="text-sm text-gray-700 bg-transparent focus:outline-none">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>
            <button onclick="exportReport()" class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                <i class="ri-download-line mr-2"></i>Export Report
            </button>
        </div>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="grid lg:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Growth -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="ri-line-chart-line text-2xl text-green-600"></i>
            </div>
            <span class="text-sm text-green-600 bg-green-100 px-2 py-1 rounded-full">+18.2%</span>
        </div>
        <div class="text-2xl font-bold text-secondary mb-1">$<?= number_format($analytics['revenue_growth'], 2) ?></div>
        <div class="text-sm text-gray-600">Revenue Growth</div>
    </div>

    <!-- Conversion Rate -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="ri-pie-chart-line text-2xl text-blue-600"></i>
            </div>
            <span class="text-sm text-blue-600 bg-blue-100 px-2 py-1 rounded-full">+5.3%</span>
        </div>
        <div class="text-2xl font-bold text-secondary mb-1"><?= number_format($analytics['conversion_rate'], 1) ?>%</div>
        <div class="text-sm text-gray-600">Conversion Rate</div>
    </div>

    <!-- Average Order Value -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="ri-money-dollar-circle-line text-2xl text-purple-600"></i>
            </div>
            <span class="text-sm text-purple-600 bg-purple-100 px-2 py-1 rounded-full">+12.8%</span>
        </div>
        <div class="text-2xl font-bold text-secondary mb-1">$<?= number_format($analytics['avg_order_value'], 2) ?></div>
        <div class="text-sm text-gray-600">Avg Order Value</div>
    </div>

    <!-- Customer Retention -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="ri-user-heart-line text-2xl text-orange-600"></i>
            </div>
            <span class="text-sm text-orange-600 bg-orange-100 px-2 py-1 rounded-full">+7.2%</span>
        </div>
        <div class="text-2xl font-bold text-secondary mb-1"><?= number_format($analytics['retention_rate'], 1) ?>%</div>
        <div class="text-sm text-gray-600">Retention Rate</div>
    </div>

    <!-- Profile Views -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="ri-eye-line text-2xl text-indigo-600"></i>
            </div>
            <span class="text-sm text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full">+23.1%</span>
        </div>
        <div class="text-2xl font-bold text-secondary mb-1"><?= number_format($analytics['profile_views']) ?></div>
        <div class="text-sm text-gray-600">Profile Views</div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid lg:grid-cols-2 gap-8 mb-8">
    <!-- Revenue Analytics -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-secondary">Revenue Analytics</h3>
            <div class="flex items-center space-x-2">
                <button class="text-sm px-3 py-1 rounded-lg bg-primary text-white">Revenue</button>
                <button class="text-sm px-3 py-1 rounded-lg text-gray-600 hover:bg-gray-100">Orders</button>
                <button class="text-sm px-3 py-1 rounded-lg text-gray-600 hover:bg-gray-100">Profit</button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="revenueAnalyticsChart"></canvas>
        </div>
    </div>

    <!-- Product Performance -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-secondary">Product Performance</h3>
            <div class="flex items-center space-x-2">
                <button class="text-sm px-3 py-1 rounded-lg bg-primary text-white">Sales</button>
                <button class="text-sm px-3 py-1 rounded-lg text-gray-600 hover:bg-gray-100">Views</button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="productPerformanceChart"></canvas>
        </div>
    </div>
</div>

<!-- Traffic Sources and Customer Demographics -->
<div class="grid lg:grid-cols-3 gap-8 mb-8">
    <!-- Traffic Sources -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-xl font-bold text-secondary mb-6">Traffic Sources</h3>
        <div class="space-y-4">
            <?php 
            $trafficSources = [
                ['name' => 'Direct', 'percentage' => 45, 'visits' => 2340, 'color' => 'bg-blue-500'],
                ['name' => 'Search Engines', 'percentage' => 28, 'visits' => 1456, 'color' => 'bg-green-500'],
                ['name' => 'Social Media', 'percentage' => 18, 'visits' => 935, 'color' => 'bg-purple-500'],
                ['name' => 'Referrals', 'percentage' => 9, 'visits' => 468, 'color' => 'bg-orange-500']
            ];
            foreach ($trafficSources as $source): 
            ?>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full <?= $source['color'] ?> mr-3"></div>
                    <span class="text-gray-700"><?= $source['name'] ?></span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500"><?= number_format($source['visits']) ?></span>
                    <div class="w-20 h-2 bg-gray-200 rounded-full">
                        <div class="h-2 <?= $source['color'] ?> rounded-full" style="width: <?= $source['percentage'] ?>%"></div>
                    </div>
                    <span class="text-sm font-semibold text-secondary w-8"><?= $source['percentage'] ?>%</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-xl font-bold text-secondary mb-6">Top Selling Products</h3>
        <div class="space-y-4">
            <?php 
            $topProducts = array_slice($products, 0, 4);
            foreach ($topProducts as $index => $product): 
            ?>
            <div class="flex items-center p-3 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 bg-gradient-to-br from-primary to-accent rounded-lg flex items-center justify-center text-white font-bold text-sm mr-4">
                    <?= $index + 1 ?>
                </div>
                <img src="<?= htmlspecialchars($product['preview_image'] ?? '/assets/images/placeholder.jpg') ?>" 
                     alt="<?= htmlspecialchars($product['title']) ?>" 
                     class="w-12 h-12 rounded-lg object-cover">
                <div class="flex-1 ml-3">
                    <h4 class="font-semibold text-secondary text-sm"><?= htmlspecialchars(substr($product['title'], 0, 25)) ?>...</h4>
                    <div class="flex items-center text-xs text-gray-600 mt-1">
                        <span><?= $product['type'] === 'template' ? $product['downloads_count'] : $product['orders_count'] ?> sales</span>
                        <span class="mx-2">•</span>
                        <span class="text-primary font-medium">$<?= $product['price'] ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Customer Satisfaction -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-xl font-bold text-secondary mb-6">Customer Satisfaction</h3>
        <div class="text-center mb-6">
            <div class="text-4xl font-bold text-secondary mb-2"><?= number_format($sellerStats['avg_rating'], 1) ?></div>
            <div class="flex items-center justify-center mb-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="ri-star-<?= $i <= round($sellerStats['avg_rating']) ? 'fill' : 'line' ?> text-yellow-400 text-xl"></i>
                <?php endfor; ?>
            </div>
            <div class="text-sm text-gray-600"><?= $sellerStats['total_reviews'] ?> reviews</div>
        </div>
        
        <div class="space-y-3">
            <?php 
            $ratings = [5, 4, 3, 2, 1];
            $ratingCounts = [145, 67, 23, 8, 2]; // Sample data
            $totalRatings = array_sum($ratingCounts);
            foreach ($ratings as $index => $rating): 
                $count = $ratingCounts[$index];
                $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
            ?>
            <div class="flex items-center text-sm">
                <span class="w-6 text-gray-600"><?= $rating ?></span>
                <i class="ri-star-fill text-yellow-400 text-xs mr-2"></i>
                <div class="flex-1 h-2 bg-gray-200 rounded-full mr-3">
                    <div class="h-2 bg-yellow-400 rounded-full" style="width: <?= $percentage ?>%"></div>
                </div>
                <span class="w-8 text-gray-500"><?= $count ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Performance Insights -->
<div class="bg-white rounded-2xl p-6 shadow-sm border mb-8">
    <h3 class="text-xl font-bold text-secondary mb-6">Performance Insights</h3>
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Sales Insights -->
        <div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-4">
                <i class="ri-trending-up-line text-2xl text-white"></i>
            </div>
            <h4 class="font-bold text-secondary mb-2">Sales Performance</h4>
            <p class="text-sm text-gray-600 mb-4">Your sales have increased by 23% compared to last month. Keep up the great work!</p>
            <ul class="text-sm text-gray-700 space-y-1">
                <li>• Best performing day: Tuesday</li>
                <li>• Peak hours: 2PM - 6PM</li>
                <li>• Top category: Templates</li>
            </ul>
        </div>

        <!-- Customer Insights -->
        <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-4">
                <i class="ri-user-3-line text-2xl text-white"></i>
            </div>
            <h4 class="font-bold text-secondary mb-2">Customer Behavior</h4>
            <p class="text-sm text-gray-600 mb-4">Your customers are highly engaged with an average session duration of 4.2 minutes.</p>
            <ul class="text-sm text-gray-700 space-y-1">
                <li>• Repeat customers: 34%</li>
                <li>• Avg. pages per visit: 3.8</li>
                <li>• Mobile traffic: 62%</li>
            </ul>
        </div>

        <!-- Growth Opportunities -->
        <div class="p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-4">
                <i class="ri-lightbulb-line text-2xl text-white"></i>
            </div>
            <h4 class="font-bold text-secondary mb-2">Growth Opportunities</h4>
            <p class="text-sm text-gray-600 mb-4">Consider expanding your service offerings to capture more market share.</p>
            <ul class="text-sm text-gray-700 space-y-1">
                <li>• Add custom services</li>
                <li>• Optimize pricing strategy</li>
                <li>• Increase social presence</li>
            </ul>
        </div>
    </div>
</div>

<!-- Analytics Chart Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Analytics Chart
    const revenueAnalyticsCtx = document.getElementById('revenueAnalyticsChart').getContext('2d');
    new Chart(revenueAnalyticsCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [
                {
                    label: 'Revenue',
                    data: [2400, 3200, 2800, 3800],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Target',
                    data: [2500, 3000, 3500, 4000],
                    borderColor: '#f093fb',
                    backgroundColor: 'rgba(240, 147, 251, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Product Performance Chart
    const productPerformanceCtx = document.getElementById('productPerformanceChart').getContext('2d');
    new Chart(productPerformanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Templates', 'Services', 'Consultations', 'Others'],
            datasets: [{
                data: [45, 30, 20, 5],
                backgroundColor: [
                    '#667eea',
                    '#f093fb',
                    '#4facfe',
                    '#43e97b'
                ],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });
});

// Export functionality
function exportReport() {
    showToast('Generating report...', 'info');
    
    // Simulate export process
    setTimeout(() => {
        showToast('Analytics report downloaded successfully!', 'success');
    }, 2000);
}

// Date range change handler
document.getElementById('dateRange').addEventListener('change', function() {
    const range = this.value;
    showToast(`Analytics updated for last ${range} days`, 'info');
    
    // Here you would typically reload the data via AJAX
    // For now, we'll just show a message
});
</script>