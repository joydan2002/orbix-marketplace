<?php
/**
 * Test Cloudinary Upload - Debug & Fix HTTP 400 Error
 */

require_once 'config/cloudinary-config.php';
require_once 'config/cloudinary-service.php';

echo "<h1>🔧 Cloudinary Upload Test & Fix</h1>";

// Test 1: Check configuration
echo "<h2>📋 1. Configuration Check</h2>";
echo "Cloud Name: " . CLOUDINARY_CLOUD_NAME . "<br>";
echo "API Key: " . CLOUDINARY_API_KEY . "<br>";
echo "API Secret: " . (strlen(CLOUDINARY_API_SECRET) > 0 ? "✅ Set (" . strlen(CLOUDINARY_API_SECRET) . " chars)" : "❌ Not set") . "<br>";
echo "Upload Preset: " . CLOUDINARY_PRODUCT_PRESET . "<br>";
echo "<hr>";

// Test 2: Check upload preset exists
echo "<h2>🎯 2. Upload Preset Check</h2>";
$presetUrl = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/upload_presets/" . CLOUDINARY_PRODUCT_PRESET;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $presetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERPWD, CLOUDINARY_API_KEY . ":" . CLOUDINARY_API_SECRET);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Upload preset exists<br>";
    $presetData = json_decode($response, true);
    echo "<pre>" . json_encode($presetData, JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "❌ Upload preset not found (HTTP $httpCode)<br>";
    echo "Response: $response<br>";
    echo "<strong>🔧 Need to create upload preset in Cloudinary dashboard</strong><br>";
}
echo "<hr>";

// Test 3: Simple upload without preset (signed)
echo "<h2>🚀 3. Test Signed Upload (No Preset)</h2>";

// Create test image
$testImagePath = tempnam(sys_get_temp_dir(), 'test_img') . '.png';
$image = imagecreate(300, 200);
$background = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 50, 90, 'TEST UPLOAD', $textColor);
imagepng($image, $testImagePath);
imagedestroy($image);

echo "Test image created: " . basename($testImagePath) . " (" . filesize($testImagePath) . " bytes)<br>";

// Prepare upload data
$timestamp = time();
$paramsToSign = [
    'folder' => 'orbix/products',
    'timestamp' => $timestamp
];

// Generate signature
ksort($paramsToSign);
$paramsString = '';
foreach ($paramsToSign as $key => $value) {
    $paramsString .= $key . '=' . $value . '&';
}
$paramsString = rtrim($paramsString, '&') . CLOUDINARY_API_SECRET;
$signature = sha1($paramsString);

echo "Signature params: " . $paramsString . "<br>";
echo "Generated signature: " . $signature . "<br>";

$uploadData = [
    'file' => new CURLFile($testImagePath, 'image/png', 'test-upload.png'),
    'folder' => 'orbix/products',
    'api_key' => CLOUDINARY_API_KEY,
    'timestamp' => $timestamp,
    'signature' => $signature
];

$uploadUrl = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $uploadUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $uploadData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "Upload URL: $uploadUrl<br>";
echo "HTTP Code: $httpCode<br>";
if ($curlError) {
    echo "cURL Error: $curlError<br>";
}

if ($httpCode === 200) {
    echo "✅ Signed upload successful!<br>";
    $responseData = json_decode($response, true);
    echo "Public ID: " . $responseData['public_id'] . "<br>";
    echo "Secure URL: " . $responseData['secure_url'] . "<br>";
    echo "<img src='" . $responseData['secure_url'] . "' style='max-width: 200px; border: 1px solid #ccc;'><br>";
} else {
    echo "❌ Signed upload failed<br>";
    echo "Response: $response<br>";
    
    $errorData = json_decode($response, true);
    if (isset($errorData['error']['message'])) {
        echo "<strong>Error:</strong> " . $errorData['error']['message'] . "<br>";
    }
}

unlink($testImagePath);
echo "<hr>";

// Test 4: Unsigned upload with preset
echo "<h2>🎯 4. Test Unsigned Upload (With Preset)</h2>";

if ($httpCode !== 200) {
    // Create test image again
    $testImagePath2 = tempnam(sys_get_temp_dir(), 'test_img2') . '.jpg';
    $image2 = imagecreate(300, 200);
    $background2 = imagecolorallocate($image2, 100, 150, 200);
    $textColor2 = imagecolorallocate($image2, 255, 255, 255);
    imagestring($image2, 5, 30, 90, 'PRESET TEST', $textColor2);
    imagejpeg($image2, $testImagePath2, 90);
    imagedestroy($image2);
    
    $uploadData2 = [
        'file' => new CURLFile($testImagePath2, 'image/jpeg', 'test-preset.jpg'),
        'upload_preset' => CLOUDINARY_PRODUCT_PRESET,
        'folder' => 'orbix/products'
    ];
    
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $uploadData2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 60);
    
    $response2 = curl_exec($ch2);
    $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    $curlError2 = curl_error($ch2);
    curl_close($ch2);
    
    echo "HTTP Code: $httpCode2<br>";
    if ($curlError2) {
        echo "cURL Error: $curlError2<br>";
    }
    
    if ($httpCode2 === 200) {
        echo "✅ Unsigned upload successful!<br>";
        $responseData2 = json_decode($response2, true);
        echo "Public ID: " . $responseData2['public_id'] . "<br>";
        echo "Secure URL: " . $responseData2['secure_url'] . "<br>";
        echo "<img src='" . $responseData2['secure_url'] . "' style='max-width: 200px; border: 1px solid #ccc;'><br>";
    } else {
        echo "❌ Unsigned upload failed<br>";
        echo "Response: $response2<br>";
    }
    
    unlink($testImagePath2);
} else {
    echo "✅ Skipping preset test - signed upload already worked<br>";
}

echo "<hr>";

// Test 5: Test CloudinaryService class
echo "<h2>🔧 5. Test CloudinaryService Class</h2>";

try {
    $cloudinary = new CloudinaryService();
    
    // Create test image
    $testImagePath3 = tempnam(sys_get_temp_dir(), 'test_service') . '.png';
    $image3 = imagecreate(400, 300);
    $background3 = imagecolorallocate($image3, 50, 50, 50);
    $textColor3 = imagecolorallocate($image3, 255, 255, 0);
    imagestring($image3, 5, 80, 140, 'SERVICE TEST', $textColor3);
    imagepng($image3, $testImagePath3);
    imagedestroy($image3);
    
    $testFile = [
        'tmp_name' => $testImagePath3,
        'name' => 'service-test.png',
        'type' => 'image/png',
        'size' => filesize($testImagePath3),
        'error' => UPLOAD_ERR_OK
    ];
    
    echo "Testing with file: " . $testFile['name'] . " (" . $testFile['size'] . " bytes)<br>";
    
    $result = $cloudinary->uploadImage($testFile, 'templates');
    
    if ($result['success']) {
        echo "✅ CloudinaryService upload successful!<br>";
        echo "Public ID: " . $result['public_id'] . "<br>";
        echo "Secure URL: " . $result['secure_url'] . "<br>";
        echo "<img src='" . $result['secure_url'] . "' style='max-width: 200px; border: 1px solid #ccc;'><br>";
        
        // This is the method that should work!
        echo "<h3>🎉 SUCCESS! CloudinaryService is working</h3>";
    } else {
        echo "❌ CloudinaryService upload failed<br>";
        echo "Error: " . $result['error'] . "<br>";
    }
    
    unlink($testImagePath3);
    
} catch (Exception $e) {
    echo "❌ Exception in CloudinaryService: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

echo "<hr>";

// Test 6: Create a fixed version of CloudinaryService if needed
echo "<h2>🛠️ 6. Fixed CloudinaryService Implementation</h2>";

class FixedCloudinaryService {
    private $cloudName;
    private $apiKey;
    private $apiSecret;
    private $uploadPreset;
    
    public function __construct() {
        $this->cloudName = CLOUDINARY_CLOUD_NAME;
        $this->apiKey = CLOUDINARY_API_KEY;
        $this->apiSecret = CLOUDINARY_API_SECRET;
        $this->uploadPreset = CLOUDINARY_PRODUCT_PRESET;
    }
    
    public function uploadImage($file, $folder = '', $options = []) {
        try {
            // Validate file
            if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
                return ['success' => false, 'error' => 'Invalid file'];
            }
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                return ['success' => false, 'error' => 'Invalid file type: ' . $file['type']];
            }
            
            // Check file size (10MB limit)
            if ($file['size'] > 10 * 1024 * 1024) {
                return ['success' => false, 'error' => 'File too large: ' . $file['size'] . ' bytes'];
            }
            
            $timestamp = time();
            
            // Try signed upload first
            $uploadFolder = !empty($folder) ? "orbix/$folder" : 'orbix/products';
            
            $paramsToSign = [
                'folder' => $uploadFolder,
                'timestamp' => $timestamp
            ];
            
            ksort($paramsToSign);
            $paramsString = '';
            foreach ($paramsToSign as $key => $value) {
                $paramsString .= $key . '=' . $value . '&';
            }
            $paramsString = rtrim($paramsString, '&') . $this->apiSecret;
            $signature = sha1($paramsString);
            
            $uploadData = [
                'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
                'folder' => $uploadFolder,
                'api_key' => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature
            ];
            
            $uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $uploadData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                return ['success' => false, 'error' => 'cURL Error: ' . $curlError];
            }
            
            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                return [
                    'success' => true,
                    'public_id' => $responseData['public_id'],
                    'secure_url' => $responseData['secure_url'],
                    'version' => $responseData['version'] ?? 1
                ];
            } else {
                $errorData = json_decode($response, true);
                $errorMessage = isset($errorData['error']['message']) ? $errorData['error']['message'] : "HTTP Error: $httpCode";
                return ['success' => false, 'error' => $errorMessage];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Upload exception: ' . $e->getMessage()];
        }
    }
}

// Test the fixed service
echo "Testing FixedCloudinaryService...<br>";

try {
    $fixedService = new FixedCloudinaryService();
    
    // Create final test image
    $testImagePath4 = tempnam(sys_get_temp_dir(), 'test_fixed') . '.jpg';
    $image4 = imagecreate(500, 300);
    $background4 = imagecolorallocate($image4, 0, 100, 0);
    $textColor4 = imagecolorallocate($image4, 255, 255, 255);
    imagestring($image4, 5, 150, 140, 'FIXED SERVICE', $textColor4);
    imagejpeg($image4, $testImagePath4, 90);
    imagedestroy($image4);
    
    $testFile4 = [
        'tmp_name' => $testImagePath4,
        'name' => 'fixed-test.jpg',
        'type' => 'image/jpeg',
        'size' => filesize($testImagePath4),
        'error' => UPLOAD_ERR_OK
    ];
    
    $result = $fixedService->uploadImage($testFile4, 'templates');
    
    if ($result['success']) {
        echo "🎉 <strong>FIXED SERVICE WORKS!</strong><br>";
        echo "Public ID: " . $result['public_id'] . "<br>";
        echo "Secure URL: " . $result['secure_url'] . "<br>";
        echo "<img src='" . $result['secure_url'] . "' style='max-width: 300px; border: 2px solid green;'><br>";
        
        echo "<h3>✅ Solution Found! Update CloudinaryService with this implementation</h3>";
    } else {
        echo "❌ Fixed service failed: " . $result['error'] . "<br>";
    }
    
    unlink($testImagePath4);
    
} catch (Exception $e) {
    echo "❌ Fixed service exception: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>📋 Summary & Next Steps</h2>";
echo "<ol>";
echo "<li>If any test above succeeded, use that method</li>";
echo "<li>If signed upload works, update CloudinaryService to use signed upload</li>";
echo "<li>If preset upload works, ensure preset exists in Cloudinary dashboard</li>";
echo "<li>Update the actual CloudinaryService class with working implementation</li>";
echo "<li>Test the modal again</li>";
echo "</ol>";
?>