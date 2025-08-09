<?php
/**
 * Root Index File - Railway Production Entry Point
 * This file handles routing for Railway deployment
 */

// Store original working directory
$original_cwd = getcwd();

// Check what path is being requested
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Debug mode check
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

if ($debug) {
    echo "<h2>🔍 Railway Debug Info</h2>";
    echo "<p><strong>Request URI:</strong> " . htmlspecialchars($request_uri) . "</p>";
    echo "<p><strong>Path:</strong> " . htmlspecialchars($path) . "</p>";
    echo "<p><strong>Query String:</strong> " . htmlspecialchars($_SERVER['QUERY_STRING'] ?? '') . "</p>";
    echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
    echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
    echo "<p><strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
    echo "<p><strong>Current Working Dir:</strong> " . $original_cwd . "</p>";
    echo "<p><strong>Request Method:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
    echo "<p><strong>GET params:</strong> " . json_encode($_GET) . "</p>";
    echo "<p><strong>POST params:</strong> " . json_encode($_POST) . "</p>";
    echo "<hr>";
}

// Function to safely include files from public directory
function includeFromPublic($filename) {
    global $original_cwd;
    
    // Change to public directory
    chdir($original_cwd . '/public');
    
    // Include the file
    require_once $filename;
    
    // Change back to original directory
    chdir($original_cwd);
}

// Handle specific routes
switch ($path) {
    case '':
    case 'index.php':
        // Include index from public directory
        includeFromPublic('index.php');
        break;
        
    // API routes - serve directly from api folder
    case 'api/auth.php':
        // Preserve query parameters and pass to API
        $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ?? '';
        require_once 'api/auth.php';
        break;
        
    case 'api/cart.php':
        $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ?? '';
        require_once 'api/cart.php';
        break;
        
    case 'api/general.php':
        $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ?? '';
        require_once 'api/general.php';
        break;
        
    case 'api/seller.php':
        $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ?? '';
        require_once 'api/seller.php';
        break;
        
    case 'debug-assets.php':
        // Serve debug assets from root
        require_once 'debug-assets.php';
        break;
        
    case 'auth.php':
    case 'public/auth.php':
        includeFromPublic('auth.php');
        break;
        
    case 'support.php':
    case 'public/support.php':
        includeFromPublic('support.php');
        break;
        
    case 'templates.php':
    case 'public/templates.php':
        includeFromPublic('templates.php');
        break;
        
    case 'services.php':
    case 'public/services.php':
        includeFromPublic('services.php');
        break;
        
    case 'cart.php':
    case 'public/cart.php':
        includeFromPublic('cart.php');
        break;
        
    case 'checkout.php':
    case 'public/checkout.php':
        includeFromPublic('checkout.php');
        break;
        
    case 'profile.php':
    case 'public/profile.php':
        includeFromPublic('profile.php');
        break;
        
    case 'seller-channel.php':
    case 'public/seller-channel.php':
        includeFromPublic('seller-channel.php');
        break;
        
    case 'template-detail.php':
    case 'public/template-detail.php':
        includeFromPublic('template-detail.php');
        break;
        
    case 'service-detail.php':
    case 'public/service-detail.php':
        includeFromPublic('service-detail.php');
        break;
        
    case 'logout.php':
    case 'public/logout.php':
        includeFromPublic('logout.php');
        break;
        
    // API endpoints - serve directly from api folder
    case 'api.php':
    case 'public/api.php':
        require_once 'api/general.php';
        break;
        
    case 'cart-api.php':
    case 'public/cart-api.php':
        require_once 'api/cart.php';
        break;
        
    case 'seller-api.php':
    case 'public/seller-api.php':
        require_once 'api/seller.php';
        break;
        
    case 'auth-handler.php':
    case 'public/auth-handler.php':
        require_once 'api/auth.php';
        break;
        
    case 'api/test.php':
        require_once 'api/test.php';
        break;
        
    case 'user-profile-api.php':
    case 'public/user-profile-api.php':
        includeFromPublic('user-profile-api.php');
        break;
        
    default:
        // Handle static assets (CSS, JS, images) - serve directly
        $static_assets = ['assets/', 'css/', 'js/', 'images/', 'vendor/'];
        $is_static = false;
        foreach ($static_assets as $asset_dir) {
            if (strpos($path, $asset_dir) === 0) {
                $is_static = true;
                break;
            }
        }
        
        if ($is_static) {
            // Look for static files in public directory first, then root
            $file_paths = ['public/' . $path, $path];
            
            foreach ($file_paths as $file_path) {
                if (file_exists($file_path) && is_file($file_path)) {
                    // Get file extension to set correct MIME type
                    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                    $mime_types = [
                        'css' => 'text/css',
                        'js' => 'application/javascript',
                        'png' => 'image/png',
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'gif' => 'image/gif',
                        'svg' => 'image/svg+xml',
                        'ico' => 'image/x-icon',
                        'json' => 'application/json',
                        'txt' => 'text/plain'
                    ];
                    
                    if (isset($mime_types[$ext])) {
                        header('Content-Type: ' . $mime_types[$ext]);
                    }
                    
                    // Output the file content
                    readfile($file_path);
                    exit;
                }
            }
        }
        
        // Check if file exists in public directory  
        $public_file = 'public/' . $path;
        if (file_exists($public_file) && is_file($public_file)) {
            // Include using our safe function
            includeFromPublic(basename($path));
        } else {
            // 404 - File not found
            http_response_code(404);
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>404 - Page Not Found | Orbix Market</title>
                <script src="https://cdn.tailwindcss.com/3.4.16"></script>
                <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
            </head>
            <body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
                <div class="bg-white/20 backdrop-blur-lg border border-white/30 rounded-2xl p-8 max-w-md mx-4 text-center">
                    <i class="ri-error-warning-line text-6xl text-white mb-4"></i>
                    <h1 class="text-3xl font-bold text-white mb-4">404 - Page Not Found</h1>
                    <p class="text-white/80 mb-6">The page you're looking for doesn't exist on Railway.</p>
                    
                    <div class="space-y-3 mb-6">
                        <p class="text-sm text-white/70">
                            <strong>Requested:</strong> <?= htmlspecialchars($path) ?>
                        </p>
                        <p class="text-sm text-white/70">
                            <strong>Host:</strong> <?= htmlspecialchars($_SERVER['HTTP_HOST']) ?>
                        </p>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="/" class="block w-full bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <i class="ri-home-line mr-2"></i>Go Home
                        </a>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <a href="/auth.php" class="bg-white/10 hover:bg-white/20 text-white py-2 px-3 rounded transition-colors">Auth</a>
                            <a href="/support.php" class="bg-white/10 hover:bg-white/20 text-white py-2 px-3 rounded transition-colors">Support</a>
                            <a href="/templates.php" class="bg-white/10 hover:bg-white/20 text-white py-2 px-3 rounded transition-colors">Templates</a>
                            <a href="/services.php" class="bg-white/10 hover:bg-white/20 text-white py-2 px-3 rounded transition-colors">Services</a>
                        </div>
                        
                        <a href="/debug-assets.php" class="block w-full bg-orange-500/20 hover:bg-orange-500/30 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <i class="ri-bug-line mr-2"></i>Debug Assets
                        </a>
                    </div>
                </div>
            </body>
            </html>
            <?php
        }
        break;
}
?>
