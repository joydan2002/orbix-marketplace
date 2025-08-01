/**
 * Filter Interactions Component Class
 * Handles category filtering and sorting functionality
 */
class FilterInteractions {
    constructor() {
        this.categoryButtons = [];
        this.activeCategory = 'all';
        this.init();
    }

    init() {
        this.categoryButtons = document.querySelectorAll('button[class*="px-4 py-2"]');
        this.bindEvents();
    }

    bindEvents() {
        this.categoryButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                this.handleCategoryClick(e, button);
            });
        });
    }

    handleCategoryClick(event, clickedButton) {
        event.preventDefault();
        
        // Remove active state from all buttons
        this.categoryButtons.forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('bg-white/80', 'text-secondary');
        });
        
        // Add active state to clicked button
        clickedButton.classList.remove('bg-white/80', 'text-secondary');
        clickedButton.classList.add('bg-primary', 'text-white');
        
        // Update active category
        this.activeCategory = clickedButton.textContent.toLowerCase().trim();
        
        // Filter templates
        this.filterTemplates(this.activeCategory);
    }

    filterTemplates(category) {
        const templateCards = document.querySelectorAll('.template-card');
        
        templateCards.forEach(card => {
            const cardCategory = card.dataset.category || 'all';
            
            if (category === 'all templates' || category === 'all' || cardCategory === category) {
                card.style.display = 'block';
                card.style.opacity = '1';
            } else {
                card.style.display = 'none';
                card.style.opacity = '0';
            }
        });
        
        // Animate the filtering
        this.animateFilterTransition();
    }

    animateFilterTransition() {
        const visibleCards = document.querySelectorAll('.template-card[style*="display: block"]');
        
        visibleCards.forEach((card, index) => {
            card.style.transform = 'translateY(20px)';
            card.style.opacity = '0';
            
            setTimeout(() => {
                card.style.transform = 'translateY(0)';
                card.style.opacity = '1';
                card.style.transition = 'all 0.3s ease';
            }, index * 100);
        });
    }

    resetFilters() {
        this.activeCategory = 'all';
        this.filterTemplates('all');
        
        // Reset button states
        this.categoryButtons.forEach((btn, index) => {
            if (index === 0) { // First button is "All Templates"
                btn.classList.add('bg-primary', 'text-white');
                btn.classList.remove('bg-white/80', 'text-secondary');
            } else {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-white/80', 'text-secondary');
            }
        });
    }
}

/**
 * Filter Interactions
 * Handles category buttons and filter functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Category filter buttons
    const categoryButtons = document.querySelectorAll('button[class*="px-4 py-2"]');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state from all buttons
            categoryButtons.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-white/80', 'text-secondary');
            });
            
            // Add active state to clicked button
            this.classList.remove('bg-white/80', 'text-secondary');
            this.classList.add('bg-primary', 'text-white');
            
            // Filter templates based on category
            const category = this.textContent.trim();
            filterTemplates(category);
        });
    });
    
    // Filter templates function
    function filterTemplates(category) {
        const templateCards = document.querySelectorAll('.template-card');
        
        templateCards.forEach(card => {
            if (category === 'All Templates') {
                card.style.display = 'block';
                card.style.opacity = '1';
            } else {
                // In a real app, you'd check the template's category
                // For now, we'll show all templates with a fade effect
                card.style.opacity = '0.5';
                setTimeout(() => {
                    card.style.opacity = '1';
                }, 300);
            }
        });
    }
    
    // Price range filters
    const priceCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    priceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // In a real app, this would filter templates by price
            console.log('Price filter changed:', this.nextElementSibling.textContent);
        });
    });
});