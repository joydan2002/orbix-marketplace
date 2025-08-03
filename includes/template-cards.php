<?php
/**
 * Template Cards Display
 * Loads template data from database and displays in cards format
 * Now with caching to prevent continuous loading
 */

require_once '../config/database.php';

// Cache configuration
$cache_file = '../cache/template-cards.json';
$cache_duration = 300; // 5 minutes

// Function to get cached data or fetch from database
function getTemplatesData() {
    global $cache_file, $cache_duration;
    
    // Check if cache exists and is still valid
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_duration) {
        $cached_data = file_get_contents($cache_file);
        return json_decode($cached_data, true);
    }
    
    try {
        // Get database connection
        $pdo = DatabaseConfig::getConnection();
        
        // Fetch templates from database
        $stmt = $pdo->prepare("
            SELECT t.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM templates t 
            LEFT JOIN categories c ON t.category_id = c.id 
            WHERE t.status = 'approved' 
            ORDER BY t.is_featured DESC, t.rating DESC, t.created_at DESC
            LIMIT 12
        ");
        $stmt->execute();
        $templates = $stmt->fetchAll();
        
        // Cache the results
        if (!file_exists(dirname($cache_file))) {
            mkdir(dirname($cache_file), 0755, true);
        }
        file_put_contents($cache_file, json_encode($templates));
        
        return $templates;
        
    } catch (Exception $e) {
        error_log("Database error in template-cards.php: " . $e->getMessage());
        return [];
    }
}

// Get templates data (from cache or database)
$templates = getTemplatesData();

if ($templates):
    foreach ($templates as $template):
?>
        <div class="template-card group cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-2xl bg-white/90 backdrop-blur-sm rounded-2xl overflow-hidden border border-white/20" 
             data-category="<?php echo htmlspecialchars($template['category_slug'] ?? 'all'); ?>"
             data-template-id="<?php echo $template['id']; ?>">
            
            <!-- Template Image -->
            <div class="relative overflow-hidden h-48 bg-gradient-to-br from-gray-100 to-gray-200">
                <?php if ($template['preview_image']): ?>
                    <img src="../assets/images/templates/<?php echo htmlspecialchars($template['preview_image']); ?>" 
                         alt="<?php echo htmlspecialchars($template['title']); ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                         loading="lazy"
                         onerror="this.src='../assets/images/fallbacks/<?php echo $template['category_slug'] ?? 'business'; ?>-fallback.svg'">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-100 to-orange-200">
                        <i class="<?php echo htmlspecialchars($template['category_icon'] ?? 'ri-image-line'); ?> text-4xl text-orange-500"></i>
                    </div>
                <?php endif; ?>
                
                <!-- Featured Badge -->
                <?php if ($template['is_featured']): ?>
                    <div class="absolute top-3 left-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                        <i class="ri-star-fill mr-1"></i>FEATURED
                    </div>
                <?php endif; ?>
                
                <!-- Quick Actions -->
                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="flex gap-2">
                        <button class="favorite-btn bg-white/90 backdrop-blur-sm p-2 rounded-full hover:bg-red-500 hover:text-white transition-all duration-300" 
                                data-template-id="<?php echo $template['id']; ?>">
                            <i class="ri-heart-line text-lg"></i>
                        </button>
                        <?php if ($template['demo_url']): ?>
                            <a href="<?php echo htmlspecialchars($template['demo_url']); ?>" 
                               target="_blank"
                               class="bg-white/90 backdrop-blur-sm p-2 rounded-full hover:bg-blue-500 hover:text-white transition-all duration-300">
                                <i class="ri-external-link-line text-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Template Info -->
            <div class="p-6">
                <!-- Header Info -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-lg text-gray-800 group-hover:text-orange-600 transition-colors">
                            <?php echo htmlspecialchars($template['title']); ?>
                        </h3>
                        <span class="text-2xl font-bold text-orange-600">
                            $<?php echo number_format($template['price'], 0); ?>
                        </span>
                    </div>
                    
                    <!-- Category and Technology -->
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">
                            <?php echo htmlspecialchars($template['category_name'] ?? 'General'); ?>
                        </span>
                        <?php if ($template['technology']): ?>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                <?php echo htmlspecialchars($template['technology']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-2">
                        <?php echo htmlspecialchars(substr($template['description'] ?? '', 0, 120)) . (strlen($template['description'] ?? '') > 120 ? '...' : ''); ?>
                    </p>
                </div>
                
                <!-- Stats -->
                <div class="flex items-center justify-between text-sm mb-4">
                    <div class="flex items-center">
                        <?php 
                        $rating = floatval($template['rating'] ?? 0);
                        for ($i = 1; $i <= 5; $i++): 
                        ?>
                            <i class="ri-star-<?php echo $i <= $rating ? 'fill' : 'line'; ?> text-yellow-400"></i>
                        <?php endfor; ?>
                        <span class="ml-2 font-medium"><?php echo number_format($rating, 1); ?></span>
                        <span class="ml-1">(<?php echo number_format($template['reviews_count']); ?>)</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <i class="ri-download-line mr-1"></i>
                        <span><?php echo number_format($template['downloads_count']); ?></span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <div class="flex space-x-2">
                        <button onclick="window.location.href='template-detail.php?id=<?= $template['id'] ?>'" class="w-10 h-10 flex items-center justify-center border-2 border-gray-200 rounded-lg hover:border-primary hover:text-primary transition-colors">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button onclick="handlePurchaseTemplate(<?= $template['id'] ?>, '<?= addslashes($template['title']) ?>', <?= $template['price'] ?>, '<?= addslashes($template['preview_image']) ?>', '<?= addslashes($template['seller_name']) ?>')" 
                                class="w-10 h-10 flex items-center justify-center bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                            <i class="ri-shopping-cart-line"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
<?php 
    endforeach;
else:
    // Show error message if no templates found
    echo '<div class="col-span-full text-center py-12">';
    echo '<p class="text-gray-500 text-lg">No templates available at the moment.</p>';
    echo '<p class="text-gray-400 text-sm mt-2">Please check back later or contact support if this persists.</p>';
    echo '</div>';
endif;
?>