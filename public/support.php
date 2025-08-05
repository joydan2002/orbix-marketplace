<?php
/**
 * Support Page - FAQ and Contact Form
 * Beautiful glassmorphism design with orange theme
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Get database connection for FAQs
try {
    $db = DatabaseConfig::getConnection();
    
    // Get popular FAQs
    $stmt = $db->query("SELECT * FROM faqs WHERE is_active = 1 AND is_popular = 1 ORDER BY sort_order ASC, id ASC LIMIT 6");
    $popularFaqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all FAQ categories
    $stmt = $db->query("SELECT DISTINCT category FROM faqs WHERE is_active = 1 ORDER BY category");
    $faqCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get FAQs by category
    $faqsByCategory = [];
    foreach ($faqCategories as $category) {
        $stmt = $db->prepare("SELECT * FROM faqs WHERE is_active = 1 AND category = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$category]);
        $faqsByCategory[$category] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (Exception $e) {
    // Fallback data if database error
    $popularFaqs = [];
    $faqCategories = ['general', 'templates', 'billing', 'account', 'technical'];
    $faqsByCategory = [];
}

// Handle contact form submission
$formMessage = '';
$formSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $formMessage = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formMessage = 'Please enter a valid email address.';
    } else {
        try {
            // Insert support ticket
            $stmt = $db->prepare("
                INSERT INTO support_tickets (user_id, name, email, subject, category, message, status, priority, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'open', 'medium', NOW())
            ");
            
            $userId = $_SESSION['user_id'] ?? null;
            $stmt->execute([$userId, $name, $email, $subject, $category, $message]);
            
            $formSuccess = true;
            $formMessage = 'Thank you for contacting us! We will get back to you within 24 hours.';
            
            // Clear form data
            $_POST = [];
            
        } catch (Exception $e) {
            $formMessage = 'There was an error submitting your message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }
        
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            box-shadow: 
                0 8px 32px rgba(255, 95, 31, 0.1),
                0 2px 16px rgba(0, 0, 0, 0.05);
        }
        
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 20px 40px rgba(255, 95, 31, 0.15),
                0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, 
                #ffffff 0%, 
                #fff7ed 25%, 
                #fed7aa 50%, 
                #fff7ed 75%, 
                #ffffff 100%);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, 
                #FF5F1F 0%, 
                #FF8C42 25%, 
                #FFB366 50%, 
                #FF8C42 75%, 
                #FF5F1F 100%);
        }
        
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .faq-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .faq-item:hover {
            transform: translateX(8px);
            background: rgba(255, 95, 31, 0.05);
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .faq-answer.open {
            max-height: 500px;
        }
        
        .category-pill {
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 95, 31, 0.2);
        }
        
        .category-pill.active {
            background: #FF5F1F;
            color: white;
            box-shadow: 0 4px 20px rgba(255, 95, 31, 0.3);
        }
        
        .category-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 95, 31, 0.2);
        }
        
        .contact-form {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(255, 95, 31, 0.1);
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            border-color: #FF5F1F;
            box-shadow: 0 0 0 4px rgba(255, 95, 31, 0.1);
            background: rgba(255, 255, 255, 1);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .support-icon {
            background: linear-gradient(135deg, #FF5F1F 0%, #FF8C42 100%);
            box-shadow: 0 8px 25px rgba(255, 95, 31, 0.3);
        }
        
        .category-icon {
            background: linear-gradient(135deg, 
                rgba(255, 95, 31, 0.1) 0%, 
                rgba(255, 95, 31, 0.2) 100%);
            border: 2px solid rgba(255, 95, 31, 0.2);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1D',
                        secondary: '#1f2937'
                    },
                    borderRadius: {
                        'button': '8px'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'pacifico': ['Pacifico', 'serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter gradient-bg min-h-screen">
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 relative overflow-hidden">
        <div class="absolute inset-0 hero-gradient opacity-10"></div>
        <div class="absolute inset-0">
            <div class="floating-animation absolute top-20 left-20 w-32 h-32 bg-primary/5 rounded-full"></div>
            <div class="floating-animation absolute top-40 right-32 w-24 h-24 bg-primary/10 rounded-full" style="animation-delay: -2s;"></div>
            <div class="floating-animation absolute bottom-32 left-1/3 w-20 h-20 bg-primary/5 rounded-full" style="animation-delay: -4s;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 support-icon rounded-3xl mb-8 pulse-animation">
                    <i class="ri-customer-service-line text-3xl text-white"></i>
                </div>
                
                <h1 class="text-5xl lg:text-6xl font-bold text-secondary mb-6">
                    How Can We
                    <span class="text-primary">Help You?</span>
                </h1>
                
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-12 leading-relaxed">
                    Our dedicated support team is here to assist you 24/7. 
                    Find answers to common questions or get in touch with us directly.
                </p>
                
                <!-- Quick Search -->
                <div class="max-w-2xl mx-auto">
                    <div class="glass-card p-2 flex items-center">
                        <div class="w-6 h-6 flex items-center justify-center ml-4">
                            <i class="ri-search-line text-gray-400"></i>
                        </div>
                        <input type="text" id="faqSearch" placeholder="Search frequently asked questions..." 
                               class="flex-1 px-4 py-4 bg-transparent border-none outline-none text-lg">
                        <button class="bg-primary text-white px-8 py-4 rounded-2xl font-medium hover:bg-primary/90 transition-colors">
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="pb-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-8 text-center transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 support-icon rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ri-time-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-secondary mb-2">< 24 Hours</h3>
                    <p class="text-gray-600">Average Response Time</p>
                </div>
                
                <div class="glass-card p-8 text-center transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 support-icon rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ri-team-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-secondary mb-2">50,000+</h3>
                    <p class="text-gray-600">Happy Customers Helped</p>
                </div>
                
                <div class="glass-card p-8 text-center transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 support-icon rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ri-star-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-secondary mb-2">4.9/5</h3>
                    <p class="text-gray-600">Customer Satisfaction</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular FAQs -->
    <?php if (!empty($popularFaqs)): ?>
    <section class="pb-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-secondary mb-4">
                    Popular Questions
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Quick answers to the most common questions from our community
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php foreach ($popularFaqs as $faq): ?>
                <div class="glass-card p-6 faq-item">
                    <button class="faq-question w-full text-left flex items-center justify-between" 
                            onclick="toggleFaq(this)">
                        <h3 class="text-lg font-semibold text-secondary pr-4">
                            <?= htmlspecialchars($faq['question']) ?>
                        </h3>
                        <div class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full flex-shrink-0">
                            <i class="ri-add-line text-primary transition-transform duration-300"></i>
                        </div>
                    </button>
                    <div class="faq-answer mt-4">
                        <p class="text-gray-600 leading-relaxed">
                            <?= htmlspecialchars($faq['answer']) ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- FAQ Categories -->
    <section class="pb-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-secondary mb-4">
                    Browse by Category
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Find specific help for different aspects of our platform
                </p>
            </div>
            
            <!-- Category Pills -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <?php foreach ($faqCategories as $index => $category): ?>
                <button class="category-pill px-6 py-3 rounded-full font-medium capitalize <?= $index === 0 ? 'active' : '' ?>" 
                        onclick="showCategory('<?= $category ?>', this)">
                    <?= ucfirst($category) ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <!-- FAQ Categories Content -->
            <?php foreach ($faqCategories as $index => $category): ?>
            <div class="faq-category <?= $index !== 0 ? 'hidden' : '' ?>" id="category-<?= $category ?>">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <?php 
                    $categoryFaqs = $faqsByCategory[$category] ?? [];
                    foreach ($categoryFaqs as $faq): 
                    ?>
                    <div class="glass-card p-6 faq-item">
                        <button class="faq-question w-full text-left flex items-center justify-between" 
                                onclick="toggleFaq(this)">
                            <h3 class="text-lg font-semibold text-secondary pr-4">
                                <?= htmlspecialchars($faq['question']) ?>
                            </h3>
                            <div class="w-8 h-8 flex items-center justify-center bg-primary/10 rounded-full flex-shrink-0">
                                <i class="ri-add-line text-primary transition-transform duration-300"></i>
                            </div>
                        </button>
                        <div class="faq-answer mt-4">
                            <p class="text-gray-600 leading-relaxed">
                                <?= htmlspecialchars($faq['answer']) ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="pb-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                <!-- Contact Info -->
                <div>
                    <h2 class="text-4xl font-bold text-secondary mb-6">
                        Still Need Help?
                    </h2>
                    <p class="text-xl text-gray-600 mb-12 leading-relaxed">
                        Can't find what you're looking for? Our friendly support team is ready to help you with any questions or issues.
                    </p>
                    
                    <div class="space-y-8">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 category-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="ri-mail-line text-primary text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-secondary mb-2">Email Support</h3>
                                <p class="text-gray-600 mb-2">Get detailed help via email</p>
                                <a href="mailto:support@orbixmarket.com" class="text-primary font-medium hover:underline">
                                    support@orbixmarket.com
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 category-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="ri-phone-line text-primary text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-secondary mb-2">Phone Support</h3>
                                <p class="text-gray-600 mb-2">Talk to us directly</p>
                                <a href="tel:1-800-ORBIX-HELP" class="text-primary font-medium hover:underline">
                                    1-800-ORBIX-HELP
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 category-icon rounded-2xl flex items-center justify-center flex-shrink-0">
                                <i class="ri-chat-3-line text-primary text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-secondary mb-2">Live Chat</h3>
                                <p class="text-gray-600 mb-2">Available 24/7 for instant help</p>
                                <button class="text-primary font-medium hover:underline" onclick="openLiveChat()">
                                    Start Chat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="contact-form rounded-3xl p-8">
                    <h3 class="text-2xl font-bold text-secondary mb-6">Send us a Message</h3>
                    
                    <?php if (!empty($formMessage)): ?>
                    <div class="mb-6 p-4 rounded-2xl <?= $formSuccess ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' ?>">
                        <div class="flex items-center">
                            <i class="<?= $formSuccess ? 'ri-check-line' : 'ri-error-warning-line' ?> text-xl mr-3"></i>
                            <span><?= htmlspecialchars($formMessage) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-secondary mb-2">
                                    Full Name *
                                </label>
                                <input type="text" id="name" name="name" required
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                       class="form-input w-full px-4 py-3 rounded-xl border-0 outline-none"
                                       placeholder="Enter your full name">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-semibold text-secondary mb-2">
                                    Email Address *
                                </label>
                                <input type="email" id="email" name="email" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       class="form-input w-full px-4 py-3 rounded-xl border-0 outline-none"
                                       placeholder="Enter your email">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="subject" class="block text-sm font-semibold text-secondary mb-2">
                                    Subject *
                                </label>
                                <input type="text" id="subject" name="subject" required
                                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                                       class="form-input w-full px-4 py-3 rounded-xl border-0 outline-none"
                                       placeholder="What's this about?">
                            </div>
                            
                            <div>
                                <label for="category" class="block text-sm font-semibold text-secondary mb-2">
                                    Category
                                </label>
                                <select id="category" name="category" 
                                        class="form-input w-full px-4 py-3 rounded-xl border-0 outline-none">
                                    <option value="general" <?= ($_POST['category'] ?? '') === 'general' ? 'selected' : '' ?>>General Question</option>
                                    <option value="templates" <?= ($_POST['category'] ?? '') === 'templates' ? 'selected' : '' ?>>Templates</option>
                                    <option value="billing" <?= ($_POST['category'] ?? '') === 'billing' ? 'selected' : '' ?>>Billing & Payment</option>
                                    <option value="account" <?= ($_POST['category'] ?? '') === 'account' ? 'selected' : '' ?>>Account</option>
                                    <option value="technical" <?= ($_POST['category'] ?? '') === 'technical' ? 'selected' : '' ?>>Technical Issue</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-semibold text-secondary mb-2">
                                Message *
                            </label>
                            <textarea id="message" name="message" required rows="6"
                                      class="form-input w-full px-4 py-3 rounded-xl border-0 outline-none resize-none"
                                      placeholder="Tell us more about your question or issue..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" name="submit_contact" 
                                class="w-full bg-primary text-white py-4 px-8 rounded-xl font-semibold text-lg hover:bg-primary/90 transition-all duration-300 transform hover:scale-105">
                            <i class="ri-send-plane-line mr-2"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // FAQ Toggle functionality
        function toggleFaq(button) {
            const answer = button.parentElement.querySelector('.faq-answer');
            const icon = button.querySelector('i');
            
            // Close all other FAQs in the same container
            const container = button.closest('.faq-category, section');
            const otherFaqs = container.querySelectorAll('.faq-answer');
            const otherIcons = container.querySelectorAll('.faq-question i');
            
            otherFaqs.forEach((faq, index) => {
                if (faq !== answer) {
                    faq.classList.remove('open');
                    otherIcons[index].style.transform = 'rotate(0deg)';
                    otherIcons[index].className = 'ri-add-line text-primary transition-transform duration-300';
                }
            });
            
            // Toggle current FAQ
            if (answer.classList.contains('open')) {
                answer.classList.remove('open');
                icon.style.transform = 'rotate(0deg)';
                icon.className = 'ri-add-line text-primary transition-transform duration-300';
            } else {
                answer.classList.add('open');
                icon.style.transform = 'rotate(45deg)';
                icon.className = 'ri-close-line text-primary transition-transform duration-300';
            }
        }
        
        // Category switching
        function showCategory(category, button) {
            // Update active pill
            document.querySelectorAll('.category-pill').forEach(pill => {
                pill.classList.remove('active');
            });
            button.classList.add('active');
            
            // Show/hide categories
            document.querySelectorAll('.faq-category').forEach(cat => {
                cat.classList.add('hidden');
            });
            
            const targetCategory = document.getElementById('category-' + category);
            if (targetCategory) {
                targetCategory.classList.remove('hidden');
            }
        }
        
        // FAQ Search functionality
        document.getElementById('faqSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question h3').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    
                    // Highlight search terms
                    if (searchTerm) {
                        highlightText(item, searchTerm);
                    }
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Live chat placeholder
        function openLiveChat() {
            alert('Live chat feature will be available soon! Please use the contact form or email for now.');
        }
        
        // Highlight search terms
        function highlightText(element, searchTerm) {
            // This is a simple implementation - in production you'd want a more robust solution
            const textElements = element.querySelectorAll('h3, p');
            textElements.forEach(el => {
                if (searchTerm && el.textContent.toLowerCase().includes(searchTerm)) {
                    el.style.background = 'rgba(255, 95, 31, 0.1)';
                    el.style.borderRadius = '8px';
                    el.style.padding = '4px 8px';
                } else {
                    el.style.background = '';
                    el.style.borderRadius = '';
                    el.style.padding = '';
                }
            });
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !subject || !message) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
        });
        
        // Auto-hide success message
        <?php if ($formSuccess): ?>
        setTimeout(() => {
            const successMessage = document.querySelector('.bg-green-50');
            if (successMessage) {
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    successMessage.remove();
                }, 300);
            }
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>