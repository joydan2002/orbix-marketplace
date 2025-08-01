<?php
/**
 * Service Cards Display - Dynamic Version with Database Integration
 * Now uses real data from database instead of static data
 */

// Get services from database
try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback to empty array if database error
    $services = [];
}
?>

<!-- Services Section -->
<section class="py-16 bg-gradient-to-r from-primary/5 to-orange-100/30">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-secondary mb-4">Additional Services</h2>
            <p class="text-xl text-gray-600">Professional web services to complement your templates</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8" id="services-grid">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                <div class="glass-effect rounded-2xl p-6 text-center hover-scale">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="<?php echo htmlspecialchars($service['icon']); ?> text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-secondary mb-2"><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($service['description']); ?></p>
                    <div class="text-lg font-bold text-primary mb-4">Starting at $<?php echo number_format($service['price'], 0); ?></div>
                    <button class="w-full bg-primary text-white py-2 !rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Get Quote</button>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback services if database is empty -->
                <div class="glass-effect rounded-2xl p-6 text-center hover-scale">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ri-palette-line text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-secondary mb-2">Custom Design</h3>
                    <p class="text-sm text-gray-600 mb-4">Get a unique website designed specifically for your brand and requirements</p>
                    <div class="text-lg font-bold text-primary mb-4">Starting at $299</div>
                    <button class="w-full bg-primary text-white py-2 !rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Get Quote</button>
                </div>
                
                <div class="glass-effect rounded-2xl p-6 text-center hover-scale">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ri-code-line text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-secondary mb-2">HTML Slicing</h3>
                    <p class="text-sm text-gray-600 mb-4">Convert your Figma or PSD designs into pixel-perfect HTML/CSS code</p>
                    <div class="text-lg font-bold text-primary mb-4">Starting at $149</div>
                    <button class="w-full bg-primary text-white py-2 !rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Get Quote</button>
                </div>
                
                <div class="glass-effect rounded-2xl p-6 text-center hover-scale">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ri-tools-line text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-secondary mb-2">Customization</h3>
                    <p class="text-sm text-gray-600 mb-4">Modify existing templates to match your brand colors and content</p>
                    <div class="text-lg font-bold text-primary mb-4">Starting at $99</div>
                    <button class="w-full bg-primary text-white py-2 !rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Get Quote</button>
                </div>
                
                <div class="glass-effect rounded-2xl p-6 text-center hover-scale">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ri-search-line text-primary text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-secondary mb-2">SEO Optimization</h3>
                    <p class="text-sm text-gray-600 mb-4">Optimize your website for search engines and improve performance</p>
                    <div class="text-lg font-bold text-primary mb-4">Starting at $199</div>
                    <button class="w-full bg-primary text-white py-2 !rounded-button text-sm font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Get Quote</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>