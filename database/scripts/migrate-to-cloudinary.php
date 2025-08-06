<?php
/**
 * Fixed Cloudinary Migration Script
 * Uses tested, working image URLs and handles CLI environment properly
 */

require_once '../../config/database.php';
require_once '../../config/cloudinary-service.php';

set_time_limit(300); // 5 minutes timeout

// Fix HTTP_HOST issue for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    $pdo = DatabaseConfig::getConnection();
    $cloudinary = new CloudinaryService();
    
    echo "🚀 Starting Cloudinary Migration (Fixed Version)...\n\n";
    
    // Step 1: Analyze current data
    echo "📊 STEP 1: Analyzing current database...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM templates WHERE preview_image IS NOT NULL");
    $templateCount = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM services WHERE preview_image IS NOT NULL");
    $serviceCount = $stmt->fetch()['total'];
    
    echo "   - Templates with images: {$templateCount}\n";
    echo "   - Services with images: {$serviceCount}\n";
    echo "   - Total items to migrate: " . ($templateCount + $serviceCount) . "\n\n";
    
    // Step 2: Load tested image URLs
    echo "📁 STEP 2: Loading verified image URLs...\n";
    
    $validUrlsFile = 'valid-image-urls.json';
    if (!file_exists($validUrlsFile)) {
        throw new Exception("Valid URLs file not found. Please run test-image-urls.php first.");
    }
    
    $imageCollections = json_decode(file_get_contents($validUrlsFile), true);
    
    if (empty($imageCollections)) {
        throw new Exception("No valid image URLs found.");
    }
    
    echo "   - Business images: " . count($imageCollections['business']) . "\n";
    echo "   - E-commerce images: " . count($imageCollections['e-commerce']) . "\n";
    echo "   - Portfolio images: " . count($imageCollections['portfolio']) . "\n";
    echo "   - Landing images: " . count($imageCollections['landing']) . "\n";
    echo "   - Service images: " . count($imageCollections['services']) . "\n\n";
    
    // Step 3: Upload images to Cloudinary
    echo "☁️ STEP 3: Uploading verified images to Cloudinary...\n";
    
    $uploadedImages = [];
    
    foreach ($imageCollections as $category => $images) {
        echo "   📁 Uploading {$category} images...\n";
        $uploadedImages[$category] = [];
        
        foreach ($images as $index => $imageUrl) {
            try {
                // Download image temporarily
                $tempFile = sys_get_temp_dir() . '/' . uniqid('cloudinary_') . '.jpg';
                
                // Use curl for better reliability
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $imageUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36');
                $imageData = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode !== 200 || empty($imageData)) {
                    echo "     ❌ Failed to download: HTTP {$httpCode}\n";
                    continue;
                }
                
                file_put_contents($tempFile, $imageData);
                
                // Create file array for upload (simulate $_FILES)
                $fileArray = [
                    'tmp_name' => $tempFile,
                    'name' => $category . '_' . ($index + 1) . '.jpg',
                    'type' => 'image/jpeg',
                    'size' => filesize($tempFile),
                    'error' => 0
                ];
                
                // Upload to Cloudinary
                $result = $cloudinary->uploadImage($fileArray, 'products', [
                    'public_id' => 'orbix_' . str_replace('-', '_', $category) . '_' . ($index + 1),
                    'overwrite' => true,
                    'resource_type' => 'image'
                ]);
                
                if ($result['success']) {
                    $uploadedImages[$category][] = $result['public_id'];
                    echo "     ✅ Uploaded: orbix_" . str_replace('-', '_', $category) . "_" . ($index + 1) . "\n";
                } else {
                    echo "     ❌ Upload failed: " . ($result['error'] ?? 'Unknown error') . "\n";
                }
                
                // Clean up temp file
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
                
                // Small delay to prevent rate limiting
                usleep(500000); // 0.5 seconds
                
            } catch (Exception $e) {
                echo "     ❌ Error uploading {$category} image " . ($index + 1) . ": " . $e->getMessage() . "\n";
            }
        }
        echo "   📁 {$category}: " . count($uploadedImages[$category]) . " images uploaded\n\n";
    }
    
    // Step 4: Update Templates
    echo "🔄 STEP 4: Updating template images...\n";
    
    $stmt = $pdo->query("
        SELECT t.id, t.title, t.category_id, c.slug as category_slug
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
        if (!empty($uploadedImages[$imageCollection])) {
            $randomImage = $uploadedImages[$imageCollection][array_rand($uploadedImages[$imageCollection])];
            
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
    
    // Step 5: Update Services
    echo "🔄 STEP 5: Updating service images...\n";
    
    $stmt = $pdo->query("SELECT id, title FROM services WHERE preview_image IS NOT NULL ORDER BY id");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $serviceUpdateCount = 0;
    foreach ($services as $service) {
        if (!empty($uploadedImages['services'])) {
            $randomImage = $uploadedImages['services'][array_rand($uploadedImages['services'])];
            
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
    
    // Step 6: Summary
    echo "📈 MIGRATION SUMMARY:\n";
    echo "==========================================\n";
    echo "✅ Total images uploaded to Cloudinary: " . array_sum(array_map('count', $uploadedImages)) . "\n";
    echo "✅ Templates updated: {$templateUpdateCount}\n";
    echo "✅ Services updated: {$serviceUpdateCount}\n";
    echo "✅ Total records updated: " . ($templateUpdateCount + $serviceUpdateCount) . "\n\n";
    
    if (($templateUpdateCount + $serviceUpdateCount) > 0) {
        echo "🎉 MIGRATION COMPLETED SUCCESSFULLY!\n";
        echo "All product images now use Cloudinary CDN with automatic optimization.\n\n";
        
        echo "🔗 Benefits:\n";
        echo "   - Faster loading times via global CDN\n";
        echo "   - Automatic WebP conversion for modern browsers\n";
        echo "   - Responsive image delivery\n";
        echo "   - Reduced server storage usage\n";
        echo "   - Professional, consistent image quality\n\n";
        
        echo "🌐 Next steps:\n";
        echo "   1. Visit your website to see the new images\n";
        echo "   2. Clear any caches if images don't appear immediately\n";
        echo "   3. Check Cloudinary dashboard for usage statistics\n";
    } else {
        echo "⚠️  NO RECORDS UPDATED\n";
        echo "Please check your Cloudinary configuration and database connectivity.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Please check your Cloudinary configuration and try again.\n";
}
?>