<?php
/**
 * Checkout Page
 * Complete purchase process with payment form and order confirmation
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cloudinary-config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get database connection
try {
    $pdo = DatabaseConfig::getConnection();
    $userId = $_SESSION['user_id'];
    
    // Get cart items - support both templates and services
    $stmt = $pdo->prepare("
        SELECT c.*,
               COALESCE(t.title, s.title) as title,
               COALESCE(t.preview_image, s.preview_image) as preview_image,
               COALESCE(t.price, s.price) as price,
               COALESCE(t.description, s.description) as description,
               COALESCE(tu.first_name, su.first_name) as seller_first_name,
               COALESCE(tu.last_name, su.last_name) as seller_last_name,
               CASE 
                   WHEN c.template_id IS NOT NULL THEN 'template'
                   WHEN c.service_id IS NOT NULL THEN 'service'
               END as item_type,
               COALESCE(c.template_id, c.service_id) as item_id
        FROM cart c
        LEFT JOIN templates t ON c.template_id = t.id
        LEFT JOIN services s ON c.service_id = s.id
        LEFT JOIN users tu ON t.seller_id = tu.id
        LEFT JOIN users su ON s.seller_id = su.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get user data for billing
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Checkout page error: " . $e->getMessage());
    $cartItems = [];
    $userData = null;
}

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
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
    <title>Checkout - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/checkout.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1F',
                        secondary: '#1f2937'
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

    <!-- Checkout Content -->
    <section class="pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-6 md:px-4 sm:px-3">
            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-sm mb-8">
                <a href="index.php" class="text-gray-500 hover:text-primary transition-colors">Home</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <a href="cart.php" class="text-gray-500 hover:text-primary transition-colors">Cart</a>
                <i class="ri-arrow-right-s-line text-gray-400"></i>
                <span class="text-secondary font-medium">Checkout</span>
            </div>

            <!-- Page Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl lg:text-4xl md:text-3xl sm:text-2xl font-bold text-secondary mb-4">Secure Checkout</h1>
                <p class="text-gray-600 lg:text-base md:text-sm sm:text-sm">Complete your purchase securely</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 lg:gap-12 md:gap-8 sm:gap-6">
                <!-- Checkout Form -->
                <div class="checkout-card checkout-form p-8">
                    <form id="checkoutForm" onsubmit="processPayment(event)">
                        <!-- Billing Information -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center">
                                <i class="ri-user-line mr-3 text-primary"></i>
                                Billing Information
                            </h2>
                            
                            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" name="first_name" class="form-input" 
                                           value="<?= htmlspecialchars($userData['first_name'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" name="last_name" class="form-input" 
                                           value="<?= htmlspecialchars($userData['last_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" class="form-input" 
                                       value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <select name="country" class="form-input" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                    <option value="DE">Germany</option>
                                    <option value="FR">France</option>
                                    <option value="JP">Japan</option>
                                    <option value="VN">Vietnam</option>
                                </select>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center">
                                <i class="ri-credit-card-line mr-3 text-primary"></i>
                                Payment Method
                            </h2>
                            
                            <div class="space-y-4 mb-6">
                                <div class="payment-method selected" onclick="selectPaymentMethod(this, 'card')">
                                    <div class="payment-method-content flex items-center lg:flex-row">
                                        <input type="radio" name="payment_method" value="card" checked class="mr-3">
                                        <div class="flex-1">
                                            <div class="font-semibold">Credit/Debit Card</div>
                                            <div class="text-sm text-gray-600">Visa, MasterCard, American Express</div>
                                        </div>
                                        <div class="payment-icons flex space-x-2">
                                            <img src="https://img.icons8.com/color/48/visa.png" alt="Visa" class="w-10 h-6 object-contain">
                                            <img src="https://img.icons8.com/color/48/mastercard.png" alt="MasterCard" class="w-10 h-6 object-contain">
                                            <img src="https://img.icons8.com/color/48/amex.png" alt="American Express" class="w-10 h-6 object-contain">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-method" onclick="selectPaymentMethod(this, 'paypal')">
                                    <div class="payment-method-content flex items-center lg:flex-row">
                                        <input type="radio" name="payment_method" value="paypal" class="mr-3">
                                        <div class="flex-1">
                                            <div class="font-semibold">PayPal</div>
                                            <div class="text-sm text-gray-600">Pay with your PayPal account</div>
                                        </div>
                                        <img src="https://img.icons8.com/color/48/paypal.png" alt="PayPal" class="w-12 h-6 object-contain">
                                    </div>
                                </div>
                            </div>
                            
                            <div id="cardDetails" class="space-y-4">
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                    <input type="text" name="card_number" class="form-input" placeholder="1234 5678 9012 3456" 
                                           pattern="[0-9\s]{13,19}" maxlength="19">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                        <input type="text" name="expiry_date" class="form-input" placeholder="MM/YY" 
                                               pattern="[0-9]{2}/[0-9]{2}" maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                        <input type="text" name="cvv" class="form-input" placeholder="123" 
                                               pattern="[0-9]{3,4}" maxlength="4">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitBtn" class="btn-primary">
                            <i class="ri-secure-payment-line mr-2"></i>
                            Complete Purchase - $<?= number_format($total, 2) ?>
                        </button>
                        
                        <div class="mt-4 text-center">
                            <div class="security-badge">
                                <i class="ri-shield-check-line mr-2"></i>
                                256-bit SSL Encrypted
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="checkout-card p-8">
                    <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center">
                        <i class="ri-shopping-bag-line mr-3 text-primary"></i>
                        Order Summary
                    </h2>
                    
                    <!-- Order Items -->
                    <div class="mb-6">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="order-item">
                            <div class="order-item-content lg:flex lg:items-center lg:w-full">
                                <img src="<?= htmlspecialchars(getOptimizedImageUrl($item['preview_image'], 'thumb')) ?>" 
                                     alt="<?= htmlspecialchars($item['title']) ?>" 
                                     class="order-item-image w-16 h-12 rounded-lg object-cover mr-4"
                                     onerror="this.src='assets/images/<?= $item['item_type'] === 'service' ? 'default-service.jpg' : 'default-template.jpg' ?>'">
                                <div class="order-item-info flex-1">
                                    <h4 class="order-item-title font-semibold text-secondary">
                                        <?= htmlspecialchars($item['title']) ?>
                                        <span class="ml-2 text-xs px-2 py-1 rounded-full <?= $item['item_type'] === 'service' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' ?>">
                                            <?= ucfirst($item['item_type']) ?>
                                        </span>
                                    </h4>
                                    <p class="order-item-seller text-sm text-gray-600">by <?= htmlspecialchars($item['seller_first_name'] . ' ' . $item['seller_last_name']) ?></p>
                                </div>
                            </div>
                            <div class="order-item-price font-bold text-primary lg:hidden">$<?= number_format($item['price'], 2) ?></div>
                            <div class="hidden lg:block font-bold text-primary">$<?= number_format($item['price'], 2) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal (<?= count($cartItems) ?> items)</span>
                                <span class="font-semibold">$<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax (10%)</span>
                                <span class="font-semibold">$<?= number_format($tax, 2) ?></span>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-secondary">Total</span>
                                    <span class="text-2xl font-bold text-primary">$<?= number_format($total, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Money Back Guarantee -->
                    <div class="mt-8 p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center mb-2">
                            <i class="ri-shield-check-line text-green-600 mr-2"></i>
                            <span class="font-semibold text-green-800">30-Day Money Back Guarantee</span>
                        </div>
                        <p class="text-sm text-green-700">If you're not satisfied with your purchase, we'll refund your money within 30 days.</p>
                    </div>
                    
                    <!-- Support Info -->
                    <div class="mt-6 text-center text-sm text-gray-600">
                        <p>Need help? <a href="#" class="text-primary hover:underline">Contact Support</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        function selectPaymentMethod(element, method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(pm => {
                pm.classList.remove('selected');
            });
            
            // Add selected class to clicked element
            element.classList.add('selected');
            
            // Update radio button
            const radio = element.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Show/hide card details
            const cardDetails = document.getElementById('cardDetails');
            if (method === 'card') {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        }

        async function processPayment(event) {
            event.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Processing Payment...';
            
            try {
                const formData = new FormData(event.target);
                
                // Simulate payment processing
                await new Promise(resolve => setTimeout(resolve, 3000));
                
                // For demo purposes, always succeed
                const success = true;
                
                if (success) {
                    // Show success message
                    showSuccessMessage();
                    
                    // Clear cart and redirect after delay
                    setTimeout(() => {
                        clearCartAndRedirect();
                    }, 2000);
                } else {
                    throw new Error('Payment failed');
                }
                
            } catch (error) {
                console.error('Payment error:', error);
                showError('Payment failed. Please try again or contact support.');
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        function showSuccessMessage() {
            // Create success overlay
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            overlay.innerHTML = `
                <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-check-line text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-secondary mb-2">Payment Successful!</h3>
                    <p class="text-gray-600 mb-4">Your order has been processed successfully. You will receive a confirmation email shortly.</p>
                    <div class="flex items-center justify-center text-sm text-gray-500">
                        <i class="ri-loader-4-line animate-spin mr-2"></i>
                        Redirecting to downloads...
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
        }

        async function clearCartAndRedirect() {
            try {
                // Clear cart
                await fetch('cart-api.php?action=clear', { method: 'POST' });
                
                // Redirect to success page or downloads
                window.location.href = 'index.php?purchase=success';
                
            } catch (error) {
                console.error('Error clearing cart:', error);
                // Still redirect even if cart clear fails
                window.location.href = 'index.php?purchase=success';
            }
        }

        // Format card number input
        document.addEventListener('input', function(e) {
            if (e.target.name === 'card_number') {
                let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            }
            
            if (e.target.name === 'expiry_date') {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            }
        });

        // Auto-focus next field on card form
        document.addEventListener('keyup', function(e) {
            const maxLengths = {
                'card_number': 19,
                'expiry_date': 5,
                'cvv': 4
            };
            
            if (maxLengths[e.target.name] && e.target.value.length >= maxLengths[e.target.name]) {
                const form = e.target.form;
                const index = Array.prototype.indexOf.call(form, e.target);
                if (form.elements[index + 1]) {
                    form.elements[index + 1].focus();
                }
            }
        });
    </script>
</body>
</html>