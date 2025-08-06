<?php
/**
 * Test Image URLs - Validate URLs before migration
 * This script tests various image URLs to ensure they're accessible
 */

echo "🔍 Testing Image URLs for Migration...\n\n";

// Function to test URL accessibility
function testImageUrl($url, $description = '') {
    echo "Testing: {$description}\n";
    echo "URL: {$url}\n";
    
    // Get headers to check if URL is accessible
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
        ]
    ]);
    
    $headers = @get_headers($url, 1, $context);
    
    if ($headers === false) {
        echo "❌ Failed to connect\n";
        return false;
    }
    
    $httpCode = substr($headers[0], 9, 3);
    
    if ($httpCode == '200') {
        echo "✅ Status: {$httpCode} OK\n";
        
        // Check content type
        $contentType = '';
        if (isset($headers['Content-Type'])) {
            $contentType = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
        }
        
        if (strpos($contentType, 'image/') === 0) {
            echo "✅ Content-Type: {$contentType}\n";
            return true;
        } else {
            echo "⚠️  Content-Type: {$contentType} (not an image)\n";
            return false;
        }
    } else {
        echo "❌ Status: {$httpCode}\n";
        return false;
    }
}

// Test collections of image URLs
echo "📋 BUSINESS/DASHBOARD IMAGES:\n";
echo "============================\n";

$businessImages = [
    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop&crop=center' => 'Analytics Dashboard',
    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop&crop=center' => 'Business Analytics',
    'https://images.unsplash.com/photo-1554774853-719586f82d77?w=800&h=600&fit=crop&crop=center' => 'Office Workspace',
    'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&h=600&fit=crop&crop=center' => 'Modern Office',
    'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&h=600&fit=crop&crop=center' => 'Team Meeting'
];

$validBusinessImages = [];
foreach ($businessImages as $url => $description) {
    if (testImageUrl($url, $description)) {
        $validBusinessImages[] = $url;
    }
    echo "\n";
}

echo "📋 E-COMMERCE IMAGES:\n";
echo "====================\n";

$ecommerceImages = [
    'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop&crop=center' => 'Shopping Experience',
    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&h=600&fit=crop&crop=center' => 'Shopping Cart',
    'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800&h=600&fit=crop&crop=center' => 'Online Shopping',
    'https://images.unsplash.com/photo-1556742111-a301076d9d18?w=800&h=600&fit=crop&crop=center' => 'E-commerce Interface',
    'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=800&h=600&fit=crop&crop=center' => 'Product Showcase'
];

$validEcommerceImages = [];
foreach ($ecommerceImages as $url => $description) {
    if (testImageUrl($url, $description)) {
        $validEcommerceImages[] = $url;
    }
    echo "\n";
}

echo "📋 PORTFOLIO/CREATIVE IMAGES:\n";
echo "============================\n";

$portfolioImages = [
    'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&h=600&fit=crop&crop=center' => 'Creative Workspace',
    'https://images.unsplash.com/photo-1558655146-9f40138edfeb?w=800&h=600&fit=crop&crop=center' => 'Design Tools',
    'https://images.unsplash.com/photo-1609081219090-a6d81d3085bf?w=800&h=600&fit=crop&crop=center' => 'Creative Portfolio',
    'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?w=800&h=600&fit=crop&crop=center' => 'Artist Workspace',
    'https://images.unsplash.com/photo-1542744094-3a31f272c490?w=800&h=600&fit=crop&crop=center' => 'Design Portfolio'
];

$validPortfolioImages = [];
foreach ($portfolioImages as $url => $description) {
    if (testImageUrl($url, $description)) {
        $validPortfolioImages[] = $url;
    }
    echo "\n";
}

echo "📋 LANDING PAGE IMAGES:\n";
echo "======================\n";

$landingImages = [
    'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?w=800&h=600&fit=crop&crop=center' => 'Landing Page Design',
    'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=600&fit=crop&crop=center' => 'Web Design',
    'https://images.unsplash.com/photo-1559028006-448665bd7c7f?w=800&h=600&fit=crop&crop=center' => 'Marketing Landing',
    'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop&crop=center' => 'Digital Marketing',
    'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=800&h=600&fit=crop&crop=center' => 'Conversion Page'
];

$validLandingImages = [];
foreach ($landingImages as $url => $description) {
    if (testImageUrl($url, $description)) {
        $validLandingImages[] = $url;
    }
    echo "\n";
}

echo "📋 SERVICE IMAGES:\n";
echo "=================\n";

$serviceImages = [
    'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&h=600&fit=crop&crop=center' => 'Professional Service',
    'https://images.unsplash.com/photo-1556761175-b413da4baf72?w=800&h=600&fit=crop&crop=center' => 'Web Development',
    'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=800&h=600&fit=crop&crop=center' => 'Design Service',
    'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&h=600&fit=crop&crop=center' => 'Tech Service',
    'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop&crop=center' => 'Business Service'
];

$validServiceImages = [];
foreach ($serviceImages as $url => $description) {
    if (testImageUrl($url, $description)) {
        $validServiceImages[] = $url;
    }
    echo "\n";
}

// Summary
echo "📊 SUMMARY:\n";
echo "==========\n";
echo "✅ Valid Business Images: " . count($validBusinessImages) . "/5\n";
echo "✅ Valid E-commerce Images: " . count($validEcommerceImages) . "/5\n";
echo "✅ Valid Portfolio Images: " . count($validPortfolioImages) . "/5\n";
echo "✅ Valid Landing Images: " . count($validLandingImages) . "/5\n";
echo "✅ Valid Service Images: " . count($validServiceImages) . "/5\n";

$totalValid = count($validBusinessImages) + count($validEcommerceImages) + 
              count($validPortfolioImages) + count($validLandingImages) + 
              count($validServiceImages);

echo "\n🎯 Total Valid Images: {$totalValid}/25\n";

if ($totalValid >= 15) {
    echo "✅ Sufficient images for migration!\n";
} else {
    echo "⚠️  Need more valid images for migration.\n";
}

// Save valid URLs to a file for migration script
$validUrls = [
    'business' => $validBusinessImages,
    'e-commerce' => $validEcommerceImages,
    'portfolio' => $validPortfolioImages,
    'landing' => $validLandingImages,
    'services' => $validServiceImages
];

file_put_contents('valid-image-urls.json', json_encode($validUrls, JSON_PRETTY_PRINT));
echo "\n💾 Valid URLs saved to: valid-image-urls.json\n";

echo "\n🚀 Ready to create updated migration script with working URLs!\n";
?>