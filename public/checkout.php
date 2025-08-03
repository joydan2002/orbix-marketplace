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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get database connection
try {
    $pdo = DatabaseConfig::getConnection();
    $userId = $_SESSION['user_id'];
    
    // Get cart items
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
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
        }
        
        .checkout-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-input:focus {
            outline: none;
            border-color: #FF5F1F;
            box-shadow: 0 0 0 4px rgba(255, 95, 31, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF5F1F, #FF8C42);
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 95, 31, 0.3);
            width: 100%;
            font-size: 1.1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 95, 31, 0.4);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }
        
        .payment-method {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover {
            border-color: #FF5F1F;
            background: rgba(255, 95, 31, 0.05);
        }
        
        .payment-method.selected {
            border-color: #FF5F1F;
            background: rgba(255, 95, 31, 0.1);
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: rgba(249, 250, 251, 0.8);
            border-radius: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .security-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: rgba(34, 197, 94, 0.1);
            color: #059669;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
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
        <div class="max-w-7xl mx-auto px-6">
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
                <h1 class="text-4xl font-bold text-secondary mb-4">Secure Checkout</h1>
                <p class="text-gray-600">Complete your purchase securely</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Checkout Form -->
                <div class="checkout-card p-8">
                    <form id="checkoutForm" onsubmit="processPayment(event)">
                        <!-- Billing Information -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-secondary mb-6 flex items-center">
                                <i class="ri-user-line mr-3 text-primary"></i>
                                Billing Information
                            </h2>
                            
                            <div class="grid md:grid-cols-2 gap-4">
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
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" value="card" checked class="mr-3">
                                        <div class="flex-1">
                                            <div class="font-semibold">Credit/Debit Card</div>
                                            <div class="text-sm text-gray-600">Visa, MasterCard, American Express</div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <img src="https://img.icons8.com/color/48/visa.png" alt="Visa" class="w-10 h-6 object-contain">
                                            <img src="https://img.icons8.com/color/48/mastercard.png" alt="MasterCard" class="w-10 h-6 object-contain">
                                            <img src="https://img.icons8.com/color/48/amex.png" alt="American Express" class="w-10 h-6 object-contain">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-method" onclick="selectPaymentMethod(this, 'paypal')">
                                    <div class="flex items-center">
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
                            <img src="<?= htmlspecialchars($item['preview_image']) ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 class="w-16 h-12 rounded-lg object-cover mr-4">
                            <div class="flex-1">
                                <h4 class="font-semibold text-secondary"><?= htmlspecialchars($item['title']) ?></h4>
                                <p class="text-sm text-gray-600">by <?= htmlspecialchars($item['seller_first_name'] . ' ' . $item['seller_last_name']) ?></p>
                            </div>
                            <div class="font-bold text-primary">$<?= number_format($item['price'], 2) ?></div>
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
                alert('Payment failed. Please try again or contact support.');
                
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