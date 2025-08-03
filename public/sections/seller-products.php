<?php
/**
 * Seller Products Management
 * Manage templates and services
 */
?>

<!-- Products Header -->
<div class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">My Products</h1>
            <p class="text-gray-600">Manage your templates and services</p>
        </div>
        <div class="mt-4 lg:mt-0 flex items-center space-x-4">
            <button onclick="showAddProductModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                <i class="ri-add-line mr-2"></i>Add Product
            </button>
            <button onclick="showBulkActionModal()" 
                    class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors flex items-center">
                <i class="ri-edit-box-line mr-2"></i>Bulk Actions
            </button>
        </div>
    </div>
</div>

<!-- Products Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-grid-line text-blue-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Total</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $sellerStats['total_products'] ?></div>
        <div class="text-sm text-gray-600">Products</div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-check-line text-green-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Active</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $sellerStats['approved_templates'] + $sellerStats['active_services'] ?></div>
        <div class="text-sm text-gray-600">Approved</div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-time-line text-orange-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Pending</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= $sellerStats['pending_approval'] ?></div>
        <div class="text-sm text-gray-600">Review</div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center">
                <i class="ri-download-line text-purple-500 text-xl"></i>
            </div>
            <span class="text-sm font-medium text-gray-500">Total</span>
        </div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($sellerStats['total_downloads']) ?></div>
        <div class="text-sm text-gray-600">Downloads</div>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white rounded-2xl p-6 shadow-sm border mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
        <!-- Search -->
        <div class="flex-1 max-w-md">
            <div class="relative">
                <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="productSearch" placeholder="Search products..." 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
        </div>

        <!-- Filters -->
        <div class="flex items-center space-x-4">
            <select id="categoryFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Categories</option>
                <option value="business">Business</option>
                <option value="creative">Creative</option>
                <option value="marketing">Marketing</option>
                <option value="ecommerce">E-commerce</option>
                <option value="portfolio">Portfolio</option>
            </select>

            <select id="statusFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
                <option value="draft">Draft</option>
            </select>

            <select id="typeFilter" class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <option value="">All Types</option>
                <option value="template">Templates</option>
                <option value="service">Services</option>
            </select>

            <button id="clearFilters" class="px-4 py-3 text-gray-600 hover:text-gray-800 font-medium">
                Clear
            </button>
        </div>
    </div>
</div>

<!-- Products Grid -->
<div id="productsContainer">
    <?php if (empty($products)): ?>
        <div class="bg-white rounded-2xl p-12 shadow-sm border text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-product-hunt-line text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-600 mb-4">No Products Yet</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Start building your marketplace presence by uploading your first template or service. 
                It's easy and takes just a few minutes!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="showAddProductModal('template')" 
                        class="bg-primary text-white px-8 py-4 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                    <i class="ri-layout-grid-line mr-2"></i>Upload Template
                </button>
                <button onclick="showAddProductModal('service')" 
                        class="bg-gray-100 text-gray-700 px-8 py-4 rounded-xl font-semibold hover:bg-gray-200 transition-colors flex items-center">
                    <i class="ri-tools-line mr-2"></i>Add Service
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 product-card" 
                 data-product-id="<?= $product['id'] ?>"
                 data-category="<?= htmlspecialchars($product['category']) ?>"
                 data-status="<?= htmlspecialchars($product['status']) ?>"
                 data-type="<?= htmlspecialchars($product['type']) ?>">
                
                <!-- Product Image -->
                <div class="relative">
                    <img src="<?= htmlspecialchars($product['preview_image']) ?>" 
                         alt="<?= htmlspecialchars($product['title']) ?>" 
                         class="w-full h-48 object-cover">
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 left-4">
                        <?php 
                        $statusColors = [
                            'approved' => 'bg-green-100 text-green-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'draft' => 'bg-gray-100 text-gray-800'
                        ];
                        $statusColor = $statusColors[$product['status']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                            <?= ucfirst($product['status']) ?>
                        </span>
                    </div>

                    <!-- Type Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                            <?= ucfirst($product['type']) ?>
                        </span>
                    </div>

                    <!-- Quick Actions -->
                    <div class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center space-x-2">
                        <button onclick="editProduct(<?= $product['id'] ?>)" 
                                class="bg-white text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                title="Edit Product">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button onclick="viewProduct(<?= $product['id'] ?>)" 
                                class="bg-white text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                title="View Details">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button onclick="duplicateProduct(<?= $product['id'] ?>)" 
                                class="bg-white text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                title="Duplicate">
                            <i class="ri-file-copy-line"></i>
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-bold text-lg text-secondary line-clamp-2">
                            <?= htmlspecialchars($product['title']) ?>
                        </h3>
                        <div class="dropdown ml-2">
                            <button class="text-gray-400 hover:text-gray-600 p-1" onclick="toggleDropdown(<?= $product['id'] ?>)">
                                <i class="ri-more-2-line"></i>
                            </button>
                            <div id="dropdown-<?= $product['id'] ?>" class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-2 z-10 hidden">
                                <button onclick="editProduct(<?= $product['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                    <i class="ri-edit-line mr-2"></i>Edit
                                </button>
                                <button onclick="viewAnalytics(<?= $product['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                    <i class="ri-line-chart-line mr-2"></i>Analytics
                                </button>
                                <button onclick="promoteProduct(<?= $product['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                    <i class="ri-megaphone-line mr-2"></i>Promote
                                </button>
                                <div class="border-t my-2"></div>
                                <button onclick="deleteProduct(<?= $product['id'] ?>)" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-red-600 flex items-center">
                                    <i class="ri-delete-bin-line mr-2"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        <?= htmlspecialchars($product['description']) ?>
                    </p>

                    <!-- Stats -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span class="flex items-center">
                                <i class="ri-download-line mr-1"></i>
                                <?= $product['type'] === 'template' ? $product['downloads_count'] : $product['orders_count'] ?>
                            </span>
                            <span class="flex items-center">
                                <i class="ri-star-line mr-1"></i>
                                <?= number_format($product['rating'], 1) ?>
                            </span>
                            <span class="flex items-center">
                                <i class="ri-eye-line mr-1"></i>
                                <?= number_format($product['views']) ?>
                            </span>
                        </div>
                        <div class="text-lg font-bold text-primary">
                            $<?= number_format($product['price']) ?>
                        </div>
                    </div>

                    <!-- Revenue Info -->
                    <?php 
                    $sales = $product['type'] === 'template' ? $product['downloads_count'] : $product['orders_count'];
                    $revenue = $sales * $product['price'];
                    ?>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Total Revenue</span>
                            <span class="font-semibold text-gray-900">$<?= number_format($revenue) ?></span>
                        </div>
                        <?php if ($sales > 0): ?>
                        <div class="flex items-center justify-between text-sm mt-1">
                            <span class="text-gray-600">Avg. per sale</span>
                            <span class="font-medium text-gray-700">$<?= number_format($revenue / $sales, 2) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2 mt-4">
                        <?php if ($product['status'] === 'approved'): ?>
                        <button onclick="toggleProductStatus(<?= $product['id'] ?>, 'pause')" 
                                class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                            Pause
                        </button>
                        <?php elseif ($product['status'] === 'draft'): ?>
                        <button onclick="submitForReview(<?= $product['id'] ?>)" 
                                class="flex-1 bg-primary text-white py-2 px-4 rounded-lg font-medium hover:bg-primary/90 transition-colors">
                            Submit
                        </button>
                        <?php endif; ?>
                        
                        <button onclick="editProduct(<?= $product['id'] ?>)" 
                                class="flex-1 bg-primary/10 text-primary py-2 px-4 rounded-lg font-medium hover:bg-primary/20 transition-colors">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-8">
            <div class="text-sm text-gray-600">
                Showing <?= count($products) ?> of <?= $sellerStats['total_products'] ?> products
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
    // Search functionality
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const clearFilters = document.getElementById('clearFilters');

    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value;
        const statusValue = statusFilter.value;
        const typeValue = typeFilter.value;

        document.querySelectorAll('.product-card').forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const category = card.dataset.category;
            const status = card.dataset.status;
            const type = card.dataset.type;

            const matchesSearch = title.includes(searchTerm);
            const matchesCategory = !categoryValue || category === categoryValue;
            const matchesStatus = !statusValue || status === statusValue;
            const matchesType = !typeValue || type === typeValue;

            if (matchesSearch && matchesCategory && matchesStatus && matchesType) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);
    statusFilter.addEventListener('change', filterProducts);
    typeFilter.addEventListener('change', filterProducts);

    clearFilters.addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = '';
        statusFilter.value = '';
        typeFilter.value = '';
        filterProducts();
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

function toggleDropdown(productId) {
    const dropdown = document.getElementById(`dropdown-${productId}`);
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdown) {
            menu.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

function editProduct(productId) {
    showEditProductModal(productId);
    document.getElementById(`dropdown-${productId}`).classList.add('hidden');
}

function viewProduct(productId) {
    window.open(`/product-detail.php?id=${productId}`, '_blank');
}

function duplicateProduct(productId) {
    if (confirm('Are you sure you want to duplicate this product?')) {
        fetch('/seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'duplicate_product',
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Product duplicated successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorToast(data.message || 'Failed to duplicate product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        fetch('/seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete_product',
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Product deleted successfully!');
                document.querySelector(`[data-product-id="${productId}"]`).remove();
            } else {
                showErrorToast(data.message || 'Failed to delete product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }
}

function toggleProductStatus(productId, action) {
    fetch('/seller-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'toggle_product_status',
            product_id: productId,
            status_action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Product status updated successfully!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showErrorToast(data.message || 'Failed to update product status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('An error occurred');
    });
}

function submitForReview(productId) {
    if (confirm('Submit this product for review? Once submitted, you cannot edit it until the review is complete.')) {
        fetch('/seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'submit_for_review',
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Product submitted for review!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorToast(data.message || 'Failed to submit product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('An error occurred');
        });
    }
}

function viewAnalytics(productId) {
    window.location.href = `?section=analytics&product=${productId}`;
}

function promoteProduct(productId) {
    showPromoteModal(productId);
}

function showBulkActionModal() {
    // Implementation for bulk actions
    showInfoToast('Bulk actions coming soon!');
}
</script>