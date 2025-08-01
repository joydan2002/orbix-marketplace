/**
 * AI Mascot Component Class
 * Handles AI mascot interactions and chat functionality
 */
class AIMascot {
    constructor() {
        this.mascotButton = null;
        this.chatModal = null;
        this.init();
    }

    init() {
        this.mascotButton = document.querySelector('.ai-mascot button');
        this.bindEvents();
    }

    bindEvents() {
        if (this.mascotButton) {
            this.mascotButton.addEventListener('click', (e) => {
                this.handleMascotClick(e);
            });
        }
    }

    handleMascotClick(event) {
        event.preventDefault();
        this.showChatModal();
    }

    showChatModal() {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4';
        
        // Create modal content
        const modal = document.createElement('div');
        modal.className = 'bg-white rounded-2xl p-6 max-w-md w-full glass-effect';
        modal.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-robot-line text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-secondary mb-2">AI Assistant</h3>
                <p class="text-gray-600 mb-6">Hi! I'm your AI assistant. How can I help you find the perfect template today?</p>
                
                <div class="space-y-3">
                    <button class="w-full bg-white/80 hover:bg-white text-secondary py-2 px-4 rounded-button text-sm font-medium transition-colors">
                        Find templates by industry
                    </button>
                    <button class="w-full bg-white/80 hover:bg-white text-secondary py-2 px-4 rounded-button text-sm font-medium transition-colors">
                        Get customization help
                    </button>
                    <button class="w-full bg-white/80 hover:bg-white text-secondary py-2 px-4 rounded-button text-sm font-medium transition-colors">
                        Contact support
                    </button>
                </div>
                
                <button class="mt-4 text-gray-500 hover:text-gray-700 text-sm" onclick="this.closest('.fixed').remove()">
                    Close
                </button>
            </div>
        `;
        
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
        
        // Close on overlay click
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.remove();
            }
        });
        
        // Add entrance animation
        modal.style.transform = 'scale(0.8)';
        modal.style.opacity = '0';
        
        requestAnimationFrame(() => {
            modal.style.transform = 'scale(1)';
            modal.style.opacity = '1';
            modal.style.transition = 'all 0.3s ease';
        });
    }
}