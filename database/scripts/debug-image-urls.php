<?php
/**
 * Debug Image URLs - Check what URLs are being generated
 */

require_once '../../config/database.php';
require_once '../../config/cloudinary-config.php';

// Fix HTTP_HOST for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "🔍 DEBUGGING IMAGE URLs...\n";
    echo "===========================\n\n";
    
    // Check few templates
    $stmt = $pdo->query("SELECT id, title, preview_image FROM templates WHERE preview_image LIKE 'orbix_%' LIMIT 5");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 TEMPLATE IMAGE URLs:\n";
    foreach ($templates as $template) {
        $originalImage = $template['preview_image'];
        $optimizedUrl = getOptimizedImageUrl($originalImage, 'thumb');
        
        echo "   Template: {$template['title']}\n";
        echo "   📄 DB Value: {$originalImage}\n";
        echo "   🔗 Generated URL: {$optimizedUrl}\n";
        
        // Test if URL is accessible
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $optimizedUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $status = ($httpCode === 200) ? "✅ Active" : "❌ Error ({$httpCode})";
        echo "   🌐 Status: {$status}\n\n";
    }
    
    // Check few services
    $stmt = $pdo->query("SELECT id, title, preview_image FROM services WHERE preview_image LIKE 'orbix_%' LIMIT 3");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "🛠️  SERVICE IMAGE URLs:\n";
    foreach ($services as $service) {
        $originalImage = $service['preview_image'];
        $optimizedUrl = getOptimizedImageUrl($originalImage, 'thumb');
        
        echo "   Service: {$service['title']}\n";
        echo "   📄 DB Value: {$originalImage}\n";
        echo "   🔗 Generated URL: {$optimizedUrl}\n";
        
        // Test if URL is accessible
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $optimizedUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $status = ($httpCode === 200) ? "✅ Active" : "❌ Error ({$httpCode})";
        echo "   🌐 Status: {$status}\n\n";
    }
    
    // Test Cloudinary function
    echo "🧪 CLOUDINARY FUNCTION TEST:\n";
    echo "   Function exists: " . (function_exists('getOptimizedImageUrl') ? "✅ Yes" : "❌ No") . "\n";
    echo "   Cloud name: " . (defined('CLOUDINARY_CLOUD_NAME') ? CLOUDINARY_CLOUD_NAME : "❌ Not defined") . "\n";
    echo "   Delivery URL: " . (defined('CLOUDINARY_DELIVERY_URL') ? CLOUDINARY_DELIVERY_URL : "❌ Not defined") . "\n\n";
    
    // Test sample URL generation
    $testPublicId = 'orbix_business_1';
    $testUrl = getOptimizedImageUrl($testPublicId, 'thumb');
    echo "🎯 SAMPLE URL TEST:\n";
    echo "   Input: {$testPublicId}\n";
    echo "   Output: {$testUrl}\n";
    echo "   Expected: https://res.cloudinary.com/dpmwj7f9j/image/upload/w_300,h_200,c_fill,q_auto,f_auto/orbix/products/orbix_business_1\n\n";
    
} catch (Exception $e) {
    echo "❌ Debug failed: " . $e->getMessage() . "\n";
}
?>