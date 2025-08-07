<?php
/**
 * Add Sample Data for Seller ID 44
 * This script adds comprehensive sample services and templates for testing
 */

require_once '../../config/database.php';

try {
    $pdo = DatabaseConfig::getConnection();
    $pdo->beginTransaction();
    
    echo "🚀 Starting to add sample data for Seller ID 44...\n";
    
    // First, verify seller exists
    $stmt = $pdo->prepare("SELECT id, username, user_type FROM users WHERE id = 44");
    $stmt->execute();
    $seller = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$seller) {
        throw new Exception("Seller with ID 44 not found!");
    }
    
    if ($seller['user_type'] !== 'seller') {
        echo "⚠️  User ID 44 is not a seller, updating user type...\n";
        $pdo->prepare("UPDATE users SET user_type = 'seller' WHERE id = 44")->execute();
    }
    
    echo "✅ Seller found: {$seller['username']}\n";
    
    // ========================================
    // ADD SERVICES DATA
    // ========================================
    echo "\n📋 Adding Services...\n";
    
    $services = [
        [
            'title' => 'Custom Website Design',
            'slug' => 'custom-website-design',
            'description' => 'Professional custom website design tailored to your brand. We create unique, responsive designs that convert visitors into customers.',
            'price' => 299.00,
            'category_id' => 7, // Design & Development
            'delivery_time' => 7,
            'demo_url' => 'https://demo.example.com/custom-design',
            'tags' => '["custom design", "responsive", "modern", "professional"]',
            'features' => 'Custom design mockups,Responsive layout,SEO optimized,Cross-browser compatible',
            'status' => 'approved'
        ],
        [
            'title' => 'E-commerce Store Setup',
            'slug' => 'ecommerce-store-setup',
            'description' => 'Complete e-commerce store setup with payment integration, inventory management, and mobile-optimized checkout process.',
            'price' => 599.00,
            'category_id' => 7, // Design & Development
            'delivery_time' => 14,
            'demo_url' => 'https://demo.example.com/ecommerce',
            'tags' => '["ecommerce", "online store", "payment integration", "shopping cart"]',
            'features' => 'Payment gateway integration,Product catalog setup,Order management system,Mobile responsive design',
            'status' => 'approved'
        ],
        [
            'title' => 'SEO Optimization Package',
            'slug' => 'seo-optimization-package',
            'description' => 'Comprehensive SEO optimization to improve your website ranking on Google and other search engines.',
            'price' => 199.00,
            'category_id' => 8, // Digital Marketing
            'delivery_time' => 10,
            'demo_url' => null,
            'tags' => '["SEO", "optimization", "google ranking", "search engine"]',
            'features' => 'Keyword research and analysis,On-page SEO optimization,Meta tags optimization,Technical SEO audit',
            'status' => 'approved'
        ],
        [
            'title' => 'Social Media Marketing',
            'slug' => 'social-media-marketing',
            'description' => 'Strategic social media marketing campaigns to boost your brand presence and engagement across all platforms.',
            'price' => 249.00,
            'category_id' => 8, // Digital Marketing
            'delivery_time' => 30,
            'demo_url' => null,
            'tags' => '["social media", "marketing", "brand awareness", "engagement"]',
            'features' => 'Content strategy development,Social media post creation,Audience engagement,Performance analytics',
            'status' => 'approved'
        ],
        [
            'title' => 'Website Content Writing',
            'slug' => 'website-content-writing',
            'description' => 'Professional website content writing services including homepage, about page, service descriptions, and blog posts.',
            'price' => 149.00,
            'category_id' => 9, // Content & Writing
            'delivery_time' => 5,
            'demo_url' => null,
            'tags' => '["content writing", "copywriting", "website content", "blog posts"]',
            'features' => 'SEO-optimized content,Engaging copywriting,Brand voice consistency,Unlimited revisions',
            'status' => 'approved'
        ],
        [
            'title' => 'Business Consulting',
            'slug' => 'business-consulting',
            'description' => 'Strategic business consulting to help grow your online presence and optimize your digital marketing strategy.',
            'price' => 399.00,
            'category_id' => 10, // Business Services
            'delivery_time' => 7,
            'demo_url' => null,
            'tags' => '["business consulting", "strategy", "growth", "digital marketing"]',
            'features' => 'Business strategy analysis,Market research,Competitor analysis,Growth recommendations',
            'status' => 'approved'
        ],
        [
            'title' => 'Technical Support & Maintenance',
            'slug' => 'technical-support-maintenance',
            'description' => 'Ongoing technical support and website maintenance to keep your site running smoothly and securely.',
            'price' => 99.00,
            'category_id' => 11, // Technical Support
            'delivery_time' => 1,
            'demo_url' => null,
            'tags' => '["technical support", "maintenance", "website updates", "bug fixes"]',
            'features' => '24/7 technical support,Regular backups,Security updates,Performance monitoring',
            'status' => 'approved'
        ],
        [
            'title' => 'Web Hosting & Security',
            'slug' => 'web-hosting-security',
            'description' => 'Premium web hosting with SSL certificates, daily backups, and advanced security features for your website.',
            'price' => 79.00,
            'category_id' => 12, // Hosting & Security
            'delivery_time' => 1,
            'demo_url' => null,
            'tags' => '["web hosting", "security", "SSL", "backups"]',
            'features' => 'Fast SSD hosting,SSL certificate included,Daily automated backups,DDoS protection',
            'status' => 'approved'
        ]
    ];
    
    $serviceStmt = $pdo->prepare("
        INSERT INTO services (
            seller_id, title, slug, description, price, category_id, delivery_time, 
            demo_url, tags, features, status, is_active, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
    ");
    
    foreach ($services as $service) {
        $serviceStmt->execute([
            44, // seller_id
            $service['title'],
            $service['slug'],
            $service['description'],
            $service['price'],
            $service['category_id'],
            $service['delivery_time'],
            $service['demo_url'],
            $service['tags'],
            $service['features'],
            $service['status']
        ]);
        echo "  ✅ Added service: {$service['title']}\n";
    }
    
    // ========================================
    // ADD TEMPLATES DATA
    // ========================================
    echo "\n🎨 Adding Templates...\n";
    
    $templates = [
        [
            'title' => 'Corporate Business Template Pro',
            'slug' => 'corporate-business-template-pro',
            'description' => 'Premium corporate business website template with modern design, perfect for professional services and enterprises.',
            'price' => 89.00,
            'category_id' => 1, // Business
            'technology' => 'HTML/CSS',
            'demo_url' => 'https://demo.example.com/corporate-business',
            'tags' => '["business", "corporate", "professional", "modern", "responsive"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Complete E-commerce Solution',
            'slug' => 'complete-ecommerce-solution',
            'description' => 'Full-featured e-commerce template with advanced product catalog, shopping cart, and payment integration.',
            'price' => 129.00,
            'category_id' => 2, // Mobile Apps (using as E-commerce category)
            'technology' => 'React',
            'demo_url' => 'https://demo.example.com/ecommerce-solution',
            'tags' => '["ecommerce", "shopping", "online store", "react", "responsive"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Designer Portfolio Showcase',
            'slug' => 'designer-portfolio-showcase',
            'description' => 'Elegant portfolio template for creative professionals to showcase their work with style and sophistication.',
            'price' => 69.00,
            'category_id' => 3, // Portfolio
            'technology' => 'Vue.js',
            'demo_url' => 'https://demo.example.com/designer-portfolio',
            'tags' => '["portfolio", "creative", "design", "showcase", "vue"]',
            'status' => 'approved'
        ],
        [
            'title' => 'SaaS Product Landing Pro',
            'slug' => 'saas-product-landing-pro',
            'description' => 'High-converting landing page template optimized for SaaS products with advanced conversion features.',
            'price' => 49.00,
            'category_id' => 4, // Landing Page
            'technology' => 'HTML/CSS',
            'demo_url' => 'https://demo.example.com/saas-product-landing',
            'tags' => '["saas", "landing page", "conversion", "software", "startup"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Advanced Admin Dashboard',
            'slug' => 'advanced-admin-dashboard',
            'description' => 'Feature-rich admin dashboard template with comprehensive data visualization and management tools.',
            'price' => 99.00,
            'category_id' => 5, // Admin Dashboard
            'technology' => 'Angular',
            'demo_url' => 'https://demo.example.com/advanced-admin',
            'tags' => '["admin", "dashboard", "analytics", "angular", "management"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Fine Dining Restaurant Template',
            'slug' => 'fine-dining-restaurant-template',
            'description' => 'Luxurious restaurant template with sophisticated design, menu showcase, and reservation system.',
            'price' => 79.00,
            'category_id' => 1, // Business
            'technology' => 'WordPress',
            'demo_url' => 'https://demo.example.com/fine-dining',
            'tags' => '["restaurant", "fine dining", "luxury", "menu", "wordpress"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Digital Magazine Platform',
            'slug' => 'digital-magazine-platform',
            'description' => 'Modern magazine and blog platform with multiple layouts, content management, and social features.',
            'price' => 59.00,
            'category_id' => 12, // Blog (using existing category)
            'technology' => 'WordPress',
            'demo_url' => 'https://demo.example.com/digital-magazine',
            'tags' => '["magazine", "blog", "news", "wordpress", "content"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Premium Fitness Club Template',
            'slug' => 'premium-fitness-club-template',
            'description' => 'Professional fitness club template with class schedules, trainer profiles, and membership management.',
            'price' => 89.00,
            'category_id' => 1, // Business
            'technology' => 'HTML/CSS',
            'demo_url' => 'https://demo.example.com/premium-fitness',
            'tags' => '["fitness", "gym", "health", "sports", "membership"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Luxury Real Estate Platform',
            'slug' => 'luxury-real-estate-platform',
            'description' => 'High-end real estate template with advanced property search, virtual tours, and agent management.',
            'price' => 109.00,
            'category_id' => 1, // Business
            'technology' => 'React',
            'demo_url' => 'https://demo.example.com/luxury-real-estate',
            'tags' => '["real estate", "luxury", "property", "react", "professional"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Online Learning Academy',
            'slug' => 'online-learning-academy',
            'description' => 'Comprehensive e-learning platform with course creation, student management, and progress tracking.',
            'price' => 149.00,
            'category_id' => 5, // Admin Dashboard
            'technology' => 'Vue.js',
            'demo_url' => 'https://demo.example.com/learning-academy',
            'tags' => '["education", "e-learning", "academy", "vue", "courses"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Medical Clinic Website',
            'slug' => 'medical-clinic-website',
            'description' => 'Professional medical clinic template with appointment booking, doctor profiles, and patient portal.',
            'price' => 95.00,
            'category_id' => 1, // Business
            'technology' => 'HTML/CSS',
            'demo_url' => 'https://demo.example.com/medical-clinic',
            'tags' => '["medical", "clinic", "healthcare", "appointment", "professional"]',
            'status' => 'approved'
        ],
        [
            'title' => 'Travel Agency Portal',
            'slug' => 'travel-agency-portal',
            'description' => 'Complete travel agency website with booking system, tour packages, and destination showcase.',
            'price' => 119.00,
            'category_id' => 1, // Business
            'technology' => 'React',
            'demo_url' => 'https://demo.example.com/travel-agency',
            'tags' => '["travel", "agency", "booking", "tourism", "react"]',
            'status' => 'approved'
        ]
    ];
    
    $templateStmt = $pdo->prepare("
        INSERT INTO templates (
            seller_id, title, slug, description, price, category_id, technology,
            demo_url, tags, status, views_count, downloads_count, rating, 
            reviews_count, is_featured, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW())
    ");
    
    foreach ($templates as $template) {
        // Generate random stats for more realistic data
        $views = rand(50, 1000);
        $downloads = rand(5, 50);
        $rating = rand(40, 50) / 10; // 4.0 to 5.0
        $reviews = rand(2, 15);
        
        $templateStmt->execute([
            44, // seller_id
            $template['title'],
            $template['slug'],
            $template['description'],
            $template['price'],
            $template['category_id'],
            $template['technology'],
            $template['demo_url'],
            $template['tags'],
            $template['status'],
            $views,
            $downloads,
            $rating,
            $reviews
        ]);
        echo "  ✅ Added template: {$template['title']}\n";
    }
    
    // ========================================
    // UPDATE STATISTICS
    // ========================================
    echo "\n📊 Updating statistics...\n";
    
    // Count total products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE seller_id = 44");
    $stmt->execute();
    $serviceCount = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM templates WHERE seller_id = 44");
    $stmt->execute();
    $templateCount = $stmt->fetchColumn();
    
    echo "  📋 Total Services: $serviceCount\n";
    echo "  🎨 Total Templates: $templateCount\n";
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n🎉 SUCCESS! Sample data has been added successfully!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 Added $serviceCount services\n";
    echo "🎨 Added $templateCount templates\n";
    echo "👤 All products assigned to Seller ID: 44\n";
    echo "⚠️  Note: Preview images are left empty for manual update\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} catch (Exception $e) {
    $pdo->rollback();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "💥 Transaction rolled back.\n";
}
?>
