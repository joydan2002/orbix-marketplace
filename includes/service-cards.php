<?php
/**
 * Service Cards Display - New Design with Database Integration
 * Uses modern design from backup with orange theme and English translation
 */

// Get services from database
try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order ASC, id ASC LIMIT 6");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If we don't have enough featured services, get the most popular ones
    if (count($services) < 6) {
        $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY is_featured DESC, orders_count DESC, rating DESC LIMIT 6");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Log error and show empty state
    error_log("Database error in service-cards.php: " . $e->getMessage());
    $services = [];
}
?>

<!-- Services Section -->
<section id="services" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-secondary">
                Our Services
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                We provide comprehensive services to help you build and maintain 
                professional websites with cutting-edge technology.
            </p>
        </div>
        
        <?php if (!empty($services)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($services as $service): ?>
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-2">
                <div class="w-16 h-16 flex items-center justify-center bg-primary/10 rounded-lg mb-6">
                    <i class="<?php echo htmlspecialchars($service['icon']); ?> text-2xl text-primary"></i>
                </div>
                
                <h3 class="text-xl font-bold mb-3 text-secondary"><?php echo htmlspecialchars($service['title']); ?></h3>
                
                <p class="text-gray-600 mb-4 leading-relaxed">
                    <?php echo htmlspecialchars($service['description']); ?>
                </p>
                
                <ul class="space-y-2 mb-6">
                    <?php 
                    // Handle features from database (JSON format)
                    $features = [];
                    if (isset($service['features']) && !empty($service['features'])) {
                        $features = json_decode($service['features'], true) ?: [];
                    }
                    
                    foreach ($features as $feature): 
                    ?>
                    <li class="flex items-start">
                        <i class="ri-check-line text-green-500 mt-1 mr-2"></i>
                        <span class="text-gray-600"><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="flex items-center justify-between">
                    <div class="text-lg font-bold text-primary">
                        Starting at $<?php echo number_format($service['price'], 0); ?>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="window.location.href='service-detail.php?id=<?= $service['id'] ?>'" class="w-10 h-10 flex items-center justify-center border-2 border-gray-200 rounded-lg hover:border-primary hover:text-primary transition-colors">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button onclick="handleOrderService(<?= $service['id'] ?>, '<?= addslashes($service['title']) ?>', <?= $service['price'] ?>, '<?= addslashes($service['preview_image']) ?>', '<?= addslashes($service['seller_name']) ?>')" 
                                class="w-10 h-10 flex items-center justify-center bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                            <i class="ri-shopping-cart-line"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">No services available at the moment.</p>
            <p class="text-gray-400 text-sm mt-2">Please check back later or contact support if this persists.</p>
        </div>
        <?php endif; ?>

        <!-- Domain Search Section -->
        <div class="mt-20 bg-gradient-to-r from-primary to-primary/80 rounded-xl p-8 md:p-12 shadow-xl">
            <div class="text-center mb-8">
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">
                    Find The Perfect Domain For Your Website
                </h3>
                <p class="text-white/90">
                    Check if your desired domain name is available and register it today.
                </p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <div class="relative">
                            <input 
                                id="domainInput"
                                type="text" 
                                placeholder="Enter your desired domain name..." 
                                class="w-full px-6 py-4 rounded-lg border-none text-gray-800 focus:outline-none text-lg pr-32"
                            />
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <select id="extensionSelect" class="appearance-none bg-transparent border-none text-gray-500 focus:outline-none pr-8 font-medium">
                                    <option value=".com">.com</option>
                                </select>
                                <div class="absolute right-0 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                    <i class="ri-arrow-down-s-line text-gray-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button id="checkDomainBtn" class="bg-white text-primary px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors whitespace-nowrap">
                        <span class="btn-text">Check Availability</span>
                        <i class="ri-loader-4-line animate-spin hidden btn-loading"></i>
                    </button>
                </div>
                
                <!-- Domain Extensions Grid (loaded from database) -->
                <div id="extensionGrid" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <!-- Extensions will be loaded here -->
                </div>
                
                <!-- Search Results -->
                <div id="domainResults" class="mt-8 hidden">
                    <!-- Search results will be displayed here -->
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const domainInput = document.getElementById('domainInput');
    const extensionSelect = document.getElementById('extensionSelect');
    const checkDomainBtn = document.getElementById('checkDomainBtn');
    const extensionGrid = document.getElementById('extensionGrid');
    const domainResults = document.getElementById('domainResults');
    
    // Load domain extensions from database
    loadDomainExtensions();
    
    // Search domain on button click
    checkDomainBtn.addEventListener('click', function() {
        const domain = domainInput.value.trim();
        const extension = extensionSelect.value;
        
        if (!domain) {
            alert('Please enter a domain name');
            return;
        }
        
        // Validate domain input - don't allow extensions in domain name
        const cleanDomain = domain.replace(/\.(com|net|org|io|co|biz|info|tech|app|dev)$/i, '');
        
        if (!cleanDomain || cleanDomain.length < 2) {
            alert('Please enter a valid domain name (at least 2 characters)');
            return;
        }
        
        // Update input with clean domain
        domainInput.value = cleanDomain;
        
        searchDomain(cleanDomain, extension);
    });
    
    // Search domain on Enter key
    domainInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            checkDomainBtn.click();
        }
    });
    
    async function loadDomainExtensions() {
        try {
            const response = await fetch('api.php?action=domain-extensions');
            const data = await response.json();
            
            if (data.success) {
                displayExtensions(data.data);
                populateExtensionSelect(data.data);
            } else {
                console.error('Failed to load domain extensions from database');
                extensionGrid.innerHTML = '<div class="col-span-full text-center text-white/70">Unable to load domain extensions</div>';
            }
        } catch (error) {
            console.error('Error loading extensions:', error);
            extensionGrid.innerHTML = '<div class="col-span-full text-center text-white/70">Error loading domain extensions</div>';
        }
    }
    
    function displayExtensions(extensions) {
        extensionGrid.innerHTML = extensions.slice(0, 4).map(ext => `
            <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                <p class="text-white font-medium">${ext.extension}</p>
                <p class="text-white/90 text-sm">$${parseFloat(ext.price).toFixed(2)}/year</p>
            </div>
        `).join('');
    }
    
    function populateExtensionSelect(extensions) {
        extensionSelect.innerHTML = extensions.map(ext => 
            `<option value="${ext.extension}">${ext.extension}</option>`
        ).join('');
    }
    
    async function searchDomain(domain, extension) {
        setLoading(true);
        domainResults.classList.add('hidden');
        
        try {
            const response = await fetch(`api.php?action=domain-search&domain=${encodeURIComponent(domain)}&extension=${encodeURIComponent(extension)}`);
            const data = await response.json();
            
            if (data.success) {
                displaySearchResults(data);
            } else {
                alert(data.message || 'Error searching domain');
            }
        } catch (error) {
            console.error('Error searching domain:', error);
            alert('Error searching domain. Please try again.');
        } finally {
            setLoading(false);
        }
    }
    
    function displaySearchResults(data) {
        const { domain_name, searched_extension, exact_match, all_extensions, suggestions } = data;
        
        let resultsHTML = `
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h4 class="text-xl font-bold text-gray-800 mb-4">Search Results for "${domain_name}${searched_extension}"</h4>
        `;
        
        // Main domain result
        if (exact_match) {
            const isAvailable = exact_match.is_available;
            const statusClass = isAvailable ? 'text-green-600' : 'text-red-600';
            const statusIcon = isAvailable ? 'ri-check-circle-line' : 'ri-close-circle-line';
            const statusText = isAvailable ? 'Available' : 'Not Available';
            
            resultsHTML += `
                <div class="flex items-center justify-between p-4 border rounded-lg mb-4 ${isAvailable ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <div class="flex items-center">
                        <i class="${statusIcon} text-xl ${statusClass} mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-800">${domain_name}${searched_extension}</p>
                            <p class="text-sm ${statusClass}">${statusText}</p>
                        </div>
                    </div>
                    ${isAvailable ? `
                        <div class="text-right">
                            <p class="font-bold text-gray-800">$${parseFloat(exact_match.price).toFixed(2)}/year</p>
                            ${exact_match.is_premium ? '<span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Premium</span>' : ''}
                            <button class="mt-2 bg-primary text-white px-4 py-2 rounded hover:bg-primary/90 transition-colors">
                                Add to Cart
                            </button>
                        </div>
                    ` : ''}
                </div>
            `;
        } else {
            // Domain not in database = available
            resultsHTML += `
                <div class="flex items-center justify-between p-4 border border-green-200 bg-green-50 rounded-lg mb-4">
                    <div class="flex items-center">
                        <i class="ri-check-circle-line text-xl text-green-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-800">${domain_name}${searched_extension}</p>
                            <p class="text-sm text-green-600">Available</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">$12.99/year</p>
                        <button class="mt-2 bg-primary text-white px-4 py-2 rounded hover:bg-primary/90 transition-colors">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Other extensions
        if (all_extensions.length > 1) {
            resultsHTML += `
                <h5 class="font-medium text-gray-700 mb-3">Other Available Extensions:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
            `;
            
            all_extensions.forEach(ext => {
                if (ext.extension !== searched_extension) {
                    const isAvailable = ext.is_available;
                    const statusClass = isAvailable ? 'text-green-600' : 'text-gray-400';
                    const statusIcon = isAvailable ? 'ri-check-circle-line' : 'ri-close-circle-line';
                    
                    resultsHTML += `
                        <div class="flex items-center justify-between p-3 border rounded ${isAvailable ? 'border-gray-200' : 'border-gray-100 bg-gray-50'}">
                            <div class="flex items-center">
                                <i class="${statusIcon} ${statusClass} mr-2"></i>
                                <span class="text-gray-800">${domain_name}${ext.extension}</span>
                            </div>
                            ${isAvailable ? `
                                <div class="text-right">
                                    <span class="font-medium text-gray-800">$${parseFloat(ext.final_price).toFixed(2)}/year</span>
                                    ${ext.is_premium ? '<span class="text-xs bg-yellow-100 text-yellow-800 px-1 py-0.5 rounded ml-2">Premium</span>' : ''}
                                    <button class="ml-2 bg-primary text-white px-3 py-1 rounded text-sm hover:bg-primary/90 transition-colors">
                                        Add
                                    </button>
                                </div>
                            ` : '<span class="text-gray-400 text-sm">Taken</span>'}
                        </div>
                    `;
                }
            });
            
            resultsHTML += '</div>';
        }
        
        // Suggestions if domain is taken
        if (suggestions.length > 0) {
            resultsHTML += `
                <h5 class="font-medium text-gray-700 mb-3">Suggested Alternatives:</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            `;
            
            suggestions.forEach(suggestion => {
                resultsHTML += `
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded">
                        <div class="flex items-center">
                            <i class="ri-check-circle-line text-green-600 mr-2"></i>
                            <span class="text-gray-800">${suggestion.name}${suggestion.extension}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-medium text-gray-800">$${parseFloat(suggestion.price).toFixed(2)}/year</span>
                            <button class="ml-2 bg-primary text-white px-3 py-1 rounded text-sm hover:bg-primary/90 transition-colors">
                                Add
                            </button>
                        </div>
                    </div>
                `;
            });
            
            resultsHTML += '</div>';
        }
        
        resultsHTML += '</div>';
        
        domainResults.innerHTML = resultsHTML;
        domainResults.classList.remove('hidden');
    }
    
    function setLoading(loading) {
        const btnText = checkDomainBtn.querySelector('.btn-text');
        const btnLoading = checkDomainBtn.querySelector('.btn-loading');
        
        if (loading) {
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            checkDomainBtn.disabled = true;
        } else {
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
            checkDomainBtn.disabled = false;
        }
    }
});

// Handle Order Service function
function handleOrderService(serviceId, title, price, image, seller) {
    // Check if user is logged in
    if (typeof cart === 'undefined') {
        window.location.href = 'public/auth.php?redirect=' + encodeURIComponent(window.location.href);
        return;
    }
    
    // Create service data object
    const serviceData = {
        id: serviceId,
        title: title,
        price: price,
        image: image,
        seller: seller,
        type: 'service'
    };
    
    // Add to cart using the global cart system
    cart.addItem(serviceData);
}
</script>