/**
 * Seller Dashboard JavaScript
 * Handles all seller dashboard interactions and AJAX calls
 */

class SellerDashboard {
    constructor() {
        this.currentSection = 'overview';
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadInitialData();
        this.initCharts();
    }
    
    bindEvents() {
        // Navigation events
        document.querySelectorAll('.nav-link[data-section]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchSection(e.target.dataset.section);
            });
        });
        
        // Modal events
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal]')) {
                this.openModal(e.target.dataset.modal);
            }
            
            if (e.target.matches('.modal-close, .modal-backdrop')) {
                this.closeModal();
            }
        });
        
        // Form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.matches('#addProductForm')) {
                e.preventDefault();
                this.handleAddProduct(e.target);
            }
            
            if (e.target.matches('#editProductForm')) {
                e.preventDefault();
                this.handleEditProduct(e.target);
            }
        });
        
        // File upload events
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[type="file"][data-upload]')) {
                this.handleFileUpload(e.target);
            }
        });
        
        // Auto-refresh for real-time data
        setInterval(() => {
            if (this.currentSection === 'overview') {
                this.loadStats();
            }
        }, 30000); // Refresh every 30 seconds
    }
    
    switchSection(section) {
        // Update navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`[data-section="${section}"]`).classList.add('active');
        
        // Update content
        document.querySelectorAll('.dashboard-section').forEach(sec => {
            sec.style.display = 'none';
        });
        document.getElementById(`${section}-section`).style.display = 'block';
        
        this.currentSection = section;
        this.loadSectionData(section);
    }
    
    async loadInitialData() {
        try {
            await this.loadStats();
            await this.loadRecentOrders();
        } catch (error) {
            console.error('Failed to load initial data:', error);
            this.showToast('Failed to load dashboard data', 'error');
        }
    }
    
    async loadStats() {
        try {
            const response = await fetch('/seller-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_seller_stats' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateStatsDisplay(data.stats);
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    }
    
    updateStatsDisplay(stats) {
        // Update stat cards
        const statElements = {
            'total-products': stats.total_products || 0,
            'total-earnings': '$' + (stats.total_earnings || 0).toLocaleString(),
            'total-orders': stats.total_orders || 0,
            'avg-rating': (stats.avg_rating || 0).toFixed(1) + ' ★'
        };
        
        Object.entries(statElements).forEach(([id, value]) => {
            const element = document.querySelector(`[data-stat="${id}"]`);
            if (element) {
                element.textContent = value;
                element.classList.add('updated');
                setTimeout(() => element.classList.remove('updated'), 1000);
            }
        });
    }
    
    async loadRecentOrders() {
        try {
            const response = await fetch('/seller-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_recent_orders', limit: 5 })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateRecentOrdersDisplay(data.orders);
            }
        } catch (error) {
            console.error('Failed to load recent orders:', error);
        }
    }
    
    updateRecentOrdersDisplay(orders) {
        const container = document.querySelector('#recent-orders-list');
        if (!container) return;
        
        if (orders.length === 0) {
            container.innerHTML = '<p class="text-muted">No recent orders</p>';
            return;
        }
        
        container.innerHTML = orders.map(order => `
            <div class="order-item d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <strong>${order.product_name}</strong>
                    <small class="text-muted d-block">${order.type} • ${new Date(order.created_at).toLocaleDateString()}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-success">${order.status}</span>
                    <div class="text-muted small">$${order.price} × ${order.quantity}</div>
                </div>
            </div>
        `).join('');
    }
    
    async loadSectionData(section) {
        switch (section) {
            case 'products':
                await this.loadProducts();
                break;
            case 'analytics':
                await this.loadAnalytics();
                break;
            case 'earnings':
                await this.loadEarnings();
                break;
            case 'orders':
                await this.loadOrders();
                break;
        }
    }
    
    async loadProducts() {
        try {
            const response = await fetch('/seller-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_seller_products', page: 1, limit: 20 })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateProductsDisplay(data.products);
            }
        } catch (error) {
            console.error('Failed to load products:', error);
        }
    }
    
    updateProductsDisplay(products) {
        const container = document.querySelector('#products-list');
        if (!container) return;
        
        if (products.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">No products found. <a href="#" data-modal="addProduct">Add your first product</a></p>';
            return;
        }
        
        container.innerHTML = products.map(product => `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card product-card">
                    <img src="${product.preview_image || '/assets/images/no-image.png'}" class="card-img-top" alt="${product.title}">
                    <div class="card-body">
                        <h6 class="card-title">${product.title}</h6>
                        <p class="card-text text-muted small">${product.description.substring(0, 100)}...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-${this.getStatusColor(product.status)}">${product.status}</span>
                            <strong>$${product.price}</strong>
                        </div>
                        <div class="mt-2 small text-muted">
                            ${product.type === 'template' ? product.downloads_count : product.orders_count} ${product.type === 'template' ? 'downloads' : 'orders'} • 
                            ${product.views_count} views
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="sellerDashboard.editProduct(${product.id}, '${product.type}')">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="sellerDashboard.deleteProduct(${product.id}, '${product.type}')">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    getStatusColor(status) {
        const colors = {
            'approved': 'success',
            'active': 'success',
            'pending': 'warning',
            'rejected': 'danger',
            'inactive': 'secondary'
        };
        return colors[status] || 'secondary';
    }
    
    async initCharts() {
        try {
            // Load Chart.js if not already loaded
            if (typeof Chart === 'undefined') {
                await this.loadScript('https://cdn.jsdelivr.net/npm/chart.js');
            }
            
            await this.loadEarningsChart();
        } catch (error) {
            console.error('Failed to initialize charts:', error);
        }
    }
    
    async loadEarningsChart() {
        try {
            const response = await fetch('/seller-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_monthly_earnings', months: 12 })
            });
            
            const data = await response.json();
            
            if (data.success && data.monthly_earnings) {
                this.renderEarningsChart(data.monthly_earnings);
            }
        } catch (error) {
            console.error('Failed to load earnings chart:', error);
        }
    }
    
    renderEarningsChart(data) {
        const ctx = document.getElementById('earningsChart');
        if (!ctx) return;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.month),
                datasets: [{
                    label: 'Monthly Earnings',
                    data: data.map(item => item.earnings),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Earnings Trend'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    
    openModal(modalId) {
        const modal = document.getElementById(`${modalId}Modal`);
        if (modal) {
            modal.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }
    
    closeModal() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
        document.body.classList.remove('modal-open');
    }
    
    async handleAddProduct(form) {
        const formData = new FormData(form);
        const productData = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('/seller-api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'create_product',
                    product_type: productData.product_type,
                    product_data: productData
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showToast(data.message, 'success');
                this.closeModal();
                form.reset();
                if (this.currentSection === 'products') {
                    await this.loadProducts();
                }
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            this.showToast(error.message, 'error');
        }
    }
    
    async handleFileUpload(input) {
        const file = input.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('action', 'upload_product_image');
        
        try {
            const response = await fetch('/seller-api.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update preview image
                const preview = input.parentElement.querySelector('.image-preview');
                if (preview) {
                    preview.src = data.url;
                    preview.style.display = 'block';
                }
                
                // Update hidden input with filename
                const hiddenInput = input.parentElement.querySelector('input[name="preview_image"]');
                if (hiddenInput) {
                    hiddenInput.value = data.filename;
                }
                
                this.showToast('Image uploaded successfully', 'success');
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            this.showToast(error.message, 'error');
        }
    }
    
    showToast(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-body">
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        // Add to toast container
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(container);
        }
        
        container.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }
    
    loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
}

// Initialize seller dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.sellerDashboard = new SellerDashboard();
});

// Export for global access
window.SellerDashboard = SellerDashboard;