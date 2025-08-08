<?php
/**
 * Testimonials Section - Database Integration
 * Uses modern design from backup with orange theme and English translation
 */

// Get testimonials from database
try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY is_featured DESC, id ASC LIMIT 6");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback to empty array if database error
    $testimonials = [];
}
?>

<!-- Testimonials Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-secondary">
                What Our Customers Say
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Thousands of customers have trusted and been satisfied with our services.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full overflow-hidden mr-4">
                        <img
                            src="<?php echo htmlspecialchars($testimonial['avatar_url']); ?>"
                            alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div>
                        <h4 class="font-bold text-secondary"><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                        <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($testimonial['position']); ?>, <?php echo htmlspecialchars($testimonial['company']); ?></p>
                    </div>
                </div>
                
                <div class="text-yellow-400 flex mb-4">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if($i <= $testimonial['rating']): ?>
                            <i class="ri-star-fill"></i>
                        <?php else: ?>
                            <i class="ri-star-line"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                
                <p class="text-gray-600 leading-relaxed">
                    "<?php echo htmlspecialchars($testimonial['testimonial']); ?>"
                </p>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Trusted Brands Section -->
        <div class="mt-20">
            <p class="text-center text-gray-600 mb-8">
                Trusted by 1000+ businesses worldwide
            </p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
                <div class="flex items-center text-gray-400 hover:text-primary transition-colors">
                    <i class="ri-microsoft-fill text-3xl"></i>
                    <span class="ml-2 text-lg font-medium">Microsoft</span>
                </div>
                <div class="flex items-center text-gray-400 hover:text-primary transition-colors">
                    <i class="ri-google-fill text-3xl"></i>
                    <span class="ml-2 text-lg font-medium">Google</span>
                </div>
                <div class="flex items-center text-gray-400 hover:text-primary transition-colors">
                    <i class="ri-amazon-fill text-3xl"></i>
                    <span class="ml-2 text-lg font-medium">Amazon</span>
                </div>
                <div class="flex items-center text-gray-400 hover:text-primary transition-colors">
                    <i class="ri-netflix-fill text-3xl"></i>
                    <span class="ml-2 text-lg font-medium">Netflix</span>
                </div>
                <div class="flex items-center text-gray-400 hover:text-primary transition-colors">
                    <i class="ri-spotify-fill text-3xl"></i>
                    <span class="ml-2 text-lg font-medium">Spotify</span>
                </div>
            </div>
        </div>
    </div>
</section>