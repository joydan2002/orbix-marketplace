<?php
/**
 * Seller Messages & Customer Support
 * Handle customer inquiries and communications
 */
?>

<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Messages</h1>
            <p class="text-gray-600">Manage customer inquiries and support</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-white border border-gray-300 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50">
                    <i class="ri-filter-line"></i>
                    <span>All Messages</span>
                    <i class="ri-arrow-down-s-line"></i>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-2 z-10">
                    <a href="?section=messages&filter=all" class="block px-4 py-2 text-sm hover:bg-gray-100">All Messages</a>
                    <a href="?section=messages&filter=unread" class="block px-4 py-2 text-sm hover:bg-gray-100">Unread</a>
                    <a href="?section=messages&filter=urgent" class="block px-4 py-2 text-sm hover:bg-gray-100">Urgent</a>
                    <a href="?section=messages&filter=archived" class="block px-4 py-2 text-sm hover:bg-gray-100">Archived</a>
                </div>
            </div>
            <button onclick="composeMessage()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 flex items-center space-x-2">
                <i class="ri-mail-add-line"></i>
                <span>New Message</span>
            </button>
        </div>
    </div>

    <!-- Message Stats -->
    <div class="grid md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-mail-line text-xl text-blue-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= number_format($message_stats['total']) ?></div>
                    <div class="text-sm text-gray-600">Total Messages</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= number_format($message_stats['unread']) ?></div>
                    <div class="text-sm text-gray-600">Unread Messages</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="ri-time-line text-xl text-orange-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= $message_stats['avg_response'] ?>h</div>
                    <div class="text-sm text-gray-600">Avg Response Time</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-star-line text-xl text-purple-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= number_format($message_stats['satisfaction'], 1) ?>%</div>
                    <div class="text-sm text-gray-600">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Layout -->
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Message List -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border">
                <div class="p-6 border-b">
                    <div class="relative">
                        <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Search messages..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <?php foreach ($messages as $message): ?>
                    <div class="message-item p-4 border-b hover:bg-gray-50 cursor-pointer <?= !$message['is_read'] ? 'bg-blue-50' : '' ?>" 
                         onclick="openMessage(<?= $message['id'] ?>)">
                        <div class="flex items-start space-x-3">
                            <img src="<?= htmlspecialchars($message['customer_avatar']) ?>" alt="Customer" 
                                 class="w-10 h-10 rounded-full object-cover">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-medium text-secondary truncate"><?= htmlspecialchars($message['customer_name']) ?></h4>
                                    <span class="text-xs text-gray-500"><?= $message['time_ago'] ?></span>
                                </div>
                                <p class="text-sm text-gray-600 truncate"><?= htmlspecialchars($message['subject']) ?></p>
                                <p class="text-xs text-gray-500 truncate mt-1"><?= htmlspecialchars($message['preview']) ?></p>
                                <div class="flex items-center justify-between mt-2">
                                    <div class="flex items-center space-x-2">
                                        <?php if (!$message['is_read']): ?>
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        <?php endif; ?>
                                        <?php if ($message['is_urgent']): ?>
                                        <i class="ri-alarm-warning-line text-red-500 text-xs"></i>
                                        <?php endif; ?>
                                        <?php if ($message['has_attachment']): ?>
                                        <i class="ri-attachment-line text-gray-400 text-xs"></i>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">
                                        <?= ucfirst($message['category']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Message Detail -->
        <div class="lg:col-span-2">
            <div id="message-detail" class="bg-white rounded-xl shadow-sm border">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&background=3B82F6&color=fff" alt="Customer" 
                                 class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <h3 class="font-semibold text-secondary">John Doe</h3>
                                <p class="text-sm text-gray-600">john.doe@example.com</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="ri-archive-line"></i>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="ri-more-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Message Thread -->
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="space-y-6">
                        <!-- Customer Message -->
                        <div class="flex space-x-3">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&background=3B82F6&color=fff" alt="Customer" 
                                 class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-sm">John Doe</span>
                                        <span class="text-xs text-gray-500">2 hours ago</span>
                                    </div>
                                    <p class="text-sm text-gray-800">Hi, I'm having trouble with my recent order. The download link doesn't seem to be working. Can you help me with this?</p>
                                </div>
                            </div>
                        </div>

                        <!-- Your Reply -->
                        <div class="flex space-x-3 justify-end">
                            <div class="flex-1 max-w-md">
                                <div class="bg-primary text-white rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-sm">You</span>
                                        <span class="text-xs opacity-80">1 hour ago</span>
                                    </div>
                                    <p class="text-sm">Hi John! I'm sorry to hear about the issue. I've resent the download link to your email. Please check your inbox (and spam folder) and let me know if you still have any problems!</p>
                                </div>
                            </div>
                            <img src="https://ui-avatars.com/api/?name=Seller&background=10B981&color=fff" alt="You" 
                                 class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="p-6 border-t bg-gray-50">
                    <form onsubmit="sendReply(event)">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <button type="button" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-attachment-line"></i>
                                    </button>
                                    <button type="button" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-emotion-line"></i>
                                    </button>
                                    <button type="button" class="text-gray-400 hover:text-gray-600">
                                        <i class="ri-image-line"></i>
                                    </button>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" class="text-sm px-3 py-1 text-gray-600 hover:bg-gray-200 rounded">
                                        Save Draft
                                    </button>
                                    <select class="text-sm border border-gray-300 rounded px-2 py-1">
                                        <option>Normal Priority</option>
                                        <option>High Priority</option>
                                        <option>Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <textarea rows="4" placeholder="Type your reply..." 
                                      class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-transparent resize-none"></textarea>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <label class="flex items-center space-x-2 text-sm">
                                        <input type="checkbox" class="rounded">
                                        <span>Close ticket after sending</span>
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                        Send Reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-secondary mb-4">Quick Actions</h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="ri-customer-service-line text-blue-600"></i>
                </div>
                <h4 class="font-medium text-secondary mb-1">Create FAQ</h4>
                <p class="text-sm text-gray-600">Add common questions</p>
            </button>
            <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="ri-mail-send-line text-green-600"></i>
                </div>
                <h4 class="font-medium text-secondary mb-1">Send Newsletter</h4>
                <p class="text-sm text-gray-600">Update customers</p>
            </button>
            <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="ri-settings-line text-purple-600"></i>
                </div>
                <h4 class="font-medium text-secondary mb-1">Auto Responses</h4>
                <p class="text-sm text-gray-600">Set up templates</p>
            </button>
            <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-left">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-3">
                    <i class="ri-file-text-line text-orange-600"></i>
                </div>
                <h4 class="font-medium text-secondary mb-1">Export Messages</h4>
                <p class="text-sm text-gray-600">Download history</p>
            </button>
        </div>
    </div>
</div>

<script>
function openMessage(messageId) {
    // Load message details via AJAX
    fetch(`/api/messages/${messageId}`)
        .then(response => response.json())
        .then(data => {
            // Update message detail view
            document.getElementById('message-detail').innerHTML = data.html;
        });
}

function sendReply(event) {
    event.preventDefault();
    // Handle reply submission
    const formData = new FormData(event.target);
    
    fetch('/api/messages/reply', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and refresh
            showToast('Reply sent successfully!', 'success');
            location.reload();
        }
    });
}

function composeMessage() {
    // Open compose modal
    document.getElementById('compose-modal').classList.remove('hidden');
}
</script>