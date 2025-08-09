<?php
/**
 * Seller Settings Management
 * Manage seller profile and account settings
 */
?>

<!-- Settings Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Settings</h1>
            <p class="text-gray-600">Manage your profile and account preferences</p>
        </div>
    </div>
</div>

<!-- Settings Tabs -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <div class="border-b border-gray-100">
        <nav class="flex space-x-8 px-6">
            <button onclick="showSettingsTab('profile')" 
                    class="settings-tab active py-4 px-2 border-b-2 border-primary text-primary font-medium">
                Profile
            </button>
            <button onclick="showSettingsTab('business')" 
                    class="settings-tab py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                Business Info
            </button>
            <button onclick="showSettingsTab('notifications')" 
                    class="settings-tab py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                Notifications
            </button>
            <button onclick="showSettingsTab('security')" 
                    class="settings-tab py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                Security
            </button>
            <button onclick="showSettingsTab('payment')" 
                    class="settings-tab py-4 px-2 border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                Payment
            </button>
        </nav>
    </div>
    
    <!-- Profile Settings Tab -->
    <div id="profileTab" class="settings-content p-6">
        <div class="max-w-4xl">
            <h3 class="text-xl font-semibold text-secondary mb-6">Profile Information</h3>
            
            <form id="profileForm" onsubmit="updateProfile(event)">
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Profile Picture -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                        <div class="flex items-center space-x-6">
                            <div class="w-24 h-24 bg-gray-200 rounded-full overflow-hidden">
                                <img id="profilePreview" 
                                     src="<?= $sellerData['profile_picture'] ? htmlspecialchars($sellerData['profile_picture']) : 'assets/images/default-avatar.png' ?>" 
                                     alt="Profile Picture"
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <input type="file" id="profilePicture" name="profile_picture" accept="image/*" class="hidden" onchange="previewImage(this, 'profilePreview')">
                                <button type="button" onclick="document.getElementById('profilePicture').click()" 
                                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                                    Change Picture
                                </button>
                                <p class="text-sm text-gray-500 mt-2">JPG, PNG up to 5MB</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Basic Info -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($sellerData['first_name']) ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($sellerData['last_name']) ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($sellerData['email']) ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($sellerData['phone'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <!-- Bio -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea name="bio" rows="4"
                                  class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                  placeholder="Tell customers about yourself..."><?= htmlspecialchars($sellerData['bio'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <select name="country" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Select Country</option>
                            <option value="US" <?= ($sellerData['country'] ?? '') === 'US' ? 'selected' : '' ?>>United States</option>
                            <option value="CA" <?= ($sellerData['country'] ?? '') === 'CA' ? 'selected' : '' ?>>Canada</option>
                            <option value="UK" <?= ($sellerData['country'] ?? '') === 'UK' ? 'selected' : '' ?>>United Kingdom</option>
                            <option value="AU" <?= ($sellerData['country'] ?? '') === 'AU' ? 'selected' : '' ?>>Australia</option>
                            <option value="VN" <?= ($sellerData['country'] ?? '') === 'VN' ? 'selected' : '' ?>>Vietnam</option>
                            <!-- Add more countries as needed -->
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($sellerData['city'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>
                
                <div class="flex items-center justify-end">
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Business Info Tab -->
    <div id="businessTab" class="settings-content hidden p-6">
        <div class="max-w-4xl">
            <h3 class="text-xl font-semibold text-secondary mb-6">Business Information</h3>
            
            <form id="businessForm" onsubmit="updateBusiness(event)">
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                        <input type="text" name="business_name" value="<?= htmlspecialchars($sellerData['business_name'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
                        <select name="business_type" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">Select Type</option>
                            <option value="individual" <?= ($sellerData['business_type'] ?? '') === 'individual' ? 'selected' : '' ?>>Individual</option>
                            <option value="llc" <?= ($sellerData['business_type'] ?? '') === 'llc' ? 'selected' : '' ?>>LLC</option>
                            <option value="corporation" <?= ($sellerData['business_type'] ?? '') === 'corporation' ? 'selected' : '' ?>>Corporation</option>
                            <option value="partnership" <?= ($sellerData['business_type'] ?? '') === 'partnership' ? 'selected' : '' ?>>Partnership</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax ID</label>
                        <input type="text" name="tax_id" value="<?= htmlspecialchars($sellerData['tax_id'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" name="website" value="<?= htmlspecialchars($sellerData['website'] ?? '') ?>"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    
                    <!-- Business Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Address</label>
                        <textarea name="business_address" rows="3"
                                  class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                  placeholder="Enter your business address..."><?= htmlspecialchars($sellerData['business_address'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-end">
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Save Business Info
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Notifications Tab -->
    <div id="notificationsTab" class="settings-content hidden p-6">
        <div class="max-w-4xl">
            <h3 class="text-xl font-semibold text-secondary mb-6">Notification Preferences</h3>
            
            <form id="notificationsForm" onsubmit="updateNotifications(event)">
                <div class="space-y-6">
                    <!-- Email Notifications -->
                    <div>
                        <h4 class="text-lg font-medium text-secondary mb-4">Email Notifications</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">New Orders</div>
                                    <div class="text-sm text-gray-600">Get notified when you receive new orders</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_new_orders" 
                                           <?= ($sellerData['notifications']['email_new_orders'] ?? true) ? 'checked' : '' ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">Order Updates</div>
                                    <div class="text-sm text-gray-600">Get notified about order status changes</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_order_updates" 
                                           <?= ($sellerData['notifications']['email_order_updates'] ?? true) ? 'checked' : '' ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">New Messages</div>
                                    <div class="text-sm text-gray-600">Get notified when you receive new messages</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_new_messages" 
                                           <?= ($sellerData['notifications']['email_new_messages'] ?? true) ? 'checked' : '' ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SMS Notifications -->
                    <div>
                        <h4 class="text-lg font-medium text-secondary mb-4">SMS Notifications</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">Urgent Orders</div>
                                    <div class="text-sm text-gray-600">Get SMS for urgent order notifications</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="sms_urgent_orders" 
                                           <?= ($sellerData['notifications']['sms_urgent_orders'] ?? false) ? 'checked' : '' ?>
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end mt-8">
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Security Tab -->
    <div id="securityTab" class="settings-content hidden p-6">
        <div class="max-w-4xl">
            <h3 class="text-xl font-semibold text-secondary mb-6">Security Settings</h3>
            
            <!-- Change Password -->
            <div class="mb-8">
                <h4 class="text-lg font-medium text-secondary mb-4">Change Password</h4>
                <form id="passwordForm" onsubmit="updatePassword(event)">
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" name="current_password"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div></div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" name="new_password"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" name="confirm_password"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                    </div>
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Update Password
                    </button>
                </form>
            </div>
            
            <!-- Two-Factor Authentication -->
            <div class="mb-8">
                <h4 class="text-lg font-medium text-secondary mb-4">Two-Factor Authentication</h4>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium text-gray-900">Enable 2FA</div>
                        <div class="text-sm text-gray-600">Add an extra layer of security to your account</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="two_factor_auth" 
                               <?= ($sellerData['two_factor_enabled'] ?? false) ? 'checked' : '' ?>
                               class="sr-only peer" onchange="toggle2FA(this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>
            
            <!-- Active Sessions -->
            <div>
                <h4 class="text-lg font-medium text-secondary mb-4">Active Sessions</h4>
                <div class="space-y-4">
                    <?php foreach ($activeSessions as $session): ?>
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="ri-computer-line text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($session['device']) ?></div>
                                <div class="text-sm text-gray-600">
                                    <?= htmlspecialchars($session['location']) ?> • 
                                    <?= date('M j, Y g:i A', strtotime($session['last_active'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!$session['is_current']): ?>
                        <button onclick="terminateSession('<?= $session['id'] ?>')" 
                                class="text-red-600 hover:text-red-700 text-sm">
                            Terminate
                        </button>
                        <?php else: ?>
                        <span class="text-green-600 text-sm font-medium">Current Session</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Tab -->
    <div id="paymentTab" class="settings-content hidden p-6">
        <div class="max-w-4xl">
            <h3 class="text-xl font-semibold text-secondary mb-6">Payment Settings</h3>
            
            <form id="paymentForm" onsubmit="updatePaymentSettings(event)">
                <div class="space-y-6">
                    <!-- Payment Methods -->
                    <div>
                        <h4 class="text-lg font-medium text-secondary mb-4">Payout Methods</h4>
                        <div class="space-y-4">
                            <!-- PayPal -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                                            <i class="ri-paypal-line text-blue-600"></i>
                                        </div>
                                        <span class="font-medium text-gray-900">PayPal</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="paypal_enabled" 
                                               <?= ($sellerData['payment_methods']['paypal_enabled'] ?? false) ? 'checked' : '' ?>
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <input type="email" name="paypal_email" 
                                       value="<?= htmlspecialchars($sellerData['payment_methods']['paypal_email'] ?? '') ?>"
                                       placeholder="PayPal email address"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            
                            <!-- Bank Transfer -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center">
                                            <i class="ri-bank-line text-green-600"></i>
                                        </div>
                                        <span class="font-medium text-gray-900">Bank Transfer</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="bank_enabled" 
                                               <?= ($sellerData['payment_methods']['bank_enabled'] ?? false) ? 'checked' : '' ?>
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input type="text" name="bank_name" 
                                           value="<?= htmlspecialchars($sellerData['payment_methods']['bank_name'] ?? '') ?>"
                                           placeholder="Bank name"
                                           class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    <input type="text" name="account_number" 
                                           value="<?= htmlspecialchars($sellerData['payment_methods']['account_number'] ?? '') ?>"
                                           placeholder="Account number"
                                           class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tax Information -->
                    <div>
                        <h4 class="text-lg font-medium text-secondary mb-4">Tax Information</h4>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                                <input type="number" name="tax_rate" step="0.01"
                                       value="<?= htmlspecialchars($sellerData['tax_rate'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tax Registration Number</label>
                                <input type="text" name="tax_registration" 
                                       value="<?= htmlspecialchars($sellerData['tax_registration'] ?? '') ?>"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end mt-8">
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Save Payment Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Settings Management Scripts -->
<script>
// Tab switching
function showSettingsTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.settings-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.settings-tab').forEach(button => {
        button.classList.remove('active', 'border-primary', 'text-primary');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById(tabName + 'Tab').classList.remove('hidden');
    
    // Add active class to selected tab button
    event.target.classList.add('active', 'border-primary', 'text-primary');
    event.target.classList.remove('border-transparent', 'text-gray-500');
}

// Image preview
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Form submissions
function updateProfile(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    fetch('seller-api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Profile updated successfully', 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error updating profile', 'error');
        console.error('Error:', error);
    });
}

function updateBusiness(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    formData.append('action', 'update_business_info');
    
    fetch('seller-api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Business information updated successfully', 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
}

function updateNotifications(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    formData.append('action', 'update_notifications');
    
    fetch('seller-api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Notification preferences updated', 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
}

function updatePassword(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    formData.append('action', 'update_password');
    
    if (formData.get('new_password') !== formData.get('confirm_password')) {
        showToast('Passwords do not match', 'error');
        return;
    }
    
    fetch('seller-api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Password updated successfully', 'success');
            event.target.reset();
        } else {
            showToast(data.message, 'error');
        }
    });
}

function updatePaymentSettings(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    formData.append('action', 'update_payment_settings');
    
    fetch('seller-api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Payment settings updated successfully', 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
}

function toggle2FA(enabled) {
    fetch('seller-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'toggle_2fa',
            enabled: enabled
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(enabled ? '2FA enabled successfully' : '2FA disabled successfully', 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
}

function terminateSession(sessionId) {
    if (confirm('Are you sure you want to terminate this session?')) {
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'terminate_session',
                session_id: sessionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Session terminated successfully', 'success');
                window.location.reload();
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}
</script>