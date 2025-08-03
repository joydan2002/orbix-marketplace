<?php
/**
 * Seller Services Management
 * Manage all seller services with CRUD operations
 */
?>

<!-- Services Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">My Services</h1>
            <p class="text-gray-600">Manage your digital services and track performance</p>
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
                    <i class="ri-tools-line text-xl text-blue-600"></i>
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
                    <div class="text-2xl font-bold text-orange-600"><?= $serviceStats['pending'] ?></div>
                    <div class="text-sm text-gray-600">Pending Review</div>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="ri-time-line text-xl text-orange-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-600"><?= number_format($serviceStats['total_orders']) ?></div>
                    <div class="text-sm text-gray-600">Total Orders</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-shopping-cart-line text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services Table -->
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
                
                <!-- Status Filter -->
                <select id="statusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                    <option value="draft">Draft</option>
                </select>
                
                <!-- Category Filter -->
                <select id="categoryFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Categories</option>
                    <?php foreach ($serviceCategories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Services List -->
    <div id="servicesContainer">
        <?php if (empty($sellerServices)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-tools-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary mb-2">No Services Yet</h3>
            <p class="text-gray-600 mb-6">Start building your service portfolio to attract customers</p>
            <button onclick="showAddServiceModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                Create Your First Service
            </button>
        </div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($sellerServices as $service): ?>
            <div class="p-6 service-item" data-status="<?= $service['status'] ?>" data-category="<?= $service['category_id'] ?>">
                <div class="flex items-start space-x-4">
                    <!-- Service Image -->
                    <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                        <?php if ($service['image_url']): ?>
                        <img src="<?= htmlspecialchars($service['image_url']) ?>" 
                             alt="<?= htmlspecialchars($service['title']) ?>"
                             class="w-full h-full object-cover">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ri-tools-line text-2xl text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Service Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-secondary mb-1 truncate">
                                    <?= htmlspecialchars($service['title']) ?>
                                </h4>
                                <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                    <?= htmlspecialchars($service['description']) ?>
                                </p>
                                
                                <!-- Service Meta -->
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i class="ri-folder-line mr-1"></i>
                                        <?= htmlspecialchars($service['category_name']) ?>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="ri-time-line mr-1"></i>
                                        <?= $service['delivery_days'] ?> days delivery
                                    </span>
                                    <span class="flex items-center">
                                        <i class="ri-eye-line mr-1"></i>
                                        <?= number_format($service['views']) ?> views
                                    </span>
                                    <span class="flex items-center">
                                        <i class="ri-shopping-cart-line mr-1"></i>
                                        <?= number_format($service['orders_count']) ?> orders
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Price & Status -->
                            <div class="text-right ml-4">
                                <div class="text-xl font-bold text-secondary mb-2">
                                    $<?= number_format($service['price'], 2) ?>
                                </div>
                                
                                <!-- Status Badge -->
                                <?php
                                $statusClasses = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-orange-100 text-orange-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'draft' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusClass = $statusClasses[$service['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                    <?= ucfirst($service['status']) ?>
                                </span>
                                
                                <!-- Rating -->
                                <?php if ($service['rating'] > 0): ?>
                                <div class="flex items-center mt-2">
                                    <div class="flex items-center">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="ri-star-<?= $i <= $service['rating'] ? 'fill' : 'line' ?> text-yellow-400 text-sm"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-1">
                                        (<?= $service['reviews_count'] ?>)
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2 mt-4">
                            <button onclick="editService(<?= $service['id'] ?>)" 
                                    class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                                <i class="ri-edit-line mr-1"></i>Edit
                            </button>
                            
                            <button onclick="viewServiceOrders(<?= $service['id'] ?>)" 
                                    class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors flex items-center">
                                <i class="ri-shopping-cart-line mr-1"></i>Orders (<?= $service['orders_count'] ?>)
                            </button>
                            
                            <button onclick="viewServiceAnalytics(<?= $service['id'] ?>)" 
                                    class="px-4 py-2 text-sm bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors flex items-center">
                                <i class="ri-bar-chart-line mr-1"></i>Analytics
                            </button>
                            
                            <?php if ($service['status'] === 'active'): ?>
                            <button onclick="toggleServiceStatus(<?= $service['id'] ?>, 'inactive')" 
                                    class="px-4 py-2 text-sm bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors flex items-center">
                                <i class="ri-pause-line mr-1"></i>Deactivate
                            </button>
                            <?php elseif ($service['status'] === 'draft'): ?>
                            <button onclick="publishService(<?= $service['id'] ?>)" 
                                    class="px-4 py-2 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors flex items-center">
                                <i class="ri-send-plane-line mr-1"></i>Publish
                            </button>
                            <?php endif; ?>
                            
                            <div class="relative">
                                <button onclick="toggleServiceMenu(<?= $service['id'] ?>)" 
                                        class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="ri-more-line"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="serviceMenu<?= $service['id'] ?>" 
                                     class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 z-10">
                                    <div class="py-1">
                                        <button onclick="duplicateService(<?= $service['id'] ?>)" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="ri-file-copy-line mr-2"></i>Duplicate
                                        </button>
                                        <button onclick="promoteService(<?= $service['id'] ?>)" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="ri-megaphone-line mr-2"></i>Promote
                                        </button>
                                        <button onclick="exportServiceData(<?= $service['id'] ?>)" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="ri-download-line mr-2"></i>Export Data
                                        </button>
                                        <hr class="my-1">
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
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="p-6 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing <?= ($currentPage - 1) * $perPage + 1 ?> to <?= min($currentPage * $perPage, $totalServices) ?> of <?= $totalServices ?> services
                </div>
                
                <div class="flex items-center space-x-2">
                    <?php if ($currentPage > 1): ?>
                    <a href="?section=services&page=<?= $currentPage - 1 ?>" 
                       class="px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg hover:bg-gray-50">
                        <i class="ri-arrow-left-line"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <a href="?section=services&page=<?= $i ?>" 
                       class="px-3 py-2 text-sm rounded-lg <?= $i === $currentPage ? 'bg-primary text-white' : 'text-gray-600 hover:text-primary hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                    <a href="?section=services&page=<?= $currentPage + 1 ?>" 
                       class="px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg hover:bg-gray-50">
                        <i class="ri-arrow-right-line"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Service Management Scripts -->
<script>
// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('serviceSearch');
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const serviceItems = document.querySelectorAll('.service-item');
    
    function filterServices() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter_val = statusFilter.value;
        const categoryFilter_val = categoryFilter.value;
        
        serviceItems.forEach(item => {
            const title = item.querySelector('h4').textContent.toLowerCase();
            const description = item.querySelector('p').textContent.toLowerCase();
            const status = item.dataset.status;
            const category = item.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesStatus = !statusFilter_val || status === statusFilter_val;
            const matchesCategory = !categoryFilter_val || category === categoryFilter_val;
            
            if (matchesSearch && matchesStatus && matchesCategory) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterServices);
    statusFilter.addEventListener('change', filterServices);
    categoryFilter.addEventListener('change', filterServices);
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
    window.location.href = `service-editor.php?id=${serviceId}`;
}

function viewServiceOrders(serviceId) {
    window.location.href = `?section=orders&service_id=${serviceId}`;
}

function viewServiceAnalytics(serviceId) {
    window.location.href = `?section=analytics&service_id=${serviceId}`;
}

function toggleServiceStatus(serviceId, status) {
    if (confirm(`Are you sure you want to ${status} this service?`)) {
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'toggle_service_status',
                service_id: serviceId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }
}

function publishService(serviceId) {
    if (confirm('Are you sure you want to publish this service?')) {
        toggleServiceStatus(serviceId, 'pending');
    }
}

function duplicateService(serviceId) {
    if (confirm('Are you sure you want to duplicate this service?')) {
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'duplicate_service',
                service_id: serviceId
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

function deleteService(serviceId) {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete_service',
                service_id: serviceId
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