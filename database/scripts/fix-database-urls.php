<?php
/**
 * Fix Database URLs to Match Cloudinary Public IDs
 * Updates database to use exact public IDs that were uploaded
 */

require_once '../../config/database.php';

// Fix HTTP_HOST issue for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "🔧 FIXING DATABASE URLs TO MATCH CLOUDINARY...\n\n";
    
    // Load upload results to get actual public IDs
    $uploadResultsFile = 'upload-results.json';
    if (!file_exists($uploadResultsFile)) {
        throw new Exception("Upload results file not found. Please run upload-to-cloudinary.php first.");
    }
    
    $uploadResults = json_decode(file_get_contents($uploadResultsFile), true);
    
    echo "📋 ACTUAL PUBLIC IDs FROM CLOUDINARY:\n";
    foreach ($uploadResults as $category => $images) {
        echo "   {$category}:\n";
        foreach ($images as $image) {
            echo "      - {$image['public_id']}\n";
        }
    }
    echo "\n";
    
    // Update Templates
    echo "🔄 UPDATING TEMPLATE URLs...\n";
    
    $stmt = $pdo->query("
        SELECT t.id, t.title, t.category_id, c.slug as category_slug, t.preview_image
        FROM templates t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.preview_image LIKE 'orbix_%'
        ORDER BY t.id
    ");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $templateUpdateCount = 0;
    foreach ($templates as $template) {
        $categorySlug = $template['category_slug'] ?? 'business';
        
        // Map category to upload results
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
                $imageCollection = 'business';
                break;
        }
        
        // Get actual public ID from upload results
        if (!empty($uploadResults[$imageCollection])) {
            $randomImage = $uploadResults[$imageCollection][array_rand($uploadResults[$imageCollection])];
            $actualPublicId = $randomImage['public_id'];
            
            // Update database
            $updateStmt = $pdo->prepare("UPDATE templates SET preview_image = ? WHERE id = ?");
            if ($updateStmt->execute([$actualPublicId, $template['id']])) {
                $templateUpdateCount++;
                echo "   ✅ Template '{$template['title']}' → {$actualPublicId}\n";
            } else {
                echo "   ❌ Failed to update template '{$template['title']}'\n";
            }
        }
    }
    
    echo "   📊 Updated {$templateUpdateCount} templates\n\n";
    
    // Update Services
    echo "🔄 UPDATING SERVICE URLs...\n";
    
    $stmt = $pdo->query("SELECT id, title, preview_image FROM services WHERE preview_image LIKE 'orbix_%' ORDER BY id");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $serviceUpdateCount = 0;
    foreach ($services as $service) {
        if (!empty($uploadResults['services'])) {
            $randomImage = $uploadResults['services'][array_rand($uploadResults['services'])];
            $actualPublicId = $randomImage['public_id'];
            
            // Update database
            $updateStmt = $pdo->prepare("UPDATE services SET preview_image = ? WHERE id = ?");
            if ($updateStmt->execute([$actualPublicId, $service['id']])) {
                $serviceUpdateCount++;
                echo "   ✅ Service '{$service['title']}' → {$actualPublicId}\n";
            } else {
                echo "   ❌ Failed to update service '{$service['title']}'\n";
            }
        }
    }
    
    echo "   📊 Updated {$serviceUpdateCount} services\n\n";
    
    // Summary
    echo "📈 URL FIX SUMMARY:\n";
    echo "==================\n";
    echo "✅ Templates updated: {$templateUpdateCount}\n";
    echo "✅ Services updated: {$serviceUpdateCount}\n";
    echo "✅ Total records updated: " . ($templateUpdateCount + $serviceUpdateCount) . "\n\n";
    
    if (($templateUpdateCount + $serviceUpdateCount) > 0) {
        echo "🎉 DATABASE URLs FIXED!\n";
        echo "URLs now match actual Cloudinary public IDs.\n\n";
        
        echo "🔄 NEXT STEP: Run verification again:\n";
        echo "   php verify-migration.php\n\n";
        
    } else {
        echo "⚠️  NO RECORDS UPDATED\n";
    }
    
} catch (Exception $e) {
    echo "❌ Fix failed: " . $e->getMessage() . "\n";
}
?>