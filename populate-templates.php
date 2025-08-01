<?php
/**
 * Script to populate database with sample templates
 * Run this script once to add sample data
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = DatabaseConfig::getConnection();
    echo "Connected to database successfully!\n";

    // First, let's add more categories
    $categories = [
        ['name' => 'Admin Dashboard', 'slug' => 'admin', 'description' => 'Professional admin panel templates', 'is_active' => 1],
        ['name' => 'Business', 'slug' => 'business', 'description' => 'Corporate and business website templates', 'is_active' => 1],
        ['name' => 'E-commerce', 'slug' => 'ecommerce', 'description' => 'Online store and shopping templates', 'is_active' => 1],
        ['name' => 'Portfolio', 'slug' => 'portfolio', 'description' => 'Creative portfolio templates', 'is_active' => 1],
        ['name' => 'Landing Page', 'slug' => 'landing', 'description' => 'Marketing and landing page templates', 'is_active' => 1],
        ['name' => 'Blog', 'slug' => 'blog', 'description' => 'Blog and content website templates', 'is_active' => 1],
        ['name' => 'SaaS', 'slug' => 'saas', 'description' => 'Software as a Service templates', 'is_active' => 1],
        ['name' => 'Education', 'slug' => 'education', 'description' => 'Educational and learning platform templates', 'is_active' => 1]
    ];

    // Insert categories
    foreach ($categories as $category) {
        $stmt = $db->prepare("INSERT IGNORE INTO categories (name, slug, description, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$category['name'], $category['slug'], $category['description'], $category['is_active']]);
    }
    echo "Categories added successfully!\n";

    // Get category IDs
    $categoryMap = [];
    $stmt = $db->query("SELECT id, slug FROM categories");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categoryMap[$row['slug']] = $row['id'];
    }

    // Add sample users (sellers)
    $users = [
        ['first_name' => 'Alex', 'last_name' => 'Johnson', 'email' => 'alex@example.com', 'profile_image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face'],
        ['first_name' => 'Sarah', 'last_name' => 'Chen', 'email' => 'sarah@example.com', 'profile_image' => 'https://images.unsplash.com/photo-1494790108755-2616b612b5c5?w=100&h=100&fit=crop&crop=face'],
        ['first_name' => 'Mike', 'last_name' => 'Rodriguez', 'email' => 'mike@example.com', 'profile_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face'],
        ['first_name' => 'Emma', 'last_name' => 'Wilson', 'email' => 'emma@example.com', 'profile_image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face'],
        ['first_name' => 'David', 'last_name' => 'Kim', 'email' => 'david@example.com', 'profile_image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face'],
        ['first_name' => 'Lisa', 'last_name' => 'Anderson', 'email' => 'lisa@example.com', 'profile_image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&fit=crop&crop=face'],
    ];

    foreach ($users as $user) {
        $stmt = $db->prepare("INSERT IGNORE INTO users (first_name, last_name, email, profile_image, password_hash, user_type, created_at) VALUES (?, ?, ?, ?, ?, 'seller', NOW())");
        $stmt->execute([$user['first_name'], $user['last_name'], $user['email'], $user['profile_image'], password_hash('password123', PASSWORD_DEFAULT)]);
    }
    echo "Users added successfully!\n";

    // Get user IDs
    $userMap = [];
    $stmt = $db->query("SELECT id, email FROM users WHERE user_type = 'seller'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userMap[$row['email']] = $row['id'];
    }

    // Sample templates with real Unsplash images
    $templates = [
        // Admin Dashboard Templates
        [
            'title' => 'Modern Admin Pro',
            'description' => 'Clean and modern admin dashboard with comprehensive analytics, user management, and customizable widgets for enterprise applications.',
            'price' => 99,
            'technology' => 'React',
            'category' => 'admin',
            'seller' => 'alex@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop',
            'tags' => '["React", "Dashboard", "Admin", "Analytics", "Enterprise"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/admin-pro',
            'downloads_count' => 1250,
            'views_count' => 15680
        ],
        [
            'title' => 'Vue Admin Panel',
            'description' => 'Responsive Vue.js admin template with dark/light themes, advanced charts, and real-time data visualization components.',
            'price' => 79,
            'technology' => 'Vue.js',
            'category' => 'admin',
            'seller' => 'sarah@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
            'tags' => '["Vue.js", "Admin", "Charts", "Dark Mode", "Responsive"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/vue-admin',
            'downloads_count' => 890,
            'views_count' => 12340
        ],
        [
            'title' => 'Angular Dashboard Kit',
            'description' => 'Professional Angular admin dashboard with Material Design, advanced tables, and comprehensive form components.',
            'price' => 119,
            'technology' => 'Angular',
            'category' => 'admin',
            'seller' => 'david@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop',
            'tags' => '["Angular", "Material Design", "Tables", "Forms", "Professional"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/angular-dash',
            'downloads_count' => 2100,
            'views_count' => 28900
        ],

        // Business Templates
        [
            'title' => 'Corporate Elite',
            'description' => 'Premium corporate website template with elegant design, team showcase, and comprehensive business sections.',
            'price' => 69,
            'technology' => 'HTML',
            'category' => 'business',
            'seller' => 'emma@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=600&fit=crop',
            'tags' => '["HTML", "Corporate", "Business", "Elegant", "Professional"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/corporate-elite',
            'downloads_count' => 1890,
            'views_count' => 23450
        ],
        [
            'title' => 'Business Hub React',
            'description' => 'Modern React business template with interactive components, service showcases, and client testimonials.',
            'price' => 129,
            'technology' => 'React',
            'category' => 'business',
            'seller' => 'alex@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop',
            'tags' => '["React", "Business", "Interactive", "Services", "Testimonials"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/business-hub',
            'downloads_count' => 1560,
            'views_count' => 19870
        ],
        [
            'title' => 'Startup Landing Pro',
            'description' => 'Dynamic startup website template with animated sections, investor presentations, and team profiles.',
            'price' => 89,
            'technology' => 'Next.js',
            'category' => 'business',
            'seller' => 'mike@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=800&h=600&fit=crop',
            'tags' => '["Next.js", "Startup", "Animated", "Investor", "Dynamic"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/startup-pro',
            'downloads_count' => 720,
            'views_count' => 8900
        ],

        // E-commerce Templates
        [
            'title' => 'ShopMax Commerce',
            'description' => 'Complete e-commerce solution with shopping cart, payment integration, inventory management, and admin panel.',
            'price' => 199,
            'technology' => 'React',
            'category' => 'ecommerce',
            'seller' => 'sarah@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop',
            'tags' => '["React", "E-commerce", "Shopping Cart", "Payment", "Admin"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/shopmax',
            'downloads_count' => 3200,
            'views_count' => 45600
        ],
        [
            'title' => 'Fashion Store Vue',
            'description' => 'Elegant Vue.js fashion e-commerce template with product galleries, size guides, and wishlist functionality.',
            'price' => 149,
            'technology' => 'Vue.js',
            'category' => 'ecommerce',
            'seller' => 'lisa@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop',
            'tags' => '["Vue.js", "Fashion", "Gallery", "Size Guide", "Wishlist"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/fashion-vue',
            'downloads_count' => 980,
            'views_count' => 14500
        ],
        [
            'title' => 'Tech Store Angular',
            'description' => 'Modern Angular e-commerce template for electronics with product comparisons and detailed specifications.',
            'price' => 169,
            'technology' => 'Angular',
            'category' => 'ecommerce',
            'seller' => 'david@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=800&h=600&fit=crop',
            'tags' => '["Angular", "Electronics", "Comparison", "Specifications", "Modern"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/tech-store',
            'downloads_count' => 1450,
            'views_count' => 18900
        ],

        // Portfolio Templates
        [
            'title' => 'Creative Portfolio Pro',
            'description' => 'Stunning portfolio template for designers and artists with fullscreen galleries and smooth animations.',
            'price' => 59,
            'technology' => 'HTML',
            'category' => 'portfolio',
            'seller' => 'mike@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&h=600&fit=crop',
            'tags' => '["HTML", "Portfolio", "Gallery", "Animations", "Creative"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/creative-portfolio',
            'downloads_count' => 2100,
            'views_count' => 31200
        ],
        [
            'title' => 'Developer Showcase',
            'description' => 'Clean React portfolio template for developers with project showcases, skills visualization, and contact forms.',
            'price' => 79,
            'technology' => 'React',
            'category' => 'portfolio',
            'seller' => 'alex@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1517180102446-f3ece451e9d8?w=800&h=600&fit=crop',
            'tags' => '["React", "Developer", "Projects", "Skills", "Contact"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/dev-showcase',
            'downloads_count' => 1680,
            'views_count' => 22400
        ],

        // Landing Page Templates
        [
            'title' => 'SaaS Landing Ultimate',
            'description' => 'High-converting SaaS landing page with pricing tables, feature comparisons, and customer testimonials.',
            'price' => 89,
            'technology' => 'Next.js',
            'category' => 'landing',
            'seller' => 'emma@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
            'tags' => '["Next.js", "SaaS", "Pricing", "Testimonials", "Conversion"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/saas-landing',
            'downloads_count' => 2890,
            'views_count' => 41200
        ],
        [
            'title' => 'App Landing Vue',
            'description' => 'Mobile app landing page built with Vue.js featuring app screenshots, download buttons, and feature highlights.',
            'price' => 69,
            'technology' => 'Vue.js',
            'category' => 'landing',
            'seller' => 'sarah@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&h=600&fit=crop',
            'tags' => '["Vue.js", "Mobile App", "Screenshots", "Download", "Features"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/app-landing',
            'downloads_count' => 1340,
            'views_count' => 18600
        ],

        // Blog Templates
        [
            'title' => 'Modern Blog React',
            'description' => 'Clean and modern blog template with category filtering, search functionality, and social media integration.',
            'price' => 49,
            'technology' => 'React',
            'category' => 'blog',
            'seller' => 'lisa@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1486312338219-ce68e2c6b4d3?w=800&h=600&fit=crop',
            'tags' => '["React", "Blog", "Categories", "Search", "Social Media"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/modern-blog',
            'downloads_count' => 890,
            'views_count' => 12300
        ],
        [
            'title' => 'Magazine Style',
            'description' => 'Professional magazine-style blog template with multiple layouts, author profiles, and newsletter signup.',
            'price' => 79,
            'technology' => 'WordPress',
            'category' => 'blog',
            'seller' => 'david@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&h=600&fit=crop',
            'tags' => '["WordPress", "Magazine", "Layouts", "Authors", "Newsletter"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/magazine-style',
            'downloads_count' => 1560,
            'views_count' => 21800
        ],

        // SaaS Templates
        [
            'title' => 'SaaS Dashboard Pro',
            'description' => 'Comprehensive SaaS application template with user management, subscription billing, and analytics dashboard.',
            'price' => 299,
            'technology' => 'React',
            'category' => 'saas',
            'seller' => 'alex@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop',
            'tags' => '["React", "SaaS", "Subscription", "Analytics", "User Management"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/saas-dashboard',
            'downloads_count' => 890,
            'views_count' => 15600
        ],

        // Education Templates
        [
            'title' => 'EduPlatform Vue',
            'description' => 'Complete online learning platform with course management, student progress tracking, and video integration.',
            'price' => 249,
            'technology' => 'Vue.js',
            'category' => 'education',
            'seller' => 'emma@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=600&fit=crop',
            'tags' => '["Vue.js", "Education", "Courses", "Progress", "Video"]',
            'is_featured' => false,
            'demo_url' => 'https://demo.example.com/edu-platform',
            'downloads_count' => 670,
            'views_count' => 9800
        ],
        [
            'title' => 'University Template',
            'description' => 'Professional university website template with course catalogs, faculty profiles, and admission forms.',
            'price' => 159,
            'technology' => 'HTML',
            'category' => 'education',
            'seller' => 'mike@example.com',
            'preview_image' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=800&h=600&fit=crop',
            'tags' => '["HTML", "University", "Courses", "Faculty", "Admissions"]',
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com/university',
            'downloads_count' => 1230,
            'views_count' => 17600
        ]
    ];

    // Insert templates
    foreach ($templates as $template) {
        $sellerId = $userMap[$template['seller']];
        $categoryId = $categoryMap[$template['category']];
        
        // Generate slug from title
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $template['title']));
        $slug = trim($slug, '-');
        
        $stmt = $db->prepare("
            INSERT INTO templates (
                title, slug, description, price, technology, category_id, seller_id, 
                preview_image, tags, is_featured, demo_url, downloads_count, 
                views_count, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', NOW())
        ");
        
        $stmt->execute([
            $template['title'],
            $slug,
            $template['description'], 
            $template['price'],
            $template['technology'],
            $categoryId,
            $sellerId,
            $template['preview_image'],
            $template['tags'],
            $template['is_featured'] ? 1 : 0,
            $template['demo_url'],
            $template['downloads_count'],
            $template['views_count']
        ]);
    }
    
    echo "Templates added successfully!\n";
    echo "Total templates added: " . count($templates) . "\n";

    // Add some sample reviews
    $templateIds = $db->query("SELECT id FROM templates")->fetchAll(PDO::FETCH_COLUMN);
    $userIds = array_values($userMap);
    
    $sampleReviews = [
        ["rating" => 5, "comment" => "Excellent template! Very professional and easy to customize."],
        ["rating" => 4, "comment" => "Great design and good documentation. Would recommend."],
        ["rating" => 5, "comment" => "Perfect for my project. Clean code and responsive design."],
        ["rating" => 4, "comment" => "Good quality template with modern design principles."],
        ["rating" => 5, "comment" => "Outstanding work! Saved me hours of development time."],
        ["rating" => 3, "comment" => "Decent template but could use better mobile optimization."],
        ["rating" => 5, "comment" => "Amazing template with great customer support."],
        ["rating" => 4, "comment" => "Well structured code and beautiful design."]
    ];

    // Add reviews for each template
    foreach ($templateIds as $templateId) {
        $numReviews = rand(3, 8); // Random number of reviews per template
        $reviewsToAdd = array_rand($sampleReviews, min($numReviews, count($sampleReviews)));
        
        if (!is_array($reviewsToAdd)) {
            $reviewsToAdd = [$reviewsToAdd];
        }
        
        foreach ($reviewsToAdd as $reviewIndex) {
            $review = $sampleReviews[$reviewIndex];
            $userId = $userIds[array_rand($userIds)];
            
            $stmt = $db->prepare("
                INSERT INTO reviews (template_id, user_id, rating, review_text, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$templateId, $userId, $review['rating'], $review['comment']]);
        }
    }
    
    echo "Sample reviews added successfully!\n";
    echo "Database population completed!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>