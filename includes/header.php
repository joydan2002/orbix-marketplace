<?php
/**
 * Header Include File
 * Contains the HTML header section with navigation
 */

require_once __DIR__ . '/../config/database.php';

$config = DatabaseConfig::getAppConfig();
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
<header class="fixed top-0 w-full z-50 glass-effect">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">O</span>
                </div>
                <span class="font-pacifico text-2xl text-secondary">Orbix Market</span>
            </div>
            
            <!-- Navigation -->
            <nav class="hidden lg:flex items-center space-x-8">
                <div class="relative group" id="templates-dropdown">
                    <a href="/orbix/public/templates.php" class="text-secondary hover:text-primary transition-colors font-medium flex items-center">
                        Templates
                        <div class="w-4 h-4 flex items-center justify-center ml-1">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </a>
                    <div class="absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                        <div class="p-4">
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-secondary mb-2">Categories</h4>
                                <div class="space-y-2">
                                    <a href="/orbix/public/templates.php?category=business" class="block text-gray-600 hover:text-primary text-sm">Business Templates</a>
                                    <a href="/orbix/public/templates.php?category=ecommerce" class="block text-gray-600 hover:text-primary text-sm">E-commerce Solutions</a>
                                    <a href="/orbix/public/templates.php?category=portfolio" class="block text-gray-600 hover:text-primary text-sm">Portfolio & CV</a>
                                    <a href="/orbix/public/templates.php?category=landing" class="block text-gray-600 hover:text-primary text-sm">Landing Pages</a>
                                    <a href="/orbix/public/templates.php?category=business" class="block text-gray-600 hover:text-primary text-sm">Admin Dashboards</a>
                                </div>
                            </div>
                            <div class="mb-4">
                                <a href="/orbix/public/templates.php" class="block w-full bg-primary text-white text-center py-2 px-4 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                    View All Templates
                                </a>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-secondary mb-2">Popular Tags</h4>
                                <div class="flex flex-wrap gap-2">
                                    <a href="/orbix/public/templates.php?tech=html" class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs hover:bg-primary hover:text-white transition-colors">Responsive</a>
                                    <a href="/orbix/public/templates.php?tech=react" class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs hover:bg-primary hover:text-white transition-colors">React</a>
                                    <a href="/orbix/public/templates.php?tech=vue" class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs hover:bg-primary hover:text-white transition-colors">Vue.js</a>
                                    <a href="/orbix/public/templates.php?tech=wordpress" class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs hover:bg-primary hover:text-white transition-colors">WordPress</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="text-secondary hover:text-primary transition-colors font-medium">Services</a>
                <a href="#" class="text-secondary hover:text-primary transition-colors font-medium">Become a Seller</a>
                <a href="#" class="text-secondary hover:text-primary transition-colors font-medium">Pricing</a>
            </nav>
            
            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="hidden md:flex items-center bg-white/50 rounded-full px-4 py-2 backdrop-blur-sm">
                    <div class="w-5 h-5 flex items-center justify-center">
                        <i class="ri-search-line text-gray-500"></i>
                    </div>
                    <input type="text" placeholder="Search templates..." class="ml-2 bg-transparent border-none outline-none text-sm w-48">
                </div>
                
                <!-- Dark Mode Toggle -->
                <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors">
                    <i class="ri-moon-line text-secondary"></i>
                </button>
                
                <!-- Cart -->
                <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition-colors relative">
                    <i class="ri-shopping-cart-line text-secondary"></i>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary rounded-full text-white text-xs flex items-center justify-center">3</span>
                </button>
                
                <!-- User Profile -->
                <div class="flex items-center space-x-2">
                    <img src="https://readdy.ai/api/search-image?query=professional%20business%20person%20avatar%20headshot%20clean%20white%20background%20modern%20style&width=40&height=40&seq=user1&orientation=squarish" alt="User" class="w-10 h-10 rounded-full object-cover">
                </div>
            </div>
        </div>
    </div>
</header>