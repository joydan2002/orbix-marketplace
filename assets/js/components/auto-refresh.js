/**
 * Auto Refresh Manager
 * Handles automatic page refresh when database changes are detected
 * Optimized for performance with minimal server requests
 */

class AutoRefreshManager {
    constructor(options = {}) {
        this.options = {
            checkInterval: 30000, // Check every 30 seconds
            apiEndpoint: 'api.php',
            enableNotifications: true,
            debugMode: false,
            ...options
        };
        
        this.lastDbTimestamp = null;
        this.checkTimer = null;
        this.isChecking = false;
        this.consecutiveErrors = 0;
        this.maxErrors = 3;
        
        this.init();
    }
    
    init() {
        // Only run if page is visible to save resources
        if (document.visibilityState === 'visible') {
            this.startChecking();
        }
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.startChecking();
            } else {
                this.stopChecking();
            }
        });
        
        // Initial check
        this.checkForChanges();
        
        this.log('Auto Refresh Manager initialized');
    }
    
    startChecking() {
        if (this.checkTimer) return;
        
        this.checkTimer = setInterval(() => {
            this.checkForChanges();
        }, this.options.checkInterval);
        
        this.log('Started checking for database changes');
    }
    
    stopChecking() {
        if (this.checkTimer) {
            clearInterval(this.checkTimer);
            this.checkTimer = null;
            this.log('Stopped checking for database changes');
        }
    }
    
    async checkForChanges() {
        if (this.isChecking) return;
        
        this.isChecking = true;
        
        try {
            const response = await fetch(`${this.options.apiEndpoint}?action=status&t=${Date.now()}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.consecutiveErrors = 0; // Reset error counter
                
                if (this.lastDbTimestamp === null) {
                    // First check, store timestamp
                    this.lastDbTimestamp = data.db_timestamp;
                    this.log('Initial database timestamp stored:', data.db_timestamp);
                } else if (data.db_timestamp > this.lastDbTimestamp) {
                    // Database has changed!
                    this.log('Database change detected!', {
                        old: this.lastDbTimestamp,
                        new: data.db_timestamp
                    });
                    
                    this.handleDatabaseChange();
                    this.lastDbTimestamp = data.db_timestamp;
                }
            } else {
                throw new Error(data.error || 'API returned error');
            }
            
        } catch (error) {
            this.consecutiveErrors++;
            this.log('Error checking for changes:', error.message);
            
            // Stop checking if too many consecutive errors
            if (this.consecutiveErrors >= this.maxErrors) {
                this.log('Too many consecutive errors, stopping auto-refresh');
                this.stopChecking();
                
                if (this.options.enableNotifications) {
                    this.showNotification('Auto-refresh disabled due to connection issues', 'error');
                }
            }
        } finally {
            this.isChecking = false;
        }
    }
    
    handleDatabaseChange() {
        if (this.options.enableNotifications) {
            this.showNotification('Content updated! Refreshing...', 'info');
        }
        
        // Add a small delay to show the notification
        setTimeout(() => {
            // Smooth reload with cache busting
            window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now();
        }, 1000);
    }
    
    showNotification(message, type = 'info') {
        // Create notification element if it doesn't exist
        let notification = document.getElementById('auto-refresh-notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'auto-refresh-notification';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            `;
            document.body.appendChild(notification);
        }
        
        // Set colors based on type
        const colors = {
            info: 'linear-gradient(135deg, #3B82F6, #1D4ED8)',
            success: 'linear-gradient(135deg, #10B981, #059669)',
            warning: 'linear-gradient(135deg, #F59E0B, #D97706)',
            error: 'linear-gradient(135deg, #EF4444, #DC2626)'
        };
        
        notification.style.background = colors[type] || colors.info;
        notification.textContent = message;
        
        // Animate in
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // Manual refresh trigger
    forceRefresh() {
        this.log('Force refresh triggered');
        this.checkForChanges();
    }
    
    // Get current status
    async getStatus() {
        try {
            const response = await fetch(`${this.options.apiEndpoint}?action=status&t=${Date.now()}`);
            const data = await response.json();
            return data;
        } catch (error) {
            this.log('Error getting status:', error.message);
            return null;
        }
    }
    
    log(...args) {
        if (this.options.debugMode) {
            console.log('[AutoRefresh]', ...args);
        }
    }
    
    // Cleanup
    destroy() {
        this.stopChecking();
        document.removeEventListener('visibilitychange', this.handleVisibilityChange);
        this.log('Auto Refresh Manager destroyed');
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on main pages that show templates
    const isMainPage = document.querySelector('.template-card') !== null;
    
    if (isMainPage) {
        window.autoRefreshManager = new AutoRefreshManager({
            checkInterval: 30000, // 30 seconds
            enableNotifications: true,
            debugMode: false // Set to true for debugging
        });
        
        // Add manual refresh button for testing (only in debug mode)
        if (window.location.search.includes('debug=1')) {
            const refreshBtn = document.createElement('button');
            refreshBtn.textContent = '🔄 Force Check';
            refreshBtn.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                padding: 10px;
                background: #FF5F1F;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                z-index: 9999;
                font-size: 12px;
            `;
            refreshBtn.onclick = () => window.autoRefreshManager.forceRefresh();
            document.body.appendChild(refreshBtn);
        }
    }
});