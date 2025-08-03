<?php
/**
 * Seller Orders Management
 * View and manage customer orders
 */
?>

<!-- Orders Header -->
<div class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Orders</h1>
            <p class="text-gray-600">Manage customer orders and deliveries</p>
        </div>
        <div class="mt-4 lg:mt-0 flex items-center space-x-4">
            <button onclick="exportOrders()" 
                    class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors flex items-center">
                <i class="ri-download-line mr-2"></i>Export
            </button>
            <button onclick="showBulkActionModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                <i class="ri-settings-3-line mr-2"></i>Bulk Actions
            </button>
        </div>
    </div>
</div>

<!-- Orders Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-shopping-bag-line text-blue-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Total</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $orderStats['total_orders'] ?></div>
        <div class="text-sm text-gray-600">All Orders</div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-time-line text-orange-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Pending</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $orderStats['pending_orders'] ?></div>
        <div class="text-sm text-gray-600">Need Action</div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-check-line text-green-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Completed</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $orderStats['completed_orders'] ?></div>
        <div class="text-sm text-gray-600">This Month</div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-money-dollar-circle-line text-purple-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Revenue</span>
        </div>
        <div class="text-2xl font-bold text-gray-900">$<?= number_format($orderStats['monthly_revenue']) ?></div>
        <div class="text-sm text-gray-600">This Month</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Quick Delivery</h3>
            <i class="ri-rocket-line text-2xl opacity-80"></i>
        </div>
        <p class="text-blue-100 mb-4">Mark multiple orders as delivered</p>
        <button onclick="showQuickDeliveryModal()" class="bg-white/20 text-white px-4 py-2 rounded-lg font-medium hover:bg-white/30 transition-colors">
            Start Process
        </button>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Message Center</h3>
            <i class="ri-message-3-line text-2xl opacity-80"></i>
        </div>
        <p class="text-green-100 mb-4">Send updates to customers</p>
        <button onclick="window.location.href='?section=messages'" class="bg-white/20 text-white px-4 py-2 rounded-lg font-medium hover:bg-white/30 transition-colors">
            Open Messages
        </button>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Order Analytics</h3>
            <i class="ri-bar-chart-line text-2xl opacity-80"></i>
        </div>
        <p class="text-purple-100 mb-4">View detailed order insights</p>
        <button onclick="window.location.href='?section=analytics'" class="bg-white/20 text-white px-4 py-2 rounded-lg font-medium hover:bg-white/30 transition-colors">
            View Analytics
        </button>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white rounded-2xl p-6 shadow-sm border mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
        <!-- Search -->
        <div class="flex-1 max-w-md">
            <div class="relative">
                <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="orderSearch" placeholder="Search orders..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
        </div>

        <!-- Filters -->
        <div class="flex items-center space-x-4">
            <select id="statusFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="delivered">Delivered</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
            </select>

            <select id="dateFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="quarter">This Quarter</option>
            </select>

            <select id="productFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Products</option>
                <?php foreach ($sellerProducts as $product): ?>
                <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></option>
                <?php endforeach; ?>
            </select>

            <button id="clearFilters" class="px-4 py-3 text-gray-600 hover:text-gray-800 font-medium">
                Clear
            </button>
        </div>
    </div>
</div>

<!-- Orders List -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <?php if (empty($orders)): ?>
        <div class="p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-shopping-bag-line text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-600 mb-4">No Orders Yet</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Once customers start purchasing your products, their orders will appear here. 
                You can track, manage, and fulfill orders from this dashboard.
            </p>
            <button onclick="window.location.href='?section=products'" 
                    class="bg-primary text-white px-8 py-4 rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                Promote Your Products
            </button>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary focus:ring-primary">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($orders as $order): ?>
                    <tr class="hover:bg-gray-50 order-row" 
                        data-order-id="<?= $order['id'] ?>"
                        data-status="<?= htmlspecialchars($order['status']) ?>"
                        data-product="<?= htmlspecialchars($order['product_id']) ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="order-checkbox rounded border-gray-300 text-primary focus:ring-primary" 
                                   value="<?= $order['id'] ?>">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#<?= $order['order_number'] ?></div>
                            <div class="text-sm text-gray-500">ID: <?= $order['id'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                    <span class="text-primary font-semibold text-sm">
                                        <?= strtoupper(substr($order['customer_name'], 0, 1)) ?>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($order['customer_email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <img src="<?= htmlspecialchars($order['product_image']) ?>" 
                                     alt="<?= htmlspecialchars($order['product_title']) ?>" 
                                     class="w-12 h-12 rounded-lg object-cover mr-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 line-clamp-1">
                                        <?= htmlspecialchars($order['product_title']) ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= ucfirst($order['product_type']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">$<?= number_format($order['amount'], 2) ?></div>
                            <div class="text-sm text-gray-500">
                                Fee: $<?= number_format($order['platform_fee'], 2) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'in_progress' => 'bg-blue-100 text-blue-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'refunded' => 'bg-gray-100 text-gray-800'
                            ];
                            $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                            ?>
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                                <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                            <div><?= date('g:i A', strtotime($order['created_at'])) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button onclick="viewOrderDetails(<?= $order['id'] ?>)" 
                                        class="text-primary hover:text-primary/80 p-1 rounded" 
                                        title="View Details">
                                    <i class="ri-eye-line"></i>
                                </button>
                                
                                <?php if ($order['status'] === 'pending'): ?>
                                <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'in_progress')" 
                                        class="text-blue-600 hover:text-blue-800 p-1 rounded" 
                                        title="Start Working">
                                    <i class="ri-play-line"></i>
                                </button>
                                <?php endif; ?>
                                
                                <?php if ($order['status'] === 'in_progress'): ?>
                                <button onclick="updateOrderStatus(<?= $order['id'] ?>, 'delivered')" 
                                        class="text-green-600 hover:text-green-800 p-1 rounded" 
                                        title="Mark as Delivered">
                                    <i class="ri-check-line"></i>
                                </button>
                                <?php endif; ?>
                                
                                <button onclick="messageCustomer(<?= $order['id'] ?>)" 
                                        class="text-gray-600 hover:text-gray-800 p-1 rounded" 
                                        title="Message Customer">
                                    <i class="ri-message-3-line"></i>
                                </button>
                                
                                <div class="relative dropdown">
                                    <button onclick="toggleOrderDropdown(<?= $order['id'] ?>)" 
                                            class="text-gray-400 hover:text-gray-600 p-1 rounded">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <div id="order-dropdown-<?= $order['id'] ?>" class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-2 z-10 hidden">
                                        <button onclick="downloadDeliverable(<?= $order['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                            <i class="ri-download-line mr-2"></i>Download Files
                                        </button>
                                        <button onclick="viewOrderHistory(<?= $order['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                            <i class="ri-history-line mr-2"></i>Order History
                                        </button>
                                        <button onclick="requestReview(<?= $order['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                            <i class="ri-star-line mr-2"></i>Request Review
                                        </button>
                                        <div class="border-t my-2"></div>
                                        <?php if (in_array($order['status'], ['pending', 'in_progress'])): ?>
                                        <button onclick="cancelOrder(<?= $order['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-red-600 flex items-center">
                                            <i class="ri-close-line mr-2"></i>Cancel Order
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Showing <?= count($orders) ?> of <?= $orderStats['total_orders'] ?> orders
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-2 text-gray-600 hover:text-gray-800 font-medium">Previous</button>
                <button class="px-3 py-2 bg-primary text-white rounded-lg font-medium">1</button>
                <button class="px-3 py-2 text-gray-600 hover:text-gray-800 font-medium">2</button>
                <button class="px-3 py-2 text-gray-600 hover:text-gray-800 font-medium">3</button>
                <button class="px-3 py-2 text-gray-600 hover:text-gray-800 font-medium">Next</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search and filter functionality
    const searchInput = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const productFilter = document.getElementById('productFilter');
    const clearFilters = document.getElementById('clearFilters');

    function filterOrders() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const productValue = productFilter.value;

        document.querySelectorAll('.order-row').forEach(row => {
            const orderNumber = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const customerName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const productTitle = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const status = row.dataset.status;
            const productId = row.dataset.product;

            const matchesSearch = orderNumber.includes(searchTerm) || 
                                customerName.includes(searchTerm) || 
                                productTitle.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            const matchesProduct = !productValue || productId === productValue;

            if (matchesSearch && matchesStatus && matchesProduct) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
    productFilter.addEventListener('change', filterOrders);

    clearFilters.addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = '';
        dateFilter.value = '';
        productFilter.value = '';
        filterOrders();
    });

    // Select all functionality
    const selectAll = document.getElementById('selectAll');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');

    selectAll.addEventListener('change', function() {
        orderCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
});

function toggleOrderDropdown(orderId) {
    const dropdown = document.getElementById(`order-dropdown-${orderId}`);
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdown) {
            menu.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

function viewOrderDetails(orderId) {
    showOrderDetailsModal(orderId);
}

function updateOrderStatus(orderId, newStatus) {
    const statusMessages = {
        'in_progress': 'Are you sure you want to start working on this order?',
        'delivered': 'Are you sure you want to mark this order as delivered?',
        'completed': 'Are you sure you want to mark this order as completed?'
    };

    if (confirm(statusMessages[newStatus] || 'Update order status?')) {
        fetch('/seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_order_status',
                order_id: orderId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Order status updated successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorToast(data.message || 'Failed to update order status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }
}

function messageCustomer(orderId) {
    showMessageModal(orderId);
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        fetch('/seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'cancel_order',
                order_id: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Order cancelled successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorToast(data.message || 'Failed to cancel order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }
}

function exportOrders() {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
    
    if (selectedOrders.length === 0) {
        showInfoToast('Please select orders to export');
        return;
    }

    window.location.href = `/seller-api.php?action=export_orders&orders=${selectedOrders.join(',')}`;
}

function showBulkActionModal() {
    showInfoToast('Bulk actions coming soon!');
}

function showQuickDeliveryModal() {
    showInfoToast('Quick delivery modal coming soon!');
}

function downloadDeliverable(orderId) {
    window.location.href = `/seller-api.php?action=download_deliverable&order=${orderId}`;
}

function viewOrderHistory(orderId) {
    showInfoToast('Order history coming soon!');
}

function requestReview(orderId) {
    if (confirm('Send a review request to the customer?')) {
        fetch('/seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'request_review',
                order_id: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Review request sent!');
            } else {
                showErrorToast(data.message || 'Failed to send review request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }
}
</script>