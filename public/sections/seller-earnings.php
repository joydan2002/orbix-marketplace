<?php
/**
 * Seller Earnings Management
 * Display real earnings from database
 */

// Load real data from database
require_once 'seller-data-loader.php';

// Calculate earnings stats
$totalWithdrawn = array_sum(array_column(array_filter($withdrawals, function($w) { 
    return $w['status'] === 'completed'; 
}), 'amount'));

$pendingWithdrawals = array_sum(array_column(array_filter($withdrawals, function($w) { 
    return $w['status'] === 'pending'; 
}), 'amount'));

$thisMonthEarnings = array_sum(array_column(array_filter($earnings, function($e) {
    return date('Y-m', strtotime($e['created_at'])) === date('Y-m');
}), 'net_amount'));

$lastMonthEarnings = array_sum(array_column(array_filter($earnings, function($e) {
    return date('Y-m', strtotime($e['created_at'])) === date('Y-m', strtotime('-1 month'));
}), 'net_amount'));
?>

<!-- Earnings Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Earnings</h1>
            <p class="text-gray-600">Track your income and manage withdrawals</p>
        </div>
        <button onclick="showWithdrawModal()" 
                class="bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 transition-colors flex items-center space-x-2 <?= $availableBalance < 10 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                <?= $availableBalance < 10 ? 'disabled' : '' ?>>
            <i class="ri-money-dollar-circle-line text-lg"></i>
            <span>Withdraw Earnings</span>
        </button>
    </div>
    
    <!-- Earnings Stats -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600">$<?= number_format($availableBalance, 2) ?></div>
                    <div class="text-sm text-gray-600">Available Balance</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-wallet-line text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-600">$<?= number_format($totalEarnings, 2) ?></div>
                    <div class="text-sm text-gray-600">Total Earnings</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-money-dollar-circle-line text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-600">$<?= number_format($thisMonthEarnings, 2) ?></div>
                    <div class="text-sm text-gray-600">This Month</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-calendar-line text-xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <?php 
                $growth = $lastMonthEarnings > 0 ? (($thisMonthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100 : 0;
                $isPositive = $growth >= 0;
                ?>
                <span class="<?= $isPositive ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $isPositive ? '↗' : '↘' ?> <?= abs(round($growth, 1)) ?>%
                </span>
                <span class="text-gray-500 ml-2">vs last month</span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-yellow-600">$<?= number_format($totalWithdrawn, 2) ?></div>
                    <div class="text-sm text-gray-600">Total Withdrawn</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ri-bank-line text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Earnings Content -->
<div class="grid lg:grid-cols-3 gap-8">
    <!-- Earnings History -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-secondary">Earnings History</h3>
                <select id="earningsFilter" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="">All Time</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_year">This Year</option>
                </select>
            </div>
        </div>
        
        <div class="divide-y divide-gray-100">
            <?php if (empty($earnings)): ?>
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-money-dollar-circle-line text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-secondary mb-2">No Earnings Yet</h3>
                <p class="text-gray-600">Earnings will appear here when you complete orders</p>
            </div>
            <?php else: ?>
                <?php foreach ($earnings as $earning): ?>
                <div class="earning-item p-6 hover:bg-gray-50 transition-colors" 
                     data-date="<?= $earning['created_at'] ?>">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="ri-money-dollar-circle-line text-xl text-green-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-secondary">Order Earnings</div>
                                <div class="text-sm text-gray-600">
                                    Order #<?= $earning['order_id'] ?> • 
                                    Commission: <?= $earning['commission_rate'] ?>%
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?= date('M j, Y \a\t g:i A', strtotime($earning['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-600">
                                +$<?= number_format($earning['net_amount'], 2) ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                Gross: $<?= number_format($earning['gross_amount'], 2) ?>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                   <?php 
                                   switch($earning['status']) {
                                       case 'available':
                                           echo 'bg-green-100 text-green-800';
                                           break;
                                       case 'pending':
                                           echo 'bg-yellow-100 text-yellow-800';
                                           break;
                                       case 'withdrawn':
                                           echo 'bg-gray-100 text-gray-800';
                                           break;
                                   }
                                   ?>">
                                <?= ucfirst($earning['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Withdrawal History & Actions -->
    <div class="space-y-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <h3 class="text-lg font-semibold text-secondary mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <button onclick="showWithdrawModal()" 
                        class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors <?= $availableBalance < 10 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= $availableBalance < 10 ? 'disabled' : '' ?>>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="ri-money-dollar-circle-line text-green-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">Withdraw Earnings</div>
                        <div class="text-sm text-gray-500">Available: $<?= number_format($availableBalance, 2) ?></div>
                    </div>
                </button>
                
                <button onclick="downloadEarningsReport()" 
                        class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="ri-download-line text-blue-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">Download Report</div>
                        <div class="text-sm text-gray-500">Export earnings data</div>
                    </div>
                </button>
                
                <button onclick="viewTaxInfo()" 
                        class="w-full flex items-center space-x-3 p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="ri-file-text-line text-purple-600"></i>
                    </div>
                    <div>
                        <div class="font-medium">Tax Information</div>
                        <div class="text-sm text-gray-500">View tax documents</div>
                    </div>
                </button>
            </div>
        </div>
        
        <!-- Recent Withdrawals -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <h3 class="text-lg font-semibold text-secondary mb-4">Recent Withdrawals</h3>
            
            <?php if (empty($withdrawals)): ?>
            <div class="text-center py-8 text-gray-500">
                <i class="ri-bank-line text-2xl mb-2"></i>
                <p class="text-sm">No withdrawals yet</p>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach (array_slice($withdrawals, 0, 5) as $withdrawal): ?>
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium text-secondary">
                            $<?= number_format($withdrawal['amount'], 2) ?>
                        </div>
                        <div class="text-sm text-gray-600">
                            <?= ucfirst(str_replace('_', ' ', $withdrawal['payment_method'])) ?>
                        </div>
                        <div class="text-xs text-gray-500">
                            <?= date('M j, Y', strtotime($withdrawal['created_at'])) ?>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                           <?php 
                           switch($withdrawal['status']) {
                               case 'completed':
                                   echo 'bg-green-100 text-green-800';
                                   break;
                               case 'processing':
                                   echo 'bg-blue-100 text-blue-800';
                                   break;
                               case 'pending':
                                   echo 'bg-yellow-100 text-yellow-800';
                                   break;
                               case 'rejected':
                                   echo 'bg-red-100 text-red-800';
                                   break;
                           }
                           ?>">
                        <?= ucfirst($withdrawal['status']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Withdrawal Modal -->
<div id="withdrawModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-secondary">Withdraw Earnings</h3>
                <button onclick="closeWithdrawModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 mb-2">
                        $<?= number_format($availableBalance, 2) ?>
                    </div>
                    <div class="text-sm text-gray-600">Available for withdrawal</div>
                </div>
            </div>
            
            <form id="withdrawForm" onsubmit="submitWithdrawal(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" 
                           id="withdrawAmount" 
                           min="10" 
                           max="<?= $availableBalance ?>" 
                           step="0.01"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                           placeholder="Enter amount">
                    <div class="text-xs text-gray-500 mt-1">Minimum withdrawal: $10.00</div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select id="paymentMethod" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="paypal">PayPal</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="wise">Wise</option>
                    </select>
                </div>
                
                <div class="flex items-center justify-end space-x-4">
                    <button type="button" onclick="closeWithdrawModal()" 
                            class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Request Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Earnings Management Scripts -->
<script>
// Filter earnings by date
document.addEventListener('DOMContentLoaded', function() {
    const filter = document.getElementById('earningsFilter');
    const items = document.querySelectorAll('.earning-item');
    
    filter.addEventListener('change', function() {
        const filterValue = this.value;
        const now = new Date();
        
        items.forEach(item => {
            const itemDate = new Date(item.dataset.date);
            let show = true;
            
            switch(filterValue) {
                case 'this_month':
                    show = itemDate.getMonth() === now.getMonth() && itemDate.getFullYear() === now.getFullYear();
                    break;
                case 'last_month':
                    const lastMonth = new Date(now.getFullYear(), now.getMonth() - 1);
                    show = itemDate.getMonth() === lastMonth.getMonth() && itemDate.getFullYear() === lastMonth.getFullYear();
                    break;
                case 'this_year':
                    show = itemDate.getFullYear() === now.getFullYear();
                    break;
            }
            
            item.style.display = show ? 'block' : 'none';
        });
    });
});

function showWithdrawModal() {
    if (<?= $availableBalance ?> < 10) {
        showToast('Minimum withdrawal amount is $10.00', 'error');
        return;
    }
    document.getElementById('withdrawModal').classList.remove('hidden');
}

function closeWithdrawModal() {
    document.getElementById('withdrawModal').classList.add('hidden');
    document.getElementById('withdrawForm').reset();
}

function submitWithdrawal(event) {
    event.preventDefault();
    
    const amount = parseFloat(document.getElementById('withdrawAmount').value);
    const method = document.getElementById('paymentMethod').value;
    
    if (amount < 10 || amount > <?= $availableBalance ?>) {
        showToast('Invalid withdrawal amount', 'error');
        return;
    }
    
    fetch('../api/seller.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'request_withdrawal',
            amount: amount,
            payment_method: method
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Withdrawal request submitted successfully', 'success');
            closeWithdrawModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    });
}

function downloadEarningsReport() {
    window.open('../api/seller.php?action=download_earnings_report', '_blank');
}

function viewTaxInfo() {
    showToast('Tax information feature coming soon', 'info');
}
</script>