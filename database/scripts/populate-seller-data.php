<?php
/**
 * Populate Sample Data for Seller Dashboard
 * Creates realistic data for seller nguyenchangxinxin2002@gmail.com (ID: 44)
 */

require_once '../../config/database.php';

try {
    $pdo = DatabaseConfig::getConnection();
    $pdo->beginTransaction();
    
    $seller_id = 44;
    echo "Adding sample data for seller ID: $seller_id\n";
    
    // First, create some buyer accounts if they don't exist
    $buyers = [
        ['buyer1', 'buyer1@example.com', 'John', 'Smith'],
        ['buyer2', 'buyer2@example.com', 'Emily', 'Johnson'],
        ['buyer3', 'buyer3@example.com', 'Michael', 'Brown'],
        ['buyer4', 'buyer4@example.com', 'Sarah', 'Davis']
    ];
    
    $buyer_ids = [43]; // Existing buyer
    foreach ($buyers as $buyer) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO users (username, email, first_name, last_name, user_type, password_hash, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'buyer', ?, 1, NOW(), NOW())
        ");
        $stmt->execute([$buyer[0], $buyer[1], $buyer[2], $buyer[3], password_hash('password123', PASSWORD_DEFAULT)]);
        
        // Get the ID of this buyer
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$buyer[1]]);
        $result = $stmt->fetch();
        if ($result) {
            $buyer_ids[] = $result['id'];
        }
    }
    
    // 1. Update seller profile
    $stmt = $pdo->prepare("
        INSERT INTO seller_profiles (user_id, business_name, description, location, phone, business_type, 
                                   total_sales, total_orders, response_time, account_balance) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            business_name = VALUES(business_name),
            description = VALUES(description),
            location = VALUES(location),
            phone = VALUES(phone),
            business_type = VALUES(business_type),
            total_sales = VALUES(total_sales),
            total_orders = VALUES(total_orders),
            response_time = VALUES(response_time),
            account_balance = VALUES(account_balance)
    ");
    $stmt->execute([$seller_id, 'TechCraft Solutions', 
                   'Professional web development and digital solutions provider with 5+ years experience', 
                   'Ho Chi Minh City, Vietnam', '+84 123 456 789', 'company',
                   28450.50, 156, 2, 2850.75]);
    
    // 2. Add templates
    $templates = [
        ['Modern Business Website', 'modern-business-website', 'Professional business website template with clean design', 49.99, 'HTML/CSS/JS'],
        ['E-commerce Store Template', 'ecommerce-store-template', 'Complete online store solution with shopping cart', 79.99, 'React/Node.js'],
        ['Portfolio Website Template', 'portfolio-website-template', 'Creative portfolio template for designers and developers', 39.99, 'Vue.js'],
        ['Restaurant Website Template', 'restaurant-website-template', 'Modern restaurant website with menu and booking system', 59.99, 'HTML/CSS/JS'],
        ['Agency Landing Page', 'agency-landing-page', 'Professional agency landing page template', 29.99, 'HTML/CSS']
    ];
    
    $template_ids = [];
    foreach ($templates as $template) {
        $stmt = $pdo->prepare("
            INSERT INTO templates (title, slug, description, price, seller_id, technology, 
                                 views_count, downloads_count, rating, reviews_count, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', NOW(), NOW())
        ");
        $stmt->execute([
            $template[0], $template[1], $template[2], $template[3], $seller_id, $template[4],
            rand(150, 800), rand(25, 150), round(rand(40, 50)/10, 1), rand(5, 25), 
        ]);
        $template_ids[] = $pdo->lastInsertId();
    }
    
    // 3. Add services to seller_services table
    $services = [
        ['Custom Website Development', 'custom-website-development-' . time(), 'Full-stack web development service from design to deployment', 'Professional web development from concept to deployment', 299.99, 14],
        ['WordPress Customization', 'wordpress-customization-' . time(), 'Professional WordPress theme customization and setup', 'Custom WordPress solutions for your business', 149.99, 7],
        ['E-commerce Setup', 'ecommerce-setup-' . time(), 'Complete e-commerce store setup with payment integration', 'Full e-commerce solution with secure payments', 399.99, 21],
        ['SEO Optimization Service', 'seo-optimization-service-' . time(), 'Comprehensive SEO audit and optimization for better rankings', 'Boost your search engine rankings', 199.99, 10]
    ];
    
    $service_ids = [];
    foreach ($services as $service) {
        $stmt = $pdo->prepare("
            INSERT INTO seller_services (seller_id, title, slug, description, short_description, price, 
                                       delivery_time_days, revisions_included, views_count, orders_count, 
                                       rating, reviews_count, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 3, ?, ?, ?, ?, 'active', NOW(), NOW())
        ");
        $stmt->execute([
            $seller_id, $service[0], $service[1], $service[2], $service[3], $service[4], $service[5],
            rand(50, 300), rand(8, 45), round(rand(42, 50)/10, 1), rand(3, 18)
        ]);
        $service_ids[] = $pdo->lastInsertId();
    }
    
    // 4. Add service orders using the correct service IDs and valid buyer IDs
    $service_orders_data = [
        ['in_progress', '2025-08-03', '2025-08-17'],
        ['completed', '2025-07-28', '2025-08-04'],
        ['pending', '2025-08-04', '2025-08-25'],
        ['completed', '2025-07-25', '2025-08-04']
    ];
    
    foreach ($service_orders_data as $index => $order_data) {
        if (isset($service_ids[$index]) && isset($buyer_ids[$index])) {
            $order_number = 'ORD-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $price = $services[$index][4]; // Get price from services array
            
            $stmt = $pdo->prepare("
                INSERT INTO service_orders (order_number, service_id, buyer_id, seller_id, price, total_amount, 
                                          delivery_date, status, payment_status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'paid', ?, NOW())
            ");
            $stmt->execute([
                $order_number, $service_ids[$index], $buyer_ids[$index], $seller_id, 
                $price, $price, $order_data[2], $order_data[0], $order_data[1]
            ]);
        }
    }
    
    // 5. Add template orders using valid buyer IDs and template IDs
    $template_orders = [
        [1, 49.99, 'completed', '2025-08-01'],
        [2, 79.99, 'completed', '2025-07-28'],
        [3, 39.99, 'completed', '2025-07-25'],
        [4, 59.99, 'completed', '2025-07-22'],
        [5, 29.99, 'completed', '2025-07-20']
    ];
    
    foreach ($template_orders as $index => $order) {
        if (isset($buyer_ids[$index]) && isset($template_ids[$index])) {
            $order_number = 'TPL-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, order_number, total_amount, status, payment_status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'paid', ?, NOW())
            ");
            $stmt->execute([$buyer_ids[$index], $order_number, $order[1], $order[2], $order[3]]);
            
            $order_id = $pdo->lastInsertId();
            
            // Add order item using the correct template ID
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, template_id, price, created_at)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$order_id, $template_ids[$index], $order[1], $order[3]]);
        }
    }
    
    // 6. Add template reviews using valid buyer IDs and template IDs
    $template_reviews = [
        [1, 5, 'Absolutely fantastic template! Clean design, easy to customize, and great documentation.', '2025-08-01'],
        [2, 4, 'Great template overall. Modern design and responsive. Could use more payment options.', '2025-07-29'],
        [3, 5, 'Perfect for my portfolio! Easy to implement and looks professional.', '2025-07-26'],
        [4, 5, 'Excellent restaurant template. All features work perfectly.', '2025-07-23'],
        [5, 4, 'Good landing page template. Clean code and nice design.', '2025-07-21']
    ];
    
    foreach ($template_reviews as $index => $review) {
        if (isset($buyer_ids[$index]) && isset($template_ids[$index])) {
            $stmt = $pdo->prepare("
                INSERT INTO reviews (template_id, user_id, rating, review_text, created_at)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$template_ids[$index], $buyer_ids[$index], $review[1], $review[2], $review[3]]);
        }
    }
    
    // 7. Add service reviews using the old services table (not seller_services)
    // First add services to the old services table for reviews compatibility
    $old_service_ids = [];
    foreach ($services as $service) {
        $stmt = $pdo->prepare("
            INSERT INTO services (title, description, seller_id, price, technology, 
                                views_count, orders_count, rating, reviews_count, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', NOW(), NOW())
        ");
        $stmt->execute([
            $service[0], $service[2], $seller_id, $service[4], $service[4],
            rand(50, 300), rand(8, 45), round(rand(42, 50)/10, 1), rand(3, 18)
        ]);
        $old_service_ids[] = $pdo->lastInsertId();
    }
    
    $service_reviews_data = [
        [1, 5, 'Outstanding WordPress customization service! Delivered exactly what I needed.', '2025-08-05'],
        [2, 4, 'Good SEO service. Saw improvements in rankings within 2 weeks.', '2025-08-05'],
        [0, 5, 'Excellent web development service. Professional and timely delivery.', '2025-08-04']
    ];
    
    foreach ($service_reviews_data as $review) {
        if (isset($old_service_ids[$review[0]]) && isset($buyer_ids[$review[0]])) {
            $stmt = $pdo->prepare("
                INSERT INTO service_reviews (service_id, user_id, rating, review_text, created_at)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$old_service_ids[$review[0]], $buyer_ids[$review[0]], $review[1], $review[2], $review[3]]);
        }
    }
    
    // 8. Add messages using valid buyer IDs
    $messages = [
        ['Question about Modern Business Website Template', 'Hi! I\'m interested in your business template. Does it include mobile responsiveness?', 0, '2025-08-04 14:30:00'],
        ['Custom development inquiry', 'Hello, I need a custom e-commerce website. Can you provide a quote?', 1, '2025-08-03 10:15:00'],
        ['Template customization request', 'Can you help customize the portfolio template to match my brand colors?', 1, '2025-08-02 16:45:00'],
        ['SEO service follow-up', 'Thank you for the SEO audit. When can we start the optimization phase?', 0, '2025-08-01 09:20:00']
    ];
    
    foreach ($messages as $index => $message) {
        if (isset($buyer_ids[$index])) {
            $stmt = $pdo->prepare("
                INSERT INTO messages (sender_id, receiver_id, subject, message, is_read, created_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$buyer_ids[$index], $seller_id, $message[0], $message[1], $message[2], $message[3]]);
        }
    }
    
    // 9. Add earnings records - fix parameter count
    $earnings_data = [
        [42.49, 15.00, 7.51, 35.98, 'available', '2025-08-01'],
        [67.99, 15.00, 12.00, 55.99, 'available', '2025-07-28'],
        [33.99, 15.00, 5.99, 28.00, 'available', '2025-07-25'],
        [50.49, 15.00, 8.99, 41.50, 'available', '2025-07-22'],
        [25.49, 15.00, 4.50, 20.99, 'available', '2025-07-20']
    ];
    
    // Get the order IDs that were just created for templates
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE user_id IN (" . implode(',', array_slice($buyer_ids, 0, 5)) . ") ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $template_order_results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($earnings_data as $index => $earning) {
        $order_id = $template_order_results[$index] ?? ($index + 1);
        $template_id = $template_ids[$index] ?? ($index + 1);
        
        $stmt = $pdo->prepare("
            INSERT INTO seller_earnings (seller_id, order_id, template_id, gross_amount, commission_rate, 
                                       commission_amount, net_amount, status, available_date, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $seller_id, $order_id, $template_id, $earning[0], $earning[1], 
            $earning[2], $earning[3], $earning[4], $earning[5]
        ]);
    }
    
    // 10. Add analytics data
    $analytics = [
        ['2025-08-04', 125, 3, 169.97, 2.4],
        ['2025-08-03', 98, 2, 89.98, 2.0],
        ['2025-08-02', 112, 1, 49.99, 0.9],
        ['2025-08-01', 156, 4, 219.96, 2.6],
        ['2025-07-31', 89, 2, 99.98, 2.2],
        ['2025-07-30', 134, 3, 149.97, 2.2],
        ['2025-07-29', 167, 5, 259.95, 3.0]
    ];
    
    foreach ($analytics as $analytic) {
        $stmt = $pdo->prepare("
            INSERT INTO seller_analytics (seller_id, date, views, orders, revenue, conversion_rate, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$seller_id, $analytic[0], $analytic[1], $analytic[2], $analytic[3], $analytic[4]]);
    }
    
    // 11. Add withdrawal request
    $stmt = $pdo->prepare("
        INSERT INTO withdrawal_requests (seller_id, amount, payment_method, status, created_at)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$seller_id, 500.00, 'bank_transfer', 'pending', '2025-08-03 09:00:00']);
    
    $pdo->commit();
    echo "✅ Sample data added successfully!\n";
    echo "📊 Data Summary:\n";
    echo "   - 1 seller profile updated\n";
    echo "   - 5 templates added\n";
    echo "   - 4 services added\n";
    echo "   - 5 service orders added\n";
    echo "   - 5 template orders added\n";
    echo "   - 5 template reviews added\n";
    echo "   - 3 service reviews added\n";
    echo "   - 4 messages added\n";
    echo "   - 5 earnings records added\n";
    echo "   - 7 analytics data points added\n";
    echo "   - 1 withdrawal request added\n";
    echo "\n🎯 All sections now have real data from database!\n";
    
} catch (Exception $e) {
    $pdo->rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>