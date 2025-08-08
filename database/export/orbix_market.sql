-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 08, 2025 at 10:40 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `orbix_market`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `service_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `template_id`, `added_at`, `service_id`) VALUES
(28, 44, NULL, '2025-08-08 07:00:32', 50);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `is_active`, `created_at`) VALUES
(1, 'Business', 'business', 'Professional business website templates', 'ri-briefcase-line', 1, '2025-08-01 01:48:53'),
(2, 'Mobile Apps', '', 'iOS and Android app templates and UI kits', NULL, 1, '2025-08-04 03:29:05'),
(3, 'Portfolio', 'portfolio', 'Creative portfolio and showcase templates', 'ri-image-line', 1, '2025-08-01 01:48:53'),
(4, 'Landing Page', 'landing', 'High-converting landing page templates', 'ri-rocket-line', 1, '2025-08-01 01:48:53'),
(5, 'Admin Dashboard', 'admin', 'Administrative dashboard templates', 'ri-dashboard-line', 1, '2025-08-01 01:48:53'),
(9, 'E-commerce', 'ecommerce', 'Online store and shopping templates', NULL, 1, '2025-08-01 16:11:31'),
(12, 'Blog', 'blog', 'Blog and content website templates', NULL, 1, '2025-08-01 16:11:31'),
(13, 'SaaS', 'saas', 'Software as a Service templates', NULL, 1, '2025-08-01 16:11:31'),
(14, 'Education', 'education', 'Educational and learning platform templates', NULL, 1, '2025-08-01 16:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `is_premium` tinyint(1) DEFAULT 0,
  `renewal_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `domains`
--

INSERT INTO `domains` (`id`, `name`, `extension`, `price`, `is_available`, `is_premium`, `renewal_price`, `created_at`, `updated_at`) VALUES
(1, 'mybusiness', '.com', 12.99, 1, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(2, 'techstartup', '.com', 12.99, 1, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(3, 'creativestudio', '.com', 12.99, 1, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(4, 'digitalagency', '.net', 14.99, 1, 0, 16.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(5, 'webdesign', '.org', 13.99, 1, 0, 15.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(6, 'cloudservice', '.io', 39.99, 1, 0, 39.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(7, 'marketpro', '.co', 29.99, 1, 0, 29.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(8, 'nextgen', '.tech', 49.99, 1, 0, 49.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(9, 'innovate', '.app', 19.99, 1, 0, 19.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(10, 'codebase', '.dev', 15.99, 1, 0, 15.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(11, 'google', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(12, 'facebook', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(13, 'amazon', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(14, 'microsoft', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(15, 'apple', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(16, 'twitter', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(17, 'instagram', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(18, 'youtube', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(19, 'netflix', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(20, 'spotify', '.com', 12.99, 0, 0, 14.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(21, 'business', '.com', 2999.99, 1, 1, 2999.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(22, 'shop', '.com', 1999.99, 1, 1, 1999.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(23, 'store', '.com', 3999.99, 1, 1, 3999.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(24, 'tech', '.com', 4999.99, 1, 1, 4999.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(25, 'digital', '.com', 2499.99, 1, 1, 2499.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(26, 'online', '.com', 1899.99, 1, 1, 1899.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(27, 'web', '.com', 5999.99, 1, 1, 5999.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(28, 'app', '.com', 3499.99, 1, 1, 3499.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(29, 'ai', '.com', 7999.99, 1, 1, 7999.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18'),
(30, 'cloud', '.com', 4499.99, 1, 1, 4499.99, '2025-08-02 10:50:18', '2025-08-02 10:50:18');

-- --------------------------------------------------------

--
-- Table structure for table `domain_extensions`
--

CREATE TABLE `domain_extensions` (
  `id` int(11) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `renewal_price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `domain_extensions`
--

INSERT INTO `domain_extensions` (`id`, `extension`, `price`, `renewal_price`, `is_active`, `sort_order`) VALUES
(1, '.com', 12.99, 14.99, 1, 1),
(2, '.net', 14.99, 16.99, 1, 2),
(3, '.org', 13.99, 15.99, 1, 3),
(4, '.io', 39.99, 39.99, 1, 4),
(5, '.co', 29.99, 29.99, 1, 5),
(6, '.biz', 19.99, 21.99, 1, 6),
(7, '.info', 18.99, 20.99, 1, 7),
(8, '.tech', 49.99, 49.99, 1, 8),
(9, '.app', 19.99, 19.99, 1, 9),
(10, '.dev', 15.99, 15.99, 1, 10);

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(100) DEFAULT 'general',
  `is_popular` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `is_popular`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'How do I create an account on Orbix Market?', 'To create an account, click the \"Sign Up\" button in the top right corner, fill in your details, and verify your email address. You can also sign up as a buyer or seller depending on your needs.', 'general', 1, 1, 1, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(2, 'What payment methods do you accept?', 'We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers. All payments are processed securely through our encrypted payment system.', 'general', 1, 1, 2, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(3, 'How do I download my purchased templates?', 'After successful payment, you will receive an email with download links. You can also access your downloads from your account dashboard under \"My Purchases\".', 'general', 1, 1, 3, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(4, 'Can I get a refund for my purchase?', 'We offer a 30-day money-back guarantee if you are not satisfied with your purchase. Contact our support team with your order details to request a refund.', 'general', 0, 1, 4, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(5, 'How do I contact customer support?', 'You can contact us through live chat, email at support@orbixmarket.com, or phone at 1-800-ORBIX-HELP. Our support team is available 24/7 to help you.', 'general', 0, 1, 5, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(6, 'What file formats are included with templates?', 'Most templates include HTML, CSS, JavaScript files, and documentation. Some may also include PSD or Figma files for design customization.', 'templates', 1, 1, 1, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(7, 'Are templates mobile responsive?', 'Yes, all templates on Orbix Market are fully responsive and optimized for mobile, tablet, and desktop devices.', 'templates', 1, 1, 2, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(8, 'Can I customize the templates?', 'Absolutely! All templates come with well-documented code and are designed to be easily customizable. You can modify colors, fonts, layouts, and content to match your brand.', 'templates', 1, 1, 3, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(9, 'Do templates include free updates?', 'Yes, when you purchase a template, you receive free updates for 12 months. You will be notified via email when updates are available.', 'templates', 0, 1, 4, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(10, 'Can I use templates for client projects?', 'Yes, our standard license allows you to use templates for commercial client projects. For multiple client projects, consider our extended license.', 'templates', 0, 1, 5, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(11, 'How does billing work for subscriptions?', 'Subscription billing is automatic and recurring. You will be charged monthly or annually based on your chosen plan. You can cancel anytime from your account settings.', 'billing', 0, 1, 1, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(12, 'Can I change my subscription plan?', 'Yes, you can upgrade or downgrade your subscription plan at any time. Changes will be prorated and reflected in your next billing cycle.', 'billing', 0, 1, 2, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(13, 'What happens if my payment fails?', 'If a payment fails, we will retry charging your card for 7 days. If unsuccessful, your account may be temporarily suspended until payment is resolved.', 'billing', 0, 1, 3, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(14, 'How do I update my payment information?', 'You can update your payment information in your account settings under \"Billing & Payments\". Changes take effect immediately.', 'billing', 0, 1, 4, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(15, 'How do I reset my password?', 'Click \"Forgot Password\" on the login page, enter your email address, and follow the instructions in the reset email we send you.', 'account', 0, 1, 1, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(16, 'How do I change my email address?', 'You can change your email address in your account settings. You will need to verify the new email address before the change takes effect.', 'account', 0, 1, 2, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(17, 'Can I delete my account?', 'Yes, you can delete your account from the account settings page. Note that this action is permanent and cannot be undone.', 'account', 0, 1, 3, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(18, 'How do I become a seller?', 'To become a seller, go to your account dashboard and click \"Upgrade to Seller\". You will need to provide additional information and agree to our seller terms.', 'account', 1, 1, 4, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(19, 'What browsers are supported?', 'Our platform supports all modern browsers including Chrome, Firefox, Safari, and Edge. We recommend using the latest version for the best experience.', 'technical', 0, 1, 1, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(20, 'Why is the website loading slowly?', 'Slow loading can be due to internet connection or browser cache. Try clearing your browser cache or switching to a different network. Contact support if issues persist.', 'technical', 0, 1, 2, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(21, 'I am having trouble downloading files.', 'Download issues can occur due to browser settings or antivirus software. Try using a different browser or temporarily disabling your antivirus during download.', 'technical', 0, 1, 3, '2025-08-05 04:34:16', '2025-08-05 04:34:16'),
(22, 'How do I report a bug or technical issue?', 'You can report bugs through our support system or email technical@orbixmarket.com. Please include detailed steps to reproduce the issue and your browser information.', 'technical', 0, 1, 4, '2025-08-05 04:34:16', '2025-08-05 04:34:16');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `order_id`, `sender_id`, `receiver_id`, `subject`, `message`, `is_read`, `attachment`, `created_at`) VALUES
(13, NULL, 43, 44, 'Question about Modern Business Website Template', 'Hi! I\'m interested in your business template. Does it include mobile responsiveness?', 0, NULL, '2025-08-04 07:30:00'),
(14, NULL, 129, 44, 'Custom development inquiry', 'Hello, I need a custom e-commerce website. Can you provide a quote?', 1, NULL, '2025-08-03 03:15:00'),
(15, NULL, 130, 44, 'Template customization request', 'Can you help customize the portfolio template to match my brand colors?', 1, NULL, '2025-08-02 09:45:00'),
(16, NULL, 131, 44, 'SEO service follow-up', 'Thank you for the SEO audit. When can we start the optimization phase?', 0, NULL, '2025-08-01 02:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('order','message','review','earning','system') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `action_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `status`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
(32, 43, 'TPL-2025-3489', 49.99, 'completed', NULL, 'paid', '2025-07-31 17:00:00', '2025-08-04 18:41:26'),
(33, 129, 'TPL-2025-6041', 79.99, 'completed', NULL, 'paid', '2025-07-27 17:00:00', '2025-08-04 18:41:26'),
(34, 130, 'TPL-2025-9700', 39.99, 'completed', NULL, 'paid', '2025-07-24 17:00:00', '2025-08-04 18:41:26'),
(35, 131, 'TPL-2025-8007', 59.99, 'completed', NULL, 'paid', '2025-07-21 17:00:00', '2025-08-04 18:41:26'),
(36, 132, 'TPL-2025-9452', 29.99, 'completed', NULL, 'paid', '2025-07-19 17:00:00', '2025-08-04 18:41:26');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `template_id`, `price`, `created_at`) VALUES
(22, 32, 100, 49.99, '2025-07-31 17:00:00'),
(23, 33, 101, 79.99, '2025-07-27 17:00:00'),
(24, 34, 102, 39.99, '2025-07-24 17:00:00'),
(25, 35, 103, 59.99, '2025-07-21 17:00:00'),
(26, 36, 104, 29.99, '2025-07-19 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `template_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
(176, 100, 43, 5, 'Absolutely fantastic template! Clean design, easy to customize, and great documentation.', '2025-07-31 17:00:00'),
(177, 101, 129, 4, 'Great template overall. Modern design and responsive. Could use more payment options.', '2025-07-28 17:00:00'),
(178, 102, 130, 5, 'Perfect for my portfolio! Easy to implement and looks professional.', '2025-07-25 17:00:00'),
(179, 103, 131, 5, 'Excellent restaurant template. All features work perfectly.', '2025-07-22 17:00:00'),
(180, 104, 132, 4, 'Good landing page template. Clean code and nice design.', '2025-07-20 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `seller_analytics`
--

CREATE TABLE `seller_analytics` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `views` int(11) DEFAULT 0,
  `orders` int(11) DEFAULT 0,
  `revenue` decimal(10,2) DEFAULT 0.00,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_analytics`
--

INSERT INTO `seller_analytics` (`id`, `seller_id`, `date`, `views`, `orders`, `revenue`, `conversion_rate`, `created_at`) VALUES
(1, 44, '2025-08-04', 125, 3, 169.97, 2.40, '2025-08-04 18:41:26'),
(2, 44, '2025-08-03', 98, 2, 89.98, 2.00, '2025-08-04 18:41:26'),
(3, 44, '2025-08-02', 112, 1, 49.99, 0.90, '2025-08-04 18:41:26'),
(4, 44, '2025-08-01', 156, 4, 219.96, 2.60, '2025-08-04 18:41:26'),
(5, 44, '2025-07-31', 89, 2, 99.98, 2.20, '2025-08-04 18:41:26'),
(6, 44, '2025-07-30', 134, 3, 149.97, 2.20, '2025-08-04 18:41:26'),
(7, 44, '2025-07-29', 167, 5, 259.95, 3.00, '2025-08-04 18:41:26');

-- --------------------------------------------------------

--
-- Table structure for table `seller_earnings`
--

CREATE TABLE `seller_earnings` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `commission_rate` decimal(5,2) DEFAULT 15.00,
  `commission_amount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','available','withdrawn') DEFAULT 'pending',
  `available_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_earnings`
--

INSERT INTO `seller_earnings` (`id`, `seller_id`, `order_id`, `template_id`, `gross_amount`, `commission_rate`, `commission_amount`, `net_amount`, `status`, `available_date`, `created_at`) VALUES
(1, 44, 32, 100, 42.49, 15.00, 7.51, 35.98, 'available', '2025-08-01', '2025-08-04 18:41:26'),
(2, 44, 33, 101, 67.99, 15.00, 12.00, 55.99, 'available', '2025-07-28', '2025-08-04 18:41:26'),
(3, 44, 34, 102, 33.99, 15.00, 5.99, 28.00, 'available', '2025-07-25', '2025-08-04 18:41:26'),
(4, 44, 35, 103, 50.49, 15.00, 8.99, 41.50, 'available', '2025-07-22', '2025-08-04 18:41:26'),
(5, 44, 36, 104, 25.49, 15.00, 4.50, 20.99, 'available', '2025-07-20', '2025-08-04 18:41:26'),
(93, 44, 34, 100, 49.99, 15.00, 7.50, 42.49, 'pending', '2025-03-29', '2025-08-07 06:17:29'),
(94, 44, 34, 100, 49.99, 15.00, 7.50, 42.49, 'withdrawn', '2025-02-19', '2025-08-07 06:17:29'),
(95, 44, 32, 100, 49.99, 15.00, 7.50, 42.49, 'available', '2025-05-26', '2025-08-07 06:17:29'),
(96, 44, 33, 100, 49.99, 15.00, 7.50, 42.49, 'withdrawn', '2025-05-25', '2025-08-07 06:17:29'),
(97, 44, 34, 100, 49.99, 15.00, 7.50, 42.49, 'pending', '2025-08-02', '2025-08-07 06:17:29'),
(98, 44, 32, 100, 49.99, 15.00, 7.50, 42.49, 'available', '2025-03-07', '2025-08-07 06:17:29'),
(99, 44, 33, 100, 49.99, 15.00, 7.50, 42.49, 'withdrawn', '2025-02-28', '2025-08-07 06:17:29'),
(100, 44, 33, 100, 49.99, 15.00, 7.50, 42.49, 'withdrawn', '2025-02-09', '2025-08-07 06:17:29'),
(101, 44, 34, 100, 49.99, 15.00, 7.50, 42.49, 'pending', '2025-04-21', '2025-08-07 06:17:29'),
(102, 44, 36, 100, 49.99, 15.00, 7.50, 42.49, 'available', '2025-03-21', '2025-08-07 06:17:29'),
(103, 44, 35, 101, 79.99, 15.00, 12.00, 67.99, 'withdrawn', '2025-03-05', '2025-08-07 06:17:29'),
(104, 44, 35, 101, 79.99, 15.00, 12.00, 67.99, 'pending', '2025-04-05', '2025-08-07 06:17:29'),
(105, 44, 35, 101, 79.99, 15.00, 12.00, 67.99, 'available', '2025-03-18', '2025-08-07 06:17:29'),
(106, 44, 35, 101, 79.99, 15.00, 12.00, 67.99, 'withdrawn', '2025-03-17', '2025-08-07 06:17:29'),
(107, 44, 35, 101, 79.99, 15.00, 12.00, 67.99, 'withdrawn', '2025-06-14', '2025-08-07 06:17:29'),
(108, 44, 34, 101, 79.99, 15.00, 12.00, 67.99, 'withdrawn', '2025-04-22', '2025-08-07 06:17:29'),
(109, 44, 34, 101, 79.99, 15.00, 12.00, 67.99, 'withdrawn', '2025-06-17', '2025-08-07 06:17:29'),
(110, 44, 36, 102, 39.99, 15.00, 6.00, 33.99, 'withdrawn', '2025-05-04', '2025-08-07 06:17:29'),
(111, 44, 32, 102, 39.99, 15.00, 6.00, 33.99, 'available', '2025-02-28', '2025-08-07 06:17:29'),
(112, 44, 35, 102, 39.99, 15.00, 6.00, 33.99, 'withdrawn', '2025-02-15', '2025-08-07 06:17:29'),
(113, 44, 32, 102, 39.99, 15.00, 6.00, 33.99, 'available', '2025-07-02', '2025-08-07 06:17:29'),
(114, 44, 36, 102, 39.99, 15.00, 6.00, 33.99, 'available', '2025-07-10', '2025-08-07 06:17:29'),
(115, 44, 33, 102, 39.99, 15.00, 6.00, 33.99, 'pending', '2025-03-27', '2025-08-07 06:17:29'),
(116, 44, 35, 102, 39.99, 15.00, 6.00, 33.99, 'withdrawn', '2025-04-18', '2025-08-07 06:17:29'),
(117, 44, 33, 102, 39.99, 15.00, 6.00, 33.99, 'available', '2025-03-14', '2025-08-07 06:17:29'),
(118, 44, 36, 102, 39.99, 15.00, 6.00, 33.99, 'withdrawn', '2025-04-13', '2025-08-07 06:17:29'),
(119, 44, 32, 102, 39.99, 15.00, 6.00, 33.99, 'available', '2025-07-11', '2025-08-07 06:17:29'),
(120, 44, 33, 103, 59.99, 15.00, 9.00, 50.99, 'available', '2025-03-07', '2025-08-07 06:17:29'),
(121, 44, 33, 103, 59.99, 15.00, 9.00, 50.99, 'withdrawn', '2025-06-03', '2025-08-07 06:17:29'),
(122, 44, 34, 103, 59.99, 15.00, 9.00, 50.99, 'available', '2025-04-07', '2025-08-07 06:17:29'),
(123, 44, 33, 103, 59.99, 15.00, 9.00, 50.99, 'available', '2025-03-07', '2025-08-07 06:17:29'),
(124, 44, 36, 103, 59.99, 15.00, 9.00, 50.99, 'withdrawn', '2025-03-01', '2025-08-07 06:17:29'),
(125, 44, 35, 103, 59.99, 15.00, 9.00, 50.99, 'withdrawn', '2025-06-08', '2025-08-07 06:17:29'),
(126, 44, 35, 103, 59.99, 15.00, 9.00, 50.99, 'available', '2025-05-14', '2025-08-07 06:17:29'),
(127, 44, 35, 103, 59.99, 15.00, 9.00, 50.99, 'available', '2025-04-05', '2025-08-07 06:17:29'),
(128, 44, 35, 103, 59.99, 15.00, 9.00, 50.99, 'available', '2025-05-29', '2025-08-07 06:17:29'),
(129, 44, 32, 103, 59.99, 15.00, 9.00, 50.99, 'withdrawn', '2025-02-08', '2025-08-07 06:17:29'),
(130, 44, 32, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-05-27', '2025-08-07 06:17:29'),
(131, 44, 35, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-06-10', '2025-08-07 06:17:29'),
(132, 44, 33, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-05-16', '2025-08-07 06:17:29'),
(133, 44, 33, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-02-23', '2025-08-07 06:17:29'),
(134, 44, 33, 104, 29.99, 15.00, 4.50, 25.49, 'pending', '2025-07-26', '2025-08-07 06:17:29'),
(135, 44, 35, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-02-16', '2025-08-07 06:17:29'),
(136, 44, 35, 104, 29.99, 15.00, 4.50, 25.49, 'withdrawn', '2025-07-13', '2025-08-07 06:17:29'),
(137, 44, 36, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-05-21', '2025-08-07 06:17:29'),
(138, 44, 33, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-06-23', '2025-08-07 06:17:29'),
(139, 44, 36, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-06-19', '2025-08-07 06:17:29'),
(140, 44, 32, 104, 29.99, 15.00, 4.50, 25.49, 'withdrawn', '2025-06-11', '2025-08-07 06:17:29'),
(141, 44, 34, 104, 29.99, 15.00, 4.50, 25.49, 'available', '2025-03-08', '2025-08-07 06:17:29'),
(142, 44, 34, 104, 29.99, 15.00, 4.50, 25.49, 'withdrawn', '2025-03-14', '2025-08-07 06:17:29'),
(143, 44, 32, 115, 59.00, 15.00, 8.85, 50.15, 'available', '2025-02-25', '2025-08-07 06:17:29'),
(144, 44, 33, 115, 59.00, 15.00, 8.85, 50.15, 'withdrawn', '2025-07-30', '2025-08-07 06:17:29'),
(145, 44, 34, 115, 59.00, 15.00, 8.85, 50.15, 'withdrawn', '2025-05-19', '2025-08-07 06:17:29'),
(146, 44, 32, 115, 59.00, 15.00, 8.85, 50.15, 'available', '2025-04-07', '2025-08-07 06:17:29'),
(147, 44, 36, 115, 59.00, 15.00, 8.85, 50.15, 'pending', '2025-04-17', '2025-08-07 06:17:29'),
(148, 44, 36, 116, 89.00, 15.00, 13.35, 75.65, 'available', '2025-03-16', '2025-08-07 06:17:29'),
(149, 44, 34, 116, 89.00, 15.00, 13.35, 75.65, 'available', '2025-02-25', '2025-08-07 06:17:29'),
(150, 44, 32, 116, 89.00, 15.00, 13.35, 75.65, 'withdrawn', '2025-03-31', '2025-08-07 06:17:29'),
(151, 44, 32, 116, 89.00, 15.00, 13.35, 75.65, 'available', '2025-05-04', '2025-08-07 06:17:29'),
(152, 44, 34, 116, 89.00, 15.00, 13.35, 75.65, 'available', '2025-03-22', '2025-08-07 06:17:29'),
(153, 44, 32, 116, 89.00, 15.00, 13.35, 75.65, 'withdrawn', '2025-05-18', '2025-08-07 06:17:29'),
(154, 44, 35, 116, 89.00, 15.00, 13.35, 75.65, 'available', '2025-08-05', '2025-08-07 06:17:29'),
(155, 44, 32, 116, 89.00, 15.00, 13.35, 75.65, 'available', '2025-03-21', '2025-08-07 06:17:29'),
(156, 44, 34, 116, 89.00, 15.00, 13.35, 75.65, 'withdrawn', '2025-03-02', '2025-08-07 06:17:29'),
(157, 44, 36, 116, 89.00, 15.00, 13.35, 75.65, 'withdrawn', '2025-06-11', '2025-08-07 06:17:29'),
(158, 44, 34, 116, 89.00, 15.00, 13.35, 75.65, 'pending', '2025-02-21', '2025-08-07 06:17:29'),
(159, 44, 35, 116, 89.00, 15.00, 13.35, 75.65, 'available', '2025-05-14', '2025-08-07 06:17:29'),
(160, 44, 36, 117, 109.00, 15.00, 16.35, 92.65, 'withdrawn', '2025-05-22', '2025-08-07 06:17:29'),
(161, 44, 34, 117, 109.00, 15.00, 16.35, 92.65, 'pending', '2025-05-14', '2025-08-07 06:17:29'),
(162, 44, 35, 117, 109.00, 15.00, 16.35, 92.65, 'pending', '2025-05-30', '2025-08-07 06:17:29'),
(163, 44, 32, 117, 109.00, 15.00, 16.35, 92.65, 'pending', '2025-05-08', '2025-08-07 06:17:29'),
(164, 44, 35, 117, 109.00, 15.00, 16.35, 92.65, 'withdrawn', '2025-06-21', '2025-08-07 06:17:29'),
(165, 44, 34, 117, 109.00, 15.00, 16.35, 92.65, 'pending', '2025-08-06', '2025-08-07 06:17:29'),
(166, 44, 36, 117, 109.00, 15.00, 16.35, 92.65, 'available', '2025-03-04', '2025-08-07 06:17:29'),
(167, 44, 33, 117, 109.00, 15.00, 16.35, 92.65, 'available', '2025-03-15', '2025-08-07 06:17:29'),
(168, 44, 33, 117, 109.00, 15.00, 16.35, 92.65, 'withdrawn', '2025-02-20', '2025-08-07 06:17:29'),
(169, 44, 32, 117, 109.00, 15.00, 16.35, 92.65, 'withdrawn', '2025-07-24', '2025-08-07 06:17:29'),
(170, 44, 34, 117, 109.00, 15.00, 16.35, 92.65, 'available', '2025-05-22', '2025-08-07 06:17:29'),
(171, 44, 33, 117, 109.00, 15.00, 16.35, 92.65, 'available', '2025-02-21', '2025-08-07 06:17:29'),
(172, 44, 32, 117, 109.00, 15.00, 16.35, 92.65, 'withdrawn', '2025-05-03', '2025-08-07 06:17:29'),
(173, 44, 35, 117, 109.00, 15.00, 16.35, 92.65, 'available', '2025-05-10', '2025-08-07 06:17:29'),
(174, 44, 35, 120, 119.00, 15.00, 17.85, 101.15, 'available', '2025-07-07', '2025-08-07 06:17:29'),
(175, 44, 33, 120, 119.00, 15.00, 17.85, 101.15, 'pending', '2025-03-12', '2025-08-07 06:17:29'),
(176, 44, 35, 120, 119.00, 15.00, 17.85, 101.15, 'available', '2025-04-03', '2025-08-07 06:17:29'),
(177, 44, 36, 120, 119.00, 15.00, 17.85, 101.15, 'withdrawn', '2025-02-18', '2025-08-07 06:17:29'),
(178, 44, 35, 120, 119.00, 15.00, 17.85, 101.15, 'withdrawn', '2025-05-09', '2025-08-07 06:17:29'),
(179, 44, 33, 120, 119.00, 15.00, 17.85, 101.15, 'withdrawn', '2025-04-16', '2025-08-07 06:17:29'),
(180, 44, 34, 120, 119.00, 15.00, 17.85, 101.15, 'withdrawn', '2025-05-31', '2025-08-07 06:17:29'),
(181, 44, 34, 120, 119.00, 15.00, 17.85, 101.15, 'withdrawn', '2025-05-30', '2025-08-07 06:17:29'),
(182, 44, 32, 120, 119.00, 15.00, 17.85, 101.15, 'available', '2025-06-18', '2025-08-07 06:17:29'),
(183, 44, 35, 120, 119.00, 15.00, 17.85, 101.15, 'withdrawn', '2025-07-08', '2025-08-07 06:17:29'),
(184, 44, 33, 120, 119.00, 15.00, 17.85, 101.15, 'available', '2025-04-15', '2025-08-07 06:17:29'),
(185, 44, 36, 120, 119.00, 15.00, 17.85, 101.15, 'withdrawn', '2025-07-11', '2025-08-07 06:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `seller_messages`
--

CREATE TABLE `seller_messages` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('inquiry','order','review','system') DEFAULT 'inquiry',
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `order_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_profiles`
--

CREATE TABLE `seller_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `profile_banner` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `business_type` enum('individual','company','agency') DEFAULT 'individual',
  `total_sales` decimal(12,2) DEFAULT 0.00,
  `total_orders` int(11) DEFAULT 0,
  `response_time` int(11) DEFAULT 24,
  `online_status` tinyint(1) DEFAULT 0,
  `last_active` timestamp NOT NULL DEFAULT current_timestamp(),
  `account_balance` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_profiles`
--

INSERT INTO `seller_profiles` (`id`, `user_id`, `business_name`, `description`, `profile_banner`, `website_url`, `location`, `phone`, `business_type`, `total_sales`, `total_orders`, `response_time`, `online_status`, `last_active`, `account_balance`, `created_at`, `updated_at`) VALUES
(10, 44, 'TechCraft Solutions', 'Professional web development and digital solutions provider with 5+ years experience', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800', 'https://changdigital.com', 'Ho Chi Minh City, Vietnam', '+84 123 456 789', 'company', 28450.50, 156, 2, 1, '2025-08-04 02:31:45', 2850.75, '2025-08-04 02:31:45', '2025-08-04 18:41:26'),
(11, 45, 'Sarah Creative Studio', 'Premium graphic design, branding, and UI/UX services for modern businesses', 'https://images.unsplash.com/photo-1558655146-d09347e92766?w=800', 'https://sarahcreative.com', 'New York, NY', '+1-555-0456', 'individual', 12450.75, 35, 4, 1, '2025-08-04 02:31:45', 1890.40, '2025-08-04 02:31:45', '2025-08-04 02:31:45'),
(12, 46, 'Mike Code Factory', 'Full-stack development, mobile apps, and custom software solutions', 'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800', 'https://mikecodefactory.com', 'Austin, TX', '+1-555-0789', 'individual', 23650.00, 42, 6, 0, '2025-08-04 02:31:45', 3547.80, '2025-08-04 02:31:45', '2025-08-04 02:31:45'),
(13, 101, 'Sarah\'s Design Studio', 'Professional web designer with 8+ years of experience in creating stunning websites and templates.', NULL, 'https://sarahdesign.portfolio.com', 'San Francisco, CA', '+1-555-0101', 'individual', 28500.00, 125, 4, 1, '2025-08-04 03:29:05', 2850.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05'),
(14, 102, 'Tech Solutions by Mike', 'Full-stack developer specializing in modern web applications and mobile app development.', NULL, 'https://miketech.dev', 'Austin, TX', '+1-555-0102', 'company', 21200.00, 89, 6, 1, '2025-08-04 03:29:05', 2120.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05'),
(15, 103, 'Emma\'s Creative Hub', 'Creative designer focused on branding, logo design, and digital marketing materials.', NULL, 'https://emmacreative.com', 'New York, NY', '+1-555-0103', 'individual', 15800.00, 67, 8, 0, '2025-08-04 03:29:05', 1580.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05'),
(16, 104, 'Alex Digital Marketing', 'Digital marketing expert helping businesses grow their online presence and sales.', NULL, 'https://alexmarketing.agency', 'Los Angeles, CA', '+1-555-0104', 'agency', 19600.00, 92, 3, 1, '2025-08-04 03:29:05', 1960.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05'),
(17, 105, 'Lisa\'s Code Factory', 'Backend developer with expertise in database design and API development.', NULL, 'https://lisacode.dev', 'Seattle, WA', '+1-555-0105', 'individual', 12800.00, 54, 12, 0, '2025-08-04 03:29:05', 1280.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05'),
(18, 106, 'Thompson Graphics', 'Graphic designer specializing in print and digital media, with a focus on modern aesthetics.', NULL, 'https://thompsongraphics.com', 'Chicago, IL', '+1-555-0106', 'individual', 18200.00, 75, 5, 1, '2025-08-04 03:29:05', 1820.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05'),
(19, 107, 'Maria\'s Web Templates', 'Template designer creating beautiful, responsive templates for various industries.', NULL, 'https://mariaweb.templates', 'Miami, FL', '+1-555-0107', 'individual', 14500.00, 62, 7, 1, '2025-08-04 03:29:05', 1450.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05'),
(20, 108, 'Wilson Development', 'Software developer creating custom solutions and enterprise applications.', NULL, 'https://wilsondev.solutions', 'Boston, MA', '+1-555-0108', 'company', 26400.00, 110, 2, 1, '2025-08-04 03:29:05', 2640.00, '2025-08-04 03:29:05', '2025-08-04 03:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `seller_services`
--

CREATE TABLE `seller_services` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `delivery_time_days` int(11) DEFAULT 7,
  `revisions_included` int(11) DEFAULT 3,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `requirements` text DEFAULT NULL,
  `gallery_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery_images`)),
  `video_url` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `views_count` int(11) DEFAULT 0,
  `orders_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_urgent` tinyint(1) DEFAULT 0,
  `status` enum('draft','pending','active','paused','rejected') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_services`
--

INSERT INTO `seller_services` (`id`, `seller_id`, `title`, `slug`, `description`, `short_description`, `category_id`, `subcategory`, `price`, `delivery_time_days`, `revisions_included`, `features`, `requirements`, `gallery_images`, `video_url`, `tags`, `views_count`, `orders_count`, `rating`, `reviews_count`, `is_featured`, `is_urgent`, `status`, `created_at`, `updated_at`) VALUES
(25, 44, 'Custom Website Development', 'custom-website-development-1754332886', 'Full-stack web development service from design to deployment', 'Professional web development from concept to deployment', NULL, NULL, 299.99, 14, 3, NULL, NULL, NULL, NULL, NULL, 114, 27, 4.20, 3, 0, 0, 'active', '2025-08-04 18:41:26', '2025-08-04 18:41:26'),
(26, 44, 'WordPress Customization', 'wordpress-customization-1754332886', 'Professional WordPress theme customization and setup', 'Custom WordPress solutions for your business', NULL, NULL, 149.99, 7, 3, NULL, NULL, NULL, NULL, NULL, 153, 42, 4.70, 11, 0, 0, 'active', '2025-08-04 18:41:26', '2025-08-04 18:41:26'),
(27, 44, 'E-commerce Setup', 'ecommerce-setup-1754332886', 'Complete e-commerce store setup with payment integration', 'Full e-commerce solution with secure payments', NULL, NULL, 399.99, 21, 3, NULL, NULL, NULL, NULL, NULL, 142, 18, 4.40, 3, 0, 0, 'active', '2025-08-04 18:41:26', '2025-08-04 18:41:26'),
(28, 44, 'SEO Optimization Service', 'seo-optimization-service-1754332886', 'Comprehensive SEO audit and optimization for better rankings', 'Boost your search engine rankings', NULL, NULL, 199.99, 10, 3, NULL, NULL, NULL, NULL, NULL, 174, 8, 4.50, 10, 0, 0, 'active', '2025-08-04 18:41:26', '2025-08-04 18:41:26');

-- --------------------------------------------------------

--
-- Table structure for table `seller_stats`
--

CREATE TABLE `seller_stats` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `total_earnings` decimal(10,2) DEFAULT 0.00,
  `pending_earnings` decimal(10,2) DEFAULT 0.00,
  `paid_earnings` decimal(10,2) DEFAULT 0.00,
  `total_sales` int(11) DEFAULT 0,
  `total_views` int(11) DEFAULT 0,
  `avg_rating` decimal(3,2) DEFAULT 0.00,
  `total_products` int(11) DEFAULT 0,
  `last_sale_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `features` text DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `delivery_time` int(11) DEFAULT 7,
  `revisions` int(11) DEFAULT 3,
  `technology` varchar(100) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `views_count` int(11) DEFAULT 0,
  `orders_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `status` enum('draft','pending','approved','rejected') DEFAULT 'approved',
  `sort_order` int(11) DEFAULT 0,
  `slug` varchar(255) DEFAULT NULL,
  `seller_name` varchar(100) DEFAULT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `avg_rating` decimal(3,2) DEFAULT 4.80,
  `review_count` int(11) DEFAULT 0,
  `category_slug` varchar(50) DEFAULT 'general',
  `starting_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `seller_id`, `category_id`, `price`, `icon`, `is_active`, `created_at`, `updated_at`, `features`, `preview_image`, `demo_url`, `delivery_time`, `revisions`, `technology`, `tags`, `views_count`, `orders_count`, `rating`, `reviews_count`, `is_featured`, `status`, `sort_order`, `slug`, `seller_name`, `profile_image`, `avg_rating`, `review_count`, `category_slug`, `starting_price`) VALUES
(50, 'Custom Website Design', 'Professional custom website design tailored to your brand. We create unique, responsive designs that convert visitors into customers.', 44, 10, 299.00, NULL, 1, '2025-08-07 01:16:05', '2025-08-06 20:25:46', 'Custom design mockups,Responsive layout,SEO optimized,Cross-browser compatible', 'orbix/products/saxgiammabfmlqmadm6m', 'https://demo.example.com/custom-design', 7, 3, NULL, '[\"custom design\",\"responsive\",\"modern\",\"professional\"]', 0, 0, 0.00, 0, 0, 'approved', 0, 'custom-website-design', NULL, NULL, 4.80, 0, 'general', NULL),
(51, 'E-commerce Store Setup', 'Complete e-commerce store setup with payment integration, inventory management, and mobile-optimized checkout process.', 44, 10, 599.00, NULL, 1, '2025-08-07 01:16:05', '2025-08-06 20:26:27', 'Payment gateway integration,Product catalog setup,Order management system,Mobile responsive design', 'orbix/products/yctyfgxasxtdywbqr5tj', 'https://demo.example.com/ecommerce', 14, 3, NULL, '[\"ecommerce\",\"online store\",\"payment integration\",\"shopping cart\"]', 0, 0, 0.00, 0, 0, 'approved', 0, 'ecommerce-store-setup', NULL, NULL, 4.80, 0, 'general', NULL),
(52, 'SEO Optimization Package', 'Comprehensive SEO optimization to improve your website ranking on Google and other search engines.', 44, 10, 199.00, NULL, 1, '2025-08-07 01:16:05', '2025-08-06 20:27:34', 'Keyword research and analysis,On-page SEO optimization,Meta tags optimization,Technical SEO audit', 'orbix/products/t35dqjpnrh14ws9ctuuc', NULL, 10, 3, NULL, '[\"SEO\",\"optimization\",\"google ranking\",\"search engine\"]', 0, 0, 0.00, 0, 0, 'approved', 0, 'seo-optimization-package', NULL, NULL, 4.80, 0, 'general', NULL),
(53, 'Social Media Marketing', 'Strategic social media marketing campaigns to boost your brand presence and engagement across all platforms.', 44, 10, 249.00, NULL, 1, '2025-08-07 01:16:05', '2025-08-06 20:29:56', 'Content strategy development,Social media post creation,Audience engagement,Performance analytics', 'orbix/products/wmt2zvplnzc1yuud1m6t', NULL, 30, 3, NULL, '[\"social media\",\"marketing\",\"brand awareness\",\"engagement\"]', 0, 0, 0.00, 0, 0, 'approved', 0, 'social-media-marketing', NULL, NULL, 4.80, 0, 'general', NULL),
(54, 'Website Content Writing', 'Professional website content writing services including homepage, about page, service descriptions, and blog posts.', 44, 8, 149.00, NULL, 1, '2025-08-07 01:16:05', '2025-08-06 20:30:47', 'SEO-optimized content,Engaging copywriting,Brand voice consistency,Unlimited revisions', 'orbix/products/phhynscfmx5zv30ysinx', NULL, 5, 3, NULL, '[\"content writing\",\"copywriting\",\"website content\",\"blog posts\"]', 0, 0, 0.00, 0, 0, 'approved', 0, 'website-content-writing', NULL, NULL, 4.80, 0, 'general', NULL),
(57, 'Web Hosting & Security', 'Premium web hosting with SSL certificates, daily backups, and advanced security features for your website.', 44, 7, 79.00, NULL, 1, '2025-08-07 01:16:05', '2025-08-06 20:32:14', 'Fast SSD hosting,SSL certificate included,Daily automated backups,DDoS protection', 'orbix/products/ae0lqpkpckyputnjsoik', NULL, 1, 3, NULL, '[\"web hosting\",\"security\",\"SSL\",\"backups\"]', 0, 0, 0.00, 0, 0, 'approved', 0, 'web-hosting-security', NULL, NULL, 4.80, 0, 'general', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

CREATE TABLE `service_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`id`, `name`, `slug`, `description`, `icon`, `is_active`, `sort_order`, `created_at`) VALUES
(7, 'Design & Development', 'design-development', 'Web design and development services', 'ri-code-line', 1, 1, '2025-08-02 22:38:51'),
(8, 'Digital Marketing', 'digital-marketing', 'SEO, social media and marketing services', 'ri-megaphone-line', 1, 2, '2025-08-02 22:38:51'),
(9, 'Content & Writing', 'content-writing', 'Content creation and copywriting services', 'ri-quill-pen-line', 1, 3, '2025-08-02 22:38:51'),
(10, 'Business Services', 'business-services', 'Business consulting and management services', 'ri-briefcase-line', 1, 4, '2025-08-02 22:38:51'),
(11, 'Technical Support', 'technical-support', 'Technical support and maintenance services', 'ri-tools-line', 1, 5, '2025-08-02 22:38:51'),
(12, 'Hosting & Security', 'hosting-security', 'Web hosting and security services', 'ri-shield-line', 1, 6, '2025-08-02 22:38:51'),
(13, 'Web Design', 'web-design', 'Custom website design services', 'ri-palette-line', 1, 1, '2025-08-07 05:41:22'),
(14, 'Development', 'development', 'Website development and coding services', 'ri-code-line', 1, 2, '2025-08-07 05:41:22'),
(15, 'SEO & Marketing', 'seo-marketing', 'Search engine optimization and digital marketing', 'ri-search-line', 1, 3, '2025-08-07 05:41:22'),
(16, 'Content Creation', 'content-creation', 'Content writing and creation services', 'ri-edit-line', 1, 4, '2025-08-07 05:41:22'),
(17, 'Consulting', 'consulting', 'Business and technical consulting', 'ri-user-star-line', 1, 5, '2025-08-07 05:41:22');

-- --------------------------------------------------------

--
-- Table structure for table `service_images`
--

CREATE TABLE `service_images` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_orders`
--

CREATE TABLE `service_orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `service_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `requirements` text DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled','disputed') DEFAULT 'pending',
  `payment_status` enum('pending','paid','released','refunded') DEFAULT 'pending',
  `delivery_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`delivery_files`)),
  `buyer_review_rating` int(11) DEFAULT NULL,
  `buyer_review_text` text DEFAULT NULL,
  `seller_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_orders`
--

INSERT INTO `service_orders` (`id`, `order_number`, `service_id`, `buyer_id`, `seller_id`, `price`, `quantity`, `total_amount`, `requirements`, `delivery_date`, `status`, `payment_status`, `delivery_files`, `buyer_review_rating`, `buyer_review_text`, `seller_response`, `created_at`, `updated_at`, `completed_at`) VALUES
(28, 'ORD-2025-1942', 25, 43, 44, 299.99, 1, 299.99, NULL, '2025-08-17', 'in_progress', 'paid', NULL, NULL, NULL, NULL, '2025-08-02 17:00:00', '2025-08-04 18:41:26', NULL),
(29, 'ORD-2025-2982', 26, 129, 44, 149.99, 1, 149.99, NULL, '2025-08-04', 'completed', 'paid', NULL, NULL, NULL, NULL, '2025-07-27 17:00:00', '2025-08-04 18:41:26', NULL),
(30, 'ORD-2025-3070', 27, 130, 44, 399.99, 1, 399.99, NULL, '2025-08-25', 'pending', 'paid', NULL, NULL, NULL, NULL, '2025-08-03 17:00:00', '2025-08-04 18:41:26', NULL),
(31, 'ORD-2025-5541', 28, 131, 44, 199.99, 1, 199.99, NULL, '2025-08-04', 'completed', 'paid', NULL, NULL, NULL, NULL, '2025-07-24 17:00:00', '2025-08-04 18:41:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `service_reviews`
--

CREATE TABLE `service_reviews` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'general',
  `message` text NOT NULL,
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `download_file` varchar(255) DEFAULT NULL,
  `technology` varchar(100) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `downloads_count` int(11) DEFAULT 0,
  `views_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `status` enum('draft','pending','approved','rejected') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `title`, `slug`, `description`, `price`, `category_id`, `seller_id`, `preview_image`, `demo_url`, `download_file`, `technology`, `tags`, `downloads_count`, `views_count`, `rating`, `reviews_count`, `is_featured`, `status`, `created_at`, `updated_at`) VALUES
(100, 'Fashion Website', 'modern-business-website', 'Professional business website template with clean design', 49.99, 13, 44, 'orbix/products/ig9rtobrqrksalquisnp', '', NULL, 'html', '[\"web\"]', 135, 427, 4.50, 6, 0, 'approved', '2025-08-04 18:41:26', '2025-08-07 01:33:04'),
(101, 'Blog post', 'ecommerce-store-template', 'Complete online store solution with shopping cart', 79.99, 9, 44, 'orbix/products/puszkj0biesilixzps7c', '', NULL, 'html', '[\"web\"]', 121, 740, 4.30, 25, 0, 'approved', '2025-08-04 18:41:26', '2025-08-07 01:33:07'),
(102, 'Portfolio Website Template', 'portfolio-website-template', 'Creative portfolio template for designers and developers', 39.99, 13, 44, 'orbix/products/oxmxavalz9us1ywd0dub', NULL, NULL, 'Vue.js', NULL, 37, 574, 4.10, 14, 0, 'approved', '2025-08-04 18:41:26', '2025-08-07 12:48:21'),
(103, 'Restaurant Website Template', 'restaurant-website-template', 'Modern restaurant website with menu and booking system', 59.99, 13, 44, 'orbix/products/ac94psfghbtdqpea5xqt', NULL, NULL, 'HTML/CSS/JS', NULL, 138, 541, 4.30, 10, 1, 'approved', '2025-08-04 18:41:26', '2025-08-08 04:16:57'),
(104, 'Agency Landing Page', 'agency-landing-page', 'Professional agency landing page template', 29.99, 13, 44, 'orbix/products/wg5czxpunrwsaie1bxlp', NULL, NULL, 'HTML/CSS', NULL, 137, 299, 4.60, 10, 1, 'approved', '2025-08-04 18:41:26', '2025-08-07 18:56:52'),
(115, 'Digital Magazine Platform', 'digital-magazine-platform', 'Modern magazine and blog platform with multiple layouts, content management, and social features.', 59.00, 12, 44, 'orbix/products/zzf8ycct8t19zkhxyyzs', 'https://demo.example.com/digital-magazine', NULL, 'WordPress', '[\"magazine\",\"blog\",\"news\",\"wordpress\",\"content\"]', 26, 352, 5.00, 9, 1, 'approved', '2025-08-07 01:16:05', '2025-08-07 01:33:21'),
(116, 'Premium Fitness Club Template', 'premium-fitness-club-template', 'Professional fitness club template with class schedules, trainer profiles, and membership management.', 89.00, 12, 44, 'orbix/products/p3iy60jzatmiss48qhzt', 'https://demo.example.com/premium-fitness', NULL, 'HTML/CSS', '[\"fitness\",\"gym\",\"health\",\"sports\",\"membership\"]', 9, 946, 4.90, 9, 1, 'approved', '2025-08-07 01:16:05', '2025-08-08 01:52:32'),
(117, 'Luxury Real Estate Platform', 'luxury-real-estate-platform', 'High-end real estate template with advanced property search, virtual tours, and agent management.', 109.00, 12, 44, 'orbix/products/dmrdlbz0lbovdgjps15y', 'https://demo.example.com/luxury-real-estate', NULL, 'React', '[\"real estate\",\"luxury\",\"property\",\"react\",\"professional\"]', 39, 737, 5.00, 4, 1, 'approved', '2025-08-07 01:16:05', '2025-08-07 01:33:16'),
(120, 'Travel Agency Portal', 'travel-agency-portal', 'Complete travel agency website with booking system, tour packages, and destination showcase.', 119.00, 12, 44, 'orbix/products/bump9rydogpfz2c93yar', 'https://demo.example.com/travel-agency', NULL, 'React', '[\"travel\",\"agency\",\"booking\",\"tourism\",\"react\"]', 34, 595, 4.10, 8, 1, 'approved', '2025-08-07 01:16:05', '2025-08-08 02:32:41');

-- --------------------------------------------------------

--
-- Table structure for table `template_images`
--

CREATE TABLE `template_images` (
  `id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `template_promotions`
--

CREATE TABLE `template_promotions` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `promotion_type` enum('featured','urgent','bestseller') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `cost` decimal(8,2) NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `testimonial` text NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `position`, `company`, `avatar_url`, `rating`, `testimonial`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Sarah Chen', 'CEO', 'Fashion Forward', 'https://images.unsplash.com/photo-1494790108755-2616b612b5c5?w=100&h=100&fit=crop&crop=face', 5, 'I was looking for a professional website solution for my fashion store and was lucky to discover their service. The modern 3D template gives customers an amazing shopping experience. Our online sales increased by 45% in just 2 months!', 1, 1, '2025-08-02 00:55:46', '2025-08-02 00:55:46'),
(2, 'David Rodriguez', 'Founder', 'TechVision', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face', 5, 'Their technical support team is truly outstanding! Whenever we encounter issues, they are always ready to resolve them quickly. The dashboard template helps us manage data efficiently with an intuitive interface. Absolutely worth every penny!', 1, 1, '2025-08-02 00:55:46', '2025-08-02 00:55:46'),
(3, 'Emily Johnson', 'Photographer', 'Creative Studios', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face', 5, 'The 3D portfolio template helped me showcase my photography work impressively. Clients constantly praise my website, and this has helped me secure more projects. Thank you for creating such an amazing product!', 1, 1, '2025-08-02 00:55:46', '2025-08-02 00:55:46'),
(4, 'Michael Thompson', 'Marketing Director', 'Digital Solutions', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face', 5, 'The landing page templates are conversion-focused and beautifully designed. Our conversion rates improved by 60% after implementing their template. The customer support is exceptional too!', 0, 1, '2025-08-02 00:55:46', '2025-08-02 00:55:46'),
(5, 'Lisa Wang', 'E-commerce Manager', 'Online Retail Co', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&fit=crop&crop=face', 5, 'The e-commerce template is feature-rich and user-friendly. Our customers love the shopping experience, and our sales have grown significantly. Highly recommended for any online business!', 0, 1, '2025-08-02 00:55:46', '2025-08-02 00:55:46'),
(6, 'James Wilson', 'Startup Founder', 'Innovation Hub', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&crop=face', 4, 'Great templates with modern design. The pricing is reasonable and the quality is top-notch. Perfect for startups looking to establish a professional online presence quickly.', 0, 1, '2025-08-02 00:55:46', '2025-08-02 00:55:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `user_type` enum('buyer','seller','admin') DEFAULT 'buyer',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `google_id` varchar(255) DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `bio` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `social_media` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_media`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `profile_image`, `user_type`, `is_verified`, `created_at`, `updated_at`, `google_id`, `remember_token`, `email_verified`, `last_login`, `password`, `is_active`, `bio`, `location`, `website`, `skills`, `phone`, `company`, `social_media`) VALUES
(26, '', 'alex@example.com', '$2y$12$ZADFlEYIbncQOxm0wKjjgOtADflxuS6TpZT60fU/H6gAH9LPGxcfm', 'Alex', 'Johnson', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face', 'seller', 0, '2025-08-01 16:17:09', '2025-08-01 16:17:09', NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'changaffiliate1', 'changaffiliate1@gmail.com', '$2y$10$aLe2e9SUHImMcJJNaq2meeuHAPkSHtv2QcU1HGYhaQReSv6vL8D6S', 'Chang', 'Nguyen', NULL, 'buyer', 0, '2025-08-02 18:04:04', '2025-08-02 18:04:04', NULL, '97d1a771d74b0f566b78bf2a88eb092f00514130770543c649dcf3e503a8c8d7', 0, NULL, '$2y$10$aLe2e9SUHImMcJJNaq2meeuHAPkSHtv2QcU1HGYhaQReSv6vL8D6S', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'nguyenchangxinxin2002', 'nguyenchangxinxin2002@gmail.com', '$2y$10$n20Uhqi1/HSGT9ObQ0xCzuX0vy86dAX0Is5CYI04bRqcORph4kZWK', 'Chang', 'Nguyen', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face', 'seller', 0, '2025-08-02 18:09:11', '2025-08-07 00:27:50', NULL, NULL, 1, '2025-08-07 00:27:50', '$2y$10$n20Uhqi1/HSGT9ObQ0xCzuX0vy86dAX0Is5CYI04bRqcORph4kZWK', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'sarahchen', 'sarah.chen@example.com', '$2y$12$LWKPTts1zpEzIm5hN874X.L8bzT/IIvst.adF1Uc2vqpoV73nI4hu', 'Sarah', 'Chen', NULL, 'seller', 0, '2025-08-02 21:00:53', '2025-08-02 21:00:53', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'mikero', 'mike.rodriguez@example.com', '$2y$12$gj/po7DESdjf4NM0ncN1cuLlx2EwtpltjyuyIc7L9CupXzF.oRp06', 'Mike', 'Rodriguez', NULL, 'seller', 0, '2025-08-02 21:00:53', '2025-08-02 21:00:53', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'emmaw', 'emma.wilson@example.com', '$2y$12$krUJ0z/UPPGH1YRCQGYHoeMNqsIQ03eNqQGKWINZHhYRQ7bSWC2qq', 'Emma', 'Wilson', NULL, 'seller', 0, '2025-08-02 21:00:53', '2025-08-02 21:00:53', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'davidkim', 'david.kim@example.com', '$2y$12$CkSg1A1CKyUjtg1uYE0F3uRNKIOnfO5Oxyq7IJETZYNiYUh13SuPm', 'David', 'Kim', NULL, 'seller', 0, '2025-08-02 21:00:53', '2025-08-02 21:00:53', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'lisaa', 'lisa.anderson@example.com', '$2y$12$wZoMEdPvU67n8qvt4Rj0/OyYVtJcHXmmHfJ46RFNekgpP/gIKAreW', 'Lisa', 'Anderson', NULL, 'seller', 0, '2025-08-02 21:00:54', '2025-08-02 21:00:54', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'sarah_designer', 'sarah@orbixmarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Johnson', 'https://images.unsplash.com/photo-1494790108755-2616b25ad8be?w=150&h=150&fit=crop&crop=face', 'seller', 1, '2025-08-04 02:11:56', '2025-08-04 02:11:56', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'mike_dev', 'mike@orbixmarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Chen', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face', 'seller', 1, '2025-08-04 02:11:56', '2025-08-04 02:11:56', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'emma_creative', 'emma@orbixmarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma', 'Davis', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face', 'seller', 1, '2025-08-04 02:11:56', '2025-08-04 02:11:56', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'alex_coder', 'alex@orbixmarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex', 'Rodriguez', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face', 'seller', 1, '2025-08-04 02:11:56', '2025-08-04 02:11:56', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'john_seller', 'john.seller@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Smith', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150', 'seller', 1, '2025-08-04 02:29:08', '2025-08-04 02:29:08', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'sarah_design', 'sarah.design@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Johnson', 'https://images.unsplash.com/photo-1494790108755-2616b612b123?w=150', 'seller', 1, '2025-08-04 02:29:08', '2025-08-04 02:29:08', NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 'sarah_johnson', 'sarah.johnson@example.com', NULL, 'Sarah', 'Johnson', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 'mike_chen', 'mike.chen@example.com', NULL, 'Mike', 'Chen', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 'emma_davis', 'emma.davis@example.com', NULL, 'Emma', 'Davis', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 'alex_rodriguez', 'alex.rodriguez@example.com', NULL, 'Alex', 'Rodriguez', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 'lisa_wang', 'lisa.wang@example.com', NULL, 'Lisa', 'Wang', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 'david_thompson', 'david.thompson@example.com', NULL, 'David', 'Thompson', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 'maria_garcia', 'maria.garcia@example.com', NULL, 'Maria', 'Garcia', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 'james_wilson', 'james.wilson@example.com', NULL, 'James', 'Wilson', NULL, 'seller', 0, '2025-08-04 02:57:02', '2025-08-04 02:57:02', NULL, NULL, 0, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 'buyer1', 'buyer1@example.com', '$2y$12$r5boh3BJ.wqc54GHUQqX8efeDqQOeUlq5QNEYq5tQOpuvJVJ5RJsO', 'John', 'Smith', NULL, 'buyer', 0, '2025-08-04 18:41:25', '2025-08-04 18:41:25', NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 'buyer2', 'buyer2@example.com', '$2y$12$G/8S5P5y3fnt7BwnVXVS/OcqLkVrqpSmlw.WLCmGyzJ7QSUNXrpam', 'Emily', 'Johnson', NULL, 'buyer', 0, '2025-08-04 18:41:26', '2025-08-04 18:41:26', NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 'buyer3', 'buyer3@example.com', '$2y$12$JEpWM8lVWFQs0fu4tm6ULODdSkVQEqWdodkXZV3hSSdh3GS6mZ74e', 'Michael', 'Brown', NULL, 'buyer', 0, '2025-08-04 18:41:26', '2025-08-04 18:41:26', NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 'buyer4', 'buyer4@example.com', '$2y$12$j/pekRkEzKxgaTSWCUXhU.UlyoRA23DYnyKBfhYWYFFe4mwJz9502', 'Sarah', 'Davis', NULL, 'buyer', 0, '2025-08-04 18:41:26', '2025-08-04 18:41:26', NULL, NULL, 0, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('paypal','bank_transfer','wise') NOT NULL,
  `payment_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_details`)),
  `status` enum('pending','processing','completed','rejected') DEFAULT 'pending',
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawal_requests`
--

INSERT INTO `withdrawal_requests` (`id`, `seller_id`, `amount`, `payment_method`, `payment_details`, `status`, `processed_at`, `created_at`) VALUES
(1, 44, 500.00, 'bank_transfer', NULL, 'pending', NULL, '2025-08-03 02:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_template` (`user_id`,`template_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `fk_cart_service` (`service_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_domain` (`name`,`extension`),
  ADD KEY `idx_extension` (`extension`),
  ADD KEY `idx_available` (`is_available`);

--
-- Indexes for table `domain_extensions`
--
ALTER TABLE `domain_extensions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `extension` (`extension`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_template` (`user_id`,`template_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviews_template` (`template_id`),
  ADD KEY `idx_reviews_user` (`user_id`);

--
-- Indexes for table `seller_analytics`
--
ALTER TABLE `seller_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seller_date` (`seller_id`,`date`);

--
-- Indexes for table `seller_earnings`
--
ALTER TABLE `seller_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `seller_messages`
--
ALTER TABLE `seller_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_seller_messages_seller` (`seller_id`),
  ADD KEY `idx_seller_messages_status` (`status`),
  ADD KEY `idx_seller_messages_type` (`type`);

--
-- Indexes for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `seller_services`
--
ALTER TABLE `seller_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `seller_stats`
--
ALTER TABLE `seller_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seller_id` (`seller_id`),
  ADD KEY `idx_seller_stats` (`seller_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_services_seller` (`seller_id`),
  ADD KEY `idx_category_slug` (`category_slug`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `fk_services_category` (`category_id`);

--
-- Indexes for table `service_categories`
--
ALTER TABLE `service_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `service_images`
--
ALTER TABLE `service_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `service_orders`
--
ALTER TABLE `service_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `service_reviews`
--
ALTER TABLE `service_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_templates_category` (`category_id`),
  ADD KEY `idx_templates_seller` (`seller_id`),
  ADD KEY `idx_templates_status` (`status`),
  ADD KEY `idx_templates_featured` (`is_featured`);

--
-- Indexes for table `template_images`
--
ALTER TABLE `template_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `template_promotions`
--
ALTER TABLE `template_promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_google_id` (`google_id`),
  ADD KEY `idx_remember_token` (`remember_token`);

--
-- Indexes for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `domain_extensions`
--
ALTER TABLE `domain_extensions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `seller_analytics`
--
ALTER TABLE `seller_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seller_earnings`
--
ALTER TABLE `seller_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `seller_messages`
--
ALTER TABLE `seller_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `seller_services`
--
ALTER TABLE `seller_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `seller_stats`
--
ALTER TABLE `seller_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `service_categories`
--
ALTER TABLE `service_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `service_images`
--
ALTER TABLE `service_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_orders`
--
ALTER TABLE `service_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `service_reviews`
--
ALTER TABLE `service_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `template_images`
--
ALTER TABLE `template_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `template_promotions`
--
ALTER TABLE `template_promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `seller_analytics`
--
ALTER TABLE `seller_analytics`
  ADD CONSTRAINT `seller_analytics_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `seller_earnings`
--
ALTER TABLE `seller_earnings`
  ADD CONSTRAINT `seller_earnings_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `seller_earnings_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `seller_earnings_ibfk_3` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`);

--
-- Constraints for table `seller_messages`
--
ALTER TABLE `seller_messages`
  ADD CONSTRAINT `seller_messages_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_messages_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `seller_messages_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `seller_profiles`
--
ALTER TABLE `seller_profiles`
  ADD CONSTRAINT `seller_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_services`
--
ALTER TABLE `seller_services`
  ADD CONSTRAINT `seller_services_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_services_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `seller_stats`
--
ALTER TABLE `seller_stats`
  ADD CONSTRAINT `seller_stats_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_services_category` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`),
  ADD CONSTRAINT `fk_services_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_images`
--
ALTER TABLE `service_images`
  ADD CONSTRAINT `service_images_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_orders`
--
ALTER TABLE `service_orders`
  ADD CONSTRAINT `service_orders_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `seller_services` (`id`),
  ADD CONSTRAINT `service_orders_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `service_orders_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `service_reviews`
--
ALTER TABLE `service_reviews`
  ADD CONSTRAINT `service_reviews_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `templates`
--
ALTER TABLE `templates`
  ADD CONSTRAINT `templates_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `templates_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `template_images`
--
ALTER TABLE `template_images`
  ADD CONSTRAINT `template_images_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `template_promotions`
--
ALTER TABLE `template_promotions`
  ADD CONSTRAINT `template_promotions_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`),
  ADD CONSTRAINT `template_promotions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD CONSTRAINT `withdrawal_requests_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
