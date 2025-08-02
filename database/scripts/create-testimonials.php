<?php
/**
 * Script to create testimonials table and populate with data from backup
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $db = DatabaseConfig::getConnection();
    echo "Connected to database successfully!\n";

    // Create testimonials table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        position VARCHAR(255) NOT NULL,
        company VARCHAR(255) NOT NULL,
        avatar_url VARCHAR(500),
        rating INT DEFAULT 5,
        testimonial TEXT NOT NULL,
        is_featured BOOLEAN DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $db->exec($createTableSQL);
    echo "Created testimonials table successfully!\n";

    // Clear existing testimonials (if any)
    $db->exec('DELETE FROM testimonials');
    echo "Cleared existing testimonials data!\n";

    // Testimonials data from backup (translated to English)
    $testimonials = [
        [
            'name' => 'Sarah Chen',
            'position' => 'CEO',
            'company' => 'Fashion Forward',
            'avatar_url' => 'https://images.unsplash.com/photo-1494790108755-2616b612b5c5?w=100&h=100&fit=crop&crop=face',
            'rating' => 5,
            'testimonial' => 'I was looking for a professional website solution for my fashion store and was lucky to discover their service. The modern 3D template gives customers an amazing shopping experience. Our online sales increased by 45% in just 2 months!',
            'is_featured' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'David Rodriguez',
            'position' => 'Founder',
            'company' => 'TechVision',
            'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face',
            'rating' => 5,
            'testimonial' => 'Their technical support team is truly outstanding! Whenever we encounter issues, they are always ready to resolve them quickly. The dashboard template helps us manage data efficiently with an intuitive interface. Absolutely worth every penny!',
            'is_featured' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Emily Johnson',
            'position' => 'Photographer',
            'company' => 'Creative Studios',
            'avatar_url' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face',
            'rating' => 5,
            'testimonial' => 'The 3D portfolio template helped me showcase my photography work impressively. Clients constantly praise my website, and this has helped me secure more projects. Thank you for creating such an amazing product!',
            'is_featured' => 1,
            'is_active' => 1
        ],
        [
            'name' => 'Michael Thompson',
            'position' => 'Marketing Director',
            'company' => 'Digital Solutions',
            'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face',
            'rating' => 5,
            'testimonial' => 'The landing page templates are conversion-focused and beautifully designed. Our conversion rates improved by 60% after implementing their template. The customer support is exceptional too!',
            'is_featured' => 0,
            'is_active' => 1
        ],
        [
            'name' => 'Lisa Wang',
            'position' => 'E-commerce Manager',
            'company' => 'Online Retail Co',
            'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&fit=crop&crop=face',
            'rating' => 5,
            'testimonial' => 'The e-commerce template is feature-rich and user-friendly. Our customers love the shopping experience, and our sales have grown significantly. Highly recommended for any online business!',
            'is_featured' => 0,
            'is_active' => 1
        ],
        [
            'name' => 'James Wilson',
            'position' => 'Startup Founder',
            'company' => 'Innovation Hub',
            'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face',
            'rating' => 4,
            'testimonial' => 'Great templates with modern design. The pricing is reasonable and the quality is top-notch. Perfect for startups looking to establish a professional online presence quickly.',
            'is_featured' => 0,
            'is_active' => 1
        ]
    ];

    // Insert testimonials
    foreach ($testimonials as $testimonial) {
        $stmt = $db->prepare("
            INSERT INTO testimonials (name, position, company, avatar_url, rating, testimonial, is_featured, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $testimonial['name'],
            $testimonial['position'],
            $testimonial['company'],
            $testimonial['avatar_url'],
            $testimonial['rating'],
            $testimonial['testimonial'],
            $testimonial['is_featured'],
            $testimonial['is_active']
        ]);
    }
    
    echo "Successfully added " . count($testimonials) . " testimonials!\n";
    
    // Display the new testimonials
    echo "\nTestimonials in database:\n";
    $stmt = $db->query("SELECT name, company, rating FROM testimonials ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['name'] . " (" . $row['company'] . ") - " . $row['rating'] . " stars\n";
    }
    
    echo "\nTestimonials table setup completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>