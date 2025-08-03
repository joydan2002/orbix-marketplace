-- Create domains table for domain availability checking
CREATE TABLE IF NOT EXISTS domains (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    extension VARCHAR(10) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    is_premium BOOLEAN DEFAULT FALSE,
    renewal_price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_extension (extension),
    INDEX idx_available (is_available),
    UNIQUE KEY unique_domain (name, extension)
);

-- Domain extensions pricing table
CREATE TABLE IF NOT EXISTS domain_extensions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    extension VARCHAR(10) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    renewal_price DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0
);

-- Insert domain extensions with realistic pricing
INSERT INTO domain_extensions (extension, price, renewal_price, sort_order) VALUES
('.com', 12.99, 14.99, 1),
('.net', 14.99, 16.99, 2),
('.org', 13.99, 15.99, 3),
('.io', 39.99, 39.99, 4),
('.co', 29.99, 29.99, 5),
('.biz', 19.99, 21.99, 6),
('.info', 18.99, 20.99, 7),
('.tech', 49.99, 49.99, 8),
('.app', 19.99, 19.99, 9),
('.dev', 15.99, 15.99, 10)
ON DUPLICATE KEY UPDATE 
    price = VALUES(price),
    renewal_price = VALUES(renewal_price),
    sort_order = VALUES(sort_order);

-- Sample domains data (mix of available and taken domains)
INSERT INTO domains (name, extension, price, is_available, is_premium, renewal_price) VALUES
-- Available domains
('mybusiness', '.com', 12.99, TRUE, FALSE, 14.99),
('techstartup', '.com', 12.99, TRUE, FALSE, 14.99),
('creativestudio', '.com', 12.99, TRUE, FALSE, 14.99),
('digitalagency', '.net', 14.99, TRUE, FALSE, 16.99),
('webdesign', '.org', 13.99, TRUE, FALSE, 15.99),
('cloudservice', '.io', 39.99, TRUE, FALSE, 39.99),
('marketpro', '.co', 29.99, TRUE, FALSE, 29.99),
('nextgen', '.tech', 49.99, TRUE, FALSE, 49.99),
('innovate', '.app', 19.99, TRUE, FALSE, 19.99),
('codebase', '.dev', 15.99, TRUE, FALSE, 15.99),

-- Taken domains (popular websites)
('google', '.com', 12.99, FALSE, FALSE, 14.99),
('facebook', '.com', 12.99, FALSE, FALSE, 14.99),
('amazon', '.com', 12.99, FALSE, FALSE, 14.99),
('microsoft', '.com', 12.99, FALSE, FALSE, 14.99),
('apple', '.com', 12.99, FALSE, FALSE, 14.99),
('twitter', '.com', 12.99, FALSE, FALSE, 14.99),
('instagram', '.com', 12.99, FALSE, FALSE, 14.99),
('youtube', '.com', 12.99, FALSE, FALSE, 14.99),
('netflix', '.com', 12.99, FALSE, FALSE, 14.99),
('spotify', '.com', 12.99, FALSE, FALSE, 14.99),

-- Premium domains (expensive)
('business', '.com', 2999.99, TRUE, TRUE, 2999.99),
('shop', '.com', 1999.99, TRUE, TRUE, 1999.99),
('store', '.com', 3999.99, TRUE, TRUE, 3999.99),
('tech', '.com', 4999.99, TRUE, TRUE, 4999.99),
('digital', '.com', 2499.99, TRUE, TRUE, 2499.99),
('online', '.com', 1899.99, TRUE, TRUE, 1899.99),
('web', '.com', 5999.99, TRUE, TRUE, 5999.99),
('app', '.com', 3499.99, TRUE, TRUE, 3499.99),
('ai', '.com', 7999.99, TRUE, TRUE, 7999.99),
('cloud', '.com', 4499.99, TRUE, TRUE, 4499.99)

ON DUPLICATE KEY UPDATE 
    price = VALUES(price),
    is_available = VALUES(is_available),
    is_premium = VALUES(is_premium),
    renewal_price = VALUES(renewal_price);