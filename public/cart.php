<?php
/**
 * Shopping Cart Page
 * Displays user's cart items with ability to update quantities and remove items
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get database connection
try {
    $pdo = DatabaseConfig::getConnection();
    $userId = $_SESSION['user_id'];
    
    // Get cart items with template details
    $stmt = $pdo->prepare("
        SELECT c.*, t.title, t.preview_image, t.price, t.description,
               u.first_name as seller_first_name, u.last_name as seller_last_name
        FROM cart c
        JOIN templates t ON c.template_id = t.id
        LEFT JOIN users u ON t.seller_id = u.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Cart page error: " . $e->getMessage());
    $cartItems = [];
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'];
}
$tax = $subtotal * 0.1; // 10% tax
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
        }
        
        .cart-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .cart-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF5F1F, #FF8C42);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 95, 31, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 95, 31, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #FF5F1F;
            color: #FF5F1F;
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #FF5F1F;
            color: white;
            transform: translateY(-2px);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1F',
                        secondary: '#1f2937'
                    },
                    borderRadius: {
                        'button': '12px'
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

    <!-- Cart Content -->
    <section class="pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-6">
            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm mb-8">
                <a href="index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-secondary font-medium">Shopping Cart</span>
            </div>

            <!-- Page Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-secondary mb-4">Shopping Cart</h1>
                <p class="text-gray-600">Review your selected templates before checkout</p>
            </div>

            <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="cart-card p-12 text-center">
                <i class="ri-shopping-cart-line text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-bold text-secondary mb-4">Your cart is empty</h2>
                <p class="text-gray-600 mb-8">Looks like you haven't added any templates to your cart yet.</p>
                <a href="templates.php" class="btn-primary inline-flex items-center">
                    <i class="ri-search-line mr-2"></i>
                    Browse Templates
                </a>
            </div>
            <?php else: ?>
            <!-- Cart Items -->
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Cart Items List -->
                <div class="lg:col-span-2">
                    <div class="cart-card p-8">
                        <h2 class="text-2xl font-bold text-secondary mb-6">Cart Items (<?= count($cartItems) ?>)</h2>
                        
                        <div class="space-y-6">
                            <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item flex items-center space-x-6 p-6 bg-gray-50 rounded-xl">
                                <!-- Template Image -->
                                <div class="w-24 h-20 rounded-lg overflow-hidden flex-shrink-0">
                                    <img src="<?= htmlspecialchars($item['preview_image']) ?>" 
                                         alt="<?= htmlspecialchars($item['title']) ?>" 
                                         class="w-full h-full object-cover">
                                </div>
                                
                                <!-- Template Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-xl font-bold text-secondary mb-2"><?= htmlspecialchars($item['title']) ?></h3>
                                    <p class="text-gray-600 text-sm mb-2 line-clamp-2"><?= htmlspecialchars(substr($item['description'], 0, 120)) ?>...</p>
                                    <p class="text-sm text-gray-500">
                                        by <?= htmlspecialchars($item['seller_first_name'] . ' ' . $item['seller_last_name']) ?>
                                    </p>
                                </div>
                                
                                <!-- Price & Actions -->
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-primary mb-4">$<?= number_format($item['price'], 2) ?></div>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewTemplate(<?= $item['template_id'] ?>)" 
                                                class="w-10 h-10 flex items-center justify-center bg-white border-2 border-gray-200 rounded-lg hover:border-primary hover:text-primary transition-colors">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button onclick="removeFromCart(<?= $item['template_id'] ?>)" 
                                                class="w-10 h-10 flex items-center justify-center bg-white border-2 border-red-200 rounded-lg hover:border-red-500 hover:text-red-500 transition-colors">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Continue Shopping -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <a href="templates.php" class="btn-secondary inline-flex items-center">
                                <i class="ri-arrow-left-line mr-2"></i>
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="cart-card p-8 sticky top-24">
                        <h2 class="text-2xl font-bold text-secondary mb-6">Order Summary</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Subtotal (<?= count($cartItems) ?> items)</span>
                                <span class="font-semibold text-secondary">$<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Tax (10%)</span>
                                <span class="font-semibold text-secondary">$<?= number_format($tax, 2) ?></span>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-secondary">Total</span>
                                    <span class="text-2xl font-bold text-primary">$<?= number_format($total, 2) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Checkout Button -->
                        <button onclick="proceedToCheckout()" class="w-full btn-primary mb-4">
                            <i class="ri-secure-payment-line mr-2"></i>
                            Proceed to Checkout
                        </button>
                        
                        <!-- Security Info -->
                        <div class="text-center text-sm text-gray-500">
                            <i class="ri-shield-check-line text-green-500 mr-1"></i>
                            Secure SSL Encrypted Payment
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-600 mb-3">We accept:</p>
                            <div class="flex items-center space-x-2">
                                <div class="w-12 h-8 bg-blue-600 rounded text-white text-xs flex items-center justify-center font-bold">VISA</div>
                                <div class="w-12 h-8 bg-red-600 rounded text-white text-xs flex items-center justify-center font-bold">MC</div>
                                <div class="w-12 h-8 bg-blue-500 rounded text-white text-xs flex items-center justify-center font-bold">AMEX</div>
                                <div class="w-12 h-8 bg-yellow-500 rounded text-white text-xs flex items-center justify-center font-bold">PP</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        function viewTemplate(templateId) {
            window.location.href = `template-detail.php?id=${templateId}`;
        }

        async function removeFromCart(templateId) {
            if (!confirm('Are you sure you want to remove this template from your cart?')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('template_id', templateId);

                const response = await fetch('cart-api.php?action=remove', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Reload page to update cart
                    window.location.reload();
                } else {
                    alert('Error removing item from cart: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error removing item from cart');
            }
        }

        function proceedToCheckout() {
            window.location.href = 'checkout.php';
        }

        // Auto-refresh cart if it's updated from another tab
        setInterval(async () => {
            try {
                const response = await fetch('cart-api.php?action=get');
                const data = await response.json();
                
                if (data.success && data.items.length !== <?= count($cartItems) ?>) {
                    // Cart changed, reload page
                    window.location.reload();
                }
            } catch (error) {
                // Ignore errors for background refresh
            }
        }, 30000); // Check every 30 seconds
    </script>
</body>
</html>