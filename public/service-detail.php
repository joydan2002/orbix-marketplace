<?php
/**
 * Service Detail Page
 * Displays detailed information about a specific service
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/service-manager.php';
require_once __DIR__ . '/../config/cloudinary-config.php'; // Add Cloudinary support

// Get service ID from URL
$serviceId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$serviceId) {
    header('Location: services.php');
    exit;
}

// Get database connection
try {
    $db = DatabaseConfig::getConnection();
    $serviceManager = new ServiceManager($db);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Get service details
$service = $serviceManager->getServiceById($serviceId);

if (!$service) {
    header('Location: services.php?error=not_found');
    exit;
}

// Get related services
$relatedServices = $serviceManager->getServices(['category' => $service['category_slug']], 'popular', 4);
$relatedServices = array_filter($relatedServices, function($s) use ($serviceId) {
    return $s['id'] != $serviceId;
});
$relatedServices = array_slice($relatedServices, 0, 3);

// Get service images with Cloudinary optimization
$images = [];
if (!empty($service['gallery_images'])) {
    // Check if gallery_images is already an array or a JSON string
    if (is_array($service['gallery_images'])) {
        $galleryImages = $service['gallery_images'];
    } else {
        $galleryImages = json_decode($service['gallery_images'], true) ?: [];
    }
    
    // Convert each gallery image to optimized URL
    foreach ($galleryImages as $image) {
        $images[] = getOptimizedImageUrl($image, 'large');
    }
}

// Add preview image as first image if not already in gallery
if (!empty($service['preview_image'])) {
    $previewUrl = getOptimizedImageUrl($service['preview_image'], 'large');
    if (!in_array($previewUrl, $images)) {
        array_unshift($images, $previewUrl);
    }
}

// Ensure we have at least one image
if (empty($images)) {
    $images[] = getOptimizedImageUrl('/orbix/assets/images/default-service.jpg', 'large');
}

// Get service tags
$tags = [];
if (!empty($service['tags'])) {
    $tags = json_decode($service['tags'], true) ?: [];
}

// Get service features
$features = [];
if (!empty($service['features'])) {
    $features = json_decode($service['features'], true) ?: [];
}

// Optimize profile image
$service['profile_image'] = getOptimizedImageUrl($service['profile_image'] ?? '/assets/images/default-avatar.png', 'avatar');
$service['preview_image_optimized'] = getOptimizedImageUrl($service['preview_image'], 'large');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($service['title']) ?> - Orbix Market</title>
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
        
        .service-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            object-position: center;
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
        
        .service-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .service-card:hover {
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
        
        .delivery-badge {
            background: linear-gradient(135deg, #10B981, #34D399);
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

    <!-- Hero Section with Service Info -->
    <section class="pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm mb-8">
                <a href="index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="services.php" class="text-gray-500 hover:text-primary transition-colors">Services</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="services.php?category=<?= htmlspecialchars($service['category_slug']) ?>" class="text-gray-500 hover:text-primary transition-colors"><?= htmlspecialchars($service['category_name']) ?></a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-secondary font-medium"><?= htmlspecialchars($service['title']) ?></span>
            </div>

            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="service-card p-8 mb-8">
                        <!-- Service Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h1 class="text-4xl font-bold text-secondary mb-4"><?= htmlspecialchars($service['title']) ?></h1>
                                <div class="flex items-center space-x-6 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center space-x-2">
                                        <img src="<?= htmlspecialchars($service['profile_image'] ?? '/assets/images/default-avatar.png') ?>" alt="<?= htmlspecialchars($service['seller_name']) ?>" class="w-8 h-8 rounded-full object-cover">
                                        <span class="font-medium"><?= htmlspecialchars($service['seller_name']) ?></span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex rating-stars">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="ri-star-<?= $i <= floor($service['avg_rating']) ? 'fill' : 'line' ?> text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span><?= number_format($service['avg_rating'], 1) ?> (<?= $service['review_count'] ?>)</span>
                                    </div>
                                    <?php if ($service['is_featured']): ?>
                                    <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs rounded-full font-bold">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center space-x-6 text-sm text-gray-500 mb-4">
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-eye-line"></i>
                                        <span><?= number_format($service['views_count']) ?> views</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-shopping-bag-line"></i>
                                        <span><?= number_format($service['orders_count']) ?> orders</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <i class="ri-calendar-line"></i>
                                        <span>Updated <?= date('M j, Y', strtotime($service['updated_at'])) ?></span>
                                    </div>
                                </div>
                                <div class="delivery-badge">
                                    <i class="ri-time-line"></i>
                                    <span>Delivery: <?= htmlspecialchars($service['delivery_time']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Images -->
                        <div class="mb-8">
                            <div class="zoom-container mb-6">
                                <img id="mainImage" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($service['title']) ?>" class="service-image zoom-image">
                            </div>
                            
                            <?php if (count($images) > 1): ?>
                            <div class="flex space-x-4 overflow-x-auto pb-2">
                                <?php foreach ($images as $index => $image): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="Preview <?= $index + 1 ?>" class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= htmlspecialchars($image) ?>', this)">
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Service Description -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-secondary mb-4">About This Service</h2>
                            <div class="prose max-w-none text-gray-700 leading-relaxed">
                                <?= nl2br(htmlspecialchars($service['description'])) ?>
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
                                            <i class="ri-settings-line text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary">Professional Service</h4>
                                            <p class="text-sm text-gray-600">High-quality work delivered on time</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="ri-customer-service-line text-purple-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary">24/7 Support</h4>
                                            <p class="text-sm text-gray-600">Get help when you need it</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                            <i class="ri-refresh-line text-orange-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary">Unlimited Revisions</h4>
                                            <p class="text-sm text-gray-600">Until you're 100% satisfied</p>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($service['technology'])): ?>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="ri-stack-line text-red-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary"><?= htmlspecialchars($service['technology']) ?></h4>
                                            <p class="text-sm text-gray-600">Modern technology stack</p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky-sidebar">
                        <!-- Purchase Card -->
                        <div class="service-card p-8 mb-8">
                            <div class="text-center mb-8">
                                <div class="price-badge mb-4">
                                    $<?= number_format($service['price'], 0) ?>
                                </div>
                                <p class="text-gray-600 font-medium">Starting price • Custom quotes available</p>
                            </div>

                            <div class="space-y-4 mb-8">
                                <button onclick="handleOrderService(<?= $service['id'] ?>, '<?= addslashes($service['title']) ?>', <?= $service['price'] ?>, '<?= addslashes($service['preview_image_optimized']) ?>', '<?= addslashes($service['seller_name']) ?>')" 
                                        class="w-full btn-primary">
                                    <i class="ri-shopping-cart-line mr-2"></i>Order Service
                                </button>
                                
                                <button class="w-full btn-secondary" onclick="contactSeller(<?= $service['id'] ?>)">
                                    <i class="ri-message-3-line mr-2"></i>Contact Seller
                                </button>
                                
                                <button class="w-full btn-secondary" onclick="addToFavorites(<?= $service['id'] ?>)">
                                    <i class="ri-heart-line mr-2"></i>Add to Favorites
                                </button>
                            </div>

                            <div class="border-t pt-6 space-y-4 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Category:</span>
                                    <span class="font-semibold text-secondary"><?= htmlspecialchars($service['category_name']) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Delivery Time:</span>
                                    <span class="font-semibold text-secondary"><?= htmlspecialchars($service['delivery_time']) ?></span>
                                </div>
                                <?php if (!empty($service['technology'])): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Technology:</span>
                                    <span class="font-semibold text-secondary"><?= htmlspecialchars($service['technology']) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Revisions:</span>
                                    <span class="font-semibold text-secondary"><?= $service['revisions'] ?? 'Unlimited' ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Last Update:</span>
                                    <span class="font-semibold text-secondary"><?= date('M j, Y', strtotime($service['updated_at'])) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Seller Info -->
                        <div class="service-card p-6">
                            <h4 class="text-xl font-bold text-secondary mb-4">About the Seller</h4>
                            <div class="flex items-center space-x-4 mb-4">
                                <img src="<?= htmlspecialchars($service['profile_image'] ?? '/assets/images/default-avatar.png') ?>" alt="<?= htmlspecialchars($service['seller_name']) ?>" class="w-16 h-16 rounded-full object-cover">
                                <div>
                                    <p class="font-bold text-lg text-secondary"><?= htmlspecialchars($service['seller_name']) ?></p>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex rating-stars">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="ri-star-<?= $i <= floor($service['avg_rating']) ? 'fill' : 'line' ?> text-sm"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-sm text-gray-600 font-medium"><?= number_format($service['avg_rating'], 1) ?> (<?= $service['review_count'] ?> reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">Professional service provider with expertise in delivering high-quality solutions for your business needs.</p>
                            <button class="w-full btn-secondary" onclick="contactSeller(<?= $service['id'] ?>)">
                                <i class="ri-message-3-line mr-2"></i>Contact Seller
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Services -->
    <?php if (!empty($relatedServices)): ?>
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h3 class="text-3xl font-bold text-secondary mb-12 text-center">Similar Services</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($relatedServices as $related): ?>
                <div class="service-card overflow-hidden">
                    <div class="relative">
                        <img src="<?= htmlspecialchars(getOptimizedImageUrl($related['preview_image'], 'medium')) ?>" alt="<?= htmlspecialchars($related['title']) ?>" class="w-full h-64 object-cover">
                        <div class="absolute top-4 right-4">
                            <button class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors shadow-lg">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                        <?php if ($related['is_featured']): ?>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs rounded-full font-bold">Featured</span>
                        </div>
                        <?php endif; ?>
                        <div class="absolute bottom-4 left-4">
                            <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full font-semibold"><?= htmlspecialchars($related['delivery_time']) ?></span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-2 mb-3">
                            <img src="<?= htmlspecialchars(getOptimizedImageUrl($related['profile_image'] ?? '/assets/images/default-avatar.png', 'avatar')) ?>" alt="<?= htmlspecialchars($related['seller_name']) ?>" class="w-6 h-6 rounded-full object-cover">
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
                                <button onclick="window.location.href='service-detail.php?id=<?= $related['id'] ?>'" class="w-10 h-10 flex items-center justify-center border-2 border-gray-200 rounded-lg hover:border-primary hover:text-primary transition-colors">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button onclick="handleOrderService(<?= $related['id'] ?>, '<?= addslashes($related['title']) ?>', <?= $related['price'] ?>, '<?= addslashes(getOptimizedImageUrl($related['preview_image'], 'medium')) ?>', '<?= addslashes($related['seller_name']) ?>')" 
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

        // Handle service order
        function handleOrderService(id, title, price, image, seller) {
            // Check if user is logged in
            if (typeof cart === 'undefined') {
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Create service data object
            const serviceData = {
                id: id,
                title: title,
                price: price,
                image: image,
                seller: seller,
                type: 'service'
            };
            
            // Add to cart using the global cart system
            cart.addItem(serviceData);
        }

        // Contact seller
        function contactSeller(serviceId) {
            // Check if user is logged in
            if (typeof cart === 'undefined') {
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Implementation for contacting seller
            console.log('Contacting seller for service:', serviceId);
            alert('Contact seller feature will be implemented soon!');
        }

        // Add to favorites
        function addToFavorites(serviceId) {
            // Check if user is logged in
            if (typeof cart === 'undefined') {
                window.location.href = 'auth.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
            
            // Implementation for adding to favorites
            console.log('Adding service to favorites:', serviceId);
            alert('Service added to favorites!');
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