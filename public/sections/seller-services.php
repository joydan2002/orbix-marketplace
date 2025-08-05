<?php
/**
 * Seller Services Management
 * Manage all seller services with real data from database
 */
?>

<!-- Services Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">My Services</h1>
            <p class="text-gray-600">Manage your service offerings and track orders</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="showAddServiceModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                <i class="ri-add-line mr-2"></i>Add New Service
            </button>
        </div>
    </div>
    
    <!-- Services Stats -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= $serviceStats['total'] ?></div>
                    <div class="text-sm text-gray-600">Total Services</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-service-line text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600"><?= $serviceStats['active'] ?></div>
                    <div class="text-sm text-gray-600">Active Services</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-check-line text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-orange-600"><?= $serviceStats['total_orders'] ?></div>
                    <div class="text-sm text-gray-600">Total Orders</div>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="ri-shopping-cart-line text-xl text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-600">$<?= number_format($serviceStats['total_revenue'], 2) ?></div>
                    <div class="text-sm text-gray-600">Services Revenue</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-money-dollar-circle-line text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services Grid -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <!-- Table Header with Filters -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-secondary">All Services</h3>
            <div class="flex items-center space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           id="serviceSearch"
                           placeholder="Search services..." 
                           class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
                    <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Category Filter -->
                <select id="serviceCategoryFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Categories</option>
                    <?php foreach ($serviceCategories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Status Filter -->
                <select id="serviceStatusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="paused">Paused</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Services Container -->
    <div id="servicesContainer">
        <?php if (empty($sellerServices)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-service-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary mb-2">No Services Yet</h3>
            <p class="text-gray-600 mb-6">Start offering services to build your business</p>
            <button onclick="showAddServiceModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                Create Your First Service
            </button>
        </div>
        <?php else: ?>
        
        <!-- Services Grid -->
        <div class="p-6">
            <div class="grid lg:grid-cols-2 gap-6">
                <?php foreach ($services as $service): ?>
                <div class="service-item bg-white rounded-xl border hover:shadow-lg transition-all duration-300 overflow-hidden" 
                     data-status="<?= $service['status'] ?>" 
                     data-category="<?= $service['category_id'] ?>">
                    
                    <!-- Service Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <?php
                                    $statusClasses = [
                                        'approved' => 'bg-green-500',
                                        'pending' => 'bg-orange-500',
                                        'draft' => 'bg-gray-400',
                                        'rejected' => 'bg-red-500'
                                    ];
                                    $statusClass = $statusClasses[$service['status']] ?? 'bg-gray-500';
                                    ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-white <?= $statusClass ?>">
                                        <?= ucfirst($service['status']) ?>
                                    </span>
                                    
                                    <?php if ($service['is_featured']): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Featured
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <h4 class="text-lg font-semibold text-secondary mb-2">
                                    <?= htmlspecialchars($service['title']) ?>
                                </h4>
                                
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    <?= htmlspecialchars($service['description']) ?>
                                </p>
                                
                                <!-- Service Meta -->
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i class="ri-folder-line mr-1"></i>
                                        Category ID: <?= $service['category_id'] ?>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="ri-time-line mr-1"></i>
                                        <?= $service['delivery_time'] ?? 7 ?> days delivery
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Service Actions -->
                            <div class="ml-4">
                                <div class="relative">
                                    <button onclick="toggleServiceMenu(<?= $service['id'] ?>)" 
                                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                                        <i class="ri-more-line"></i>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div id="serviceMenu<?= $service['id'] ?>" 
                                         class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 z-10">
                                        <div class="py-1">
                                            <button onclick="editService(<?= $service['id'] ?>)" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="ri-edit-line mr-2"></i>Edit Service
                                            </button>
                                            <button onclick="duplicateService(<?= $service['id'] ?>)" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="ri-file-copy-line mr-2"></i>Duplicate
                                            </button>
                                            <button onclick="viewServiceAnalytics(<?= $service['id'] ?>)" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="ri-bar-chart-line mr-2"></i>Analytics
                                            </button>
                                            <hr class="my-1">
                                            <button onclick="toggleServiceStatus(<?= $service['id'] ?>, '<?= $service['status'] ?>')" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="ri-toggle-line mr-2"></i>
                                                <?= $service['status'] === 'approved' ? 'Pause' : 'Activate' ?>
                                            </button>
                                            <button onclick="deleteService(<?= $service['id'] ?>)" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="ri-delete-bin-line mr-2"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Stats -->
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">$<?= number_format($service['price'], 2) ?></div>
                                <div class="text-xs text-gray-500">Starting Price</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600"><?= number_format($service['orders_count']) ?></div>
                                <div class="text-xs text-gray-500">Orders</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600"><?= number_format($service['views_count']) ?></div>
                                <div class="text-xs text-gray-500">Views</div>
                            </div>
                        </div>
                        
                        <!-- Rating -->
                        <?php if ($service['rating'] > 0): ?>
                        <div class="flex items-center justify-center mb-4">
                            <div class="flex items-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="ri-star-<?= $i <= $service['rating'] ? 'fill' : 'line' ?> text-yellow-400 text-sm"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm text-gray-600 ml-2">
                                <?= number_format($service['rating'], 1) ?> (<?= $service['reviews_count'] ?> reviews)
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <button onclick="editService(<?= $service['id'] ?>)" 
                                    class="flex-1 px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center">
                                Edit Service
                            </button>
                            
                            <button onclick="viewServiceAnalytics(<?= $service['id'] ?>)" 
                                    class="flex-1 px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-center">
                                View Analytics
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Service Management Scripts -->
<script>
// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('serviceSearch');
    const categoryFilter = document.getElementById('serviceCategoryFilter');
    const statusFilter = document.getElementById('serviceStatusFilter');
    const serviceItems = document.querySelectorAll('.service-item');
    
    function filterServices() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryFilter_val = categoryFilter.value;
        const statusFilter_val = statusFilter.value;
        
        serviceItems.forEach(item => {
            const title = item.querySelector('h4').textContent.toLowerCase();
            const description = item.querySelector('p').textContent.toLowerCase();
            const status = item.dataset.status;
            const category = item.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !categoryFilter_val || category === categoryFilter_val;
            const matchesStatus = !statusFilter_val || status === statusFilter_val;
            
            if (matchesSearch && matchesCategory && matchesStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterServices);
    categoryFilter.addEventListener('change', filterServices);
    statusFilter.addEventListener('change', filterServices);
});

// Service management functions
function toggleServiceMenu(serviceId) {
    const menu = document.getElementById('serviceMenu' + serviceId);
    
    // Close all other menus
    document.querySelectorAll('[id^="serviceMenu"]').forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });
    
    menu.classList.toggle('hidden');
}

function editService(serviceId) {
    // Use the unified editProduct function for services
    if (typeof editProduct === 'function') {
        editProduct(serviceId, 'service');
    } else {
        console.error('editProduct function not found');
        showToast('Edit function not available', 'error');
    }
}

function viewServiceAnalytics(serviceId) {
    window.location.href = `?section=analytics&service_id=${serviceId}`;
}

function duplicateService(serviceId) {
    if (confirm('Are you sure you want to duplicate this service?')) {
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'duplicate_product',
                type: 'service',
                id: serviceId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Service duplicated successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

function toggleServiceStatus(serviceId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'paused' : 'active';
    
    fetch('seller-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'toggle_service_status',
            service_id: serviceId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Service ${newStatus} successfully`, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    });
}

function deleteService(serviceId) {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete_product',
                type: 'service',
                id: serviceId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Service deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="serviceMenu"]') && !event.target.closest('button[onclick*="toggleServiceMenu"]')) {
        document.querySelectorAll('[id^="serviceMenu"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
</script>