<?php
/**
 * Script to update services table with data from backup/change-section.html
 * This will replace all existing services with new ones
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $db = DatabaseConfig::getConnection();
    echo "Connected to database successfully!\n";

    // Clear existing services
    $db->exec('DELETE FROM services');
    echo "Cleared existing services data!\n";

    // Services data from backup/change-section.html (translated to English)
    $services = [
        [
            'title' => 'Website Design',
            'description' => 'Custom website design with modern 3D interface, glassmorphism effects and optimized user experience for professional businesses.',
            'price' => 299.00,
            'icon' => 'ri-code-line',
            'features' => json_encode([
                'Professional UI/UX Design',
                '3D Effects & Animations', 
                'Responsive on All Devices'
            ]),
            'is_active' => 1
        ],
        [
            'title' => 'Domain Registration',
            'description' => 'Easy domain registration with multiple domain extensions and brand protection on the internet with competitive pricing.',
            'price' => 12.99,
            'icon' => 'ri-global-line',
            'features' => json_encode([
                'Various Domain Extensions (.com, .net, .org)',
                'WHOIS Privacy Protection',
                'Auto Domain Renewal'
            ]),
            'is_active' => 1
        ],
        [
            'title' => 'Hosting & Cloud',
            'description' => 'High-performance hosting service and flexible cloud solutions ensuring your website runs smoothly with maximum uptime.',
            'price' => 49.00,
            'icon' => 'ri-server-line',
            'features' => json_encode([
                'High-Speed SSD Hosting',
                'Cloud Server with Auto-scaling',
                'Daily Automatic Backup'
            ]),
            'is_active' => 1
        ],
        [
            'title' => 'SSL & Security',
            'description' => 'Protect your website and customer data with SSL certificates and comprehensive security solutions against cyber threats.',
            'price' => 99.00,
            'icon' => 'ri-shield-check-line',
            'features' => json_encode([
                'SSL Certificates DV, OV, EV',
                'DDoS Protection Firewall',
                'Automatic Malware Scanning'
            ]),
            'is_active' => 1
        ],
        [
            'title' => 'Technical Support',
            'description' => '24/7 technical support team ready to solve any issues and help you operate your website effectively with expert guidance.',
            'price' => 199.00,
            'icon' => 'ri-customer-service-2-line',
            'features' => json_encode([
                '24/7 Live Chat Support',
                'Quick Issue Resolution',
                'Website Optimization Consulting'
            ]),
            'is_active' => 1
        ],
        [
            'title' => 'Website Maintenance',
            'description' => 'Professional website maintenance service ensuring your website is always updated, secure and performing at its best.',
            'price' => 149.00,
            'icon' => 'ri-tools-line',
            'features' => json_encode([
                'Regular Version Updates',
                'Bug Checking and Fixing',
                'Performance Optimization'
            ]),
            'is_active' => 1
        ]
    ];

    // Check if features column exists, if not add it
    try {
        $db->exec("ALTER TABLE services ADD COLUMN features TEXT");
        echo "Added features column to services table!\n";
    } catch (Exception $e) {
        // Column probably already exists
        echo "Features column already exists or couldn't be added.\n";
    }

    // Insert new services
    foreach ($services as $service) {
        $stmt = $db->prepare("
            INSERT INTO services (title, description, price, icon, features, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $service['title'],
            $service['description'],
            $service['price'],
            $service['icon'],
            $service['features'],
            $service['is_active']
        ]);
    }
    
    echo "Successfully added " . count($services) . " new services!\n";
    
    // Display the new services
    echo "\nNew services in database:\n";
    $stmt = $db->query("SELECT * FROM services ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['title'] . " ($" . $row['price'] . ")\n";
    }
    
    echo "\nDatabase update completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>