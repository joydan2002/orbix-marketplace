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
                <form id="editProductForm">
                    <input type="hidden" name="product_id" id="edit-product-id">
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Basic Information</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Title *</label>
                                    <input type="text" name="title" id="edit-title" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
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
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (USD) *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                        <input type="number" name="price" id="edit-price" required min="1" step="0.01"
                                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" id="edit-status"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                        <option value="active">Active</option>
                                        <option value="draft">Draft</option>
                                        <option value="paused">Paused</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Current Preview -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Current Preview</h4>
                                <div id="current-preview" class="border border-gray-200 rounded-lg p-4">
                                    <img id="current-preview-img" src="" alt="Current preview" class="w-full h-32 object-cover rounded-lg">
                                </div>
                                
                                <!-- Update Preview -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Preview Image</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-primary transition-colors">
                                        <div onclick="document.getElementById('edit-preview-upload').click()" class="cursor-pointer">
                                            <i class="ri-image-line text-2xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-600">Click to update preview image</p>
                                        </div>
                                        <input type="file" id="edit-preview-upload" name="preview_image" accept="image/*" class="hidden">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Description -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Description & Details</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                    <textarea name="description" id="edit-description" required rows="8"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                    <input type="text" name="tags" id="edit-tags"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="web, design, template, responsive (comma separated)">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Demo URL</label>
                                    <input type="url" name="demo_url" id="edit-demo-url"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                </div>
                            </div>
                            
                            <!-- Statistics -->
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
                            <button type="button" onclick="deleteProduct()" 
                                    class="bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600 transition-colors flex items-center">
                                <i class="ri-delete-bin-line mr-2"></i>
                                Delete Product
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
function editProduct(productId) {
    // Fetch product data
    fetch(`seller-api.php?action=get_product&id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                
                // Fill form fields
                document.getElementById('edit-product-id').value = product.id;
                document.getElementById('edit-title').value = product.title;
                document.getElementById('edit-category').value = product.category;
                document.getElementById('edit-price').value = product.price;
                document.getElementById('edit-status').value = product.status;
                document.getElementById('edit-description').value = product.description;
                document.getElementById('edit-tags').value = product.tags || '';
                document.getElementById('edit-demo-url').value = product.demo_url || '';
                
                // Update preview image
                if (product.preview_image) {
                    document.getElementById('current-preview-img').src = product.preview_image;
                }
                
                // Update statistics
                document.getElementById('edit-views').textContent = product.views || 0;
                document.getElementById('edit-sales').textContent = product.sales || 0;
                document.getElementById('edit-rating').textContent = product.rating || '0.0';
                document.getElementById('edit-revenue').textContent = `$${product.revenue || 0}`;
                
                showModal('editProductModal');
            } else {
                showErrorToast('Failed to load product data');
            }
        })
        .catch(error => {
            console.error('Edit product error:', error);
            showErrorToast('Failed to load product data');
        });
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
            setTimeout(() => window.location.reload(), 1500);
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

function deleteProduct() {
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }
    
    const productId = document.getElementById('edit-product-id').value;
    
    fetch('seller-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_product&id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Product deleted successfully!');
            hideModal('editProductModal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.error || 'Failed to delete product');
        }
    })
    .catch(error => {
        console.error('Delete product error:', error);
        showErrorToast(error.message || 'Failed to delete product');
    });
}

// Edit preview upload preview
document.getElementById('edit-preview-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('current-preview-img').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>