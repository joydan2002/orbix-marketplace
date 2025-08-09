<?php
/**
 * Database Setup Script
 * Run this once to create database and populate with sample data
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL successfully!\n";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS orbix_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'orbix_market' created successfully!\n";
    
    // Select database
    $pdo->exec("USE orbix_market");
    
    // Create tables
    $tables = [
        // Users table
        "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            profile_image VARCHAR(255),
            user_type ENUM('buyer', 'seller', 'admin') DEFAULT 'buyer',
            is_verified BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        // Categories table
        "CREATE TABLE IF NOT EXISTS categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(50) NOT NULL,
            slug VARCHAR(50) UNIQUE NOT NULL,
            description TEXT,
            icon VARCHAR(50),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Templates table
        "CREATE TABLE IF NOT EXISTS templates (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            category_id INT,
            seller_id INT,
            preview_image VARCHAR(255),
            demo_url VARCHAR(255),
            download_file VARCHAR(255),
            technology VARCHAR(100),
            tags JSON,
            downloads_count INT DEFAULT 0,
            views_count INT DEFAULT 0,
            rating DECIMAL(3,2) DEFAULT 0.00,
            reviews_count INT DEFAULT 0,
            is_featured BOOLEAN DEFAULT FALSE,
            status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id),
            FOREIGN KEY (seller_id) REFERENCES users(id)
        )",
        
        // Reviews table
        "CREATE TABLE IF NOT EXISTS reviews (
            id INT PRIMARY KEY AUTO_INCREMENT,
            template_id INT,
            user_id INT,
            rating INT CHECK (rating >= 1 AND rating <= 5),
            review_text TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    echo "Tables created successfully!\n";
    
    // Insert sample data
    
    // Categories
    $pdo->exec("INSERT IGNORE INTO categories (name, slug, description, icon) VALUES
        ('Business', 'business', 'Professional business website templates', 'ri-briefcase-line'),
        ('E-commerce', 'e-commerce', 'Online store and shopping cart templates', 'ri-shopping-cart-line'),
        ('Portfolio', 'portfolio', 'Creative portfolio and showcase templates', 'ri-image-line'),
        ('Landing Page', 'landing', 'High-converting landing page templates', 'ri-rocket-line'),
        ('Admin Dashboard', 'admin', 'Administrative dashboard templates', 'ri-dashboard-line')");
    
    // Users (sellers)
    $pdo->exec("INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, profile_image, user_type, is_verified) VALUES
        ('alex_johnson', 'alex@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex', 'Johnson', 'https://readdy.ai/api/search-image?query=professional%20designer%20avatar%20headshot%20clean%20background%20modern%20style&width=100&height=100&seq=seller1&orientation=squarish', 'seller', TRUE),
        ('sarah_chen', 'sarah@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Chen', 'https://readdy.ai/api/search-image?query=female%20designer%20avatar%20headshot%20professional%20clean%20background%20modern%20style&width=100&height=100&seq=seller2&orientation=squarish', 'seller', TRUE),
        ('mike_rodriguez', 'mike@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Rodriguez', 'https://readdy.ai/api/search-image?query=creative%20designer%20avatar%20headshot%20artistic%20background%20modern%20style&width=100&height=100&seq=seller3&orientation=squarish', 'seller', TRUE),
        ('emma_wilson', 'emma@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma', 'Wilson', 'https://readdy.ai/api/search-image?query=tech%20designer%20avatar%20headshot%20professional%20clean%20background%20modern%20style&width=100&height=100&seq=seller4&orientation=squarish', 'seller', TRUE),
        ('david_kim', 'david@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Kim', 'https://readdy.ai/api/search-image?query=chef%20designer%20avatar%20headshot%20culinary%20background%20modern%20style&width=100&height=100&seq=seller5&orientation=squarish', 'seller', TRUE),
        ('lisa_thompson', 'lisa@example.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa', 'Thompson', 'https://readdy.ai/api/search-image?query=fitness%20trainer%20avatar%20headshot%20athletic%20background%20modern%20style&width=100&height=100&seq=seller6&orientation=squarish', 'seller', TRUE)");
    
    // Templates
    $pdo->exec("INSERT IGNORE INTO templates (title, slug, description, price, category_id, seller_id, preview_image, demo_url, technology, tags, downloads_count, views_count, rating, reviews_count, is_featured, status) VALUES
        ('Business Pro Dashboard', 'business-pro-dashboard', 'Complete business dashboard template with modern design and responsive layout', 89.00, 1, 1, 'https://readdy.ai/api/search-image?query=modern%20business%20website%20template%20professional%20corporate%20design%20clean%20interface%20white%20background%20dashboard%20layout&width=400&height=300&seq=card1&orientation=landscape', '#', 'React', '[\"responsive\", \"dashboard\", \"business\", \"modern\"]', 127, 1250, 4.9, 127, TRUE, 'approved'),
        
        ('ShopMax E-commerce', 'shopmax-ecommerce', 'Full-featured e-commerce template with cart, checkout, and admin panel', 129.00, 2, 2, 'https://readdy.ai/api/search-image?query=e-commerce%20website%20template%20online%20shopping%20interface%20modern%20design%20clean%20layout%20white%20background%20product%20showcase&width=400&height=300&seq=card2&orientation=landscape', '#', 'Vue.js', '[\"ecommerce\", \"shopping\", \"cart\", \"responsive\"]', 89, 890, 4.8, 89, TRUE, 'approved'),
        
        ('Creative Portfolio', 'creative-portfolio', 'Stunning portfolio template for creative professionals and agencies', 49.00, 3, 3, 'https://readdy.ai/api/search-image?query=creative%20portfolio%20website%20template%20modern%20design%20artistic%20layout%20clean%20interface%20white%20background%20showcase%20gallery&width=400&height=300&seq=card3&orientation=landscape', '#', 'HTML', '[\"portfolio\", \"creative\", \"responsive\", \"gallery\"]', 203, 2030, 5.0, 203, TRUE, 'approved'),
        
        ('SaaS Landing Pro', 'saas-landing-pro', 'High-converting landing page template perfect for SaaS products', 39.00, 4, 4, 'https://readdy.ai/api/search-image?query=landing%20page%20website%20template%20modern%20design%20conversion%20focused%20clean%20interface%20white%20background%20marketing%20layout&width=400&height=300&seq=card4&orientation=landscape', '#', 'Figma', '[\"landing\", \"saas\", \"conversion\", \"responsive\"]', 156, 1560, 4.7, 156, FALSE, 'approved'),
        
        ('Foodie Restaurant', 'foodie-restaurant', 'Beautiful restaurant template with online ordering and reservation system', 69.00, 1, 5, 'https://readdy.ai/api/search-image?query=restaurant%20website%20template%20food%20service%20design%20modern%20layout%20clean%20interface%20white%20background%20menu%20showcase&width=400&height=300&seq=card5&orientation=landscape', '#', 'WordPress', '[\"restaurant\", \"food\", \"ordering\", \"responsive\"]', 74, 740, 4.6, 74, FALSE, 'approved'),
        
        ('FitLife Gym', 'fitlife-gym', 'Complete fitness website with class booking and trainer profiles', 79.00, 1, 6, 'https://readdy.ai/api/search-image?query=fitness%20website%20template%20gym%20health%20modern%20design%20clean%20interface%20white%20background%20workout%20programs&width=400&height=300&seq=card6&orientation=landscape', '#', 'React', '[\"fitness\", \"gym\", \"booking\", \"responsive\"]', 92, 920, 4.9, 92, FALSE, 'approved')");
    
    // Reviews
    $pdo->exec("INSERT IGNORE INTO reviews (template_id, user_id, rating, review_text) VALUES
        (1, 2, 5, 'Excellent template! Very professional and easy to customize.'),
        (1, 3, 5, 'Perfect for my business dashboard project.'),
        (1, 4, 4, 'Great design, could use more color options.'),
        (2, 1, 5, 'Amazing e-commerce template with all features I needed.'),
        (2, 3, 4, 'Good quality, responsive design works well.'),
        (3, 1, 5, 'Beautiful portfolio design, exactly what I was looking for.'),
        (3, 2, 5, 'Outstanding creative template!'),
        (4, 2, 5, 'Perfect landing page for my SaaS product.'),
        (4, 5, 4, 'Good conversion focused design.'),
        (5, 3, 4, 'Nice restaurant template, easy to customize.'),
        (6, 4, 5, 'Excellent fitness template with great features.')");
    
    echo "Sample data inserted successfully!\n";
    echo "Database setup completed!\n";
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}
?>