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
    if (is_array($service['tags'])) {
        $tags = $service['tags'];
    } else {
        $tags = json_decode($service['tags'], true) ?: [];
    }
}

// Get service features
$features = [];
if (!empty($service['features'])) {
    if (is_array($service['features'])) {
        $features = $service['features'];
    } else {
        $features = json_decode($service['features'], true) ?: [];
    }
}

// Format delivery time with proper unit
function formatDeliveryTime($deliveryTime) {
    if (empty($deliveryTime) || !is_numeric($deliveryTime)) {
        return '7 days';
    }
    
    $days = intval($deliveryTime);
    
    if ($days == 1) {
        return '1 day';
    } elseif ($days <= 7) {
        return $days . ' days';
    } elseif ($days <= 14) {
        $weeks = round($days / 7, 1);
        if ($weeks == 1) {
            return '1 week';
        } elseif ($weeks == 2) {
            return '2 weeks';
        } else {
            return $weeks . ' weeks';
        }
    } elseif ($days <= 30) {
        return round($days / 7) . ' weeks';
    } else {
        $months = round($days / 30, 1);
        if ($months == 1) {
            return '1 month';
        } else {
            return $months . ' months';
        }
    }
}

// Optimize profile image
$service['profile_image'] = getOptimizedImageUrl($service['profile_image'] ?? '/assets/images/default-avatar.png', 'avatar');
$service['preview_image_optimized'] = getOptimizedImageUrl($service['preview_image'], 'large');
$service['delivery_time_formatted'] = formatDeliveryTime($service['delivery_time']);
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
    <link href="assets/css/service-detail.css" rel="stylesheet">
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
    <section class="pt-24 pb-12 lg:pt-24 lg:pb-12 md:pt-20 md:pb-8 sm:pt-16 sm:pb-6">
        <div class="max-w-7xl mx-auto px-6 lg:px-6 md:px-4 sm:px-3">
            <!-- Breadcrumb -->
            <div class="breadcrumb-mobile flex items-center space-x-2 text-sm mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                <a href="index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="services.php" class="text-gray-500 hover:text-primary transition-colors">Services</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="services.php?category=<?= htmlspecialchars($service['category_slug']) ?>" class="text-gray-500 hover:text-primary transition-colors"><?= htmlspecialchars($service['category_name']) ?></a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-secondary font-medium"><?= htmlspecialchars($service['title']) ?></span>
            </div>

            <div class="grid lg:grid-cols-3 gap-12 lg:gap-12 md:gap-8 sm:gap-6 grid-mobile-responsive">
                <!-- Main Content -->
                <div class="lg:col-span-2 mobile-order-content">
                    <div class="service-card p-8 lg:p-8 md:p-6 sm:p-4 mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                        <!-- Service Header -->
                        <div class="service-header-mobile flex items-start justify-between mb-6 lg:mb-6 md:mb-4 sm:mb-3">
                            <div class="w-full">
                                <h1 class="text-4xl lg:text-4xl md:text-3xl sm:text-2xl font-bold text-secondary mb-4 lg:mb-4 md:mb-3 sm:mb-2"><?= htmlspecialchars($service['title']) ?></h1>
                                <div class="service-meta-mobile flex items-center space-x-6 lg:space-x-6 md:space-x-4 sm:space-x-3 text-sm text-gray-600 mb-4 lg:mb-4 md:mb-3 sm:mb-2">
                                    <div class="flex items-center space-x-2">
                                        <img src="<?= htmlspecialchars($service['profile_image'] ?? '/assets/images/default-avatar.png') ?>" alt="<?= htmlspecialchars($service['seller_name']) ?>" class="seller-profile-mobile w-8 h-8 lg:w-8 lg:h-8 md:w-7 md:h-7 sm:w-6 sm:h-6 rounded-full object-cover">
                                        <span class="font-medium lg:text-sm md:text-sm sm:text-xs"><?= htmlspecialchars($service['seller_name']) ?></span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex rating-stars rating-stars-mobile">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="ri-star-<?= $i <= floor($service['avg_rating']) ? 'fill' : 'line' ?> text-sm lg:text-sm md:text-sm sm:text-xs"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="lg:text-sm md:text-sm sm:text-xs"><?= number_format($service['avg_rating'], 1) ?> (<?= $service['review_count'] ?>)</span>
                                    </div>
                                    <?php if ($service['is_featured']): ?>
                                    <span class="px-3 py-1 lg:px-3 lg:py-1 md:px-2 md:py-1 sm:px-2 sm:py-0.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs lg:text-xs md:text-xs sm:text-xs rounded-full font-bold">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <div class="service-stats-mobile flex items-center space-x-6 lg:space-x-6 md:space-x-4 sm:space-x-0 text-sm text-gray-500 mb-4 lg:mb-4 md:mb-3 sm:mb-2">
                                    <div class="service-stats-item flex items-center space-x-1">
                                        <i class="ri-eye-line"></i>
                                        <span class="lg:text-sm md:text-sm sm:text-xs"><?= number_format($service['views_count']) ?> views</span>
                                    </div>
                                    <div class="service-stats-item flex items-center space-x-1">
                                        <i class="ri-shopping-bag-line"></i>
                                        <span class="lg:text-sm md:text-sm sm:text-xs"><?= number_format($service['orders_count']) ?> orders</span>
                                    </div>
                                    <div class="service-stats-item flex items-center space-x-1">
                                        <i class="ri-calendar-line"></i>
                                        <span class="lg:text-sm md:text-sm sm:text-xs">Updated <?= date('M j, Y', strtotime($service['updated_at'])) ?></span>
                                    </div>
                                </div>
                                <div class="delivery-badge">
                                    <i class="ri-time-line"></i>
                                    <span>Delivery: <?= htmlspecialchars($service['delivery_time_formatted']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Images -->
                        <div class="mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                            <div class="zoom-container mb-6 lg:mb-6 md:mb-4 sm:mb-3">
                                <img id="mainImage" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($service['title']) ?>" class="service-image zoom-image">
                            </div>
                            
                            <?php if (count($images) > 1): ?>
                            <div class="thumbnail-container-mobile flex space-x-4 lg:space-x-4 md:space-x-3 sm:space-x-2 overflow-x-auto pb-2">
                                <?php foreach ($images as $index => $image): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="Preview <?= $index + 1 ?>" class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage('<?= htmlspecialchars($image) ?>', this)">
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Service Description -->
                        <div class="mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                            <h2 class="text-2xl lg:text-2xl md:text-xl sm:text-lg font-bold text-secondary mb-4 lg:mb-4 md:mb-3 sm:mb-2">About This Service</h2>
                            <div class="prose max-w-none text-gray-700 leading-relaxed lg:text-base md:text-sm sm:text-sm">
                                <?= nl2br(htmlspecialchars($service['description'])) ?>
                            </div>
                        </div>

                        <!-- Tags -->
                        <?php if (!empty($tags)): ?>
                        <div class="mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                            <h3 class="text-xl lg:text-xl md:text-lg sm:text-base font-semibold text-secondary mb-4 lg:mb-4 md:mb-3 sm:mb-2">Tags</h3>
                            <div class="flex flex-wrap gap-3 lg:gap-3 md:gap-2 sm:gap-1">
                                <?php foreach ($tags as $tag): ?>
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- What's Included -->
                        <div class="mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                            <h3 class="text-2xl lg:text-2xl md:text-xl sm:text-lg font-bold text-secondary mb-6 lg:mb-6 md:mb-4 sm:mb-3">What's Included</h3>
                            <div class="features-grid-mobile grid md:grid-cols-2 lg:grid-cols-2 gap-4 lg:gap-4 md:gap-3 sm:gap-2">
                                <?php if (!empty($features)): ?>
                                    <?php foreach ($features as $feature): ?>
                                    <div class="feature-card">
                                        <div class="flex items-center space-x-3 lg:space-x-3 md:space-x-2 sm:space-x-2">
                                            <div class="feature-icon-mobile w-12 h-12 lg:w-12 lg:h-12 md:w-10 md:h-10 sm:w-8 sm:h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="ri-check-line text-green-600 text-xl lg:text-xl md:text-lg sm:text-base"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-secondary lg:text-base md:text-sm sm:text-sm"><?= htmlspecialchars($feature) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3 lg:space-x-3 md:space-x-2 sm:space-x-2">
                                        <div class="feature-icon-mobile w-12 h-12 lg:w-12 lg:h-12 md:w-10 md:h-10 sm:w-8 sm:h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="ri-settings-line text-blue-600 text-xl lg:text-xl md:text-lg sm:text-base"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary lg:text-base md:text-sm sm:text-sm">Professional Service</h4>
                                            <p class="text-sm lg:text-sm md:text-xs sm:text-xs text-gray-600">High-quality work delivered on time</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3 lg:space-x-3 md:space-x-2 sm:space-x-2">
                                        <div class="feature-icon-mobile w-12 h-12 lg:w-12 lg:h-12 md:w-10 md:h-10 sm:w-8 sm:h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="ri-customer-service-line text-purple-600 text-xl lg:text-xl md:text-lg sm:text-base"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary lg:text-base md:text-sm sm:text-sm">24/7 Support</h4>
                                            <p class="text-sm lg:text-sm md:text-xs sm:text-xs text-gray-600">Get help when you need it</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3 lg:space-x-3 md:space-x-2 sm:space-x-2">
                                        <div class="feature-icon-mobile w-12 h-12 lg:w-12 lg:h-12 md:w-10 md:h-10 sm:w-8 sm:h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                            <i class="ri-refresh-line text-orange-600 text-xl lg:text-xl md:text-lg sm:text-base"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary lg:text-base md:text-sm sm:text-sm">Unlimited Revisions</h4>
                                            <p class="text-sm lg:text-sm md:text-xs sm:text-xs text-gray-600">Until you're 100% satisfied</p>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($service['technology'])): ?>
                                <div class="feature-card">
                                    <div class="flex items-center space-x-3 lg:space-x-3 md:space-x-2 sm:space-x-2">
                                        <div class="feature-icon-mobile w-12 h-12 lg:w-12 lg:h-12 md:w-10 md:h-10 sm:w-8 sm:h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="ri-stack-line text-red-600 text-xl lg:text-xl md:text-lg sm:text-base"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-secondary lg:text-base md:text-sm sm:text-sm"><?= htmlspecialchars($service['technology']) ?></h4>
                                            <p class="text-sm lg:text-sm md:text-xs sm:text-xs text-gray-600">Modern technology stack</p>
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
                <div class="lg:col-span-1 sidebar-mobile mobile-order-sidebar">
                    <div class="sticky-sidebar">
                        <!-- Purchase Card -->
                        <div class="service-card p-8 lg:p-8 md:p-6 sm:p-4 mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                            <div class="text-center mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                                <div class="price-badge mb-4 lg:mb-4 md:mb-3 sm:mb-2">
                                    $<?= number_format($service['price'], 0) ?>
                                </div>
                                <p class="text-gray-600 font-medium lg:text-base md:text-sm sm:text-sm">Starting price • Custom quotes available</p>
                            </div>

                            <div class="space-y-4 lg:space-y-4 md:space-y-3 sm:space-y-2 mb-8 lg:mb-8 md:mb-6 sm:mb-4">
                                <button onclick="handleOrderService(<?= $service['id'] ?>, '<?= addslashes($service['title']) ?>', <?= $service['price'] ?>, '<?= addslashes($service['preview_image_optimized']) ?>', '<?= addslashes($service['seller_name']) ?>')" 
                                        class="mobile-full-width w-full btn-primary">
                                    <i class="ri-shopping-cart-line mr-2"></i>Order Service
                                </button>
                                
                                <button class="mobile-full-width w-full btn-secondary" onclick="contactSeller(<?= $service['id'] ?>)">
                                    <i class="ri-message-3-line mr-2"></i>Contact Seller
                                </button>
                                
                                <button class="mobile-full-width w-full btn-secondary" onclick="addToFavorites(<?= $service['id'] ?>)">
                                    <i class="ri-heart-line mr-2"></i>Add to Favorites
                                </button>
                            </div>

                            <div class="border-t pt-6 lg:pt-6 md:pt-4 sm:pt-3 space-y-4 lg:space-y-4 md:space-y-3 sm:space-y-2 text-sm lg:text-sm md:text-sm sm:text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Category:</span>
                                    <span class="font-semibold text-secondary"><?= htmlspecialchars($service['category_name']) ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Delivery Time:</span>
                                    <span class="font-semibold text-secondary"><?= htmlspecialchars($service['delivery_time_formatted']) ?></span>
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
                        <div class="service-card p-6 lg:p-6 md:p-4 sm:p-3">
                            <h4 class="text-xl lg:text-xl md:text-lg sm:text-base font-bold text-secondary mb-4 lg:mb-4 md:mb-3 sm:mb-2">About the Seller</h4>
                            <div class="flex items-center space-x-4 lg:space-x-4 md:space-x-3 sm:space-x-2 mb-4 lg:mb-4 md:mb-3 sm:mb-2">
                                <img src="<?= htmlspecialchars($service['profile_image'] ?? '/assets/images/default-avatar.png') ?>" alt="<?= htmlspecialchars($service['seller_name']) ?>" class="w-16 h-16 lg:w-16 lg:h-16 md:w-14 md:h-14 sm:w-12 sm:h-12 rounded-full object-cover">
                                <div>
                                    <p class="font-bold text-lg lg:text-lg md:text-base sm:text-sm text-secondary"><?= htmlspecialchars($service['seller_name']) ?></p>
                                    <div class="flex items-center space-x-1">
                                        <div class="flex rating-stars rating-stars-mobile">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="ri-star-<?= $i <= floor($service['avg_rating']) ? 'fill' : 'line' ?> text-sm lg:text-sm md:text-sm sm:text-xs"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-sm lg:text-sm md:text-sm sm:text-xs text-gray-600 font-medium"><?= number_format($service['avg_rating'], 1) ?> (<?= $service['review_count'] ?> reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm lg:text-sm md:text-sm sm:text-xs mb-4 lg:mb-4 md:mb-3 sm:mb-2 leading-relaxed">Professional service provider with expertise in delivering high-quality solutions for your business needs.</p>
                            <button class="mobile-full-width w-full btn-secondary" onclick="contactSeller(<?= $service['id'] ?>)">
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
    <section class="py-16 lg:py-16 md:py-12 sm:py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-6 md:px-4 sm:px-3">
            <h3 class="section-title-mobile text-3xl lg:text-3xl md:text-2xl sm:text-xl font-bold text-secondary mb-12 lg:mb-12 md:mb-8 sm:mb-6 text-center">Similar Services</h3>
            <div class="related-services-mobile grid md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-8 md:gap-6 sm:gap-4">
                <?php foreach ($relatedServices as $related): ?>
                <div class="related-service-card service-card overflow-hidden">
                    <div class="relative">
                        <img src="<?= htmlspecialchars(getOptimizedImageUrl($related['preview_image'], 'medium')) ?>" alt="<?= htmlspecialchars($related['title']) ?>" class="related-service-image w-full h-64 lg:h-64 md:h-48 sm:h-40 object-cover">
                        <div class="absolute top-4 lg:top-4 md:top-3 sm:top-2 right-4 lg:right-4 md:right-3 sm:right-2">
                            <button class="w-10 h-10 lg:w-10 lg:h-10 md:w-8 md:h-8 sm:w-8 sm:h-8 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition-colors shadow-lg">
                                <i class="ri-heart-line text-gray-600 lg:text-base md:text-sm sm:text-sm"></i>
                            </button>
                        </div>
                        <?php if ($related['is_featured']): ?>
                        <div class="absolute top-4 lg:top-4 md:top-3 sm:top-2 left-4 lg:left-4 md:left-3 sm:left-2">
                            <span class="px-3 py-1 lg:px-3 lg:py-1 md:px-2 md:py-1 sm:px-2 sm:py-0.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs lg:text-xs md:text-xs sm:text-xs rounded-full font-bold">Featured</span>
                        </div>
                        <?php endif; ?>
                        <div class="absolute bottom-4 lg:bottom-4 md:bottom-3 sm:bottom-2 left-4 lg:left-4 md:left-3 sm:left-2">
                            <span class="px-3 py-1 lg:px-3 lg:py-1 md:px-2 md:py-1 sm:px-2 sm:py-0.5 bg-green-500 text-white text-xs lg:text-xs md:text-xs sm:text-xs rounded-full font-semibold"><?= htmlspecialchars(formatDeliveryTime($related['delivery_time'])) ?></span>
                        </div>
                    </div>
                    <div class="p-6 lg:p-6 md:p-4 sm:p-3">
                        <div class="flex items-center space-x-2 lg:space-x-2 md:space-x-1 sm:space-x-1 mb-3 lg:mb-3 md:mb-2 sm:mb-2">
                            <img src="<?= htmlspecialchars(getOptimizedImageUrl($related['profile_image'] ?? '/assets/images/default-avatar.png', 'avatar')) ?>" alt="<?= htmlspecialchars($related['seller_name']) ?>" class="w-6 h-6 lg:w-6 lg:h-6 md:w-5 md:h-5 sm:w-4 sm:h-4 rounded-full object-cover">
                            <span class="text-sm lg:text-sm md:text-xs sm:text-xs text-gray-600 font-medium"><?= htmlspecialchars($related['seller_name']) ?></span>
                        </div>
                        <h4 class="text-xl lg:text-xl md:text-lg sm:text-base font-bold text-secondary mb-2 lg:mb-2 md:mb-1 sm:mb-1"><?= htmlspecialchars($related['title']) ?></h4>
                        <p class="text-sm lg:text-sm md:text-xs sm:text-xs text-gray-600 mb-4 lg:mb-4 md:mb-3 sm:mb-2 leading-relaxed"><?= htmlspecialchars(substr($related['description'], 0, 100)) ?>...</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl lg:text-2xl md:text-xl sm:text-lg font-bold text-primary mb-1 lg:mb-1 md:mb-0.5 sm:mb-0.5">$<?= number_format($related['price'], 0) ?></div>
                                <div class="flex items-center space-x-1 lg:space-x-1 md:space-x-0.5 sm:space-x-0.5">
                                    <div class="flex rating-stars rating-stars-mobile">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="ri-star-<?= $i <= floor($related['avg_rating']) ? 'fill' : 'line' ?> text-xs lg:text-xs md:text-xs sm:text-xs"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-xs lg:text-xs md:text-xs sm:text-xs text-gray-600"><?= number_format($related['avg_rating'], 1) ?> (<?= $related['review_count'] ?>)</span>
                                </div>
                            </div>
                            <div class="flex space-x-2 lg:space-x-2 md:space-x-1 sm:space-x-1">
                                <button onclick="window.location.href='service-detail.php?id=<?= $related['id'] ?>'" class="w-10 h-10 lg:w-10 lg:h-10 md:w-8 md:h-8 sm:w-8 sm:h-8 flex items-center justify-center border-2 border-gray-200 rounded-lg hover:border-primary hover:text-primary transition-colors">
                                    <i class="ri-eye-line lg:text-base md:text-sm sm:text-sm"></i>
                                </button>
                                <button onclick="handleOrderService(<?= $related['id'] ?>, '<?= addslashes($related['title']) ?>', <?= $related['price'] ?>, '<?= addslashes(getOptimizedImageUrl($related['preview_image'], 'medium')) ?>', '<?= addslashes($related['seller_name']) ?>')" 
                                        class="w-10 h-10 lg:w-10 lg:h-10 md:w-8 md:h-8 sm:w-8 sm:h-8 flex items-center justify-center bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                    <i class="ri-shopping-cart-line lg:text-base md:text-sm sm:text-sm"></i>
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
            showInfo('Contact seller feature will be implemented soon!');
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
            showSuccess('Service added to favorites!');
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