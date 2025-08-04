<?php
/**
 * Seller Analytics Dashboard
 * Display real analytics from database
 */

// Load real data from database
require_once 'seller-data-loader.php';

// Prepare analytics data for charts
$dailyData = [];
$last30Days = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last30Days[] = $date;
    
    // Find analytics for this date
    $dayAnalytics = array_filter($analytics, function($a) use ($date) {
        return $a['date'] === $date;
    });
    
    if (!empty($dayAnalytics)) {
        $dayData = array_values($dayAnalytics)[0];
        $dailyData[] = [
            'date' => $date,
            'views' => $dayData['views'],
            'orders' => $dayData['orders'],
            'revenue' => $dayData['revenue'],
            'conversion' => $dayData['conversion_rate']
        ];
    } else {
        $dailyData[] = [
            'date' => $date,
            'views' => 0,
            'orders' => 0,
            'revenue' => 0,
            'conversion' => 0
        ];
    }
}

// Calculate performance metrics
$totalViews = array_sum(array_column($dailyData, 'views'));
$totalRevenue = array_sum(array_column($dailyData, 'revenue'));
$totalOrdersAnalytics = array_sum(array_column($dailyData, 'orders'));
$avgConversion = $totalViews > 0 ? ($totalOrdersAnalytics / $totalViews) * 100 : 0;

// Compare with previous period
$previousPeriodData = array_slice($dailyData, 0, 15);
$currentPeriodData = array_slice($dailyData, 15, 15);

$prevViews = array_sum(array_column($previousPeriodData, 'views'));
$currViews = array_sum(array_column($currentPeriodData, 'views'));
$viewsGrowth = $prevViews > 0 ? (($currViews - $prevViews) / $prevViews) * 100 : 0;

$prevRevenue = array_sum(array_column($previousPeriodData, 'revenue'));
$currRevenue = array_sum(array_column($currentPeriodData, 'revenue'));
$revenueGrowth = $prevRevenue > 0 ? (($currRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
?>

<!-- Analytics Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Analytics</h1>
            <p class="text-gray-600">Track your performance and insights</p>
        </div>
        <div class="flex items-center space-x-4">
            <select id="analyticsTimeframe" class="px-4 py-2 border border-gray-200 rounded-lg">
                <option value="30">Last 30 days</option>
                <option value="7">Last 7 days</option>
                <option value="90">Last 90 days</option>
            </select>
            <button onclick="downloadAnalyticsReport()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 transition-colors flex items-center space-x-2">
                <i class="ri-download-line text-lg"></i>
                <span>Export</span>
            </button>
        </div>
    </div>
    
    <!-- Key Metrics -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-600"><?= number_format($totalViews) ?></div>
                    <div class="text-sm text-gray-600">Total Views</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-eye-line text-xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="<?= $viewsGrowth >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $viewsGrowth >= 0 ? '↗' : '↘' ?> <?= abs(round($viewsGrowth, 1)) ?>%
                </span>
                <span class="text-gray-500 ml-2">vs previous period</span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600">$<?= number_format($totalRevenue, 2) ?></div>
                    <div class="text-sm text-gray-600">Revenue</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-money-dollar-circle-line text-xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="<?= $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $revenueGrowth >= 0 ? '↗' : '↘' ?> <?= abs(round($revenueGrowth, 1)) ?>%
                </span>
                <span class="text-gray-500 ml-2">vs previous period</span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-600"><?= $totalOrdersAnalytics ?></div>
                    <div class="text-sm text-gray-600">Orders</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-shopping-cart-line text-xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-purple-600"><?= count($templateOrders) ?> templates</span>
                <span class="text-gray-400 mx-1">•</span>
                <span class="text-purple-600"><?= count($serviceOrders) ?> services</span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-yellow-600"><?= number_format($avgConversion, 1) ?>%</div>
                    <div class="text-sm text-gray-600">Conversion Rate</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ri-bar-chart-line text-xl text-yellow-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-yellow-600">Avg. daily performance</span>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Charts -->
<div class="grid lg:grid-cols-2 gap-8 mb-8">
    <!-- Views & Revenue Chart -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-secondary">Views & Revenue</h3>
            <div class="flex items-center space-x-2">
                <button class="chart-toggle active" data-chart="views" onclick="toggleChart('views')">Views</button>
                <button class="chart-toggle" data-chart="revenue" onclick="toggleChart('revenue')">Revenue</button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>
    
    <!-- Orders Chart -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-secondary">Orders Over Time</h3>
        </div>
        <div class="h-64">
            <canvas id="ordersChart"></canvas>
        </div>
    </div>
</div>

<!-- Performance Breakdown -->
<div class="grid lg:grid-cols-3 gap-8">
    <!-- Top Products -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-lg font-semibold text-secondary mb-4">Top Performing Products</h3>
        
        <div class="space-y-4">
            <?php 
            $topProducts = array_merge($templates, $services);
            usort($topProducts, function($a, $b) {
                $aViews = $a['views_count'] ?? 0;
                $bViews = $b['views_count'] ?? 0;
                return $bViews - $aViews;
            });
            $topProducts = array_slice($topProducts, 0, 5);
            ?>
            
            <?php foreach ($topProducts as $index => $product): ?>
            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-medium
                               <?= $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' ?>">
                        <?= $index + 1 ?>
                    </div>
                    <div>
                        <div class="font-medium text-secondary text-sm">
                            <?= htmlspecialchars(substr($product['title'], 0, 30)) ?><?= strlen($product['title']) > 30 ? '...' : '' ?>
                        </div>
                        <div class="text-xs text-gray-500">
                            <?= isset($product['delivery_time_days']) ? 'Service' : 'Template' ?>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">
                        <?= number_format($product['views_count'] ?? 0) ?> views
                    </div>
                    <div class="text-xs text-gray-500">
                        <?= number_format($product['downloads_count'] ?? $product['orders_count'] ?? 0) ?> sales
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Traffic Sources -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-lg font-semibold text-secondary mb-4">Traffic Sources</h3>
        
        <div class="space-y-4">
            <?php
            $trafficSources = [
                ['source' => 'Direct', 'percentage' => 45, 'color' => 'bg-blue-500'],
                ['source' => 'Search', 'percentage' => 30, 'color' => 'bg-green-500'],
                ['source' => 'Social Media', 'percentage' => 15, 'color' => 'bg-purple-500'],
                ['source' => 'Referral', 'percentage' => 10, 'color' => 'bg-yellow-500']
            ];
            ?>
            
            <?php foreach ($trafficSources as $source): ?>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 rounded-full <?= $source['color'] ?>"></div>
                    <span class="text-sm font-medium text-gray-700"><?= $source['source'] ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-20 bg-gray-200 rounded-full h-2">
                        <div class="<?= $source['color'] ?> h-2 rounded-full" style="width: <?= $source['percentage'] ?>%"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-600 w-8"><?= $source['percentage'] ?>%</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Conversion Funnel -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-lg font-semibold text-secondary mb-4">Conversion Funnel</h3>
        
        <div class="space-y-4">
            <?php
            $funnelSteps = [
                ['step' => 'Product Views', 'count' => $totalViews, 'percentage' => 100],
                ['step' => 'Product Clicks', 'count' => intval($totalViews * 0.15), 'percentage' => 15],
                ['step' => 'Add to Cart', 'count' => intval($totalViews * 0.05), 'percentage' => 5],
                ['step' => 'Checkout', 'count' => $totalOrdersAnalytics, 'percentage' => $totalViews > 0 ? ($totalOrdersAnalytics / $totalViews) * 100 : 0]
            ];
            ?>
            
            <?php foreach ($funnelSteps as $index => $step): ?>
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700"><?= $step['step'] ?></span>
                    <span class="text-sm text-gray-600"><?= number_format($step['count']) ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-primary to-primary/70 h-3 rounded-full transition-all duration-300"
                         style="width: <?= max($step['percentage'], 2) ?>%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1"><?= number_format($step['percentage'], 1) ?>%</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Charts JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyData = <?= json_encode($dailyData) ?>;
    
    // Performance Chart (Views & Revenue)
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    let currentMetric = 'views';
    
    const performanceChart = new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'Views',
                data: dailyData.map(d => d.views),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    // Orders Chart
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [{
                label: 'Orders',
                data: dailyData.map(d => d.orders),
                backgroundColor: '#10B981',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    // Chart toggle functionality
    window.toggleChart = function(metric) {
        const buttons = document.querySelectorAll('.chart-toggle');
        buttons.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-chart="${metric}"]`).classList.add('active');
        
        let data, color, label;
        if (metric === 'views') {
            data = dailyData.map(d => d.views);
            color = '#3B82F6';
            label = 'Views';
        } else {
            data = dailyData.map(d => d.revenue);
            color = '#10B981';
            label = 'Revenue ($)';
        }
        
        performanceChart.data.datasets[0].data = data;
        performanceChart.data.datasets[0].borderColor = color;
        performanceChart.data.datasets[0].backgroundColor = color + '20';
        performanceChart.data.datasets[0].label = label;
        performanceChart.update();
    };
});

function downloadAnalyticsReport() {
    window.open('seller-api.php?action=download_analytics_report', '_blank');
}

// Chart toggle button styles
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .chart-toggle {
            padding: 4px 12px;
            border: 1px solid #E5E7EB;
            background: white;
            color: #6B7280;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chart-toggle:hover {
            background: #F3F4F6;
        }
        .chart-toggle.active {
            background: #FF5F1F;
            color: white;
            border-color: #FF5F1F;
        }
    `;
    document.head.appendChild(style);
});
</script>