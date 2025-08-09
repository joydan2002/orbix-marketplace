<!-- Message Modal -->
<div id="messageModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[85vh] overflow-hidden">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-6 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-secondary flex items-center">
                        <i class="ri-message-3-line mr-3 text-primary"></i>
                        <span id="message-modal-title">Customer Message</span>
                    </h2>
                    <button onclick="hideModal('messageModal')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                        <i class="ri-close-line text-xl text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Message Thread -->
                <div id="message-thread" class="max-h-96 overflow-y-auto mb-6 space-y-4">
                    <!-- Messages will be loaded here -->
                </div>
                
                <!-- Reply Form -->
                <div class="border-t border-gray-200 pt-6">
                    <form id="replyForm" class="space-y-4">
                        <input type="hidden" name="thread_id" id="reply-thread-id">
                        <input type="hidden" name="customer_id" id="reply-customer-id">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reply</label>
                            <textarea name="message" id="reply-message" required rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                      placeholder="Type your reply here..."></textarea>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button type="button" onclick="attachFile()" class="text-gray-500 hover:text-primary flex items-center">
                                    <i class="ri-attachment-line mr-2"></i>
                                    Attach File
                                </button>
                                <input type="file" id="message-attachment" name="attachment" class="hidden" accept="image/*,.pdf,.doc,.docx">
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="markAsResolved()" class="text-green-600 hover:text-green-700 flex items-center">
                                    <i class="ri-check-line mr-2"></i>
                                    Mark Resolved
                                </button>
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary/90 transition-colors flex items-center">
                                    <i class="ri-send-plane-line mr-2"></i>
                                    Send Reply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewMessage(threadId) {
    // Fetch message thread
    fetch(`api/seller.php?action=get_message_thread&thread_id=${threadId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const thread = data.thread;
                
                // Update modal title
                document.getElementById('message-modal-title').textContent = `Message from ${thread.customer_name}`;
                
                // Set form fields
                document.getElementById('reply-thread-id').value = threadId;
                document.getElementById('reply-customer-id').value = thread.customer_id;
                
                // Build message thread
                const messageThread = document.getElementById('message-thread');
                messageThread.innerHTML = '';
                
                thread.messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `flex ${message.sender_type === 'seller' ? 'justify-end' : 'justify-start'}`;
                    
                    messageDiv.innerHTML = `
                        <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-lg ${
                            message.sender_type === 'seller' 
                                ? 'bg-primary text-white' 
                                : 'bg-gray-100 text-gray-800'
                        }">
                            <div class="text-sm">${message.message}</div>
                            ${message.attachment ? `
                                <div class="mt-2 pt-2 border-t border-white/20">
                                    <a href="${message.attachment}" target="_blank" class="flex items-center text-xs opacity-80 hover:opacity-100">
                                        <i class="ri-attachment-line mr-1"></i>
                                        Attachment
                                    </a>
                                </div>
                            ` : ''}
                            <div class="text-xs opacity-80 mt-2">${formatDate(message.created_at)}</div>
                        </div>
                    `;
                    
                    messageThread.appendChild(messageDiv);
                });
                
                // Scroll to bottom
                messageThread.scrollTop = messageThread.scrollHeight;
                
                showModal('messageModal');
            } else {
                showErrorToast('Failed to load message thread');
            }
        })
        .catch(error => {
            console.error('View message error:', error);
            showErrorToast('Failed to load message thread');
        });
}

function attachFile() {
    document.getElementById('message-attachment').click();
}

function markAsResolved() {
    const threadId = document.getElementById('reply-thread-id').value;
    
    fetch('api/seller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=mark_message_resolved&thread_id=${threadId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Message marked as resolved');
            hideModal('messageModal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            throw new Error(data.error || 'Failed to mark as resolved');
        }
    })
    .catch(error => {
        console.error('Mark resolved error:', error);
        showErrorToast(error.message || 'Failed to mark as resolved');
    });
}

// Handle reply form submission
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'reply_message');
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i>Sending...';
    submitBtn.disabled = true;
    
    fetch('api/seller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Reply sent successfully!');
            
            // Add new message to thread
            const messageThread = document.getElementById('message-thread');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-end';
            messageDiv.innerHTML = `
                <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-lg bg-primary text-white">
                    <div class="text-sm">${document.getElementById('reply-message').value}</div>
                    <div class="text-xs opacity-80 mt-2">Just now</div>
                </div>
            `;
            messageThread.appendChild(messageDiv);
            messageThread.scrollTop = messageThread.scrollHeight;
            
            // Clear form
            document.getElementById('reply-message').value = '';
        } else {
            throw new Error(data.error || 'Failed to send reply');
        }
    })
    .catch(error => {
        console.error('Reply error:', error);
        showErrorToast(error.message || 'Failed to send reply');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) {
        return 'Today';
    } else if (diffDays === 2) {
        return 'Yesterday';
    } else if (diffDays <= 7) {
        return `${diffDays - 1} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}
</script>