<?php
/**
 * Shopping Cart Page
 * Display cart items and manage cart operations
 */

require_once 'config/database.php';
require_once 'includes/header.php';

// Redirect if not logged in
if (!$isLoggedIn) {
    header('Location: auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get cart items
try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->prepare("
        SELECT 
            c.template_id,
            t.title,
            t.slug,
            t.description,
            t.price,
            t.preview_image,
            t.demo_url,
            t.technology,
            CONCAT(u.first_name, ' ', u.last_name) as seller_name,
            u.profile_image as seller_image,
            c.added_at
        FROM cart c
        JOIN templates t ON c.template_id = t.id
        JOIN users u ON t.seller_id = u.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total
    $total = array_sum(array_column($cartItems, 'price'));
    
} catch (Exception $e) {
    $cartItems = [];
    $total = 0;
}
?>

<!-- Main Content -->
<main class="pt-20 pb-16 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-6">
        <!-- Page Header -->
        <div class="bg-white rounded-2xl p-8 mb-8 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-secondary mb-2">Shopping Cart</h1>
                    <p class="text-gray-600">
                        <?= count($cartItems) ?> <?= count($cartItems) === 1 ? 'item' : 'items' ?> in your cart
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500 mb-1">Total</div>
                    <div class="text-3xl font-bold text-primary">$<?= number_format($total, 2) ?></div>
                </div>
            </div>
        </div>

        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-shopping-cart-line text-4xl text-gray-400"></i>
                </div>
                <h2 class="text-2xl font-bold text-secondary mb-4">Your cart is empty</h2>
                <p class="text-gray-600 mb-8">Discover amazing templates to kickstart your projects</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="templates.php" class="bg-primary text-white px-8 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                        Browse Templates
                    </a>
                    <a href="services.php" class="text-primary border-2 border-primary px-8 py-3 rounded-xl font-semibold hover:bg-primary/5 transition-colors">
                        Explore Services
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-6">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="bg-white rounded-2xl p-6 shadow-sm cart-item" data-template-id="<?= $item['template_id'] ?>">
                        <div class="flex items-start space-x-4">
                            <!-- Template Image -->
                            <div class="flex-shrink-0">
                                <img src="<?= htmlspecialchars($item['preview_image']) ?>" 
                                     alt="<?= htmlspecialchars($item['title']) ?>"
                                     class="w-24 h-24 rounded-xl object-cover">
                            </div>
                            
                            <!-- Template Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-secondary mb-2">
                                    <a href="template-detail.php?slug=<?= urlencode($item['slug']) ?>" class="hover:text-primary transition-colors">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                    <?= htmlspecialchars(substr($item['description'], 0, 150)) ?>...
                                </p>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                    <div class="flex items-center">
                                        <?php if ($item['seller_image']): ?>
                                            <img src="<?= htmlspecialchars($item['seller_image']) ?>" 
                                                 alt="<?= htmlspecialchars($item['seller_name']) ?>"
                                                 class="w-5 h-5 rounded-full mr-2">
                                        <?php else: ?>
                                            <div class="w-5 h-5 bg-primary rounded-full flex items-center justify-center mr-2">
                                                <span class="text-white text-xs font-bold">
                                                    <?= strtoupper(substr($item['seller_name'], 0, 1)) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <span>by <?= htmlspecialchars($item['seller_name']) ?></span>
                                    </div>
                                    
                                    <?php if ($item['technology']): ?>
                                    <div class="flex items-center">
                                        <i class="ri-code-line mr-1"></i>
                                        <span><?= htmlspecialchars($item['technology']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center">
                                        <i class="ri-time-line mr-1"></i>
                                        <span>Added <?= date('M j', strtotime($item['added_at'])) ?></span>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <?php if ($item['demo_url']): ?>
                                        <a href="<?= htmlspecialchars($item['demo_url']) ?>" 
                                           target="_blank"
                                           class="text-primary hover:text-primary/80 text-sm font-medium flex items-center">
                                            <i class="ri-external-link-line mr-1"></i>
                                            Live Preview
                                        </a>
                                        <?php endif; ?>
                                        
                                        <button onclick="removeFromCart(<?= $item['template_id'] ?>)" 
                                                class="text-red-500 hover:text-red-600 text-sm font-medium flex items-center">
                                            <i class="ri-delete-bin-line mr-1"></i>
                                            Remove
                                        </button>
                                    </div>
                                    
                                    <div class="text-xl font-bold text-primary">
                                        $<?= number_format($item['price'], 2) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
                        <h3 class="text-xl font-bold text-secondary mb-6">Order Summary</h3>
                        
                        <!-- Items List -->
                        <div class="space-y-3 mb-6">
                            <?php foreach ($cartItems as $item): ?>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 truncate pr-2">
                                    <?= htmlspecialchars($item['title']) ?>
                                </span>
                                <span class="font-semibold text-secondary">
                                    $<?= number_format($item['price'], 2) ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-4"></div>
                        
                        <!-- Total -->
                        <div class="flex items-center justify-between text-lg font-bold mb-6">
                            <span class="text-secondary">Total</span>
                            <span class="text-primary">$<?= number_format($total, 2) ?></span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="space-y-3">
                            <button onclick="proceedToCheckout()" 
                                    class="w-full bg-primary text-white py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center justify-center">
                                <i class="ri-secure-payment-line mr-2"></i>
                                Proceed to Checkout
                            </button>
                            
                            <button onclick="clearCart()" 
                                    class="w-full bg-gray-100 text-gray-600 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                                Clear Cart
                            </button>
                        </div>
                        
                        <!-- Security Notice -->
                        <div class="mt-6 p-4 bg-green-50 rounded-xl">
                            <div class="flex items-center text-green-700 mb-2">
                                <i class="ri-shield-check-line mr-2"></i>
                                <span class="font-semibold text-sm">Secure Checkout</span>
                            </div>
                            <p class="text-xs text-green-600">
                                Your payment information is encrypted and secure. All templates come with lifetime updates and support.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- JavaScript -->
<script>
// Remove item from cart
async function removeFromCart(templateId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
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
            // Remove the item from DOM
            const cartItem = document.querySelector(`[data-template-id="${templateId}"]`);
            if (cartItem) {
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    cartItem.remove();
                    // Reload page if no items left
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        window.location.reload();
                    } else {
                        updateOrderSummary();
                    }
                }, 300);
            }
            
            if (window.toast) {
                window.toast.success('Item removed from cart', {
                    duration: 2000,
                    position: 'top-right'
                });
            }
        } else {
            throw new Error(data.error || 'Failed to remove item');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        if (window.toast) {
            window.toast.error('Failed to remove item. Please try again.', {
                duration: 3000,
                position: 'top-right'
            });
        } else {
            alert('Failed to remove item. Please try again.');
        }
    }
}

// Clear entire cart
async function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('cart-api.php?action=clear', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (window.toast) {
                window.toast.success('Cart cleared successfully', {
                    duration: 2000,
                    position: 'top-right'
                });
            }
            // Reload page to show empty cart
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.error || 'Failed to clear cart');
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        if (window.toast) {
            window.toast.error('Failed to clear cart. Please try again.', {
                duration: 3000,
                position: 'top-right'
            });
        } else {
            alert('Failed to clear cart. Please try again.');
        }
    }
}

// Proceed to checkout
function proceedToCheckout() {
    window.location.href = 'checkout.php';
}

// Update order summary after removing items
function updateOrderSummary() {
    // Recalculate total from remaining items
    const cartItems = document.querySelectorAll('.cart-item');
    let newTotal = 0;
    
    cartItems.forEach(item => {
        const priceElement = item.querySelector('.text-primary');
        if (priceElement) {
            const price = parseFloat(priceElement.textContent.replace('$', ''));
            newTotal += price;
        }
    });
    
    // Update total display
    const totalElements = document.querySelectorAll('.text-primary');
    totalElements.forEach(element => {
        if (element.textContent.includes('$') && element.textContent.includes('.')) {
            element.textContent = `$${newTotal.toFixed(2)}`;
        }
    });
    
    // Update item count
    const itemCount = cartItems.length;
    const countElement = document.querySelector('p.text-gray-600');
    if (countElement) {
        countElement.textContent = `${itemCount} ${itemCount === 1 ? 'item' : 'items'} in your cart`;
    }
}

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cart items on load
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.5s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php require_once 'includes/footer.php'; ?>