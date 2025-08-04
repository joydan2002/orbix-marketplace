/**
 * Main Application JavaScript
 * Initializes all components and handles global functionality
 */

class OrbixMarket {
    constructor() {
        this.components = {};
        this.init();
    }
    
    init() {
        this.loadComponents();
        this.setupGlobalEventListeners();
        this.initializeAnimations();
    }
    
    loadComponents() {
        // Initialize component classes if they exist
        if (typeof FilterInteractions !== 'undefined') {
            this.components.filters = new FilterInteractions();
        }
        
        if (typeof AIMascot !== 'undefined') {
            this.components.aiMascot = new AIMascot();
        }
    }
    
    setupGlobalEventListeners() {
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Search functionality
        const searchInputs = document.querySelectorAll('input[placeholder*="Search"]');
        searchInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleSearch(input.value);
                }
            });
        });
        
        // Mobile menu toggle (if needed)
        this.setupMobileMenu();
    }
    
    setupMobileMenu() {
        const mobileMenuButton = document.querySelector('[data-mobile-menu]');
        const mobileMenu = document.querySelector('[data-mobile-menu-content]');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    }
    
    handleSearch(query) {
        console.log('Search query:', query);
        // In a real application, this would make an API call
        // For now, we'll just filter visible templates
        this.filterTemplatesBySearch(query);
    }
    
    filterTemplatesBySearch(query) {
        const templateCards = document.querySelectorAll('.template-card');
        const searchTerm = query.toLowerCase();
        
        templateCards.forEach(card => {
            const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
            const description = card.querySelector('p')?.textContent.toLowerCase() || '';
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = searchTerm ? 'none' : 'block';
            }
        });
    }
    
    initializeAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);
        
        // Observe template cards and service cards
        document.querySelectorAll('.template-card, .service-card').forEach(card => {
            observer.observe(card);
        });
    }
    
    // Utility methods
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        } text-white max-w-sm`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    updateCartCount(count) {
        const cartBadge = document.querySelector('[class*="bg-primary rounded-full text-white"]');
        if (cartBadge) {
            cartBadge.textContent = count;
        }
    }
}

// Initialize the application when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.orbixMarket = new OrbixMarket();
});

// Modal functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
}

// Specific function for Add Product modal to match the button onclick
function showAddProductModal() {
    showModal('addProductModal');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
        // This is the modal backdrop
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                hideModal(modal.id);
            }
        });
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                hideModal(modal.id);
            }
        });
    }
});

// Auto-refresh functionality for development
if (typeof AutoRefresh !== 'undefined') {
    new AutoRefresh({
        interval: 30000, // 30 seconds
        checkUrl: window.location.href
    });
}