<?php
/**
 * Seller Earnings Section
 * Detailed earnings management and withdrawal system
 */
?>

<!-- Earnings Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Earnings & Payouts</h1>
            <p class="text-gray-600">Manage your earnings and withdrawal requests</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="requestPayout()" class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                <i class="ri-bank-card-line mr-2"></i>Request Payout
            </button>
        </div>
    </div>
</div>

<!-- Earnings Overview Cards -->
<div class="grid lg:grid-cols-4 md:grid-cols-2 gap-6 mb-8">
    <!-- Available Balance -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="ri-wallet-3-line text-2xl"></i>
            </div>
            <div class="text-right">
                <div class="text-sm opacity-90">Available</div>
                <div class="text-2xl font-bold">$<?= number_format($earnings['available_balance'], 2) ?></div>
            </div>
        </div>
        <div class="flex items-center text-sm opacity-90">
            <i class="ri-arrow-up-line mr-1"></i>
            <span>Ready for withdrawal</span>
        </div>
    </div>

    <!-- Pending Clearance -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="ri-time-line text-2xl"></i>
            </div>
            <div class="text-right">
                <div class="text-sm opacity-90">Pending</div>
                <div class="text-2xl font-bold">$<?= number_format($earnings['pending_balance'], 2) ?></div>
            </div>
        </div>
        <div class="flex items-center text-sm opacity-90">
            <i class="ri-information-line mr-1"></i>
            <span>7-14 days clearance</span>
        </div>
    </div>

    <!-- Total Earnings -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="ri-money-dollar-circle-line text-2xl"></i>
            </div>
            <div class="text-right">
                <div class="text-sm opacity-90">Total Earned</div>
                <div class="text-2xl font-bold">$<?= number_format($earnings['total_earned'], 2) ?></div>
            </div>
        </div>
        <div class="flex items-center text-sm opacity-90">
            <i class="ri-trophy-line mr-1"></i>
            <span>All time earnings</span>
        </div>
    </div>

    <!-- This Month -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="ri-calendar-line text-2xl"></i>
            </div>
            <div class="text-right">
                <div class="text-sm opacity-90">This Month</div>
                <div class="text-2xl font-bold">$<?= number_format($earnings['monthly_earnings'], 2) ?></div>
            </div>
        </div>
        <div class="flex items-center text-sm opacity-90">
            <i class="ri-trending-up-line mr-1"></i>
            <span>+23% from last month</span>
        </div>
    </div>
</div>

<!-- Earnings Chart and Payment Methods -->
<div class="grid lg:grid-cols-3 gap-8 mb-8">
    <!-- Earnings Trend -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-secondary">Earnings Trend</h3>
            <div class="flex items-center space-x-2">
                <button class="text-sm px-3 py-1 rounded-lg bg-primary text-white">Earnings</button>
                <button class="text-sm px-3 py-1 rounded-lg text-gray-600 hover:bg-gray-100">Payouts</button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-secondary">Payment Methods</h3>
            <button onclick="addPaymentMethod()" class="text-primary hover:text-primary/80 text-sm font-medium flex items-center">
                <i class="ri-add-line mr-1"></i>Add New
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- PayPal -->
            <div class="p-4 border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="ri-paypal-line text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-secondary">PayPal</div>
                            <div class="text-sm text-gray-600">john@example.com</div>
                        </div>
                    </div>
                    <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Primary</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Processing: 1-2 days</span>
                    <span>Fee: 2.9%</span>
                </div>
            </div>

            <!-- Bank Transfer -->
            <div class="p-4 border border-gray-200 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="ri-bank-line text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-secondary">Bank Transfer</div>
                            <div class="text-sm text-gray-600">****1234</div>
                        </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="ri-more-line"></i>
                    </button>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Processing: 3-5 days</span>
                    <span>Fee: $1.50</span>
                </div>
            </div>

            <!-- Add New Payment Method -->
            <button onclick="addPaymentMethod()" class="w-full p-4 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 hover:border-primary hover:text-primary transition-colors flex items-center justify-center">
                <i class="ri-add-line mr-2"></i>
                Add Payment Method
            </button>
        </div>
    </div>
</div>

<!-- Earnings Breakdown and Recent Transactions -->
<div class="grid lg:grid-cols-2 gap-8 mb-8">
    <!-- Revenue Breakdown -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-xl font-bold text-secondary mb-6">Revenue Breakdown</h3>
        <div class="space-y-4">
            <?php 
            $revenueBreakdown = [
                ['type' => 'Template Sales', 'amount' => 2840.50, 'percentage' => 45, 'color' => 'bg-blue-500'],
                ['type' => 'Service Orders', 'amount' => 1892.30, 'percentage' => 30, 'color' => 'bg-green-500'],
                ['type' => 'Consultations', 'amount' => 1261.20, 'percentage' => 20, 'color' => 'bg-purple-500'],
                ['type' => 'Affiliate Commissions', 'amount' => 315.75, 'percentage' => 5, 'color' => 'bg-orange-500']
            ];
            foreach ($revenueBreakdown as $revenue): 
            ?>
            <div class="flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full <?= $revenue['color'] ?> mr-4"></div>
                    <div>
                        <div class="font-semibold text-secondary"><?= $revenue['type'] ?></div>
                        <div class="text-sm text-gray-600"><?= $revenue['percentage'] ?>% of total revenue</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-secondary">$<?= number_format($revenue['amount'], 2) ?></div>
                    <div class="w-20 h-2 bg-gray-200 rounded-full mt-1">
                        <div class="h-2 <?= $revenue['color'] ?> rounded-full" style="width: <?= $revenue['percentage'] ?>%"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-secondary">Recent Transactions</h3>
            <a href="#" class="text-primary hover:text-primary/80 text-sm font-medium">View All</a>
        </div>
        
        <div class="space-y-4">
            <?php 
            $recentTransactions = [
                [
                    'type' => 'sale',
                    'description' => 'E-commerce Template Sale',
                    'customer' => 'Sarah Johnson',
                    'amount' => 89.00,
                    'status' => 'completed',
                    'date' => '2 hours ago'
                ],
                [
                    'type' => 'service',
                    'description' => 'Website Development Service',
                    'customer' => 'Mike Chen',
                    'amount' => 450.00,
                    'status' => 'pending',
                    'date' => '5 hours ago'
                ],
                [
                    'type' => 'payout',
                    'description' => 'PayPal Withdrawal',
                    'customer' => 'Payout Request',
                    'amount' => -250.00,
                    'status' => 'processing',
                    'date' => '1 day ago'
                ],
                [
                    'type' => 'sale',
                    'description' => 'Logo Design Template',
                    'customer' => 'Alex Rivera',
                    'amount' => 35.00,
                    'status' => 'completed',
                    'date' => '2 days ago'
                ]
            ];
            foreach ($recentTransactions as $transaction): 
                $isNegative = $transaction['amount'] < 0;
                $statusColors = [
                    'completed' => 'bg-green-100 text-green-600',
                    'pending' => 'bg-orange-100 text-orange-600',
                    'processing' => 'bg-blue-100 text-blue-600'
                ];
                $typeIcons = [
                    'sale' => 'ri-shopping-cart-line',
                    'service' => 'ri-tools-line',
                    'payout' => 'ri-bank-card-line'
                ];
            ?>
            <div class="flex items-center justify-between p-4 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="<?= $typeIcons[$transaction['type']] ?> text-gray-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-secondary"><?= $transaction['description'] ?></div>
                        <div class="text-sm text-gray-600"><?= $transaction['customer'] ?> • <?= $transaction['date'] ?></div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold <?= $isNegative ? 'text-red-600' : 'text-green-600' ?>">
                        <?= $isNegative ? '-' : '+' ?>$<?= number_format(abs($transaction['amount']), 2) ?>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full <?= $statusColors[$transaction['status']] ?>">
                        <?= ucfirst($transaction['status']) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Payout History -->
<div class="bg-white rounded-2xl p-6 shadow-sm border">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-secondary">Payout History</h3>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <span>Show:</span>
                <select class="border border-gray-200 rounded-lg px-3 py-1 focus:outline-none focus:border-primary">
                    <option value="all">All Payouts</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-4 px-2 font-semibold text-secondary">Date</th>
                    <th class="text-left py-4 px-2 font-semibold text-secondary">Amount</th>
                    <th class="text-left py-4 px-2 font-semibold text-secondary">Method</th>
                    <th class="text-left py-4 px-2 font-semibold text-secondary">Status</th>
                    <th class="text-left py-4 px-2 font-semibold text-secondary">Transaction ID</th>
                    <th class="text-right py-4 px-2 font-semibold text-secondary">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $payoutHistory = [
                    [
                        'date' => '2025-08-01',
                        'amount' => 500.00,
                        'method' => 'PayPal',
                        'status' => 'completed',
                        'transaction_id' => 'TXN-2025080001',
                        'processed_date' => '2025-08-02'
                    ],
                    [
                        'date' => '2025-07-28',
                        'amount' => 750.00,
                        'method' => 'Bank Transfer',
                        'status' => 'processing',
                        'transaction_id' => 'TXN-2025072801',
                        'processed_date' => null
                    ],
                    [
                        'date' => '2025-07-15',
                        'amount' => 320.50,
                        'method' => 'PayPal',
                        'status' => 'completed',
                        'transaction_id' => 'TXN-2025071501',
                        'processed_date' => '2025-07-16'
                    ]
                ];
                foreach ($payoutHistory as $payout): 
                    $statusColors = [
                        'completed' => 'bg-green-100 text-green-600',
                        'processing' => 'bg-blue-100 text-blue-600',
                        'pending' => 'bg-orange-100 text-orange-600',
                        'failed' => 'bg-red-100 text-red-600'
                    ];
                ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-4 px-2">
                        <div class="font-medium text-secondary"><?= date('M j, Y', strtotime($payout['date'])) ?></div>
                        <?php if ($payout['processed_date']): ?>
                        <div class="text-sm text-gray-500">Processed: <?= date('M j', strtotime($payout['processed_date'])) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-2">
                        <div class="font-bold text-secondary">$<?= number_format($payout['amount'], 2) ?></div>
                    </td>
                    <td class="py-4 px-2">
                        <div class="flex items-center">
                            <?php if ($payout['method'] === 'PayPal'): ?>
                            <i class="ri-paypal-line text-blue-600 mr-2"></i>
                            <?php else: ?>
                            <i class="ri-bank-line text-green-600 mr-2"></i>
                            <?php endif; ?>
                            <span class="text-secondary"><?= $payout['method'] ?></span>
                        </div>
                    </td>
                    <td class="py-4 px-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $statusColors[$payout['status']] ?>">
                            <?= ucfirst($payout['status']) ?>
                        </span>
                    </td>
                    <td class="py-4 px-2">
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded"><?= $payout['transaction_id'] ?></code>
                    </td>
                    <td class="py-4 px-2 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button class="text-gray-400 hover:text-primary text-sm">
                                <i class="ri-download-line"></i>
                            </button>
                            <button class="text-gray-400 hover:text-primary text-sm">
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Charts and Modals Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Earnings Chart
    const earningsCtx = document.getElementById('earningsChart').getContext('2d');
    new Chart(earningsCtx, {
        type: 'area',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [
                {
                    label: 'Earnings',
                    data: [1200, 1900, 1500, 2200, 1800, 2400, 2100],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Payouts',
                    data: [800, 1200, 1000, 1500, 1200, 1800, 1400],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
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
});

// Payout request function
function requestPayout() {
    const availableBalance = <?= $earnings['available_balance'] ?>;
    
    if (availableBalance < 50) {
        showToast('Minimum payout amount is $50', 'error');
        return;
    }
    
    // Show payout request modal
    const modal = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="payoutModal">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-secondary">Request Payout</h3>
                    <button onclick="closePayoutModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                        <div class="text-sm text-green-600">Available Balance</div>
                        <div class="text-2xl font-bold text-green-700">$${availableBalance.toFixed(2)}</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-secondary mb-2">Payout Amount</label>
                        <input type="number" id="payoutAmount" max="${availableBalance}" min="50" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-primary" 
                               placeholder="Enter amount">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-secondary mb-2">Payment Method</label>
                        <select id="paymentMethod" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-primary">
                            <option value="paypal">PayPal (2.9% fee)</option>
                            <option value="bank">Bank Transfer ($1.50 fee)</option>
                        </select>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-4">
                        <i class="ri-information-line mr-1"></i>
                        Processing time: 1-5 business days depending on method
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button onclick="closePayoutModal()" 
                            class="flex-1 px-6 py-3 border border-gray-200 rounded-xl font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="submitPayoutRequest()" 
                            class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Request Payout
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
}

function closePayoutModal() {
    const modal = document.getElementById('payoutModal');
    if (modal) {
        modal.remove();
    }
}

function submitPayoutRequest() {
    const amount = document.getElementById('payoutAmount').value;
    const method = document.getElementById('paymentMethod').value;
    
    if (!amount || amount < 50) {
        showToast('Please enter a valid amount (minimum $50)', 'error');
        return;
    }
    
    // Simulate payout request
    showToast('Payout request submitted successfully!', 'success');
    closePayoutModal();
    
    // Here you would typically send the request to your backend
}

function addPaymentMethod() {
    showToast('Payment method setup coming soon!', 'info');
}
</script>