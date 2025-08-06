<?php
/**
 * Simplified Cloudinary Upload - No signature required
 * Use unsigned upload preset instead
 */

require_once '../../config/database.php';

// Fix HTTP_HOST issue for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    echo "☁️ CLOUDINARY UNSIGNED UPLOAD TEST\n";
    echo "===================================\n\n";
    
    // Load verified URLs
    $validUrlsFile = 'valid-image-urls.json';
    if (!file_exists($validUrlsFile)) {
        throw new Exception("Please run test-image-urls.php first to generate valid URLs.");
    }
    
    $imageCollections = json_decode(file_get_contents($validUrlsFile), true);
    
    // Cloudinary configuration
    $cloudName = 'dpmwj7f9j';
    $uploadPreset = 'orbix_products'; // Use the preset you created
    
    echo "🔑 Using Cloudinary Account: {$cloudName}\n";
    echo "📋 Upload Preset: {$uploadPreset} (unsigned)\n\n";
    
    // Test upload just one image first
    $testImageUrl = $imageCollections['business'][0];
    $publicId = 'orbix_test_' . time();
    
    echo "🧪 TESTING WITH ONE IMAGE:\n";
    echo "   URL: {$testImageUrl}\n";
    echo "   Public ID: {$publicId}\n\n";
    
    // Download image
    echo "   📥 Downloading image...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testImageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || empty($imageData)) {
        throw new Exception("Failed to download test image (HTTP {$httpCode})");
    }
    
    echo "   ✅ Downloaded: " . number_format(strlen($imageData) / 1024, 1) . " KB\n\n";
    
    // Save to temp file
    $tempFile = sys_get_temp_dir() . '/' . $publicId . '.jpg';
    file_put_contents($tempFile, $imageData);
    
    // Upload to Cloudinary using unsigned preset
    echo "   ☁️ Uploading to Cloudinary...\n";
    $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
    
    $postData = [
        'file' => new CURLFile($tempFile, 'image/jpeg', $publicId . '.jpg'),
        'upload_preset' => $uploadPreset,
        'public_id' => $publicId,
        'folder' => 'orbix/products'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    // Clean up temp file
    unlink($tempFile);
    
    echo "   📡 Response Code: {$httpCode}\n";
    
    if ($curlError) {
        echo "   ❌ cURL Error: {$curlError}\n";
    } else if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        if ($responseData && isset($responseData['secure_url'])) {
            echo "   ✅ SUCCESS!\n";
            echo "   🔗 URL: {$responseData['secure_url']}\n";
            echo "   📏 Size: {$responseData['width']}x{$responseData['height']}\n";
            echo "   📁 Public ID: {$responseData['public_id']}\n";
            echo "   💾 File size: " . number_format($responseData['bytes'] / 1024, 1) . " KB\n\n";
            
            echo "🎯 TEST SUCCESSFUL! You can now:\n";
            echo "1. Copy this URL to browser: {$responseData['secure_url']}\n";
            echo "2. Use this public ID in your database: {$responseData['public_id']}\n";
            echo "3. Create optimized URLs: https://res.cloudinary.com/{$cloudName}/image/upload/w_400,h_300,c_fill/{$responseData['public_id']}\n\n";
            
            echo "✅ Ready to upload all images with this method!\n";
        } else {
            echo "   ❌ Invalid response format\n";
            echo "   Response: " . substr($response, 0, 500) . "\n";
        }
    } else {
        echo "   ❌ Upload failed\n";
        echo "   Response: " . substr($response, 0, 500) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}
?>