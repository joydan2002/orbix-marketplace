<?php
/**
 * Service Cards Display - New Design with Database Integration
 * Uses modern design from backup with orange theme and English translation
 */

// Get services from database
try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback to static services if database error
    $services = [
        [
            'id' => 1,
            'title' => 'Website Design',
            'description' => 'Custom website design with modern 3D interface, glassmorphism effects and optimized user experience.',
            'icon' => 'ri-code-line',
            'price' => 299,
            'features' => ['Professional UI/UX Design', '3D Effects & Animations', 'Responsive on All Devices']
        ],
        [
            'id' => 2,
            'title' => 'Domain Registration',
            'description' => 'Easy domain registration with multiple domain extensions and brand protection on the internet.',
            'icon' => 'ri-global-line',
            'price' => 19,
            'features' => ['Various Domain Extensions', 'WHOIS Privacy Protection', 'Auto Domain Renewal']
        ],
        [
            'id' => 3,
            'title' => 'Hosting & Cloud',
            'description' => 'High-performance hosting service and flexible cloud solutions ensuring your website runs smoothly.',
            'icon' => 'ri-server-line',
            'price' => 49,
            'features' => ['High-Speed SSD Hosting', 'Cloud Server with Auto-scaling', 'Daily Automatic Backup']
        ],
        [
            'id' => 4,
            'title' => 'SSL & Security',
            'description' => 'Protect your website and customer data with SSL certificates and comprehensive security solutions.',
            'icon' => 'ri-shield-check-line',
            'price' => 99,
            'features' => ['SSL Certificates DV, OV, EV', 'DDoS Protection Firewall', 'Automatic Malware Scanning']
        ],
        [
            'id' => 5,
            'title' => 'Technical Support',
            'description' => '24/7 technical support team ready to solve any issues and help you operate your website effectively.',
            'icon' => 'ri-customer-service-2-line',
            'price' => 199,
            'features' => ['24/7 Live Chat Support', 'Quick Issue Resolution', 'Website Optimization Consulting']
        ],
        [
            'id' => 6,
            'title' => 'Website Maintenance',
            'description' => 'Professional website maintenance service ensuring your website is always updated, secure and performing well.',
            'icon' => 'ri-tools-line',
            'price' => 149,
            'features' => ['Regular Version Updates', 'Bug Checking and Fixing', 'Performance Optimization']
        ]
    ];
}
?>

<!-- Services Section -->
<section id="services" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-secondary">
                Our Services
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                We provide comprehensive services to help you build and maintain 
                professional websites with cutting-edge technology.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($services as $service): ?>
            <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-2">
                <div class="w-16 h-16 flex items-center justify-center bg-primary/10 rounded-lg mb-6">
                    <i class="<?php echo htmlspecialchars($service['icon']); ?> text-2xl text-primary"></i>
                </div>
                
                <h3 class="text-xl font-bold mb-3 text-secondary"><?php echo htmlspecialchars($service['title']); ?></h3>
                
                <p class="text-gray-600 mb-4 leading-relaxed">
                    <?php echo htmlspecialchars($service['description']); ?>
                </p>
                
                <ul class="space-y-2 mb-6">
                    <?php 
                    // Handle features from database (JSON format)
                    $features = [];
                    if (isset($service['features']) && !empty($service['features'])) {
                        $features = json_decode($service['features'], true) ?: [];
                    }
                    
                    // Fallback if no features
                    if (empty($features)) {
                        $features = [
                            'Professional Service',
                            'Quality Guarantee', 
                            '24/7 Support'
                        ];
                    }
                    
                    foreach ($features as $feature): 
                    ?>
                    <li class="flex items-start">
                        <i class="ri-check-line text-green-500 mt-1 mr-2"></i>
                        <span class="text-gray-600"><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="flex items-center justify-between">
                    <div class="text-lg font-bold text-primary">
                        Starting at $<?php echo number_format($service['price'], 0); ?>
                    </div>
                    <a href="#" class="text-primary font-medium flex items-center hover:text-primary/80 transition-colors">
                        Learn More <i class="ri-arrow-right-line ml-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Domain Search Section -->
        <div class="mt-20 bg-gradient-to-r from-primary to-primary/80 rounded-xl p-8 md:p-12 shadow-xl">
            <div class="text-center mb-8">
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">
                    Find The Perfect Domain For Your Website
                </h3>
                <p class="text-white/90">
                    Check if your desired domain name is available and register it today.
                </p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <div class="relative">
                            <input 
                                type="text" 
                                placeholder="Enter your desired domain name..." 
                                class="w-full px-6 py-4 rounded-lg border-none text-gray-800 focus:outline-none text-lg"
                            />
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <select class="appearance-none bg-transparent border-none text-gray-500 focus:outline-none pr-8">
                                    <option>.com</option>
                                    <option>.net</option>
                                    <option>.org</option>
                                    <option>.io</option>
                                    <option>.co</option>
                                </select>
                                <div class="absolute right-0 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                    <i class="ri-arrow-down-s-line text-gray-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="bg-white text-primary px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors whitespace-nowrap">
                        Check Availability
                    </button>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                        <p class="text-white font-medium">.com</p>
                        <p class="text-white/90 text-sm">$12.99/year</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                        <p class="text-white font-medium">.net</p>
                        <p class="text-white/90 text-sm">$14.99/year</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                        <p class="text-white font-medium">.org</p>
                        <p class="text-white/90 text-sm">$13.99/year</p>
                    </div>
                    <div class="bg-white/20 rounded-lg p-3 text-center backdrop-blur-sm">
                        <p class="text-white font-medium">.io</p>
                        <p class="text-white/90 text-sm">$39.99/year</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>