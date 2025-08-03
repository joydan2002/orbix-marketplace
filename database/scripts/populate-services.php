<?php
/**
 * Populate Services with Rich Data
 * This script adds comprehensive service data with preview images
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $db = DatabaseConfig::getConnection();
    
    // Clear existing services
    $db->exec("DELETE FROM services");
    $db->exec("ALTER TABLE services AUTO_INCREMENT = 1");
    
    // Comprehensive services data
    $services = [
        // Web Design Services
        [
            'title' => 'Professional Website Design',
            'slug' => 'professional-website-design',
            'description' => 'Custom responsive website design with modern UI/UX principles, mobile optimization, and SEO-friendly structure. Perfect for businesses looking to establish a strong online presence.',
            'price' => 499.00,
            'starting_price' => 499.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=professional%20website%20design%20service%20modern%20clean%20interface%20mockup%20desktop%20mobile%20responsive&width=400&height=300&seq=srv1&orientation=landscape',
            'seller_name' => 'David Wilson',
            'profile_image' => 'https://readdy.ai/api/search-image?query=web%20designer%20professional%20avatar%20headshot%20clean%20background%20modern%20style&width=50&height=50&seq=seller1&orientation=squarish',
            'avg_rating' => 4.9,
            'review_count' => 87,
            'orders_count' => 234,
            'delivery_time' => 7,
            'tags' => '["Design", "Responsive", "Modern", "UI/UX"]',
            'is_featured' => 1,
            'category_slug' => 'design',
            'icon' => 'ri-palette-line',
            'demo_url' => 'https://demo.orbix.com/website-design-1',
            'technology' => 'HTML5, CSS3, JavaScript'
        ],
        [
            'title' => 'E-commerce Website Development',
            'slug' => 'ecommerce-website-development',
            'description' => 'Complete e-commerce website with payment integration, shopping cart, admin panel, inventory management, and order tracking system. Built with modern technologies.',
            'price' => 899.00,
            'starting_price' => 899.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=ecommerce%20development%20service%20online%20store%20shopping%20cart%20product%20catalog%20modern%20interface&width=400&height=300&seq=srv2&orientation=landscape',
            'seller_name' => 'Emma Rodriguez',
            'profile_image' => 'https://readdy.ai/api/search-image?query=female%20developer%20professional%20avatar%20headshot%20tech%20background%20modern%20style&width=50&height=50&seq=seller2&orientation=squarish',
            'avg_rating' => 4.8,
            'review_count' => 156,
            'orders_count' => 89,
            'delivery_time' => 14,
            'tags' => '["E-commerce", "Development", "Payment", "Shopping Cart"]',
            'is_featured' => 0,
            'category_slug' => 'development',
            'icon' => 'ri-shopping-cart-line',
            'demo_url' => 'https://demo.orbix.com/ecommerce-store',
            'technology' => 'PHP, MySQL, JavaScript'
        ],
        [
            'title' => 'Logo & Brand Identity Design',
            'slug' => 'logo-brand-identity-design',
            'description' => 'Professional logo design and complete brand identity package including business cards, letterheads, brand guidelines, and social media assets.',
            'price' => 399.00,
            'starting_price' => 399.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=logo%20design%20service%20brand%20identity%20creative%20modern%20business%20branding%20package&width=400&height=300&seq=srv3&orientation=landscape',
            'seller_name' => 'Sofia Martinez',
            'profile_image' => 'https://readdy.ai/api/search-image?query=graphic%20designer%20creative%20professional%20avatar%20headshot%20artistic%20background&width=50&height=50&seq=seller3&orientation=squarish',
            'avg_rating' => 4.9,
            'review_count' => 142,
            'orders_count' => 267,
            'delivery_time' => 5,
            'tags' => '["Logo", "Branding", "Creative", "Identity"]',
            'is_featured' => 1,
            'category_slug' => 'design',
            'icon' => 'ri-brush-line',
            'demo_url' => 'https://demo.orbix.com/logo-portfolio',
            'technology' => 'Adobe Illustrator, Photoshop'
        ],
        
        // Development Services
        [
            'title' => 'Custom Web Application Development',
            'slug' => 'custom-web-application-development',
            'description' => 'Full-stack web application development using modern frameworks like React, Vue.js, Node.js, and Python. Database design and API integration included.',
            'price' => 1299.00,
            'starting_price' => 1299.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=web%20application%20development%20service%20custom%20software%20coding%20programming%20modern%20interface&width=400&height=300&seq=srv4&orientation=landscape',
            'seller_name' => 'Alex Chen',
            'profile_image' => 'https://readdy.ai/api/search-image?query=software%20developer%20professional%20avatar%20headshot%20tech%20background%20modern%20style&width=50&height=50&seq=seller4&orientation=squarish',
            'avg_rating' => 5.0,
            'review_count' => 73,
            'orders_count' => 128,
            'delivery_time' => 21,
            'tags' => '["Development", "Custom", "Full-stack", "Modern"]',
            'is_featured' => 1,
            'category_slug' => 'development',
            'icon' => 'ri-code-line',
            'demo_url' => 'https://demo.orbix.com/web-app-demo',
            'technology' => 'React, Node.js, MongoDB'
        ],
        [
            'title' => 'WordPress Website Development',
            'slug' => 'wordpress-website-development',
            'description' => 'Professional WordPress website development with custom themes, plugins, and optimizations. Content management system setup and SEO optimization included.',
            'price' => 699.00,
            'starting_price' => 699.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=wordpress%20development%20service%20cms%20website%20custom%20theme%20professional%20interface&width=400&height=300&seq=srv5&orientation=landscape',
            'seller_name' => 'Michael Taylor',
            'profile_image' => 'https://readdy.ai/api/search-image?query=wordpress%20developer%20professional%20avatar%20headshot%20clean%20background&width=50&height=50&seq=seller5&orientation=squarish',
            'avg_rating' => 4.7,
            'review_count' => 98,
            'orders_count' => 187,
            'delivery_time' => 10,
            'tags' => '["WordPress", "CMS", "Development", "Custom Theme"]',
            'is_featured' => 0,
            'category_slug' => 'development',
            'icon' => 'ri-wordpress-line',
            'demo_url' => 'https://demo.orbix.com/wordpress-demo',
            'technology' => 'WordPress, PHP, MySQL'
        ],
        
        // SEO & Marketing Services
        [
            'title' => 'SEO Optimization Service',
            'slug' => 'seo-optimization-service',
            'description' => 'Complete SEO audit and optimization to improve your website ranking and organic traffic. Includes keyword research, on-page optimization, and performance analysis.',
            'price' => 299.00,
            'starting_price' => 299.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=seo%20optimization%20service%20search%20engine%20ranking%20analytics%20chart%20growth&width=400&height=300&seq=srv6&orientation=landscape',
            'seller_name' => 'Rachel Green',
            'profile_image' => 'https://readdy.ai/api/search-image?query=seo%20specialist%20professional%20avatar%20headshot%20marketing%20background&width=50&height=50&seq=seller6&orientation=squarish',
            'avg_rating' => 5.0,
            'review_count' => 203,
            'orders_count' => 456,
            'delivery_time' => 5,
            'tags' => '["SEO", "Marketing", "Analytics", "Optimization"]',
            'is_featured' => 0,
            'category_slug' => 'marketing',
            'icon' => 'ri-search-line',
            'demo_url' => 'https://demo.orbix.com/seo-results',
            'technology' => 'Google Analytics, SEMrush'
        ],
        [
            'title' => 'Digital Marketing Strategy',
            'slug' => 'digital-marketing-strategy',
            'description' => 'Comprehensive digital marketing strategy including social media marketing, content planning, PPC campaigns, and brand positioning for maximum ROI.',
            'price' => 549.00,
            'starting_price' => 549.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=digital%20marketing%20strategy%20service%20social%20media%20campaign%20analytics%20planning&width=400&height=300&seq=srv7&orientation=landscape',
            'seller_name' => 'James Parker',
            'profile_image' => 'https://readdy.ai/api/search-image?query=marketing%20strategist%20professional%20avatar%20headshot%20business%20background&width=50&height=50&seq=seller7&orientation=squarish',
            'avg_rating' => 4.8,
            'review_count' => 167,
            'orders_count' => 89,
            'delivery_time' => 7,
            'tags' => '["Marketing", "Strategy", "Social Media", "PPC"]',
            'is_featured' => 1,
            'category_slug' => 'marketing',
            'icon' => 'ri-megaphone-line',
            'demo_url' => 'https://demo.orbix.com/marketing-case-study',
            'technology' => 'Facebook Ads, Google Ads'
        ],
        
        // Content & Writing Services
        [
            'title' => 'Content Writing & Copywriting',
            'slug' => 'content-writing-copywriting',
            'description' => 'Professional website content, blog posts, and marketing copy that converts visitors to customers. SEO-optimized content that drives engagement and sales.',
            'price' => 149.00,
            'starting_price' => 149.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=content%20writing%20service%20copywriting%20blog%20articles%20professional%20text%20creation&width=400&height=300&seq=srv8&orientation=landscape',
            'seller_name' => 'Lisa Johnson',
            'profile_image' => 'https://readdy.ai/api/search-image?query=content%20writer%20professional%20avatar%20headshot%20writing%20background&width=50&height=50&seq=seller8&orientation=squarish',
            'avg_rating' => 4.8,
            'review_count' => 95,
            'orders_count' => 178,
            'delivery_time' => 3,
            'tags' => '["Content", "Writing", "Marketing", "SEO"]',
            'is_featured' => 0,
            'category_slug' => 'content',
            'icon' => 'ri-edit-line',
            'demo_url' => 'https://demo.orbix.com/content-samples',
            'technology' => 'WordPress, Grammarly'
        ],
        [
            'title' => 'Technical Documentation Writing',
            'slug' => 'technical-documentation-writing',
            'description' => 'Professional technical documentation, API documentation, user manuals, and help guides. Clear, concise, and user-friendly documentation for any technical product.',
            'price' => 199.00,
            'starting_price' => 199.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=technical%20documentation%20writing%20service%20manual%20guide%20professional%20layout&width=400&height=300&seq=srv9&orientation=landscape',
            'seller_name' => 'Kevin Anderson',
            'profile_image' => 'https://readdy.ai/api/search-image?query=technical%20writer%20professional%20avatar%20headshot%20documentation%20background&width=50&height=50&seq=seller9&orientation=squarish',
            'avg_rating' => 4.9,
            'review_count' => 124,
            'orders_count' => 67,
            'delivery_time' => 5,
            'tags' => '["Technical", "Documentation", "Writing", "Manual"]',
            'is_featured' => 0,
            'category_slug' => 'content',
            'icon' => 'ri-file-text-line',
            'demo_url' => 'https://demo.orbix.com/documentation',
            'technology' => 'Markdown, GitBook'
        ],
        
        // Maintenance & Support Services
        [
            'title' => 'Website Maintenance & Support',
            'slug' => 'website-maintenance-support',
            'description' => 'Monthly website maintenance, updates, security monitoring, backup management, and technical support service. Keep your website running smoothly 24/7.',
            'price' => 199.00,
            'starting_price' => 199.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=website%20maintenance%20service%20support%20technical%20monitoring%20dashboard%20security&width=400&height=300&seq=srv10&orientation=landscape',
            'seller_name' => 'Sarah Wilson',
            'profile_image' => 'https://readdy.ai/api/search-image?query=technical%20support%20specialist%20professional%20avatar%20headshot%20service&width=50&height=50&seq=seller10&orientation=squarish',
            'avg_rating' => 4.7,
            'review_count' => 78,
            'orders_count' => 123,
            'delivery_time' => 1,
            'tags' => '["Maintenance", "Support", "Security", "Monitoring"]',
            'is_featured' => 0,
            'category_slug' => 'maintenance',
            'icon' => 'ri-tools-line',
            'demo_url' => 'https://demo.orbix.com/maintenance-dashboard',
            'technology' => 'cPanel, Cloudflare'
        ],
        [
            'title' => 'Website Speed Optimization',
            'slug' => 'website-speed-optimization',
            'description' => 'Comprehensive website performance optimization including image optimization, code minification, caching setup, and CDN configuration for lightning-fast loading.',
            'price' => 249.00,
            'starting_price' => 249.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=website%20speed%20optimization%20service%20performance%20fast%20loading%20analytics%20improvement&width=400&height=300&seq=srv11&orientation=landscape',
            'seller_name' => 'Tom Richards',
            'profile_image' => 'https://readdy.ai/api/search-image?query=performance%20optimization%20specialist%20professional%20avatar%20headshot%20tech&width=50&height=50&seq=seller11&orientation=squarish',
            'avg_rating' => 4.9,
            'review_count' => 156,
            'orders_count' => 89,
            'delivery_time' => 3,
            'tags' => '["Performance", "Speed", "Optimization", "Analytics"]',
            'is_featured' => 1,
            'category_slug' => 'maintenance',
            'icon' => 'ri-speed-line',
            'demo_url' => 'https://demo.orbix.com/speed-test',
            'technology' => 'GTmetrix, PageSpeed'
        ],
        
        // UI/UX Design Services
        [
            'title' => 'UI/UX Design & Prototyping',
            'slug' => 'ui-ux-design-prototyping',
            'description' => 'Complete UI/UX design service including user research, wireframing, prototyping, and interactive design. Create intuitive and engaging user experiences.',
            'price' => 799.00,
            'starting_price' => 799.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=ui%20ux%20design%20service%20prototyping%20wireframe%20user%20interface%20modern%20design&width=400&height=300&seq=srv12&orientation=landscape',
            'seller_name' => 'Maria Garcia',
            'profile_image' => 'https://readdy.ai/api/search-image?query=ui%20ux%20designer%20professional%20avatar%20headshot%20creative%20background&width=50&height=50&seq=seller12&orientation=squarish',
            'avg_rating' => 5.0,
            'review_count' => 89,
            'orders_count' => 145,
            'delivery_time' => 10,
            'tags' => '["UI/UX", "Design", "Prototyping", "User Experience"]',
            'is_featured' => 1,
            'category_slug' => 'design',
            'icon' => 'ri-layout-line',
            'demo_url' => 'https://demo.orbix.com/ui-ux-portfolio',
            'technology' => 'Figma, Adobe XD'
        ],
        [
            'title' => 'Mobile App UI Design',
            'slug' => 'mobile-app-ui-design',
            'description' => 'Professional mobile app UI design for iOS and Android platforms. Modern, intuitive interface design that enhances user engagement and retention.',
            'price' => 649.00,
            'starting_price' => 649.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=mobile%20app%20ui%20design%20service%20interface%20modern%20smartphone%20application&width=400&height=300&seq=srv13&orientation=landscape',
            'seller_name' => 'Daniel Kim',
            'profile_image' => 'https://readdy.ai/api/search-image?query=mobile%20app%20designer%20professional%20avatar%20headshot%20tech%20background&width=50&height=50&seq=seller13&orientation=squarish',
            'avg_rating' => 4.8,
            'review_count' => 134,
            'orders_count' => 76,
            'delivery_time' => 8,
            'tags' => '["Mobile", "App", "UI Design", "iOS", "Android"]',
            'is_featured' => 0,
            'category_slug' => 'design',
            'icon' => 'ri-smartphone-line',
            'demo_url' => 'https://demo.orbix.com/mobile-app-ui',
            'technology' => 'Sketch, Figma'
        ],
        
        // Database & Backend Services
        [
            'title' => 'Database Design & Development',
            'slug' => 'database-design-development',
            'description' => 'Professional database design, optimization, and development services. MySQL, PostgreSQL, MongoDB expertise with performance tuning and security implementation.',
            'price' => 449.00,
            'starting_price' => 449.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=database%20design%20development%20service%20mysql%20postgresql%20data%20structure&width=400&height=300&seq=srv14&orientation=landscape',
            'seller_name' => 'Robert Chen',
            'profile_image' => 'https://readdy.ai/api/search-image?query=database%20developer%20professional%20avatar%20headshot%20data%20background&width=50&height=50&seq=seller14&orientation=squarish',
            'avg_rating' => 4.9,
            'review_count' => 67,
            'orders_count' => 98,
            'delivery_time' => 7,
            'tags' => '["Database", "Backend", "MySQL", "Development"]',
            'is_featured' => 0,
            'category_slug' => 'development',
            'icon' => 'ri-database-line',
            'demo_url' => 'https://demo.orbix.com/database-schema',
            'technology' => 'MySQL, PostgreSQL'
        ],
        [
            'title' => 'API Development & Integration',
            'slug' => 'api-development-integration',
            'description' => 'RESTful API development and third-party service integration. Secure, scalable API solutions with comprehensive documentation and testing.',
            'price' => 599.00,
            'starting_price' => 599.00,
            'preview_image' => 'https://readdy.ai/api/search-image?query=api%20development%20service%20integration%20restful%20backend%20programming%20code&width=400&height=300&seq=srv15&orientation=landscape',
            'seller_name' => 'Anna Thompson',
            'profile_image' => 'https://readdy.ai/api/search-image?query=api%20developer%20professional%20avatar%20headshot%20programming%20background&width=50&height=50&seq=seller15&orientation=squarish',
            'avg_rating' => 4.8,
            'review_count' => 92,
            'orders_count' => 134,
            'delivery_time' => 12,
            'tags' => '["API", "Integration", "Backend", "Development"]',
            'is_featured' => 0,
            'category_slug' => 'development',
            'icon' => 'ri-code-s-slash-line',
            'demo_url' => 'https://demo.orbix.com/api-docs',
            'technology' => 'Node.js, Express.js'
        ]
    ];
    
    // Insert services
    $stmt = $db->prepare("
        INSERT INTO services (
            title, slug, description, price, starting_price, preview_image, 
            seller_name, profile_image, avg_rating, review_count, orders_count, 
            delivery_time, tags, is_featured, category_slug, icon, demo_url, 
            technology, is_active, status
        ) VALUES (
            :title, :slug, :description, :price, :starting_price, :preview_image,
            :seller_name, :profile_image, :avg_rating, :review_count, :orders_count,
            :delivery_time, :tags, :is_featured, :category_slug, :icon, :demo_url,
            :technology, 1, 'approved'
        )
    ");
    
    foreach ($services as $service) {
        $stmt->execute($service);
    }
    
    echo "✅ Successfully populated " . count($services) . " services with preview images!\n";
    echo "📊 Services by category:\n";
    
    // Count by category
    $categories = $db->query("
        SELECT category_slug, COUNT(*) as count 
        FROM services 
        GROUP BY category_slug 
        ORDER BY count DESC
    ");
    
    while ($row = $categories->fetch(PDO::FETCH_ASSOC)) {
        echo "   - " . ucfirst($row['category_slug']) . ": " . $row['count'] . " services\n";
    }
    
    echo "\n🎯 Featured services: " . $db->query("SELECT COUNT(*) FROM services WHERE is_featured = 1")->fetchColumn() . "\n";
    echo "📈 Total services: " . $db->query("SELECT COUNT(*) FROM services")->fetchColumn() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>