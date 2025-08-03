<!-- Promote Modal -->
<div id="promoteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-secondary">Promote Your Products</h3>
            <button onclick="hideModal('promoteModal')" class="text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                <h4 class="font-semibold text-blue-800 mb-2">Featured Listing</h4>
                <p class="text-sm text-blue-600 mb-3">Get your product featured at the top of search results</p>
                <div class="flex justify-between items-center">
                    <span class="text-blue-800 font-bold">$9.99/month</span>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded text-sm hover:bg-blue-600">
                        Select
                    </button>
                </div>
            </div>
            
            <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                <h4 class="font-semibold text-green-800 mb-2">Premium Boost</h4>
                <p class="text-sm text-green-600 mb-3">Increase visibility across all categories</p>
                <div class="flex justify-between items-center">
                    <span class="text-green-800 font-bold">$19.99/month</span>
                    <button class="bg-green-500 text-white px-4 py-2 rounded text-sm hover:bg-green-600">
                        Select
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex gap-3">
            <button onclick="hideModal('promoteModal')" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded hover:bg-gray-50">
                Cancel
            </button>
        </div>
    </div>
</div>