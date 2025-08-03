<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-secondary flex items-center">
                        <i class="ri-file-list-3-line mr-3 text-primary"></i>
                        <span id="order-modal-title">Order Details</span>
                    </h2>
                    <button onclick="hideModal('orderModal')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                        <i class="ri-close-line text-xl text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div id="order-details-content">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrderDetails(orderId) {
    // Show loading state
    document.getElementById('order-details-content').innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        </div>
    `;
    
    showModal('orderModal');
    
    // Fetch order details
    fetch(`seller-api.php?action=get_order_details&order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const order = data.order;
                displayOrderDetails(order);
            } else {
                throw new Error(data.error || 'Failed to load order details');
            }
        })
        .catch(error => {
            console.error('Order details error:', error);
            document.getElementById('order-details-content').innerHTML = `
                <div class="text-center py-12">
                    <i class="ri-error-warning-line text-4xl text-red-500 mb-4"></i>
                    <p class="text-gray-600">Failed to load order details</p>
                </div>
            `;
        });
}

function displayOrderDetails(order) {
    const statusBadgeClass = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'refunded': 'bg-gray-100 text-gray-800'
    };
    
    document.getElementById('order-modal-title').textContent = `Order #${order.id}`;
    
    document.getElementById('order-details-content').innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Information -->
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center">
                        <i class="ri-information-line mr-2 text-primary"></i>
                        Order Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order ID:</span>
                            <span class="font-medium">#${order.id}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${statusBadgeClass[order.status] || 'bg-gray-100 text-gray-800'}">
                                ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Date:</span>
                            <span class="font-medium">${formatDateTime(order.created_at)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="font-bold text-primary">$${parseFloat(order.total_amount).toFixed(2)}</span>
                        </div>
                        ${order.commission_amount ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Your Commission:</span>
                                <span class="font-bold text-green-600">$${parseFloat(order.commission_amount).toFixed(2)}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center">
                        <i class="ri-user-line mr-2 text-primary"></i>
                        Customer Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium">${order.customer_name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">${order.customer_email}</span>
                        </div>
                        ${order.customer_phone ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-medium">${order.customer_phone}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center">
                        <i class="ri-shopping-bag-line mr-2 text-primary"></i>
                        Order Items
                    </h3>
                    <div class="space-y-4">
                        ${order.items.map(item => `
                            <div class="flex items-center space-x-4 p-4 bg-white rounded-lg">
                                <img src="${item.image || '/assets/images/default-service.jpg'}" 
                                     alt="${item.name}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-secondary">${item.name}</h4>
                                    <p class="text-sm text-gray-600">${item.type}</p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-sm text-gray-600">Qty: ${item.quantity}</span>
                                        <span class="font-semibold text-primary">$${parseFloat(item.price).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <!-- Order Actions -->
                <div class="bg-primary/5 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center">
                        <i class="ri-settings-line mr-2 text-primary"></i>
                        Actions
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        ${order.status === 'pending' ? `
                            <button onclick="updateOrderStatus(${order.id}, 'processing')" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center">
                                <i class="ri-play-line mr-2"></i>
                                Start Processing
                            </button>
                        ` : ''}
                        
                        ${order.status === 'processing' ? `
                            <button onclick="updateOrderStatus(${order.id}, 'completed')" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center">
                                <i class="ri-check-line mr-2"></i>
                                Mark Complete
                            </button>
                        ` : ''}
                        
                        ${['pending', 'processing'].includes(order.status) ? `
                            <button onclick="updateOrderStatus(${order.id}, 'cancelled')" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors flex items-center">
                                <i class="ri-close-line mr-2"></i>
                                Cancel Order
                            </button>
                        ` : ''}
                        
                        <button onclick="contactCustomer('${order.customer_email}', '${order.customer_name}')" 
                                class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors flex items-center">
                            <i class="ri-mail-line mr-2"></i>
                            Contact Customer
                        </button>
                        
                        <button onclick="downloadInvoice(${order.id})" 
                                class="bg-gray-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-700 transition-colors flex items-center">
                            <i class="ri-download-line mr-2"></i>
                            Download Invoice
                        </button>
                    </div>
                </div>
                
                <!-- Order Timeline -->
                ${order.timeline && order.timeline.length > 0 ? `
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-secondary mb-4 flex items-center">
                            <i class="ri-time-line mr-2 text-primary"></i>
                            Order Timeline
                        </h3>
                        <div class="space-y-4">
                            ${order.timeline.map((event, index) => `
                                <div class="flex items-start space-x-3">
                                    <div class="w-3 h-3 bg-primary rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-secondary">${event.title}</span>
                                            <span class="text-sm text-gray-500">${formatDateTime(event.created_at)}</span>
                                        </div>
                                        ${event.description ? `<p class="text-sm text-gray-600 mt-1">${event.description}</p>` : ''}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

function updateOrderStatus(orderId, newStatus) {
    if (!confirm(`Are you sure you want to update this order status to "${newStatus}"?`)) {
        return;
    }
    
    fetch('seller-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update_order_status&order_id=${orderId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Order status updated successfully!');
            hideModal('orderModal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.error || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Update status error:', error);
        showErrorToast(error.message || 'Failed to update order status');
    });
}

function contactCustomer(email, name) {
    const subject = encodeURIComponent(`Regarding your order - ${name}`);
    const body = encodeURIComponent(`Hello ${name},\n\nI hope this message finds you well. I wanted to reach out regarding your recent order.\n\nBest regards,\nYour Seller`);
    
    window.open(`mailto:${email}?subject=${subject}&body=${body}`, '_blank');
}

function downloadInvoice(orderId) {
    // Create a temporary link to download the invoice
    const link = document.createElement('a');
    link.href = `seller-api.php?action=download_invoice&order_id=${orderId}`;
    link.download = `invoice-${orderId}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}
</script>