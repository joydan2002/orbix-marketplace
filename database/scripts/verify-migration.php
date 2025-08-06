<?php
/**
 * Post-Migration Verification Script
 * Check if Cloudinary migration was successful
 */

require_once '../../config/database.php';
require_once '../../config/cloudinary-config.php';

// Fix HTTP_HOST issue for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "🔍 Verifying Cloudinary migration results...\n\n";
    
    // Check templates
    echo "📋 TEMPLATES VERIFICATION:\n";
    echo "========================\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM templates");
    $totalTemplates = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as cloudinary_count FROM templates WHERE preview_image LIKE 'orbix_%'");
    $cloudinaryTemplates = $stmt->fetch()['cloudinary_count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as old_count FROM templates WHERE preview_image LIKE 'https://readdy.ai%' OR preview_image LIKE 'https://images.unsplash%'");
    $oldTemplates = $stmt->fetch()['old_count'];
    
    echo "📊 Total templates: {$totalTemplates}\n";
    echo "✅ Using Cloudinary: {$cloudinaryTemplates}\n";
    echo "❌ Using old URLs: {$oldTemplates}\n";
    echo "📈 Migration rate: " . ($totalTemplates > 0 ? round(($cloudinaryTemplates / $totalTemplates) * 100, 1) : 0) . "%\n\n";
    
    // Check services
    echo "🛠️  SERVICES VERIFICATION:\n";
    echo "======================\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM services");
    $totalServices = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as cloudinary_count FROM services WHERE preview_image LIKE 'orbix_%'");
    $cloudinaryServices = $stmt->fetch()['cloudinary_count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as old_count FROM services WHERE preview_image LIKE 'https://readdy.ai%' OR preview_image LIKE 'https://images.unsplash%'");
    $oldServices = $stmt->fetch()['old_count'];
    
    echo "📊 Total services: {$totalServices}\n";
    echo "✅ Using Cloudinary: {$cloudinaryServices}\n";
    echo "❌ Using old URLs: {$oldServices}\n";
    echo "📈 Migration rate: " . ($totalServices > 0 ? round(($cloudinaryServices / $totalServices) * 100, 1) : 0) . "%\n\n";
    
    // Test Cloudinary URLs
    echo "🌐 CLOUDINARY URL TESTING:\n";
    echo "=========================\n";
    
    // Get sample Cloudinary images
    $stmt = $pdo->query("SELECT preview_image FROM templates WHERE preview_image LIKE 'orbix_%' LIMIT 3");
    $sampleImages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($sampleImages as $public_id) {
        $optimizedUrl = getOptimizedImageUrl($public_id, 'thumb');
        echo "🔗 Sample URL: {$optimizedUrl}\n";
        
        // Test if URL is accessible
        $headers = @get_headers($optimizedUrl);
        if ($headers && count($headers) > 0) {
            $httpCode = substr($headers[0], 9, 3);
            
            if ($httpCode == '200') {
                echo "   ✅ Status: Active\n";
            } else {
                echo "   ❌ Status: Failed ({$httpCode})\n";
            }
        } else {
            echo "   ❌ Status: Connection failed\n";
        }
    }
    
    echo "\n📈 OVERALL SUMMARY:\n";
    echo "==================\n";
    $totalItems = $totalTemplates + $totalServices;
    $totalCloudinary = $cloudinaryTemplates + $cloudinaryServices;
    $overallRate = $totalItems > 0 ? round(($totalCloudinary / $totalItems) * 100, 1) : 0;
    
    echo "📊 Total items: {$totalItems}\n";
    echo "✅ Migrated to Cloudinary: {$totalCloudinary}\n";
    echo "📈 Overall migration rate: {$overallRate}%\n\n";
    
    if ($overallRate >= 95) {
        echo "🎉 MIGRATION SUCCESSFUL!\n";
        echo "Your images are now served via Cloudinary CDN with automatic optimization.\n\n";
        
        echo "🚀 PERFORMANCE BENEFITS:\n";
        echo "   - 3-5x faster image loading\n";
        echo "   - Automatic WebP conversion\n";
        echo "   - Global CDN delivery\n";
        echo "   - Responsive image sizing\n";
        echo "   - Reduced server bandwidth\n\n";
        
        echo "🔗 View your products at:\n";
        echo "   - Templates: http://localhost/orbix/public/templates.php\n";
        echo "   - Services: http://localhost/orbix/public/services.php\n";
        echo "   - Seller Dashboard: http://localhost/orbix/public/seller-channel.php\n";
        
    } else if ($overallRate >= 50) {
        echo "⚠️  PARTIAL MIGRATION\n";
        echo "Some items were migrated successfully, but others may need attention.\n";
        echo "Consider running the migration script again.\n";
        
    } else {
        echo "❌ MIGRATION INCOMPLETE\n";
        echo "Most items were not migrated. Please check:\n";
        echo "   - Cloudinary API credentials\n";
        echo "   - Internet connection\n";
        echo "   - Upload preset configurations\n";
        echo "   - Run migration script again\n";
    }
    
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
}
?>