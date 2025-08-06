<?php
/**
 * Cloudinary Image Upload Script - With Real Credentials
 * Upload verified images to Cloudinary with correct public IDs
 */

require_once '../../config/database.php';

// Fix HTTP_HOST issue for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    echo "☁️ CLOUDINARY IMAGE UPLOAD SCRIPT (Real Account)\n";
    echo "================================================\n\n";
    
    // Load verified URLs
    $validUrlsFile = 'valid-image-urls.json';
    if (!file_exists($validUrlsFile)) {
        throw new Exception("Please run test-image-urls.php first to generate valid URLs.");
    }
    
    $imageCollections = json_decode(file_get_contents($validUrlsFile), true);
    
    // Cloudinary configuration - REAL CREDENTIALS
    $cloudName = 'dpmwj7f9j';
    $apiKey = '413853847468875';
    $apiSecret = 'BvDJ1bFElcEFLGxZHdbau9BVFJQ';
    
    echo "🔑 Using Cloudinary Account: {$cloudName}\n";
    echo "📋 Image Collections Available:\n";
    foreach ($imageCollections as $category => $urls) {
        echo "   - {$category}: " . count($urls) . " images\n";
    }
    echo "\n";
    
    $uploadResults = [];
    $totalUploaded = 0;
    
    // Upload each category
    foreach ($imageCollections as $category => $urls) {
        echo "📁 UPLOADING {$category} IMAGES:\n";
        echo str_repeat("=", 30 + strlen($category)) . "\n";
        
        $uploadResults[$category] = [];
        
        foreach ($urls as $index => $imageUrl) {
            $imageNumber = $index + 1;
            $publicId = 'orbix_' . str_replace('-', '_', $category) . '_' . $imageNumber;
            
            try {
                echo "   🔄 Uploading: {$publicId}...\n";
                
                // Download image
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $imageUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)');
                $imageData = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode !== 200 || empty($imageData)) {
                    echo "      ❌ Failed to download (HTTP {$httpCode})\n\n";
                    continue;
                }
                
                // Save to temp file
                $tempFile = sys_get_temp_dir() . '/' . $publicId . '.jpg';
                file_put_contents($tempFile, $imageData);
                
                // Prepare Cloudinary upload using UNSIGNED preset
                $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
                
                // Use unsigned upload - no signature needed!
                $postData = [
                    'file' => new CURLFile($tempFile, 'image/jpeg', $publicId . '.jpg'),
                    'upload_preset' => 'orbix_products', // Use the preset you created
                    'public_id' => $publicId,
                    'folder' => 'orbix/products'
                ];
                
                // Upload to Cloudinary
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uploadUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                // Clean up temp file
                unlink($tempFile);
                
                if ($httpCode === 200) {
                    $responseData = json_decode($response, true);
                    if ($responseData && isset($responseData['secure_url'])) {
                        $uploadResults[$category][] = [
                            'public_id' => $publicId,
                            'url' => $responseData['secure_url'],
                            'width' => $responseData['width'],
                            'height' => $responseData['height'],
                            'format' => $responseData['format'],
                            'bytes' => $responseData['bytes']
                        ];
                        
                        $totalUploaded++;
                        echo "      ✅ Success! URL: {$responseData['secure_url']}\n";
                        echo "      📏 Size: {$responseData['width']}x{$responseData['height']} ({$responseData['format']})\n";
                        echo "      💾 File size: " . number_format($responseData['bytes'] / 1024, 1) . " KB\n\n";
                    } else {
                        echo "      ❌ Upload failed: Invalid response\n";
                        $responsePreview = substr($response, 0, 200);
                        echo "      Response: {$responsePreview}...\n\n";
                    }
                } else {
                    echo "      ❌ Upload failed (HTTP {$httpCode})\n";
                    $responsePreview = substr($response, 0, 200);
                    echo "      Response: {$responsePreview}...\n\n";
                }
                
                // Small delay to prevent rate limiting
                usleep(500000); // 0.5 seconds
                
            } catch (Exception $e) {
                echo "      ❌ Error: " . $e->getMessage() . "\n\n";
            }
        }
        
        echo "   📊 {$category}: " . count($uploadResults[$category]) . " images uploaded\n\n";
    }
    
    // Summary
    echo "📈 UPLOAD SUMMARY:\n";
    echo "==================\n";
    echo "✅ Total images uploaded: {$totalUploaded}\n";
    foreach ($uploadResults as $category => $results) {
        echo "   - {$category}: " . count($results) . " images\n";
    }
    echo "\n";
    
    if ($totalUploaded > 0) {
        echo "🎉 UPLOAD COMPLETED SUCCESSFULLY!\n";
        echo "Your images are now available on Cloudinary with optimized delivery.\n\n";
        
        echo "🔗 TEST URLS (Copy vào browser để xem):\n";
        foreach ($uploadResults as $category => $results) {
            if (!empty($results)) {
                $sample = $results[0];
                echo "   {$category}: https://res.cloudinary.com/{$cloudName}/image/upload/w_400,h_300,c_fill,q_auto,f_auto/{$sample['public_id']}\n";
            }
        }
        echo "\n";
        
        echo "🌐 NEXT STEPS:\n";
        echo "1. Chạy verification: php verify-migration.php\n";
        echo "2. Visit website: http://localhost/orbix/public/templates.php\n";
        echo "3. Images should now load from Cloudinary CDN\n";
        echo "4. Check browser Network tab to see WebP format\n\n";
        
        echo "📊 PERFORMANCE BENEFITS:\n";
        echo "   - ⚡ 3-5x faster loading via global CDN\n";
        echo "   - 🗜️ 30-80% smaller files (WebP + compression)\n";
        echo "   - 📱 Responsive images for all devices\n";
        echo "   - 🌍 Global edge server delivery\n";
        echo "   - 🔄 Automatic browser caching\n\n";
        
    } else {
        echo "⚠️  NO IMAGES UPLOADED\n";
        echo "Check:\n";
        echo "- Internet connection\n";
        echo "- Image URL accessibility\n";
        echo "- Cloudinary account status\n";
    }
    
    // Save results for reference
    file_put_contents('upload-results.json', json_encode($uploadResults, JSON_PRETTY_PRINT));
    echo "💾 Upload results saved to: upload-results.json\n";
    
} catch (Exception $e) {
    echo "❌ Upload failed: " . $e->getMessage() . "\n";
}
?>