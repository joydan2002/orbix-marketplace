<?php
/**
 * Template Detail Page
 * Displays detailed information about a specific template
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/template-manager.php';
} catch (Exception $e) {
    die('Error loading required files: ' . $e->getMessage());
}

// Get template ID from URL
$templateId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$templateId) {
    die('Template ID is required');
}

// Get database connection
try {
    $db = DatabaseConfig::getConnection();
    $templateManager = new TemplateManager($db);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Get template details
try {
    $template = $templateManager->getTemplateById($templateId);
} catch (Exception $e) {
    die('Error fetching template: ' . $e->getMessage());
}

if (!$template) {
    die('Template not found with ID: ' . $templateId);
}

// Debug: Let's see what we got
// echo '<pre>Template data: '; print_r($template); echo '</pre>';

// Get related templates
try {
    $relatedTemplates = $templateManager->getTemplates(['category' => $template['category_slug']], 'popular', 4);
    $relatedTemplates = array_filter($relatedTemplates, function($t) use ($templateId) {
        return $t['id'] != $templateId;
    });
    $relatedTemplates = array_slice($relatedTemplates, 0, 3);
} catch (Exception $e) {
    error_log('Error getting related templates: ' . $e->getMessage());
    $relatedTemplates = [];
}

// Get template images
$images = [];
if (!empty($template['gallery_images'])) {
    // Check if gallery_images is already an array or a JSON string
    if (is_array($template['gallery_images'])) {
        $images = $template['gallery_images'];
    } else {
        $images = json_decode($template['gallery_images'], true) ?: [];
    }
}
if (!empty($template['preview_image'])) {
    array_unshift($images, $template['preview_image']);
}

// Get template tags
$tags = [];
if (!empty($template['tags'])) {
    // Check if tags is already an array or a JSON string
    if (is_array($template['tags'])) {
        $tags = $template['tags'];
    } else {
        $tags = json_decode($template['tags'], true) ?: [];
    }
}

// Get template features
$features = [];
if (!empty($template['features'])) {
    // Check if features is already an array or a JSON string
    if (is_array($template['features'])) {
        $features = $template['features'];
    } else {
        $features = json_decode($template['features'], true) ?: [];
    }
}

// Set default values for missing fields
$template['seller_name'] = $template['seller_name'] ?? 'Unknown';
$template['profile_image'] = $template['profile_image'] ?? '/assets/images/default-avatar.png';
$template['avg_rating'] = $template['avg_rating'] ?? 4.5;
$template['review_count'] = $template['review_count'] ?? 0;
$template['views_count'] = $template['views_count'] ?? 0;
$template['downloads_count'] = $template['downloads_count'] ?? 0;
$template['is_featured'] = $template['is_featured'] ?? 0;
$template['technology'] = $template['technology'] ?? 'HTML/CSS';
$template['category_name'] = $template['category_name'] ?? 'General';
$template['category_slug'] = $template['category_slug'] ?? 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($template['title']) ?> - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }
        
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
        }
        
        .template-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            object-position: top;
            border-radius: 1rem;
        }
        
        .thumbnail {
            width: 100px;
            height: 75px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }
        
        .thumbnail:hover, .thumbnail.active {
            border: 3px solid #FF5F1F;
            transform: scale(1.05);
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 95, 31, 0.1);
        }
        
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(255, 95, 31, 0.1);
            border-color: rgba(255, 95, 31, 0.2);
        }
        
        .rating-stars {
            color: #fbbf24;
        }
        
        .tag {
            background: rgba(255, 95, 31, 0.1);
            color: #FF5F1F;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid rgba(255, 95, 31, 0.2);
        }
        
        .sticky-sidebar {
            position: sticky;
            top: 2rem;
        }
        
        .zoom-container {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }
        
        .zoom-image {
            transition: transform 0.3s ease;
        }
        
        .zoom-container:hover .zoom-image {
            transform: scale(1.05);
        }
        
        .template-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .template-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }
        
        .price-badge {
            background: linear-gradient(135deg, #FF5F1F, #FF8C42);
            color: white;
            font-weight: 700;
            font-size: 2rem;
            padding: 1rem 2rem;
            border-radius: 1rem;
            text-align: center;
            box-shadow: 0 10px 25px rgba(255, 95, 31, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF5F1F, #FF8C42);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 95, 31, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 95, 31, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #FF5F1F;
            color: #FF5F1F;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #FF5F1F;
            color: white;
            transform: translateY(-2px);
        }
        
        .tech-badge {
            background: linear-gradient(135deg, #3B82F6, #60A5FA);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1F',
                        secondary: '#1f2937'
                    },
                    borderRadius: {
                        'button': '12px'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'pacifico': ['Pacifico', 'serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter gradient-bg min-h-screen">
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section with Template Info -->
    <section class="pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm mb-8">
                <a href="index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="templates.php" class="text-gray-500 hover:text-primary transition-colors">Templates</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="templates.php?category=<?= htmlspecialchars($template['category_slug']) ?>" class="text-gray-500 hover:text-primary transition-colors"><?= htmlspecialchars($template['category_name']) ?></a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-secondary font-medium"><?= htmlspecialchars($template['title']) ?></span>
            </div>

            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="template-card p-8 mb-8">
                        <!-- Template Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h1 class="text-4xl font-bold text-secondary mb-4"><?= htmlspecialchars($template['title']) ?></h1>
                                <div class="flex items-center space-x-6 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center space-x-2">
                                        <img src="<?= htmlspecialchars($template['profile_image'] ?? '/assets/images/default-avatar.png') ?>" alt="<?= htmlspecialchars($template['seller_name']) ?>" class="w-8 h-8 rounded-full object-cover">
                                        <span class="font-medium"><?= htmlspecialchars($template['seller_name']) ?></span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex rating-stars">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="ri-star-<?= $i <= floor($template['avg_rating']) ? 'fill' : 'line' ?> text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span><?= number_format($template['avg_rating'], 1) ?> (<?= $template['review_count'] ?>)</span>
                                    </div>
                                    <?php if ($template['is_featured']): ?>
                                    <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs rounded-full font-bold">Best Seller</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center space-x-6 text-sm text-gray-500 mb-4">
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-eye-line"></i>
                                        <span><?= number_format($template['views_count']) ?> views</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-download-line"></i>
                                        <span><?= number_format($template['downloads_count']) ?> downloads</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-calendar-line"></i>
                                        <span>Updated <?= date('M j, Y', strtotime($template['updated_at'])) ?></span>
                                    </div>
                                </div>
                                <?php if (!empty($template['technology'])): ?>
                                <div class="tech-badge">
                                    <i class="ri-code-s-line"></i>
                                    <span><?= htmlspecialchars($template['technology']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Template Images -->
                        <div class="mb-8">
                            <div class="zoom-container mb-6">
                                <img id="mainImage" src="<?= htmlspecialchars($images[0] ?? $template['preview_image']) ?>" alt="<?= htmlspecialchars($template['title']) ?>" class="template-image zoom-image">
                            </div>
                            
                            <?php if (count($images) > 1): ?>
                            <div class="flex space-x-4 overflow-x-auto pb-2">
                                <?php foreach ($images as $index => $image): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="Preview <?= $index + 1 ?>" class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= htmlspecialchars($image) ?>', this)">
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Template Description -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-secondary mb-4">About This Template</h2>
                            <div class="prose max-w-none text-gray-700 leading-relaxed">
                                <?= nl2br(htmlspecialchars($template['description'])) ?>
                            </div>
                        </div>

                        <!-- Tags -->
                        <?php if (!empty($tags)): ?>
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-secondary mb-4">Tags</h3>
                            <div class="flex flex-wrap gap-3">
                                <?php foreach ($tags as $tag): ?>
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- What's Included -->
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-secondary mb-6">What's Included</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <?php if (!empty($features)): ?>
                                    <?php foreach ($features as $feature): ?>
                                    <div class="feature-card">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="ri-check-line text-green-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-secondary"><?= htmlspecialchars($feature) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="ri-file-code-line text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary">Source Files</h4>
                                            <p class="text-sm text-gray-600">All HTML, CSS, and JS files included</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="ri-smartphone-line text-purple-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary">Responsive Design</h4>
                                            <p class="text-sm text-gray-600">Works perfectly on all devices</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                            <i class="ri-palette-line text-orange-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary">Easy Customization</h4>
                                            <p class="text-sm text-gray-600">Well-documented and easy to modify</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="ri-customer-service-line text-red-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary">Free Support</h4>
                                            <p class="text-sm text-gray-600">Get help when you need it</p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky-sidebar">
                        <!-- Purchase Card -->
                        <div class="template-card p-8 mb-8">
                            <div class="text-center mb-8">
                                <div class="price-badge mb-4">
                                    $<?= number_format($template['price'], 0) ?>
                                </div>
                                <p class="text-gray-600 font-medium">One-time purchase • Lifetime access</p>
                            </div>

                            <div class="space-y-4 mb-8">
                                <button onclick="handlePurchaseTemplate(<?= $template['id'] ?>, '<?= addslashes($template['title']) ?>', <?= $template['price'] ?>, '<?= addslashes($template['preview_image']) ?>', '<?= addslashes($template['seller_name']) ?>')" 
                                        class="w-full btn-primary">
                                    <i class="ri-shopping-cart-line mr-2"></i>Add to Cart
                                </button>
                                
                                <?php if (!empty($template['demo_url'])): ?>
                                <a href="<?= htmlspecialchars($template['demo_url']) ?>" target="_blank" class="w-full btn-secondary inline-flex justify-center items-center">
                                    <i class="ri-external-link-line mr-2"></i>Live Preview
                                </a>
                                <?php endif; ?>
                                
                                <button class="w-full btn-secondary" onclick="addToFavorites(<?= $template['id'] ?>)">
                                    <i class="ri-heart-line mr-2"></i>Add to Favorites
                                </button>
                            </div>

                            <div class="border-t pt-6 space-y-4 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Category:</span>
                                    <span class="font-semibold text-secondary"><?= htmlspecialchars($template['category_name']) ?></span>
                                </div>
                                <?php if (!empty($template['technology'])): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Technology:</span>
                                    <span class="font-semibold text-secondary"><?= htmlspecialchars($template['technology']) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Downloads:</span>
                                    <span class="font-semibold text-secondary"><?= number_format($template['downloads_count']) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Last Update:</span>
                                    <span class="font-semibold text-secondary"><?= date('M j, Y', strtotime($template['updated_at'])) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">File Size:</span>
                                    <span class="font-semibold text-secondary"><?= $template['file_size'] ?? '2.5 MB' ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Seller Info -->
                        <div class="template-card p-6">
                            <h4 class="text-xl font-bold text-secondary mb-4">About the Author</h4>
                            <div class="flex items-center space-x-4 mb-4">
                                <img src="<?= htmlspecialchars($template['profile_image'] ?? '/assets/images/default-avatar.png') ?>" alt="<?= htmlspecialchars($template['seller_name']) ?>" class="w-16 h-16 rounded-full object-cover">
                                <div>
                                    <p class="font-bold text-lg text-secondary"><?= htmlspecialchars($template['seller_name']) ?></p>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex rating-stars">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="ri-star-<?= $i <= floor($template['avg_rating']) ? 'fill' : 'line' ?> text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-sm text-gray-600 font-medium"><?= number_format($template['avg_rating'], 1) ?> (<?= $template['review_count'] ?> reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Professional template designer with expertise in creating modern, responsive websites that convert visitors into customers.</p>
                            <button class="w-full btn-secondary" onclick="contactSeller(<?= $template['id'] ?>)">
                                <i class="ri-message-3-line mr-2"></i>Contact Author
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Templates -->
    <?php if (!empty($relatedTemplates)): ?>
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h3 class="text-3xl font-bold text-secondary mb-12 text-center">Similar Templates</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($relatedTemplates as $related): ?>
                <div class="template-card overflow-hidden">
                    <div class="relative">
                        <img src="<?= htmlspecialchars($related['preview_image']) ?>" alt="<?= htmlspecialchars($related['title']) ?>" class="w-full h-64 object-cover object-top">
                        <div class="absolute top-4 right-4">
                            <button class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors shadow-lg">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                        <?php if ($related['is_featured']): ?>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs rounded-full font-bold">Best Seller</span>
                        </div>
                        <?php endif; ?>
                        <div class="absolute bottom-4 left-4">
                            <span class="px-3 py-1 bg-primary text-white text-xs rounded-full font-semibold"><?= htmlspecialchars($related['technology']) ?></span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-2 mb-3">
                            <img src="<?= htmlspecialchars($related['profile_image']) ?>" alt="<?= htmlspecialchars($related['seller_name']) ?>" class="w-6 h-6 rounded-full object-cover">
                            <span class="text-sm text-gray-600 font-medium"><?= htmlspecialchars($related['seller_name']) ?></span>
                        </div>
                        <h4 class="text-xl font-bold text-secondary mb-2"><?= htmlspecialchars($related['title']) ?></h4>
                        <p class="text-sm text-gray-600 mb-4 leading-relaxed"><?= htmlspecialchars(substr($related['description'], 0, 100)) ?>...</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold text-primary mb-1">$<?= number_format($related['price'], 0) ?></div>
                                <div class="flex items-center space-x-1">
                                    <div class="flex rating-stars">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="ri-star-<?= $i <= floor($related['avg_rating']) ? 'fill' : 'line' ?> text-xs"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-xs text-gray-600"><?= number_format($related['avg_rating'], 1) ?> (<?= $related['review_count'] ?>)</span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="window.location.href='template-detail.php?id=<?= $related['id'] ?>'" class="w-10 h-10 flex items-center justify-center border-2 border-gray-200 rounded-lg hover:border-primary hover:text-primary transition-colors">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button onclick="handlePurchaseTemplate(<?= $related['id'] ?>, '<?= addslashes($related['title']) ?>', <?= $related['price'] ?>, '<?= addslashes($related['preview_image']) ?>', '<?= addslashes($related['seller_name']) ?>')" 
                                        class="w-10 h-10 flex items-center justify-center bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                    <i class="ri-shopping-cart-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Change main image
        function changeMainImage(imageSrc, thumbnail) {
            document.getElementById('mainImage').src = imageSrc;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            thumbnail.classList.add('active');
        }

        // Handle template purchase
        function handlePurchaseTemplate(id, title, price, image, seller) {
            // Check if user is logged in
            if (typeof cart === 'undefined') {
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Create template data object
            const templateData = {
                id: id,
                title: title,
                price: price,
                image: image,
                seller: seller,
                type: 'template'
            };
            
            // Add to cart using the global cart system
            cart.addItem(templateData);
        }

        // Contact seller
        function contactSeller(templateId) {
            // Check if user is logged in
            if (typeof cart === 'undefined') {
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Implementation for contacting seller
            console.log('Contacting seller for template:', templateId);
            alert('Contact seller feature will be implemented soon!');
        }

        // Add to favorites
        function addToFavorites(templateId) {
            // Check if user is logged in
            if (typeof cart === 'undefined') {
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Implementation for adding to favorites
            console.log('Adding template to favorites:', templateId);
            alert('Template added to favorites!');
        }

        // Smooth scroll for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>