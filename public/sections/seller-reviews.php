<?php
/**
 * Seller Reviews Management
 * Display real reviews from database
 */

// Load real data from database
require_once 'seller-data-loader.php';
?>

<!-- Reviews Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Reviews</h1>
            <p class="text-gray-600">Manage customer reviews and feedback</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <div class="text-2xl font-bold text-yellow-600"><?= number_format($reviewStats['average_rating'], 1) ?></div>
                <div class="text-sm text-gray-600">Average Rating</div>
            </div>
        </div>
    </div>
    
    <!-- Reviews Stats -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= $reviewStats['total'] ?></div>
                    <div class="text-sm text-gray-600">Total Reviews</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ri-star-line text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600"><?= $reviewStats['five_star'] ?></div>
                    <div class="text-sm text-gray-600">5-Star Reviews</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-star-fill text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-600"><?= $reviewStats['four_star'] ?></div>
                    <div class="text-sm text-gray-600">4-Star Reviews</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-star-fill text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-yellow-600"><?= number_format($reviewStats['average_rating'], 1) ?></div>
                    <div class="text-sm text-gray-600">Average Rating</div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="ri-award-line text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reviews List -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <!-- Search and Filter -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-secondary">All Reviews</h3>
            <div class="flex items-center space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           id="reviewSearch"
                           placeholder="Search reviews..." 
                           class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
                    <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Rating Filter -->
                <select id="reviewRatingFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
                
                <!-- Type Filter -->
                <select id="reviewTypeFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Types</option>
                    <option value="template">Templates</option>
                    <option value="service">Services</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Reviews Container -->
    <div id="reviewsContainer">
        <?php if (empty($allReviews)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-star-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary mb-2">No Reviews Yet</h3>
            <p class="text-gray-600 mb-6">Customer reviews will appear here when you receive feedback</p>
        </div>
        <?php else: ?>
        
        <!-- Reviews List -->
        <div class="divide-y divide-gray-100">
            <?php foreach ($allReviews as $review): ?>
            <div class="review-item p-6 hover:bg-gray-50 transition-colors" 
                 data-rating="<?= $review['rating'] ?>" 
                 data-type="<?= isset($review['template_title']) ? 'template' : 'service' ?>">
                
                <div class="flex items-start space-x-4">
                    <!-- Reviewer Avatar -->
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-medium text-gray-600">
                            <?= strtoupper(substr($review['first_name'], 0, 1) . substr($review['last_name'], 0, 1)) ?>
                        </span>
                    </div>
                    
                    <!-- Review Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-3">
                                <h4 class="text-lg font-semibold text-secondary">
                                    <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?>
                                </h4>
                                
                                <!-- Product Type Badge -->
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= isset($review['template_title']) ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= isset($review['template_title']) ? 'Template' : 'Service' ?>
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= date('M j, Y \a\t g:i A', strtotime($review['created_at'])) ?>
                            </div>
                        </div>
                        
                        <!-- Product Name -->
                        <div class="text-base font-medium text-gray-800 mb-2">
                            For: <?= htmlspecialchars($review['template_title'] ?? $review['service_title']) ?>
                        </div>
                        
                        <!-- Rating Stars -->
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="flex items-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="ri-star-<?= $i <= $review['rating'] ? 'fill' : 'line' ?> text-yellow-400 text-lg"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-sm font-medium text-gray-700">
                                <?= $review['rating'] ?> out of 5 stars
                            </span>
                        </div>
                        
                        <!-- Review Text -->
                        <div class="text-gray-700 mb-4">
                            <?= htmlspecialchars($review['review_text']) ?>
                        </div>
                        
                        <!-- Review Actions -->
                        <div class="flex items-center space-x-4">
                            <button onclick="respondToReview(<?= $review['id'] ?>)" 
                                    class="text-sm text-primary hover:text-primary/80 font-medium">
                                Respond
                            </button>
                            
                            <button onclick="reportReview(<?= $review['id'] ?>)" 
                                    class="text-sm text-gray-500 hover:text-gray-700">
                                Report
                            </button>
                            
                            <?php if ($review['rating'] >= 4): ?>
                            <button onclick="shareReview(<?= $review['id'] ?>)" 
                                    class="text-sm text-green-600 hover:text-green-700">
                                Share
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Response Modal -->
<div id="responseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-2xl w-full mx-4">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-secondary">Respond to Review</h3>
                <button onclick="closeResponseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="reviewContext" class="mb-4 p-4 bg-gray-50 rounded-lg">
                <!-- Review context will be loaded here -->
            </div>
            
            <form id="responseForm" onsubmit="submitResponse(event)">
                <input type="hidden" id="responseReviewId" value="">
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Response</label>
                    <textarea id="responseText" rows="4"
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                              placeholder="Thank you for your feedback..."></textarea>
                </div>
                
                <div class="flex items-center justify-end space-x-4">
                    <button type="button" onclick="closeResponseModal()" 
                            class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Send Response
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Management Scripts -->
<script>
// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('reviewSearch');
    const ratingFilter = document.getElementById('reviewRatingFilter');
    const typeFilter = document.getElementById('reviewTypeFilter');
    const reviewItems = document.querySelectorAll('.review-item');
    
    function filterReviews() {
        const searchTerm = searchInput.value.toLowerCase();
        const ratingFilter_val = ratingFilter.value;
        const typeFilter_val = typeFilter.value;
        
        reviewItems.forEach(item => {
            const reviewer = item.querySelector('h4').textContent.toLowerCase();
            const content = item.querySelector('.text-gray-700').textContent.toLowerCase();
            const productName = item.querySelector('.font-medium').textContent.toLowerCase();
            const rating = item.dataset.rating;
            const type = item.dataset.type;
            
            const matchesSearch = reviewer.includes(searchTerm) || content.includes(searchTerm) || productName.includes(searchTerm);
            const matchesRating = !ratingFilter_val || rating === ratingFilter_val;
            const matchesType = !typeFilter_val || type === typeFilter_val;
            
            if (matchesSearch && matchesRating && matchesType) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterReviews);
    ratingFilter.addEventListener('change', filterReviews);
    typeFilter.addEventListener('change', filterReviews);
});

// Review management functions
function respondToReview(reviewId) {
    // Get review data and show response modal
    fetch(`seller-api.php?action=get_review&review_id=${reviewId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showResponseModal(data.review);
            } else {
                showToast('Error loading review', 'error');
            }
        })
        .catch(error => {
            showToast('Error loading review', 'error');
            console.error('Error:', error);
        });
}

function showResponseModal(review) {
    const modal = document.getElementById('responseModal');
    const context = document.getElementById('reviewContext');
    
    // Show review context
    const stars = Array.from({length: 5}, (_, i) => 
        `<i class="ri-star-${i < review.rating ? 'fill' : 'line'} text-yellow-400"></i>`
    ).join('');
    
    context.innerHTML = `
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="font-medium">${review.first_name} ${review.last_name}</span>
                <div class="flex">${stars}</div>
                <span class="text-sm text-gray-500">${review.rating}/5</span>
            </div>
            <div class="text-sm font-medium text-gray-800">For: ${review.template_title || review.service_title}</div>
            <div class="text-sm text-gray-600">"${review.review}"</div>
        </div>
    `;
    
    document.getElementById('responseReviewId').value = review.id;
    modal.classList.remove('hidden');
}

function closeResponseModal() {
    document.getElementById('responseModal').classList.add('hidden');
    document.getElementById('responseForm').reset();
}

function submitResponse(event) {
    event.preventDefault();
    
    const reviewId = document.getElementById('responseReviewId').value;
    const responseText = document.getElementById('responseText').value;
    
    if (!responseText.trim()) {
        showToast('Please enter a response', 'error');
        return;
    }
    
    fetch('seller-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'respond_to_review',
            review_id: reviewId,
            response: responseText
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Response sent successfully', 'success');
            closeResponseModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Error sending response', 'error');
        console.error('Error:', error);
    });
}

function reportReview(reviewId) {
    if (confirm('Are you sure you want to report this review for inappropriate content?')) {
        fetch('seller-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'report_review',
                review_id: reviewId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Review reported successfully', 'success');
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

function shareReview(reviewId) {
    // Create a shareable link for the positive review
    const url = `${window.location.origin}/review/${reviewId}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Customer Review',
            text: 'Check out this great review!',
            url: url
        });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Review link copied to clipboard', 'success');
        });
    }
}
</script>