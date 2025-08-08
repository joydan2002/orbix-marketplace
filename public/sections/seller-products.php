<?php
/**
 * Seller Products Management
 * Display real templates and services from database
 */

// Load real data from database
require_once 'seller-data-loader.php';
require_once '../../config/cloudinary-config.php'; // Fix path for Cloudinary support

// Combine templates and services for unified product view
$allProducts = [];
foreach ($templates as $template) {
    $allProducts[] = [
        'id' => $template['id'],
        'type' => 'template',
        'title' => $template['title'],
        'description' => $template['description'],
        'price' => $template['price'],
        'status' => $template['status'],
        'views' => $template['views_count'],
        'sales' => $template['downloads_count'],
        'rating' => $template['rating'],
        'reviews' => $template['reviews_count'],
        'created_at' => $template['created_at'],
        'preview_image' => $template['preview_image'],
        'technology' => $template['technology']
    ];
}
foreach ($services as $service) {
    $allProducts[] = [
        'id' => $service['id'],
        'type' => 'service',
        'title' => $service['title'],
        'description' => $service['description'],
        'price' => $service['price'],
        'status' => $service['status'],
        'views' => $service['views_count'],
        'sales' => $service['orders_count'],
        'rating' => $service['rating'],
        'reviews' => $service['reviews_count'],
        'created_at' => $service['created_at'],
        'preview_image' => $service['preview_image'] ?? null,
        'technology' => $service['delivery_time'] ? $service['delivery_time'] . ' days delivery' : '7 days delivery'
    ];
}

// Sort by creation date
usort($allProducts, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>

<!-- Products Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Products</h1>
            <p class="text-gray-600">Manage your templates and services</p>
        </div>
        <button onclick="showAddProductModal()" 
                class="bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 transition-colors flex items-center space-x-2">
            <i class="ri-add-line text-lg"></i>
            <span>Add Product</span>
        </button>
    </div>
    
    <!-- Products Stats -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= count($allProducts) ?></div>
                    <div class="text-sm text-gray-600">Total Products</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-store-line text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-600"><?= $totalTemplates ?></div>
                    <div class="text-sm text-gray-600">Templates</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-layout-line text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600"><?= $totalServices ?></div>
                    <div class="text-sm text-gray-600">Services</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-tools-line text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-yellow-600"><?= $templateStats['active'] + $serviceStats['active'] ?></div>
                    <div class="text-sm text-gray-600">Active Products</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ri-check-line text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products List -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <!-- Search and Filter -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-secondary">All Products</h3>
            <div class="flex items-center space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           id="productSearch"
                           placeholder="Search products..." 
                           class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
                    <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Type Filter -->
                <select id="productTypeFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Types</option>
                    <option value="template">Templates</option>
                    <option value="service">Services</option>
                </select>
                
                <!-- Status Filter -->
                <select id="productStatusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Status</option>
                    <option value="approved">Active</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Products Container -->
    <div id="productsContainer">
        <?php if (empty($allProducts)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-store-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary mb-2">No Products Yet</h3>
            <p class="text-gray-600 mb-6">Start by creating your first template or service</p>
            <button onclick="showAddProductModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 transition-colors">
                Add Your First Product
            </button>
        </div>
        <?php else: ?>
        
        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <?php foreach ($allProducts as $product): ?>
            <div class="product-item bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 h-full flex flex-col" 
                 data-id="<?= $product['id'] ?>"
                 data-type="<?= $product['type'] ?>" 
                 data-status="<?= $product['status'] ?>">
                
                <!-- Product Image -->
                <div class="relative h-48 bg-gradient-to-br from-primary/10 to-primary/5 flex-shrink-0">
                    <?php if ($product['preview_image']): ?>
                    <img src="<?= getOptimizedImageUrl($product['preview_image'], 'thumb') ?>" 
                         alt="<?= htmlspecialchars($product['title']) ?>"
                         class="w-full h-full object-cover"
                         loading="lazy">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="ri-<?= $product['type'] === 'template' ? 'layout' : 'tools' ?>-line text-4xl text-primary/60"></i>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Status Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                               <?php 
                               switch($product['status']) {
                                   case 'approved':
                                   case 'active':
                                       echo 'bg-green-100 text-green-800';
                                       break;
                                   case 'pending':
                                       echo 'bg-yellow-100 text-yellow-800';
                                       break;
                                   case 'draft':
                                       echo 'bg-gray-100 text-gray-800';
                                       break;
                                   default:
                                       echo 'bg-red-100 text-red-800';
                               }
                               ?>">
                            <?= ucfirst($product['status']) ?>
                        </span>
                    </div>
                    
                    <!-- Type Badge -->
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?= $product['type'] === 'template' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                            <?= ucfirst($product['type']) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Product Content -->
                <div class="p-5 flex flex-col flex-grow">
                    <!-- Title Section -->
                    <div class="mb-3">
                        <h3 class="text-lg font-semibold text-secondary mb-2 line-clamp-2 min-h-[3.5rem] leading-7">
                            <?= htmlspecialchars($product['title']) ?>
                        </h3>
                        
                        <div class="text-sm text-gray-600 h-5 flex items-center">
                            <?= htmlspecialchars($product['technology']) ?>
                        </div>
                    </div>
                    
                    <!-- Stats Section -->
                    <div class="mb-4 flex-grow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <div class="flex items-center space-x-1">
                                    <i class="ri-eye-line text-gray-400"></i>
                                    <span><?= number_format($product['views']) ?></span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <i class="ri-shopping-cart-line text-gray-400"></i>
                                    <span><?= number_format($product['sales']) ?></span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <i class="ri-star-fill text-yellow-400"></i>
                                    <span><?= number_format($product['rating'], 1) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Price Section -->
                        <div class="mt-3">
                            <div class="text-xl font-bold text-primary">
                                $<?= number_format($product['price'], 2) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions Section - Always at bottom -->
                    <div class="mt-auto pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-2">
                            <button onclick="editProductFromList(<?= $product['id'] ?>)" 
                                    class="flex-1 bg-primary/10 text-primary px-4 py-2.5 rounded-lg hover:bg-primary/20 transition-colors text-sm font-medium">
                                Edit
                            </button>
                            
                            <div class="relative">
                                <button onclick="toggleProductMenu(<?= $product['id'] ?>)" 
                                        class="p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="ri-more-2-line text-lg"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="productMenu-<?= $product['id'] ?>" class="hidden absolute right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10 min-w-40">
                                    <button onclick="duplicateProduct('<?= $product['type'] ?>', <?= $product['id'] ?>)" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                        <i class="ri-file-copy-line mr-2"></i>Duplicate
                                    </button>
                                    <button onclick="promoteProduct('<?= $product['type'] ?>', <?= $product['id'] ?>)" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="ri-megaphone-line mr-2"></i>Promote
                                    </button>
                                    <button onclick="downloadAnalytics('<?= $product['type'] ?>', <?= $product['id'] ?>)" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="ri-bar-chart-line mr-2"></i>Analytics
                                    </button>
                                    <hr class="my-1">
                                    <button onclick="deleteProduct('<?= $product['type'] ?>', <?= $product['id'] ?>)" 
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-b-lg">
                                        <i class="ri-delete-bin-line mr-2"></i>Delete
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

<!-- Product Management Scripts -->
<script>
// Expose server-side product data to JavaScript
const productsData = <?php echo json_encode($allProducts, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;

// Helper to get product by ID
function getProductById(id) {
    return productsData.find(p => p.id === id) || {};
}

// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearch');
    const typeFilter = document.getElementById('productTypeFilter');
    const statusFilter = document.getElementById('productStatusFilter');
    const productItems = document.querySelectorAll('.product-item');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeFilter_val = typeFilter.value;
        const statusFilter_val = statusFilter.value;
        
        productItems.forEach(item => {
            const title = item.querySelector('h3').textContent.toLowerCase();
            const technology = item.querySelector('.text-gray-600').textContent.toLowerCase();
            const type = item.dataset.type;
            const status = item.dataset.status;
            
            const matchesSearch = title.includes(searchTerm) || technology.includes(searchTerm);
            const matchesType = !typeFilter_val || type === typeFilter_val;
            const matchesStatus = !statusFilter_val || status === statusFilter_val;
            
            if (matchesSearch && matchesType && matchesStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterProducts);
    typeFilter.addEventListener('change', filterProducts);
    statusFilter.addEventListener('change', filterProducts);
});

function editProductFromList(id) {
    // Get product data to determine type
    const product = getProductById(id);
    
    console.log('🔧 editProductFromList called with:', { id, type: product.type, product });
    
    // Call the global editProduct function with correct type
    if (typeof window.editProduct === 'function') {
        console.log('✅ Calling global editProduct with type:', product.type);
        window.editProduct(id, product.type);
    } else {
        console.error('❌ Global editProduct function not found');
        // Fallback: make direct API call to load product data
        console.log('🔧 Using fallback API call with type:', product.type);
        
        fetch(`seller-api.php?action=get_product&id=${id}&type=${product.type}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                
                // Fill the modal with product data
                currentEditingProductId = id;
                editProductType = product.type;
                
                // Set basic information
                document.getElementById('edit-product-id').value = id;
                document.getElementById('edit-product-type').value = product.type;
                document.getElementById('edit-title').value = product.title;
                document.getElementById('edit-price').value = product.price;
                document.getElementById('edit-status').value = product.status;
                document.getElementById('edit-description').value = product.description;
                
                // Set product type display
                const typeIcon = document.getElementById('typeIcon');
                const typeName = document.getElementById('typeName');
                const typeDescription = document.getElementById('typeDescription');
                
                if (product.type === 'template') {
                    typeIcon.innerHTML = '<i class="ri-layout-grid-line text-2xl text-blue-500"></i>';
                    typeName.textContent = 'Template';
                    typeDescription.textContent = 'Digital template product';
                    
                    // Show template-specific fields
                    document.querySelectorAll('.edit-template-only').forEach(el => el.classList.remove('hidden'));
                    document.querySelectorAll('.edit-service-only').forEach(el => el.classList.add('hidden'));
                } else {
                    typeIcon.innerHTML = '<i class="ri-tools-line text-2xl text-green-500"></i>';
                    typeName.textContent = 'Service';
                    typeDescription.textContent = 'Professional service offering';
                    
                    // Show service-specific fields
                    document.querySelectorAll('.edit-service-only').forEach(el => el.classList.remove('hidden'));
                    document.querySelectorAll('.edit-template-only').forEach(el => el.classList.add('hidden'));
                }
                
                // Set statistics
                document.getElementById('edit-views').textContent = product.views || '0';
                document.getElementById('edit-sales').textContent = product.sales || '0';
                document.getElementById('edit-rating').textContent = (product.rating || 0).toFixed(1);
                document.getElementById('edit-revenue').textContent = '$' + ((product.price * product.sales) || 0).toFixed(2);
                
                // Set preview image if exists
                if (product.preview_image) {
                    document.getElementById('current-preview-img').src = product.preview_image;
                    document.getElementById('current-preview-container').style.display = 'block';
                } else {
                    document.getElementById('current-preview-container').style.display = 'none';
                }
                
                // Show the modal
                showModal('editProductModal');
            } else {
                showToast('Failed to load product data', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            showToast('Error loading product data', 'error');
        });
    }
}

function viewProductModal(id) {
    // Get product data to determine type
    const product = getProductById(id);
    
    console.log('👁️ viewProductModal called with:', { id, type: product.type, product });
    
    // Call the global viewProduct function with correct type
    if (typeof window.viewProduct === 'function') {
        console.log('✅ Calling global viewProduct with type:', product.type);
        window.viewProduct(id, product.type);
    } else {
        console.error('❌ Global viewProduct function not found');
        showErrorToast('View function not available');
    }
}

function toggleProductMenu(id) {
    const menu = document.getElementById(`productMenu-${id}`);
    const allMenus = document.querySelectorAll('[id^="productMenu-"]');
    
    // Close other menus
    allMenus.forEach(m => {
        if (m.id !== `productMenu-${id}`) {
            m.classList.add('hidden');
        }
    });
    
    menu.classList.toggle('hidden');
}

function duplicateProduct(type, id) {
    showConfirm('Are you sure you want to duplicate this product?', () => {
        fetch('seller-api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'duplicate_product',
                type: type,
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Product duplicated successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    });
}

function deleteProduct(type, id) {
    showConfirm('Are you sure you want to delete this product? This action cannot be undone.', () => {
        fetch('seller-api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'delete_product',
                type: type,
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Product deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    });
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="productMenu-"]') && !event.target.closest('button[onclick*="toggleProductMenu"]')) {
        document.querySelectorAll('[id^="productMenu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
</script>