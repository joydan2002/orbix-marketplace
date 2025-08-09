<!-- Add Product Modal -->
<div id="addProductModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-secondary flex items-center">
                        <i class="ri-add-circle-line mr-3 text-primary"></i>
                        Add New Product
                    </h2>
                    <button onclick="hideModal('addProductModal')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                        <i class="ri-close-line text-xl text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-8">
                <!-- Product Type Selection -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-secondary mb-4">What would you like to sell?</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <button onclick="selectProductType('template')" 
                                class="product-type-btn p-6 border-2 border-gray-200 rounded-xl hover:border-primary hover:bg-primary/5 transition-all text-left group"
                                data-type="template">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-500 rounded-lg flex items-center justify-center mr-4 transition-colors">
                                    <i class="ri-layout-grid-line text-2xl text-blue-500 group-hover:text-white transition-colors"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-secondary">Template</h4>
                                    <p class="text-sm text-gray-600">Sell digital templates</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">Perfect for website templates, UI kits, graphics, and downloadable digital assets.</p>
                        </button>
                        
                        <button onclick="selectProductType('service')" 
                                class="product-type-btn p-6 border-2 border-gray-200 rounded-xl hover:border-primary hover:bg-primary/5 transition-all text-left group"
                                data-type="service">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 group-hover:bg-green-500 rounded-lg flex items-center justify-center mr-4 transition-colors">
                                    <i class="ri-tools-line text-2xl text-green-500 group-hover:text-white transition-colors"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-secondary">Service</h4>
                                    <p class="text-sm text-gray-600">Offer custom services</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">Great for custom development, design work, consulting, and personalized services.</p>
                        </button>
                    </div>
                </div>
                
                <!-- Product Form -->
                <form id="addProductForm" class="hidden">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Basic Information</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Title *</label>
                                    <input type="text" name="title" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="Enter product title">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                    <select name="category" required
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Technology</label>
                                    <select name="technology"
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
                                
                                <div class="service-only hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Time *</label>
                                    <select name="delivery_time"
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
                                        <input type="number" name="price" required min="1" step="0.01"
                                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                               placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Files Upload -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">Files & Media</h4>
                                
                                <!-- Preview Image -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Image *</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                        <div class="upload-area" onclick="document.getElementById('preview-upload').click()">
                                            <i class="ri-image-line text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-gray-600 mb-2">Click to upload preview image</p>
                                            <p class="text-xs text-gray-500">PNG, JPG up to 5MB (1200x800 recommended)</p>
                                        </div>
                                        <input type="file" id="preview-upload" name="preview_image" accept="image/*" class="hidden">
                                    </div>
                                </div>
                                
                                <!-- Product Files (Template only) -->
                                <div class="template-only">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Files *</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                        <div class="upload-area" onclick="document.getElementById('files-upload').click()">
                                            <i class="ri-file-zip-line text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-gray-600 mb-2">Click to upload product files</p>
                                            <p class="text-xs text-gray-500">ZIP files up to 100MB</p>
                                        </div>
                                        <input type="file" id="files-upload" name="product_files" accept=".zip,.rar" class="hidden">
                                    </div>
                                </div>
                                
                                <!-- Demo URL -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Demo URL</label>
                                    <input type="url" name="demo_url"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="https://example.com/demo">
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
                                    <textarea name="description" required rows="6"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                              placeholder="Describe your product in detail..."></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                    <input type="text" name="tags"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="web, design, template, responsive (comma separated)">
                                    <p class="text-xs text-gray-500 mt-1">Add relevant tags to help customers find your product</p>
                                </div>
                                
                                <!-- Features List -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Key Features</label>
                                    <div id="features-list" class="space-y-2 mb-2">
                                        <div class="flex items-center">
                                            <input type="text" name="features[]" 
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                                   placeholder="Enter a key feature">
                                            <button type="button" onclick="removeFeature(this)" class="ml-2 w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" onclick="addFeature()" class="text-primary hover:text-primary/80 text-sm flex items-center">
                                        <i class="ri-add-line mr-1"></i>Add Feature
                                    </button>
                                </div>
                                
                                <!-- Service Packages (Service only) -->
                                <div class="service-only hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Packages</label>
                                    <div class="space-y-4" id="service-packages">
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="grid grid-cols-3 gap-3 mb-3">
                                                <input type="text" name="package_names[]" placeholder="Basic" 
                                                       class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <input type="number" name="package_prices[]" placeholder="Price" min="1" step="0.01"
                                                       class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <input type="number" name="package_delivery[]" placeholder="Days" min="1"
                                                       class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            </div>
                                            <textarea name="package_descriptions[]" rows="2" placeholder="Package description..."
                                                      class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"></textarea>
                                        </div>
                                    </div>
                                    <button type="button" onclick="addServicePackage()" class="text-primary hover:text-primary/80 text-sm flex items-center mt-2">
                                        <i class="ri-add-line mr-1"></i>Add Package
                                    </button>
                                </div>
                            </div>
                            
                            <!-- SEO & Marketing -->
                            <div>
                                <h4 class="font-semibold text-secondary mb-4">SEO & Marketing</h4>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">SEO Title</label>
                                    <input type="text" name="seo_title"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="SEO optimized title">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                    <textarea name="meta_description" rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                              placeholder="Brief description for search engines..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex items-center justify-between mt-8 pt-8 border-t border-gray-200">
                        <div class="flex items-center">
                            <input type="checkbox" id="terms-agree" name="terms_agree" required class="mr-2">
                            <label for="terms-agree" class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-primary hover:underline">Terms of Service</a> and 
                                <a href="#" class="text-primary hover:underline">Content Policy</a>
                            </label>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="hideModal('addProductModal')" 
                                    class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors flex items-center">
                                <i class="ri-upload-line mr-2"></i>
                                Publish Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let selectedProductType = null;

function selectProductType(type) {
    selectedProductType = type;
    
    // Update button styles
    document.querySelectorAll('.product-type-btn').forEach(btn => {
        btn.classList.remove('border-primary', 'bg-primary/5');
        btn.classList.add('border-gray-200');
    });
    
    document.querySelector(`[data-type="${type}"]`).classList.remove('border-gray-200');
    document.querySelector(`[data-type="${type}"]`).classList.add('border-primary', 'bg-primary/5');
    
    // Show/hide form sections
    document.getElementById('addProductForm').classList.remove('hidden');
    
    if (type === 'template') {
        document.querySelectorAll('.template-only').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.service-only').forEach(el => el.classList.add('hidden'));
    } else {
        document.querySelectorAll('.template-only').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.service-only').forEach(el => el.classList.remove('hidden'));
    }
}

function addFeature() {
    const featuresList = document.getElementById('features-list');
    const featureDiv = document.createElement('div');
    featureDiv.className = 'flex items-center';
    featureDiv.innerHTML = `
        <input type="text" name="features[]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
               placeholder="Enter a key feature">
        <button type="button" onclick="removeFeature(this)" class="ml-2 w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded">
            <i class="ri-delete-bin-line"></i>
        </button>
    `;
    featuresList.appendChild(featureDiv);
}

function removeFeature(button) {
    button.parentElement.remove();
}

function addServicePackage() {
    const packagesContainer = document.getElementById('service-packages');
    const packageDiv = document.createElement('div');
    packageDiv.className = 'border border-gray-200 rounded-lg p-4';
    packageDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h5 class="font-medium text-gray-700">Package ${packagesContainer.children.length + 1}</h5>
            <button type="button" onclick="removeServicePackage(this)" class="text-red-500 hover:bg-red-50 p-1 rounded">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-3">
            <input type="text" name="package_names[]" placeholder="Package Name" 
                   class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <input type="number" name="package_prices[]" placeholder="Price" min="1" step="0.01"
                   class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <input type="number" name="package_delivery[]" placeholder="Days" min="1"
                   class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary">
        </div>
        <textarea name="package_descriptions[]" rows="2" placeholder="Package description..."
                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"></textarea>
    `;
    packagesContainer.appendChild(packageDiv);
}

function removeServicePackage(button) {
    button.closest('.border').remove();
}

// Handle form submission
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!selectedProductType) {
        showErrorToast('Please select a product type');
        return;
    }
    
    const formData = new FormData(this);
    formData.append('action', 'add_product');
    formData.append('type', selectedProductType); // Thêm field type
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i>Publishing...';
    submitBtn.disabled = true;
    
    fetch('../api/seller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Product published successfully!');
            hideModal('addProductModal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.error || 'Failed to publish product');
        }
    })
    .catch(error => {
        console.error('Add product error:', error);
        showErrorToast(error.message || 'Failed to publish product');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// File upload preview
document.getElementById('preview-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const uploadArea = document.querySelector('.upload-area');
            uploadArea.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover rounded-lg mb-2">
                <p class="text-sm text-gray-600">Click to change image</p>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>