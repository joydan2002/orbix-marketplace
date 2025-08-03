<?php
/**
 * Seller Reviews Section
 * Customer reviews and feedback management
 */
?>

<div class="space-y-8">
    <!-- Reviews Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Reviews & Feedback</h1>
            <p class="text-gray-600">Manage customer reviews and improve your products</p>
        </div>
        <div class="flex items-center space-x-4">
            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                <option>All Products</option>
                <option>Templates</option>
                <option>Services</option>
            </select>
            <select class="border border-gray-300 rounded-lg px-4 py-2">
                <option>All Ratings</option>
                <option>5 Stars</option>
                <option>4 Stars</option>
                <option>3 Stars</option>
                <option>2 Stars</option>
                <option>1 Star</option>
            </select>
        </div>
    </div>

    <!-- Reviews Stats -->
    <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-6 border border-yellow-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                    <i class="ri-star-line text-xl text-white"></i>
                </div>
                <span class="text-yellow-600 text-sm font-medium bg-yellow-200 px-2 py-1 rounded-full">Excellent</span>
            </div>
            <div class="text-2xl font-bold text-yellow-800 mb-1">4.8</div>
            <div class="text-yellow-600 text-sm">Average Rating</div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i class="ri-chat-3-line text-xl text-white"></i>
                </div>
                <span class="text-blue-600 text-sm font-medium bg-blue-200 px-2 py-1 rounded-full">Total</span>
            </div>
            <div class="text-2xl font-bold text-blue-800 mb-1">
                <?= count($reviews ?? []) ?>
            </div>
            <div class="text-blue-600 text-sm">Total Reviews</div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                    <i class="ri-thumb-up-line text-xl text-white"></i>
                </div>
                <span class="text-green-600 text-sm font-medium bg-green-200 px-2 py-1 rounded-full">+15%</span>
            </div>
            <div class="text-2xl font-bold text-green-800 mb-1">92%</div>
            <div class="text-green-600 text-sm">Positive Reviews</div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                    <i class="ri-time-line text-xl text-white"></i>
                </div>
                <span class="text-purple-600 text-sm font-medium bg-purple-200 px-2 py-1 rounded-full">Avg</span>
            </div>
            <div class="text-2xl font-bold text-purple-800 mb-1">2.4h</div>
            <div class="text-purple-600 text-sm">Response Time</div>
        </div>
    </div>

    <!-- Rating Distribution -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border">
        <h3 class="text-xl font-bold text-secondary mb-6">Rating Distribution</h3>
        <div class="space-y-4">
            <?php 
            $ratingData = [
                5 => 68,
                4 => 24,
                3 => 5,
                2 => 2,
                1 => 1
            ];
            $totalRatings = array_sum($ratingData);
            
            foreach ($ratingData as $stars => $count): 
                $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
            ?>
            <div class="flex items-center space-x-4">
                <div class="flex items-center w-20">
                    <span class="text-sm font-medium text-gray-700"><?= $stars ?></span>
                    <i class="ri-star-fill text-yellow-400 ml-1"></i>
                </div>
                <div class="flex-1 bg-gray-200 rounded-full h-3">
                    <div class="bg-yellow-400 h-3 rounded-full transition-all duration-500" 
                         style="width: <?= $percentage ?>%"></div>
                </div>
                <div class="text-sm text-gray-600 w-16 text-right">
                    <?= $count ?> (<?= number_format($percentage, 1) ?>%)
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="bg-white rounded-2xl shadow-sm border">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-secondary">Recent Reviews</h3>
                <div class="flex items-center space-x-4">
                    <input type="search" placeholder="Search reviews..." 
                           class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button class="text-primary hover:text-primary/80">View All</button>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            <?php 
            $sampleReviews = [
                [
                    'id' => 1,
                    'customer_name' => 'Sarah Johnson',
                    'customer_avatar' => 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=667eea&color=fff',
                    'product_title' => 'Modern Business Website Template',
                    'rating' => 5,
                    'comment' => 'Absolutely fantastic template! Clean design, easy to customize, and great documentation. Saved me hours of work.',
                    'created_at' => '2 days ago',
                    'replied' => false
                ],
                [
                    'id' => 2,
                    'customer_name' => 'Mike Chen',
                    'customer_avatar' => 'https://ui-avatars.com/api/?name=Mike+Chen&background=10b981&color=fff',
                    'product_title' => 'E-commerce Store Template',
                    'rating' => 4,
                    'comment' => 'Great template overall. The design is modern and responsive. Could use more payment gateway options though.',
                    'created_at' => '1 week ago',
                    'replied' => true
                ],
                [
                    'id' => 3,
                    'customer_name' => 'Emily Davis',
                    'customer_avatar' => 'https://ui-avatars.com/api/?name=Emily+Davis&background=f59e0b&color=fff',
                    'product_title' => 'Portfolio Website Service',
                    'rating' => 5,
                    'comment' => 'Exceeded my expectations! Professional service, quick delivery, and beautiful results.',
                    'created_at' => '2 weeks ago',
                    'replied' => true
                ]
            ];
            
            foreach ($sampleReviews as $review): ?>
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-start space-x-4">
                    <img src="<?= $review['customer_avatar'] ?>" 
                         alt="<?= htmlspecialchars($review['customer_name']) ?>"
                         class="w-12 h-12 rounded-full">
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-secondary"><?= htmlspecialchars($review['customer_name']) ?></h4>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($review['product_title']) ?></p>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="ri-star-<?= $i <= $review['rating'] ? 'fill' : 'line' ?> text-yellow-400"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-sm text-gray-500"><?= $review['created_at'] ?></p>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 mb-4"><?= htmlspecialchars($review['comment']) ?></p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <?php if ($review['replied']): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="ri-check-line mr-1"></i>
                                        Replied
                                    </span>
                                <?php else: ?>
                                    <button class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm">
                                        <i class="ri-reply-line mr-2"></i>
                                        Reply
                                    </button>
                                <?php endif; ?>
                                
                                <button class="text-gray-500 hover:text-gray-700 transition-colors">
                                    <i class="ri-heart-line"></i>
                                </button>
                                <button class="text-gray-500 hover:text-gray-700 transition-colors">
                                    <i class="ri-flag-line"></i>
                                </button>
                            </div>
                            
                            <button class="text-gray-500 hover:text-gray-700 transition-colors">
                                <i class="ri-more-2-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($sampleReviews)): ?>
            <div class="p-12 text-center text-gray-500">
                <i class="ri-chat-3-line text-4xl mb-4"></i>
                <p class="text-lg mb-2">No reviews yet</p>
                <p class="text-sm">Your customer reviews will appear here</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Management Tools -->
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Quick Response Templates -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-secondary">Quick Response Templates</h3>
                <button class="text-primary hover:text-primary/80 flex items-center">
                    <i class="ri-add-line mr-2"></i>Add Template
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-secondary">Thank You Response</h4>
                        <button class="text-primary hover:text-primary/80">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Thank you so much for your positive review! We're thrilled that you're satisfied with our product...</p>
                    <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors text-sm">
                        Use Template
                    </button>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-secondary">Issue Resolution</h4>
                        <button class="text-primary hover:text-primary/80">
                            <i class="ri-edit-line"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">We appreciate your feedback and apologize for any inconvenience. We'd love to help resolve this issue...</p>
                    <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors text-sm">
                        Use Template
                    </button>
                </div>
            </div>
        </div>

        <!-- Review Insights -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border">
            <h3 class="text-xl font-bold text-secondary mb-6">Review Insights</h3>
            
            <div class="space-y-6">
                <div>
                    <h4 class="font-semibold text-secondary mb-3">Most Mentioned Keywords</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Clean Design</span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Easy to Use</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">Professional</span>
                        <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm">Fast Loading</span>
                        <span class="px-3 py-1 bg-pink-100 text-pink-800 rounded-full text-sm">Responsive</span>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold text-secondary mb-3">Common Feedback</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-sm text-green-800">Great documentation</span>
                            <span class="text-xs text-green-600">85% positive</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm text-blue-800">Fast support response</span>
                            <span class="text-xs text-blue-600">78% positive</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <span class="text-sm text-orange-800">Need more customization</span>
                            <span class="text-xs text-orange-600">12% feedback</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-secondary">Reply to Review</h3>
            <button onclick="closeReplyModal()" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
        
        <div class="mb-6">
            <div class="flex items-center space-x-3 mb-4">
                <img src="https://ui-avatars.com/api/?name=Customer&background=667eea&color=fff" 
                     alt="Customer" class="w-10 h-10 rounded-full">
                <div>
                    <h4 class="font-semibold">Customer Review</h4>
                    <div class="flex items-center">
                        <div class="flex text-yellow-400 mr-2">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">Original review text will appear here...</p>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Your Reply</label>
            <textarea class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary focus:border-transparent" 
                      rows="4" placeholder="Write your reply..."></textarea>
        </div>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <select class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                    <option>Select Template</option>
                    <option>Thank You Response</option>
                    <option>Issue Resolution</option>
                </select>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="closeReplyModal()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                    Send Reply
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openReplyModal() {
    document.getElementById('replyModal').classList.remove('hidden');
    document.getElementById('replyModal').classList.add('flex');
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
    document.getElementById('replyModal').classList.remove('flex');
}

// Add event listeners to reply buttons
document.addEventListener('DOMContentLoaded', function() {
    const replyButtons = document.querySelectorAll('button[data-action="reply"]');
    replyButtons.forEach(button => {
        button.addEventListener('click', openReplyModal);
    });
});
</script>