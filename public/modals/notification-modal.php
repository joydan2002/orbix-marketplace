<!-- Notification Modal -->
<div id="notificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 max-w-lg w-full mx-4 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-secondary">Notifications</h3>
            <button onclick="hideModal('notificationModal')" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="p-4 border-l-4 border-green-500 bg-green-50 rounded">
                <div class="flex items-start">
                    <i class="ri-check-circle-line text-green-500 mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-green-800">New Sale!</h4>
                        <p class="text-sm text-green-600">Your template "Modern Business Website" was purchased</p>
                        <span class="text-xs text-green-500">2 hours ago</span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-l-4 border-blue-500 bg-blue-50 rounded">
                <div class="flex items-start">
                    <i class="ri-message-3-line text-blue-500 mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-800">New Message</h4>
                        <p class="text-sm text-blue-600">Customer inquiry about your web design service</p>
                        <span class="text-xs text-blue-500">5 hours ago</span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-l-4 border-yellow-500 bg-yellow-50 rounded">
                <div class="flex items-start">
                    <i class="ri-star-line text-yellow-500 mr-3 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-yellow-800">New Review</h4>
                        <p class="text-sm text-yellow-600">You received a 5-star review on your latest service</p>
                        <span class="text-xs text-yellow-500">1 day ago</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <button onclick="hideModal('notificationModal')" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary/90">
                Mark All as Read
            </button>
        </div>
    </div>
</div>