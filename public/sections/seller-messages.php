<?php
/**
 * Seller Messages Management
 * Display real messages from database
 */

// Load real data from database
require_once 'seller-data-loader.php';
?>

<!-- Messages Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">Messages</h1>
            <p class="text-gray-600">Communicate with your customers</p>
        </div>
        <div class="flex items-center space-x-4">
            <?php if ($unreadMessages > 0): ?>
            <div class="bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm font-medium">
                <?= $unreadMessages ?> unread messages
            </div>
            <?php endif; ?>
            <button onclick="composeMessage()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 transition-colors flex items-center space-x-2">
                <i class="ri-mail-line text-lg"></i>
                <span>New Message</span>
            </button>
        </div>
    </div>
</div>

<!-- Messages List -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <!-- Search and Filter -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-secondary">All Messages</h3>
            <div class="flex items-center space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           id="messageSearch"
                           placeholder="Search messages..." 
                           class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
                    <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Status Filter -->
                <select id="messageStatusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Messages</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Messages Container -->
    <div id="messagesContainer">
        <?php if (empty($sellerMessages)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-message-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary mb-2">No Messages Yet</h3>
            <p class="text-gray-600 mb-6">Messages from customers will appear here</p>
        </div>
        <?php else: ?>
        
        <!-- Messages List -->
        <div class="divide-y divide-gray-100">
            <?php foreach ($sellerMessages as $message): ?>
            <div class="message-item p-6 hover:bg-gray-50 transition-colors cursor-pointer <?= !$message['is_read'] ? 'bg-blue-50 border-l-4 border-blue-400' : '' ?>" 
                 data-read="<?= $message['is_read'] ? 'read' : 'unread' ?>"
                 onclick="openMessage(<?= $message['id'] ?>)">
                
                <div class="flex items-start space-x-4">
                    <!-- Sender Avatar -->
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-medium text-gray-600">
                            <?= strtoupper(substr($message['first_name'], 0, 1) . substr($message['last_name'], 0, 1)) ?>
                        </span>
                    </div>
                    
                    <!-- Message Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-3">
                                <h4 class="text-lg font-semibold text-secondary">
                                    <?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?>
                                </h4>
                                <?php if (!$message['is_read']): ?>
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?= date('M j, Y \a\t g:i A', strtotime($message['created_at'])) ?>
                            </div>
                        </div>
                        
                        <!-- Subject -->
                        <div class="text-base font-medium text-gray-800 mb-2">
                            <?= htmlspecialchars($message['subject']) ?>
                        </div>
                        
                        <!-- Message Preview -->
                        <div class="text-gray-600 line-clamp-2 mb-3">
                            <?= htmlspecialchars(substr($message['message'], 0, 150)) ?><?= strlen($message['message']) > 150 ? '...' : '' ?>
                        </div>
                        
                        <!-- Message Actions -->
                        <div class="flex items-center space-x-4">
                            <button onclick="event.stopPropagation(); replyToMessage(<?= $message['id'] ?>)" 
                                    class="text-sm text-primary hover:text-primary/80 font-medium">
                                Reply
                            </button>
                            
                            <?php if (!$message['is_read']): ?>
                            <button onclick="event.stopPropagation(); markAsRead(<?= $message['id'] ?>)" 
                                    class="text-sm text-blue-600 hover:text-blue-700">
                                Mark as Read
                            </button>
                            <?php endif; ?>
                            
                            <button onclick="event.stopPropagation(); archiveMessage(<?= $message['id'] ?>)" 
                                    class="text-sm text-gray-500 hover:text-gray-700">
                                Archive
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Message Modal -->
<div id="messageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-secondary">Message Details</h3>
                <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh]" id="messageContent">
            <!-- Message content will be loaded here -->
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl max-w-2xl w-full mx-4">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-secondary">Reply to Message</h3>
                <button onclick="closeReplyModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form id="replyForm" onsubmit="sendReply(event)">
                <input type="hidden" id="replyMessageId" value="">
                <input type="hidden" id="replyRecipientId" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <input type="text" 
                           id="replySubject"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                           placeholder="Re: Original subject">
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea id="replyMessage" rows="6"
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                              placeholder="Type your reply..."></textarea>
                </div>
                
                <div class="flex items-center justify-end space-x-4">
                    <button type="button" onclick="closeReplyModal()" 
                            class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Messages Management Scripts -->
<script>
// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('messageSearch');
    const statusFilter = document.getElementById('messageStatusFilter');
    const messageItems = document.querySelectorAll('.message-item');
    
    function filterMessages() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter_val = statusFilter.value;
        
        messageItems.forEach(item => {
            const sender = item.querySelector('h4').textContent.toLowerCase();
            const subject = item.querySelector('.font-medium').textContent.toLowerCase();
            const content = item.querySelector('.line-clamp-2').textContent.toLowerCase();
            const status = item.dataset.read;
            
            const matchesSearch = sender.includes(searchTerm) || subject.includes(searchTerm) || content.includes(searchTerm);
            const matchesStatus = !statusFilter_val || status === statusFilter_val;
            
            if (matchesSearch && matchesStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterMessages);
    statusFilter.addEventListener('change', filterMessages);
});

// Message management functions
function openMessage(messageId) {
    fetch(`seller-api.php?action=get_message&message_id=${messageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessageModal(data.message);
                if (!data.message.is_read) {
                    markAsRead(messageId);
                }
            } else {
                showToast('Error loading message', 'error');
            }
        })
        .catch(error => {
            showToast('Error loading message', 'error');
            console.error('Error:', error);
        });
}

function showMessageModal(message) {
    const modal = document.getElementById('messageModal');
    const content = document.getElementById('messageContent');
    
    content.innerHTML = `
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-600">
                        ${message.first_name.charAt(0).toUpperCase()}${message.last_name.charAt(0).toUpperCase()}
                    </span>
                </div>
                <div>
                    <div class="font-semibold text-lg">${message.first_name} ${message.last_name}</div>
                    <div class="text-sm text-gray-600">${new Date(message.created_at).toLocaleString()}</div>
                </div>
            </div>
            
            <div>
                <h4 class="text-xl font-semibold text-gray-900 mb-4">${message.subject}</h4>
                <div class="prose max-w-none text-gray-700 whitespace-pre-wrap">${message.message}</div>
            </div>
            
            <div class="flex items-center space-x-4 pt-4 border-t">
                <button onclick="replyToMessage(${message.id})" 
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90">
                    Reply
                </button>
                <button onclick="archiveMessage(${message.id})" 
                        class="text-gray-600 hover:text-gray-800">
                    Archive
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.add('hidden');
}

function replyToMessage(messageId) {
    fetch(`seller-api.php?action=get_message&message_id=${messageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showReplyModal(data.message);
                closeMessageModal();
            }
        });
}

function showReplyModal(message) {
    const modal = document.getElementById('replyModal');
    
    document.getElementById('replyMessageId').value = message.id;
    document.getElementById('replyRecipientId').value = message.sender_id;
    document.getElementById('replySubject').value = 'Re: ' + message.subject;
    
    modal.classList.remove('hidden');
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
    document.getElementById('replyForm').reset();
}

function sendReply(event) {
    event.preventDefault();
    
    const recipientId = document.getElementById('replyRecipientId').value;
    const subject = document.getElementById('replySubject').value;
    const message = document.getElementById('replyMessage').value;
    
    if (!subject.trim() || !message.trim()) {
        showToast('Please fill in all fields', 'error');
        return;
    }
    
    fetch('seller-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'send_message',
            recipient_id: recipientId,
            subject: subject,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Reply sent successfully', 'success');
            closeReplyModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    });
}

function markAsRead(messageId) {
    fetch('seller-api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'mark_message_read',
            message_id: messageId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const messageItem = document.querySelector(`[onclick*="${messageId}"]`);
            if (messageItem) {
                messageItem.classList.remove('bg-blue-50', 'border-l-4', 'border-blue-400');
                messageItem.dataset.read = 'read';
                const unreadDot = messageItem.querySelector('.bg-blue-500');
                if (unreadDot) unreadDot.remove();
            }
        }
    });
}

function archiveMessage(messageId) {
    showConfirm('Are you sure you want to archive this message?', () => {
        fetch('seller-api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'archive_message',
                message_id: messageId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Message archived successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    });
}

function composeMessage() {
    showToast('Compose message feature coming soon', 'info');
}
</script>