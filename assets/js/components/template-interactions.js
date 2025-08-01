/**
 * Template Interactions Component Class
 * Handles template card interactions like favorites and add to cart
 */

document.addEventListener('DOMContentLoaded', function() {
    // Heart/Favorite button functionality
    const heartButtons = document.querySelectorAll('.favorite-btn');
    heartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('ri-heart-line')) {
                icon.classList.remove('ri-heart-line');
                icon.classList.add('ri-heart-fill');
                icon.style.color = '#ef4444';
                
                // Add animation
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            } else {
                icon.classList.remove('ri-heart-fill');
                icon.classList.add('ri-heart-line');
                icon.style.color = '';
            }
        });
    });
    
    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('button:contains("Add to Cart")');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add animation
            const originalText = this.textContent;
            this.textContent = 'Added!';
            this.style.background = '#10b981';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.background = '';
            }, 2000);
        });
    });
    
    // Preview button functionality
    const previewButtons = document.querySelectorAll('button:contains("Preview")');
    previewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            // In a real app, this would open a modal or new window
            console.log('Preview functionality would be implemented here');
        });
    });
});