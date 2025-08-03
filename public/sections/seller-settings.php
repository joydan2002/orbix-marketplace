<?php
/**
 * Seller Settings Section
 * Account settings, preferences, and configuration
 */
?>

<div class="space-y-8">
    <!-- Settings Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Settings</h1>
            <p class="text-gray-600">Manage your account settings and preferences</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Settings Menu -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border">
                <nav class="p-2">
                    <a href="#profile" class="settings-tab active flex items-center w-full px-4 py-3 text-left rounded-xl hover:bg-gray-50 transition-colors">
                        <i class="ri-user-line mr-3 text-primary"></i>
                        <span>Profile Information</span>
                    </a>
                    <a href="#account" class="settings-tab flex items-center w-full px-4 py-3 text-left rounded-xl hover:bg-gray-50 transition-colors">
                        <i class="ri-shield-user-line mr-3"></i>
                        <span>Account Security</span>
                    </a>
                    <a href="#notifications" class="settings-tab flex items-center w-full px-4 py-3 text-left rounded-xl hover:bg-gray-50 transition-colors">
                        <i class="ri-notification-line mr-3"></i>
                        <span>Notifications</span>
                    </a>
                    <a href="#payment" class="settings-tab flex items-center w-full px-4 py-3 text-left rounded-xl hover:bg-gray-50 transition-colors">
                        <i class="ri-bank-card-line mr-3"></i>
                        <span>Payment Methods</span>
                    </a>
                    <a href="#store" class="settings-tab flex items-center w-full px-4 py-3 text-left rounded-xl hover:bg-gray-50 transition-colors">
                        <i class="ri-store-line mr-3"></i>
                        <span>Store Settings</span>
                    </a>
                    <a href="#privacy" class="settings-tab flex items-center w-full px-4 py-3 text-left rounded-xl hover:bg-gray-50 transition-colors">
                        <i class="ri-lock-line mr-3"></i>
                        <span>Privacy</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-2">
            <!-- Profile Information -->
            <div id="profile-section" class="settings-section bg-white rounded-2xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-secondary">Profile Information</h3>
                    <button class="text-primary hover:text-primary/80">
                        <i class="ri-edit-line mr-2"></i>Edit
                    </button>
                </div>

                <form class="space-y-6">
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&background=667eea&color=fff&size=100" 
                                 alt="Profile Picture" class="w-20 h-20 rounded-full">
                            <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white hover:bg-primary/90">
                                <i class="ri-camera-line text-sm"></i>
                            </button>
                        </div>
                        <div>
                            <h4 class="font-semibold text-secondary">Profile Picture</h4>
                            <p class="text-sm text-gray-600">JPG, PNG or GIF. Max size 2MB</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" value="John" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" value="Doe" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" value="john.doe@example.com" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" value="+1 (555) 123-4567" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                  rows="4" placeholder="Tell customers about yourself and your expertise...">Experienced web developer with 5+ years in creating modern, responsive websites and applications.</textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>United States</option>
                                <option>Canada</option>
                                <option>United Kingdom</option>
                                <option>Australia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                            <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>UTC-8 (Pacific Time)</option>
                                <option>UTC-5 (Eastern Time)</option>
                                <option>UTC+0 (Greenwich Time)</option>
                                <option>UTC+1 (Central European Time)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Account Security -->
            <div id="account-section" class="settings-section bg-white rounded-2xl shadow-sm border p-6 hidden">
                <h3 class="text-xl font-bold text-secondary mb-6">Account Security</h3>

                <div class="space-y-8">
                    <!-- Change Password -->
                    <div class="border-b border-gray-100 pb-8">
                        <h4 class="font-semibold text-secondary mb-4">Change Password</h4>
                        <form class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors">
                                Update Password
                            </button>
                        </form>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div class="border-b border-gray-100 pb-8">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-semibold text-secondary">Two-Factor Authentication</h4>
                                <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        <button class="text-primary hover:text-primary/80 text-sm">
                            <i class="ri-smartphone-line mr-2"></i>Set up authenticator app
                        </button>
                    </div>

                    <!-- Login Sessions -->
                    <div>
                        <h4 class="font-semibold text-secondary mb-4">Active Sessions</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="ri-computer-line text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">MacBook Pro</p>
                                        <p class="text-sm text-gray-600">Chrome • San Francisco, CA • Current session</p>
                                    </div>
                                </div>
                                <span class="text-green-600 text-sm font-medium">Active</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="ri-smartphone-line text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">iPhone 13</p>
                                        <p class="text-sm text-gray-600">Safari • New York, NY • 2 hours ago</p>
                                    </div>
                                </div>
                                <button class="text-red-600 hover:text-red-700 text-sm">End session</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div id="notifications-section" class="settings-section bg-white rounded-2xl shadow-sm border p-6 hidden">
                <h3 class="text-xl font-bold text-secondary mb-6">Notification Preferences</h3>

                <div class="space-y-8">
                    <div>
                        <h4 class="font-semibold text-secondary mb-4">Email Notifications</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">New Orders</p>
                                    <p class="text-sm text-gray-600">Get notified when you receive new orders</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Payment Received</p>
                                    <p class="text-sm text-gray-600">Get notified when payments are processed</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">New Reviews</p>
                                    <p class="text-sm text-gray-600">Get notified when customers leave reviews</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-secondary mb-4">Marketing Emails</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Platform Updates</p>
                                    <p class="text-sm text-gray-600">News about new features and improvements</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Tips & Tutorials</p>
                                    <p class="text-sm text-gray-600">Helpful content to grow your business</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div id="payment-section" class="settings-section bg-white rounded-2xl shadow-sm border p-6 hidden">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-secondary">Payment Methods</h3>
                    <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        <i class="ri-add-line mr-2"></i>Add Payment Method
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- PayPal -->
                    <div class="border border-gray-200 rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="ri-paypal-line text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-secondary">PayPal</h4>
                                    <p class="text-sm text-gray-600">john.doe@paypal.com</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Primary
                                </span>
                                <button class="text-gray-500 hover:text-gray-700">
                                    <i class="ri-more-2-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Account -->
                    <div class="border border-gray-200 rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i class="ri-bank-line text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-secondary">Bank Account</h4>
                                    <p class="text-sm text-gray-600">****1234 - Chase Bank</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Verified
                                </span>
                                <button class="text-gray-500 hover:text-gray-700">
                                    <i class="ri-more-2-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Settings -->
            <div id="store-section" class="settings-section bg-white rounded-2xl shadow-sm border p-6 hidden">
                <h3 class="text-xl font-bold text-secondary mb-6">Store Settings</h3>

                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
                        <input type="text" value="John's Design Studio" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Store Description</label>
                        <textarea class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                  rows="4">Professional web design and development services. Creating modern, responsive websites that drive results.</textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Response Time</label>
                            <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>Within 1 hour</option>
                                <option>Within 6 hours</option>
                                <option>Within 24 hours</option>
                                <option>Within 3 days</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Time</label>
                            <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>1-3 days</option>
                                <option>3-7 days</option>
                                <option>1-2 weeks</option>
                                <option>Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Privacy -->
            <div id="privacy-section" class="settings-section bg-white rounded-2xl shadow-sm border p-6 hidden">
                <h3 class="text-xl font-bold text-secondary mb-6">Privacy Settings</h3>

                <div class="space-y-8">
                    <div>
                        <h4 class="font-semibold text-secondary mb-4">Profile Visibility</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Show Online Status</p>
                                    <p class="text-sm text-gray-600">Let customers see when you're online</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Show Response Time</p>
                                    <p class="text-sm text-gray-600">Display your average response time</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-secondary mb-4">Data & Analytics</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">Analytics Tracking</p>
                                    <p class="text-sm text-gray-600">Help us improve the platform</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-8">
                        <h4 class="font-semibold text-secondary mb-4">Account Actions</h4>
                        <div class="space-y-4">
                            <button class="flex items-center text-orange-600 hover:text-orange-700">
                                <i class="ri-download-line mr-2"></i>
                                Download My Data
                            </button>
                            <button class="flex items-center text-red-600 hover:text-red-700">
                                <i class="ri-delete-bin-line mr-2"></i>
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const settingsTabs = document.querySelectorAll('.settings-tab');
    const settingsSections = document.querySelectorAll('.settings-section');

    settingsTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            settingsTabs.forEach(t => t.classList.remove('active', 'bg-primary/10', 'text-primary'));
            
            // Add active class to clicked tab
            this.classList.add('active', 'bg-primary/10', 'text-primary');
            
            // Hide all sections
            settingsSections.forEach(section => section.classList.add('hidden'));
            
            // Show target section
            const targetId = this.getAttribute('href').substring(1) + '-section';
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
        });
    });

    // Set initial active state
    const firstTab = document.querySelector('.settings-tab.active');
    if (firstTab) {
        firstTab.classList.add('bg-primary/10', 'text-primary');
    }
});
</script>