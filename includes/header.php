<?php
/**
 * Header Include File
 * Contains the HTML header section with navigation
 */

require_once __DIR__ . '/../config/database.php';

$config = DatabaseConfig::getAppConfig();

// Get categories and technologies for dropdown
try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get categories with template count
    $stmt = $pdo->query("SELECT c.name, c.slug, COUNT(t.id) as template_count 
                        FROM categories c 
                        LEFT JOIN templates t ON c.id = t.category_id AND t.status = 'approved'
                        WHERE c.is_active = 1 
                        GROUP BY c.id, c.name, c.slug
                        ORDER BY template_count DESC, c.name");
    $headerCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get popular technologies
    $stmt = $pdo->query("SELECT technology, COUNT(*) as count 
                        FROM templates 
                        WHERE status = 'approved' AND technology IS NOT NULL 
                        GROUP BY technology 
                        ORDER BY count DESC 
                        LIMIT 6");
    $headerTechnologies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Fallback to empty arrays if database error
    $headerCategories = [];
    $headerTechnologies = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['name']; ?> - Premium Website Templates Marketplace</title>
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
        .neon-glow {
            box-shadow: 0 0 20px rgba(255, 95, 31, 0.3);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
        .template-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .template-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .ai-mascot {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
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
                        'none': '0px',
                        'sm': '4px',
                        DEFAULT: '8px',
                        'md': '12px',
                        'lg': '16px',
                        'xl': '20px',
                        '2xl': '24px',
                        '3xl': '32px',
                        'full': '9999px',
                        'button': '8px'
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
<header class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md" style="background: rgba(255, 255, 255, 0.1);">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">O</span>
                </div>
                <span class="font-pacifico text-3xl text-secondary">Orbix Market</span>
            </div>
            
            <!-- Navigation -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="index.php" class="text-secondary hover:text-primary transition-colors font-medium">Home</a>
                
                <!-- Templates Dropdown - Dynamic with wider layout -->
                <div id="templates-dropdown" class="relative group">
                    <a href="templates.php" class="text-secondary hover:text-primary transition-colors font-medium flex items-center">
                        Templates 
                        <div class="w-4 h-4 flex items-center justify-center ml-1">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </a>
                    
                    <!-- Expanded Dropdown Content -->
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 w-[600px] bg-white rounded-2xl shadow-2xl border border-gray-100 opacity-0 invisible translate-y-2 transition-all duration-300" style="display: none;">
                        <div class="p-8">
                            <div class="grid grid-cols-2 gap-8">
                                <!-- Categories Section -->
                                <div>
                                    <h3 class="font-semibold text-secondary mb-4 flex items-center">
                                        <i class="ri-folder-line mr-2 text-primary"></i>
                                        Categories
                                    </h3>
                                    <div class="space-y-2">
                                        <a href="templates.php" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <span class="text-gray-600 group-hover:text-primary">All Templates</span>
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">
                                                <?= array_sum(array_column($headerCategories, 'template_count')) ?>
                                            </span>
                                        </a>
                                        <?php foreach ($headerCategories as $category): ?>
                                        <a href="templates.php?category=<?= urlencode($category['slug']) ?>" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <span class="text-gray-600 group-hover:text-primary"><?= htmlspecialchars($category['name']) ?></span>
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full"><?= $category['template_count'] ?></span>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Technologies Section -->
                                <div>
                                    <h3 class="font-semibold text-secondary mb-4 flex items-center">
                                        <i class="ri-code-line mr-2 text-primary"></i>
                                        Technologies
                                    </h3>
                                    <div class="space-y-2">
                                        <?php foreach ($headerTechnologies as $tech): ?>
                                        <a href="templates.php?technology=<?= urlencode($tech['technology']) ?>" class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                            <span class="text-gray-600 group-hover:text-primary"><?= htmlspecialchars($tech['technology']) ?></span>
                                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full"><?= $tech['count'] ?></span>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="mt-6 pt-4 border-t border-gray-100">
                                        <div class="flex space-x-2">
                                            <a href="templates.php?featured=1" class="flex-1 bg-primary text-white text-center py-2 px-4 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                                Featured
                                            </a>
                                            <a href="templates.php?sort=newest" class="flex-1 bg-gray-100 text-gray-600 text-center py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                                New
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="#services" class="text-secondary hover:text-primary transition-colors font-medium">Services</a>
                <a href="#" class="text-secondary hover:text-primary transition-colors font-medium">Pricing</a>
                <a href="#" class="text-secondary hover:text-primary transition-colors font-medium">Contact</a>
            </nav>
            
            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <a href="#" class="hidden md:block text-secondary hover:text-primary transition-colors font-medium">Sign In</a>
                <a href="#" class="bg-primary text-white px-6 py-2 rounded-button font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">Get Started</a>
                
                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden w-8 h-8 flex items-center justify-center">
                    <i class="ri-menu-line text-secondary text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</header>