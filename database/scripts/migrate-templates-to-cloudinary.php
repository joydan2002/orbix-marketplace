<?php
/**
 * Migrate Templates to Cloudinary
 * Update templates table with Cloudinary image references like services
 */

require_once '../../config/database.php';
require_once '../../config/cloudinary-config.php';

// Fix HTTP_HOST for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "🔄 MIGRATING TEMPLATES TO CLOUDINARY...\n";
    echo "=====================================\n\n";
    
    // Get all templates that still have fallback images
    $stmt = $pdo->query("
        SELECT id, title, preview_image, category_id 
        FROM templates 
        WHERE preview_image LIKE '%default-template.jpg%' 
        OR preview_image NOT LIKE 'orbix_%'
        ORDER BY id
    ");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($templates)) {
        echo "✅ No templates need migration - all already have Cloudinary images!\n";
        exit;
    }
    
    echo "📋 Found " . count($templates) . " templates to migrate:\n\n";
    
    // Define available Cloudinary images (reuse from services)
    $cloudinaryImages = [
        'orbix_business_2',
        'orbix_services_5', 
        'orbix_portfolio_3',
        'orbix_ecommerce_1',
        'orbix_landing_4',
        'orbix_admin_6',
        'orbix_blog_7',
        'orbix_education_8'
    ];
    
    // Category mapping for better image selection
    $categoryImageMap = [
        1 => 'orbix_business_2',     // Business
        2 => 'orbix_ecommerce_1',   // E-commerce  
        3 => 'orbix_portfolio_3',   // Portfolio
        4 => 'orbix_landing_4',     // Landing Page
        5 => 'orbix_admin_6',       // Admin
        6 => 'orbix_blog_7',        // Blog
        7 => 'orbix_education_8',   // Education
    ];
    
    $updateStmt = $pdo->prepare("UPDATE templates SET preview_image = ? WHERE id = ?");
    $updated = 0;
    
    foreach ($templates as $template) {
        // Select appropriate image based on category
        $selectedImage = $categoryImageMap[$template['category_id']] ?? $cloudinaryImages[array_rand($cloudinaryImages)];
        
        // Update database
        $updateStmt->execute([$selectedImage, $template['id']]);
        
        echo "✅ Template #{$template['id']}: {$template['title']}\n";
        echo "   📸 Updated to: {$selectedImage}\n";
        echo "   🔗 URL: " . getOptimizedImageUrl($selectedImage, 'thumb') . "\n\n";
        
        $updated++;
    }
    
    echo "🎉 MIGRATION COMPLETED!\n";
    echo "=====================================\n";
    echo "✅ Updated {$updated} templates with Cloudinary images\n";
    echo "🌟 Templates now use the same image system as services\n\n";
    
    // Verify the migration
    echo "🔍 VERIFICATION:\n";
    $verifyStmt = $pdo->query("
        SELECT 
            COUNT(*) as total_templates,
            SUM(CASE WHEN preview_image LIKE 'orbix_%' THEN 1 ELSE 0 END) as cloudinary_images,
            SUM(CASE WHEN preview_image LIKE '%default-%' THEN 1 ELSE 0 END) as fallback_images
        FROM templates
    ");
    $stats = $verifyStmt->fetch(PDO::FETCH_ASSOC);
    
    echo "📊 Total templates: {$stats['total_templates']}\n";
    echo "🌟 Using Cloudinary: {$stats['cloudinary_images']}\n"; 
    echo "📦 Using fallback: {$stats['fallback_images']}\n";
    
    if ($stats['fallback_images'] == 0) {
        echo "✅ ALL TEMPLATES NOW USE CLOUDINARY! 🎉\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>