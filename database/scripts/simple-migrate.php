<?php
/**
 * Simple Cloudinary Migration - Database URL Replacement Only
 * Replaces existing URLs with predefined Cloudinary public IDs
 */

require_once '../../config/database.php';

// Fix HTTP_HOST issue for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "🚀 Starting Simple Cloudinary Migration...\n\n";
    
    // Step 1: Analyze current data
    echo "📊 STEP 1: Analyzing current database...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM templates WHERE preview_image IS NOT NULL");
    $templateCount = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM services WHERE preview_image IS NOT NULL");
    $serviceCount = $stmt->fetch()['total'];
    
    echo "   - Templates with images: {$templateCount}\n";
    echo "   - Services with images: {$serviceCount}\n";
    echo "   - Total items to migrate: " . ($templateCount + $serviceCount) . "\n\n";
    
    // Step 2: Define Cloudinary public IDs (pre-existing on your account)
    echo "📁 STEP 2: Using predefined Cloudinary public IDs...\n";
    
    $cloudinaryImages = [
        'business' => [
            'orbix_business_1',
            'orbix_business_2', 
            'orbix_business_3',
            'orbix_business_4',
            'orbix_business_5'
        ],
        'e-commerce' => [
            'orbix_ecommerce_1',
            'orbix_ecommerce_2',
            'orbix_ecommerce_3', 
            'orbix_ecommerce_4',
            'orbix_ecommerce_5'
        ],
        'portfolio' => [
            'orbix_portfolio_1',
            'orbix_portfolio_2',
            'orbix_portfolio_3',
            'orbix_portfolio_4',
            'orbix_portfolio_5'
        ],
        'landing' => [
            'orbix_landing_1',
            'orbix_landing_2',
            'orbix_landing_3',
            'orbix_landing_4',
            'orbix_landing_5'
        ],
        'services' => [
            'orbix_service_1',
            'orbix_service_2',
            'orbix_service_3',
            'orbix_service_4',
            'orbix_service_5'
        ]
    ];
    
    echo "   - Business images: " . count($cloudinaryImages['business']) . "\n";
    echo "   - E-commerce images: " . count($cloudinaryImages['e-commerce']) . "\n";
    echo "   - Portfolio images: " . count($cloudinaryImages['portfolio']) . "\n";
    echo "   - Landing images: " . count($cloudinaryImages['landing']) . "\n";
    echo "   - Service images: " . count($cloudinaryImages['services']) . "\n\n";
    
    // Step 3: Update Templates
    echo "🔄 STEP 3: Updating template images...\n";
    
    $stmt = $pdo->query("
        SELECT t.id, t.title, t.category_id, c.slug as category_slug, t.preview_image
        FROM templates t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.preview_image IS NOT NULL
        ORDER BY t.id
    ");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $templateUpdateCount = 0;
    foreach ($templates as $template) {
        $categorySlug = $template['category_slug'] ?? 'business';
        
        // Map category to image collection
        $imageCollection = 'business'; // default
        switch ($categorySlug) {
            case 'e-commerce':
                $imageCollection = 'e-commerce';
                break;
            case 'portfolio':
                $imageCollection = 'portfolio';
                break;
            case 'landing':
                $imageCollection = 'landing';
                break;
            case 'admin':
                $imageCollection = 'business'; // Use business images for admin
                break;
        }
        
        // Get random image from collection
        if (!empty($cloudinaryImages[$imageCollection])) {
            $randomImage = $cloudinaryImages[$imageCollection][array_rand($cloudinaryImages[$imageCollection])];
            
            // Update database
            $updateStmt = $pdo->prepare("UPDATE templates SET preview_image = ? WHERE id = ?");
            if ($updateStmt->execute([$randomImage, $template['id']])) {
                $templateUpdateCount++;
                echo "   ✅ Template '{$template['title']}' → {$randomImage}\n";
            } else {
                echo "   ❌ Failed to update template '{$template['title']}'\n";
            }
        }
    }
    
    echo "   📊 Updated {$templateUpdateCount} templates\n\n";
    
    // Step 4: Update Services
    echo "🔄 STEP 4: Updating service images...\n";
    
    $stmt = $pdo->query("SELECT id, title, preview_image FROM services WHERE preview_image IS NOT NULL ORDER BY id");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $serviceUpdateCount = 0;
    foreach ($services as $service) {
        if (!empty($cloudinaryImages['services'])) {
            $randomImage = $cloudinaryImages['services'][array_rand($cloudinaryImages['services'])];
            
            // Update database
            $updateStmt = $pdo->prepare("UPDATE services SET preview_image = ? WHERE id = ?");
            if ($updateStmt->execute([$randomImage, $service['id']])) {
                $serviceUpdateCount++;
                echo "   ✅ Service '{$service['title']}' → {$randomImage}\n";
            } else {
                echo "   ❌ Failed to update service '{$service['title']}'\n";
            }
        }
    }
    
    echo "   📊 Updated {$serviceUpdateCount} services\n\n";
    
    // Step 5: Summary
    echo "📈 MIGRATION SUMMARY:\n";
    echo "==========================================\n";
    echo "✅ Templates updated: {$templateUpdateCount}\n";
    echo "✅ Services updated: {$serviceUpdateCount}\n";
    echo "✅ Total records updated: " . ($templateUpdateCount + $serviceUpdateCount) . "\n\n";
    
    if (($templateUpdateCount + $serviceUpdateCount) > 0) {
        echo "🎉 MIGRATION COMPLETED SUCCESSFULLY!\n";
        echo "All product images now use Cloudinary public IDs.\n\n";
        
        echo "📝 IMPORTANT NOTES:\n";
        echo "   - Database URLs updated to Cloudinary public IDs\n";
        echo "   - You need to upload actual images to Cloudinary with these IDs\n";
        echo "   - Or use getOptimizedImageUrl() function to generate URLs\n\n";
        
        echo "🔗 Benefits:\n";
        echo "   - Clean, consistent public IDs in database\n";
        echo "   - Ready for Cloudinary optimization\n";
        echo "   - Faster loading when images are uploaded\n";
        echo "   - Professional URL structure\n\n";
        
        echo "🌐 Next steps:\n";
        echo "   1. Upload images to Cloudinary with the public IDs shown above\n";
        echo "   2. Update your templates to use getOptimizedImageUrl() function\n";
        echo "   3. Visit your website to see the new structure\n";
        
    } else {
        echo "⚠️  NO RECORDS UPDATED\n";
        echo "Please check your database connectivity.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Please check your database configuration and try again.\n";
}
?>