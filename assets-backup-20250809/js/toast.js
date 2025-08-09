/**
 * Toast Notification System for Orbix Market
 * A modern replacement for alert(), confirm(), and other browser dialogs
 */

class ToastManager {
    constructor() {
        this.container = null;
        this.toasts = [];
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.querySelector('.toast-container')) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.querySelector('.toast-container');
        }
    }

    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - Type: 'success', 'error', 'warning', 'info'
     * @param {string} title - Optional title for the toast
     * @param {number} duration - Duration in milliseconds (0 for persistent)
     */
    show(message, type = 'info', title = '', duration = 5000) {
        const toast = this.createToast(message, type, title, duration);
        this.container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Auto remove
        if (duration > 0) {
            const progressBar = toast.querySelector('.toast-progress');
            if (progressBar) {
                progressBar.style.width = '100%';
                progressBar.style.transitionDuration = `${duration}ms`;
                setTimeout(() => {
                    progressBar.style.width = '0%';
                }, 10);
            }

            setTimeout(() => {
                this.remove(toast);
            }, duration);
        }

        return toast;
    }

    createToast(message, type, title, duration) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'i'
        };

        const titles = {
            success: title || 'Success',
            error: title || 'Error',
            warning: title || 'Warning',
            info: title || 'Info'
        };

        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-content">
                <div class="toast-title">${titles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="toastManager.remove(this.parentElement)">×</button>
            ${duration > 0 ? '<div class="toast-progress"></div>' : ''}
        `;

        return toast;
    }

    remove(toast) {
        if (!toast || !toast.parentElement) return;
        
        toast.classList.add('hide');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 300);
    }

    // Convenience methods
    success(message, title = '', duration = 5000) {
        return this.show(message, 'success', title, duration);
    }

    error(message, title = '', duration = 7000) {
        return this.show(message, 'error', title, duration);
    }

    warning(message, title = '', duration = 6000) {
        return this.show(message, 'warning', title, duration);
    }

    info(message, title = '', duration = 5000) {
        return this.show(message, 'info', title, duration);
    }

    /**
     * Show a confirmation dialog using toast
     * @param {string} message - The confirmation message
     * @param {function} onConfirm - Callback when confirmed
     * @param {function} onCancel - Callback when cancelled
     * @param {string} title - Optional title
     */
    confirm(message, onConfirm = () => {}, onCancel = () => {}, title = 'Confirm Action') {
        const toast = document.createElement('div');
        toast.className = 'toast warning';
        
        toast.innerHTML = `
            <div class="toast-icon">?</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
                <div style="margin-top: 12px; display: flex; gap: 8px;">
                    <button class="confirm-btn" style="background: var(--primary-color); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">Confirm</button>
                    <button class="cancel-btn" style="background: var(--gray-200); color: var(--gray-700); border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">Cancel</button>
                </div>
            </div>
            <button class="toast-close">×</button>
        `;

        const confirmBtn = toast.querySelector('.confirm-btn');
        const cancelBtn = toast.querySelector('.cancel-btn');
        const closeBtn = toast.querySelector('.toast-close');

        confirmBtn.onclick = () => {
            this.remove(toast);
            onConfirm();
        };

        cancelBtn.onclick = () => {
            this.remove(toast);
            onCancel();
        };

        closeBtn.onclick = () => {
            this.remove(toast);
            onCancel();
        };

        this.container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        return toast;
    }

    /**
     * Clear all toasts
     */
    clear() {
        const toasts = this.container.querySelectorAll('.toast');
        toasts.forEach(toast => this.remove(toast));
    }
}

// Create global instance
const toastManager = new ToastManager();

// Helper functions to replace alert, confirm, etc.
function showToast(message, type = 'info', title = '', duration = 5000) {
    return toastManager.show(message, type, title, duration);
}

function showSuccess(message, title = '', duration = 5000) {
    return toastManager.success(message, title, duration);
}

function showError(message, title = '', duration = 7000) {
    return toastManager.error(message, title, duration);
}

function showWarning(message, title = '', duration = 6000) {
    return toastManager.warning(message, title, duration);
}

function showInfo(message, title = '', duration = 5000) {
    return toastManager.info(message, title, duration);
}

function showConfirm(message, onConfirm, onCancel, title = 'Confirm Action') {
    return toastManager.confirm(message, onConfirm, onCancel, title);
}

// Replace browser alert
function toast(message, type = 'info') {
    return showToast(message, type);
}

// Make it available globally
window.toastManager = toastManager;
window.showToast = showToast;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.showConfirm = showConfirm;
window.toast = toast;
