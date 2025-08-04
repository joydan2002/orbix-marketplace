<!-- Edit Product Modal -->
<div id="editProductModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-secondary flex items-center">
                        <i class="ri-edit-line mr-3 text-primary"></i>
                        Edit Product
                    </h2>
                    <button onclick="hideModal('editProductModal')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                        <i class="ri-close-line text-xl text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-8">
                <!-- Product Type Display (Read-only) -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-secondary mb-4">Product Type</h3>
                    <div id="currentProductType" class="p-6 border-2 border-primary bg-primary/5 rounded-xl">
                        <div class="flex items-center">
                            <div id="typeIcon" class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="ri-layout-grid-line text-2xl text-blue-500"></i>
                            </div>
                            <div>
                                <h4 id="typeName" class="font-bold text-secondary">Template</h4>
                                <p id="typeDescription" class="text-sm text-gray-600">Digital template product</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Form -->
                <form id="editProductForm">
                    <input type="hidden" name="product_id" id="edit-product-id">
                    <input type="hidden" name="product_type" id="edit-product-type">
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Basic Information</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Title *</label>
                                    <input type="text" name="title" id="edit-title" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="Enter product title">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                    <select name="category" id="edit-category" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        <option value="">Select Category</option>
                                        <option value="web-design">Web Design</option>
                                        <option value="mobile-app">Mobile App</option>
                                        <option value="ui-ux">UI/UX Design</option>
                                        <option value="graphics">Graphics</option>
                                        <option value="development">Development</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="business">Business</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4 edit-template-only">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Technology</label>
                                    <select name="technology" id="edit-technology"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        <option value="">Select Technology</option>
                                        <option value="html">HTML/CSS</option>
                                        <option value="react">React</option>
                                        <option value="vue">Vue.js</option>
                                        <option value="angular">Angular</option>
                                        <option value="wordpress">WordPress</option>
                                        <option value="shopify">Shopify</option>
                                        <option value="figma">Figma</option>
                                        <option value="sketch">Sketch</option>
                                        <option value="photoshop">Photoshop</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="edit-service-only hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Time *</label>
                                    <select name="delivery_time" id="edit-delivery-time"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        <option value="">Select Delivery Time</option>
                                        <option value="1">1 Day</option>
                                        <option value="3">3 Days</option>
                                        <option value="7">1 Week</option>
                                        <option value="14">2 Weeks</option>
                                        <option value="30">1 Month</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (USD) *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                        <input type="number" name="price" id="edit-price" required min="1" step="0.01"
                                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                               placeholder="0.00">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" id="edit-status"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        <option value="active">Active</option>
                                        <option value="draft">Draft</option>
                                        <option value="paused">Paused</option>
                                        <option value="pending">Pending Review</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Files & Media -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Files & Media</h4>
                                
                                <!-- Current Preview Image -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Preview Image</label>
                                    <div id="current-preview-container" class="border border-gray-200 rounded-lg p-4 mb-4">
                                        <img id="current-preview-img" src="" alt="Current preview" class="w-full h-48 object-cover rounded-lg">
                                    </div>
                                </div>
                                
                                <!-- Update Preview Image -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Preview Image</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                        <div class="upload-area" onclick="document.getElementById('edit-preview-upload').click()">
                                            <i class="ri-image-line text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-gray-600 mb-2">Click to upload new preview image</p>
                                            <p class="text-xs text-gray-500">PNG, JPG up to 5MB (1200x800 recommended)</p>
                                        </div>
                                        <input type="file" id="edit-preview-upload" name="preview_image" accept="image/*" class="hidden">
                                    </div>
                                </div>
                                
                                <!-- Product Files (Template only) -->
                                <div class="edit-template-only">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Product Files</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                        <div class="upload-area" onclick="document.getElementById('edit-files-upload').click()">
                                            <i class="ri-file-zip-line text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-gray-600 mb-2">Click to upload new product files</p>
                                            <p class="text-xs text-gray-500">ZIP files up to 100MB</p>
                                        </div>
                                        <input type="file" id="edit-files-upload" name="product_files" accept=".zip,.rar" class="hidden">
                                    </div>
                                    <div id="current-files-info" class="mt-2 text-sm text-gray-600"></div>
                                </div>
                                
                                <!-- Demo URL -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Demo URL</label>
                                    <input type="url" name="demo_url" id="edit-demo-url"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="https://example.com/demo">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Description & Details -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Description & Details</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                    <textarea name="description" id="edit-description" required rows="6"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                              placeholder="Describe your product in detail..."></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                    <input type="text" name="tags" id="edit-tags"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="web, design, template, responsive (comma separated)">
                                    <p class="text-xs text-gray-500 mt-1">Add relevant tags to help customers find your product</p>
                                </div>
                                
                                <!-- Features List -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Key Features</label>
                                    <div id="edit-features-list" class="space-y-2 mb-2">
                                        <!-- Features will be loaded here -->
                                    </div>
                                    <button type="button" onclick="addEditFeature()" class="text-primary hover:text-primary/80 text-sm flex items-center">
                                        <i class="ri-add-line mr-1"></i>Add Feature
                                    </button>
                                </div>
                                
                                <!-- Service Packages (Service only) -->
                                <div class="edit-service-only hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Packages</label>
                                    <div class="space-y-4" id="edit-service-packages">
                                        <!-- Packages will be loaded here -->
                                    </div>
                                    <button type="button" onclick="addEditServicePackage()" class="text-primary hover:text-primary/80 text-sm flex items-center mt-2">
                                        <i class="ri-add-line mr-1"></i>Add Package
                                    </button>
                                </div>
                            </div>
                            
                            <!-- SEO & Marketing -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">SEO & Marketing</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">SEO Title</label>
                                    <input type="text" name="seo_title" id="edit-seo-title"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="SEO optimized title">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                    <textarea name="meta_description" id="edit-meta-description" rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                              placeholder="Brief description for search engines..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Product Statistics -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Product Statistics</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-2xl font-bold text-primary" id="edit-views">0</div>
                                        <div class="text-sm text-gray-600">Views</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-2xl font-bold text-green-600" id="edit-sales">0</div>
                                        <div class="text-sm text-gray-600">Sales</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-2xl font-bold text-yellow-600" id="edit-rating">0.0</div>
                                        <div class="text-sm text-gray-600">Rating</div>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-600" id="edit-revenue">$0</div>
                                        <div class="text-sm text-gray-600">Revenue</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex items-center justify-between mt-8 pt-8 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="confirmDeleteProduct()" 
                                    class="bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600 transition-colors flex items-center">
                                <i class="ri-delete-bin-line mr-2"></i>
                                Delete Product
                            </button>
                            <button type="button" onclick="duplicateCurrentProduct()" 
                                    class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors flex items-center">
                                <i class="ri-file-copy-line mr-2"></i>
                                Duplicate
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="hideModal('editProductModal')" 
                                    class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors flex items-center">
                                <i class="ri-save-line mr-2"></i>
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let editProductType = null;
let currentEditingProductId = null;

// Add feature function for edit modal
function addEditFeature(featureText = '') {
    const featuresList = document.getElementById('edit-features-list');
    const featureDiv = document.createElement('div');
    featureDiv.className = 'flex items-center';
    featureDiv.innerHTML = `
        <input type="text" name="features[]" value="${featureText}"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
               placeholder="Enter a key feature">
        <button type="button" onclick="removeEditFeature(this)" class="ml-2 w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded">
            <i class="ri-delete-bin-line"></i>
        </button>
    `;
    featuresList.appendChild(featureDiv);
}

function removeEditFeature(button) {
    button.parentElement.remove();
}

// Service package functions for edit modal
function addEditServicePackage(packageData = null) {
    const packagesContainer = document.getElementById('edit-service-packages');
    const packageDiv = document.createElement('div');
    packageDiv.className = 'border border-gray-200 rounded-lg p-4';
    
    const packageName = packageData ? packageData.name : '';
    const packagePrice = packageData ? packageData.price : '';
    const packageDelivery = packageData ? packageData.delivery : '';
    const packageDescription = packageData ? packageData.description : '';
    
    packageDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h5 class="font-medium text-gray-700">Package ${packagesContainer.children.length + 1}</h5>
            <button type="button" onclick="removeEditServicePackage(this)" class="text-red-500 hover:bg-red-50 p-1 rounded">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-3">
            <input type="text" name="package_names[]" value="${packageName}" placeholder="Package Name" 
                   class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <input type="number" name="package_prices[]" value="${packagePrice}" placeholder="Price" min="1" step="0.01"
                   class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <input type="number" name="package_delivery[]" value="${packageDelivery}" placeholder="Days" min="1"
                   class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
        </div>
        <textarea name="package_descriptions[]" rows="2" placeholder="Package description..."
                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none">${packageDescription}</textarea>
    `;
    packagesContainer.appendChild(packageDiv);
}

function removeEditServicePackage(button) {
    button.closest('.border').remove();
}

// Enhanced editProduct function
function editProduct(id, type = 'template') {
    currentEditingProductId = id;
    editProductType = type;
    
    // Fetch product data first
    fetch(`seller-api.php?action=get_product&id=${id}&type=${type}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                
                // Update product type display
                updateProductTypeDisplay(type);
                
                // Fill basic form fields
                document.getElementById('edit-product-id').value = product.id;
                document.getElementById('edit-product-type').value = type;
                document.getElementById('edit-title').value = product.title;
                document.getElementById('edit-category').value = getCategorySlug(product.category_id);
                document.getElementById('edit-price').value = product.price;
                document.getElementById('edit-status').value = product.status;
                document.getElementById('edit-description').value = product.description;
                document.getElementById('edit-demo-url').value = product.demo_url || '';
                
                // Handle tags
                if (product.tags) {
                    let tags = '';
                    try {
                        const tagsArray = JSON.parse(product.tags);
                        tags = Array.isArray(tagsArray) ? tagsArray.join(', ') : product.tags;
                    } catch (e) {
                        tags = product.tags;
                    }
                    document.getElementById('edit-tags').value = tags;
                }
                
                // Type-specific fields
                if (type === 'template') {
                    document.querySelectorAll('.edit-template-only').forEach(el => el.classList.remove('hidden'));
                    document.querySelectorAll('.edit-service-only').forEach(el => el.classList.add('hidden'));
                    
                    if (product.technology) {
                        document.getElementById('edit-technology').value = product.technology;
                    }
                } else {
                    document.querySelectorAll('.edit-template-only').forEach(el => el.classList.add('hidden'));
                    document.querySelectorAll('.edit-service-only').forEach(el => el.classList.remove('hidden'));
                    
                    if (product.delivery_time) {
                        document.getElementById('edit-delivery-time').value = product.delivery_time;
                    }
                }
                
                // Update preview image
                if (product.preview_image) {
                    document.getElementById('current-preview-img').src = '/' + product.preview_image;
                    document.getElementById('current-preview-container').style.display = 'block';
                } else {
                    document.getElementById('current-preview-container').style.display = 'none';
                }
                
                // Load features
                loadProductFeatures(product);
                
                // Load service packages if service
                if (type === 'service') {
                    loadServicePackages(product);
                }
                
                // Update statistics
                document.getElementById('edit-views').textContent = product.views || 0;
                document.getElementById('edit-sales').textContent = product.sales || 0;
                document.getElementById('edit-rating').textContent = product.rating || '0.0';
                document.getElementById('edit-revenue').textContent = `$${product.revenue || 0}`;
                
                // Show current files info for templates
                if (type === 'template' && product.download_file) {
                    document.getElementById('current-files-info').textContent = `Current file: ${product.download_file}`;
                }
                
                // Show modal
                showModal('editProductModal');
            } else {
                showErrorToast(data.error || 'Failed to load product data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error loading product data');
        });
}

function updateProductTypeDisplay(type) {
    const typeIcon = document.getElementById('typeIcon');
    const typeName = document.getElementById('typeName');
    const typeDescription = document.getElementById('typeDescription');
    
    if (type === 'service') {
        typeIcon.innerHTML = '<i class="ri-tools-line text-2xl text-green-500"></i>';
        typeIcon.className = 'w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4';
        typeName.textContent = 'Service';
        typeDescription.textContent = 'Custom service offering';
    } else {
        typeIcon.innerHTML = '<i class="ri-layout-grid-line text-2xl text-blue-500"></i>';
        typeIcon.className = 'w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4';
        typeName.textContent = 'Template';
        typeDescription.textContent = 'Digital template product';
    }
}

function loadProductFeatures(product) {
    const featuresList = document.getElementById('edit-features-list');
    featuresList.innerHTML = '';
    
    // Load existing features
    if (product.features) {
        const features = product.features.split(',').filter(f => f.trim());
        features.forEach(feature => {
            addEditFeature(feature.trim());
        });
    }
    
    // Ensure at least one empty feature field
    if (featuresList.children.length === 0) {
        addEditFeature();
    }
}

function loadServicePackages(product) {
    const packagesContainer = document.getElementById('edit-service-packages');
    packagesContainer.innerHTML = '';
    
    // For now, add one empty package - this can be enhanced to load actual packages
    addEditServicePackage();
}

function getCategorySlug(categoryId) {
    const categoryMap = {
        1: 'business',
        2: 'mobile-app',
        3: 'ui-ux',
        4: 'graphics',
        5: 'development',
        9: 'marketing',
        12: 'web-design',
        13: 'other'
    };
    return categoryMap[categoryId] || 'other';
}

function confirmDeleteProduct() {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        deleteProduct(editProductType, currentEditingProductId);
    }
}

function duplicateCurrentProduct() {
    if (confirm('Are you sure you want to duplicate this product?')) {
        duplicateProduct(editProductType, currentEditingProductId);
    }
}

// Handle edit form submission
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update_product');
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i>Updating...';
    submitBtn.disabled = true;
    
    fetch('seller-api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Product updated successfully!');
            hideModal('editProductModal');
            setTimeout(() => loadSection('products'), 1500);
        } else {
            throw new Error(data.error || 'Failed to update product');
        }
    })
    .catch(error => {
        console.error('Update product error:', error);
        showErrorToast(error.message || 'Failed to update product');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// File upload preview for edit modal
document.getElementById('edit-preview-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('current-preview-img').src = e.target.result;
            document.getElementById('current-preview-container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>