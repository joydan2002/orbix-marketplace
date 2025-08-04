<?php
/**
 * Seller Orders Management
 * Display real orders from database
 */

// Load real data from database
require_once 'seller-data-loader.php';

// Combine template and service orders
$allOrders = [];
foreach ($templateOrders as $order) {
    $allOrders[] = [
        'id' => $order['id'],
        'type' => 'template',
        'order_number' => $order['order_number'],
        'product_title' => $order['template_title'],
        'buyer_name' => $order['first_name'] . ' ' . $order['last_name'],
        'amount' => $order['total_amount'],
        'status' => $order['status'],
        'created_at' => $order['created_at'],
        'payment_status' => $order['payment_status']
    ];
}
foreach ($serviceOrders as $order) {
    $allOrders[] = [
        'id' => $order['id'],
        'type' => 'service',
        'order_number' => $order['order_number'],
        'product_title' => $order['service_title'],
        'buyer_name' => $order['first_name'] . ' ' . $order['last_name'],
        'amount' => $order['total_amount'],
        'status' => $order['status'],
        'created_at' => $order['created_at'],
        'payment_status' => $order['payment_status'],
        'delivery_date' => $order['delivery_date'] ?? null
    ];
}

// Sort by creation date
usort($allOrders, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Calculate order stats
$orderStats = [
    'total' => count($allOrders),
    'pending' => count(array_filter($allOrders, function($o) { return $o['status'] === 'pending'; })),
    'in_progress' => count(array_filter($allOrders, function($o) { return $o['status'] === 'in_progress'; })),
    'completed' => count(array_filter($allOrders, function($o) { return $o['status'] === 'completed'; })),
    'cancelled' => count(array_filter($allOrders, function($o) { return $o['status'] === 'cancelled'; }))
];
?>

<!-- Orders Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Orders</h1>
            <p class="text-gray-600">Manage customer orders and deliveries</p>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold text-green-600"><?= $orderStats['total'] ?></div>
            <div class="text-sm text-gray-600">Total Orders</div>
        </div>
    </div>
    
    <!-- Order Stats -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-yellow-600"><?= $orderStats['pending'] ?></div>
                    <div class="text-sm text-gray-600">Pending</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ri-time-line text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-600"><?= $orderStats['in_progress'] ?></div>
                    <div class="text-sm text-gray-600">In Progress</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-progress-line text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600"><?= $orderStats['completed'] ?></div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-check-line text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-red-600"><?= $orderStats['cancelled'] ?></div>
                    <div class="text-sm text-gray-600">Cancelled</div>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="ri-close-line text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Orders List -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <!-- Search and Filter -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-secondary">All Orders</h3>
            <div class="flex items-center space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           id="orderSearch"
                           placeholder="Search orders..." 
                           class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
                    <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Status Filter -->
                <select id="orderStatusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                
                <!-- Type Filter -->
                <select id="orderTypeFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Types</option>
                    <option value="template">Templates</option>
                    <option value="service">Services</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Orders Container -->
    <div id="ordersContainer">
        <?php if (empty($allOrders)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-shopping-cart-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary mb-2">No Orders Yet</h3>
            <p class="text-gray-600 mb-6">Orders will appear here when customers purchase your products</p>
        </div>
        <?php else: ?>
        
        <!-- Orders List -->
        <div class="divide-y divide-gray-100">
            <?php foreach ($allOrders as $order): ?>
            <div class="order-item p-6 hover:bg-gray-50 transition-colors" 
                 data-status="<?= $order['status'] ?>" 
                 data-type="<?= $order['type'] ?>">
                
                <div class="flex items-center justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Order Icon -->
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 <?= $order['type'] === 'template' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' ?>">
                            <i class="ri-<?= $order['type'] === 'template' ? 'layout' : 'tools' ?>-line text-xl"></i>
                        </div>
                        
                        <!-- Order Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-lg font-semibold text-secondary">
                                    <?= htmlspecialchars($order['product_title']) ?>
                                </h4>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-600">$<?= number_format($order['amount'], 2) ?></div>
                                    <div class="text-xs text-gray-500"><?= $order['order_number'] ?></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 mb-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">
                                            <?= strtoupper(substr($order['buyer_name'], 0, 2)) ?>
                                        </span>
                                    </div>
                                    <span class="font-medium text-gray-800"><?= htmlspecialchars($order['buyer_name']) ?></span>
                                </div>
                                
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $order['type'] === 'template' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= ucfirst($order['type']) ?>
                                </span>
                                
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                       <?php 
                                       switch($order['status']) {
                                           case 'pending':
                                               echo 'bg-yellow-100 text-yellow-800';
                                               break;
                                           case 'in_progress':
                                               echo 'bg-blue-100 text-blue-800';
                                               break;
                                           case 'completed':
                                               echo 'bg-green-100 text-green-800';
                                               break;
                                           case 'cancelled':
                                               echo 'bg-red-100 text-red-800';
                                               break;
                                           default:
                                               echo 'bg-gray-100 text-gray-800';
                                       }
                                       ?>">
                                    <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Ordered on <?= date('M j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
                                    <?php if (isset($order['delivery_date']) && $order['delivery_date']): ?>
                                    • Due: <?= date('M j, Y', strtotime($order['delivery_date'])) ?>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Order Actions -->
                                <div class="flex items-center space-x-3">
                                    <?php if ($order['status'] === 'pending'): ?>
                                    <button onclick="updateOrderStatus(<?= $order['id'] ?>, '<?= $order['type'] ?>', 'in_progress')" 
                                            class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        Start Work
                                    </button>
                                    <?php elseif ($order['status'] === 'in_progress'): ?>
                                    <button onclick="deliverOrder(<?= $order['id'] ?>, '<?= $order['type'] ?>')" 
                                            class="text-sm text-green-600 hover:text-green-700 font-medium">
                                        Mark Complete
                                    </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="viewOrderDetails(<?= $order['id'] ?>, '<?= $order['type'] ?>')" 
                                            class="text-sm text-primary hover:text-primary/80 font-medium">
                                        View Details
                                    </button>
                                    
                                    <button onclick="contactBuyer(<?= $order['id'] ?>)" 
                                            class="text-sm text-gray-600 hover:text-gray-700">
                                        Message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Order Management Scripts -->
<script>
// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('orderStatusFilter');
    const typeFilter = document.getElementById('orderTypeFilter');
    const orderItems = document.querySelectorAll('.order-item');
    
    function filterOrders() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter_val = statusFilter.value;
        const typeFilter_val = typeFilter.value;
        
        orderItems.forEach(item => {
            const productTitle = item.querySelector('h4').textContent.toLowerCase();
            const buyerName = item.querySelector('.font-medium').textContent.toLowerCase();
            const orderNumber = item.querySelector('.text-xs').textContent.toLowerCase();
            const status = item.dataset.status;
            const type = item.dataset.type;
            
            const matchesSearch = productTitle.includes(searchTerm) || buyerName.includes(searchTerm) || orderNumber.includes(searchTerm);
            const matchesStatus = !statusFilter_val || status === statusFilter_val;
            const matchesType = !typeFilter_val || type === typeFilter_val;
            
            if (matchesSearch && matchesStatus && matchesType) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterOrders);
    statusFilter.addEventListener('change', filterOrders);
    typeFilter.addEventListener('change', filterOrders);
});

// Order management functions
function updateOrderStatus(orderId, type, newStatus) {
    fetch('seller-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'update_order_status',
            order_id: orderId,
            type: type,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Order status updated successfully', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    });
}

function deliverOrder(orderId, type) {
    // Show delivery modal or form
    showOrderDeliveryModal(orderId, type);
}

function viewOrderDetails(orderId, type) {
    // Show order details modal
    showOrderDetailsModal(orderId, type);
}

function contactBuyer(orderId) {
    // Show message modal
    showMessageModal(orderId);
}
</script>