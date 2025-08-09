/**
 * Header Interactions
 * Handles header scroll effects and dropdown functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const templatesDropdown = document.getElementById('templates-dropdown');
    
    // Header scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.1)';
        }
    });
    
    // Templates dropdown functionality
    if (templatesDropdown) {
        const dropdownContent = templatesDropdown.querySelector('div[class*="absolute"]');
        
        templatesDropdown.addEventListener('mouseenter', () => {
            dropdownContent.style.display = 'block';
            requestAnimationFrame(() => {
                dropdownContent.style.opacity = '1';
                dropdownContent.style.visibility = 'visible';
                dropdownContent.style.transform = 'translateY(0)';
            });
        });
        
        templatesDropdown.addEventListener('mouseleave', () => {
            dropdownContent.style.opacity = '0';
            dropdownContent.style.visibility = 'hidden';
            dropdownContent.style.transform = 'translateY(2px)';
        });
    }
});