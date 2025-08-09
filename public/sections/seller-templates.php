<?php
/**
 * Seller Templates Management
 * Manage all seller templates with CRUD operations
 */
?>

<!-- Templates Management Header -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-secondary mb-2">My Templates</h1>
            <p class="text-gray-600">Manage your digital templates and track downloads</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="showAddTemplateModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors flex items-center">
                <i class="ri-add-line mr-2"></i>Add New Template
            </button>
        </div>
    </div>
    
    <!-- Templates Stats -->
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-secondary"><?= $templateStats['total'] ?></div>
                    <div class="text-sm text-gray-600">Total Templates</div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="ri-layout-line text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600"><?= $templateStats['active'] ?></div>
                    <div class="text-sm text-gray-600">Active Templates</div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="ri-check-line text-xl text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-600"><?= number_format($templateStats['total_downloads']) ?></div>
                    <div class="text-sm text-gray-600">Total Downloads</div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="ri-download-line text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-orange-600">$<?= number_format($templateStats['total_revenue'], 2) ?></div>
                    <div class="text-sm text-gray-600">Templates Revenue</div>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="ri-money-dollar-circle-line text-xl text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates Grid -->
<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <!-- Table Header with Filters -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-secondary">All Templates</h3>
            <div class="flex items-center space-x-3">
                <!-- View Toggle -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="switchView('grid')" 
                            class="px-3 py-1 rounded-md text-sm font-medium bg-white shadow-sm text-primary">
                        <i class="ri-grid-line"></i>
                    </button>
                    <button id="listViewBtn" onclick="switchView('list')" 
                            class="px-3 py-1 rounded-md text-sm font-medium text-gray-600">
                        <i class="ri-list-check"></i>
                    </button>
                </div>
                
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           id="templateSearch"
                           placeholder="Search templates..." 
                           class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
                    <i class="ri-search-line absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <!-- Category Filter -->
                <select id="templateCategoryFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Categories</option>
                    <?php foreach ($templateCategories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Status Filter -->
                <select id="templateStatusFilter" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Templates Container -->
    <div id="templatesContainer">
        <?php if (empty($sellerTemplates)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-layout-line text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary mb-2">No Templates Yet</h3>
            <p class="text-gray-600 mb-6">Start creating templates to expand your digital product library</p>
            <button onclick="showAddTemplateModal()" 
                    class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                Create Your First Template
            </button>
        </div>
        <?php else: ?>
        
        <!-- Grid View -->
        <div id="gridView" class="p-6">
            <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-6">
                <?php foreach ($sellerTemplates as $template): ?>
                <div class="template-item bg-white rounded-xl border hover:shadow-lg transition-all duration-300 overflow-hidden" 
                     data-status="<?= $template['status'] ?>" 
                     data-category="<?= $template['category_id'] ?>">
                    
                    <!-- Template Preview -->
                    <div class="aspect-video bg-gray-100 relative overflow-hidden group">
                        <?php if ($template['preview_image']): ?>
                        <img src="<?= htmlspecialchars($template['preview_image']) ?>" 
                             alt="<?= htmlspecialchars($template['title']) ?>"
                             class="w-full h-full object-cover">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ri-layout-line text-4xl text-gray-400"></i>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Status Badge -->
                        <?php
                        $statusClasses = [
                            'approved' => 'bg-green-500',
                            'pending' => 'bg-orange-500',
                            'rejected' => 'bg-red-500',
                            'draft' => 'bg-gray-500'
                        ];
                        $statusClass = $statusClasses[$template['status']] ?? 'bg-gray-500';
                        ?>
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-white <?= $statusClass ?>">
                                <?= ucfirst($template['status']) ?>
                            </span>
                        </div>
                        
                        <!-- Download Count -->
                        <div class="absolute bottom-3 left-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-black/60 text-white">
                                <i class="ri-download-line mr-1"></i>
                                <?= number_format($template['downloads_count']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Template Info -->
                    <div class="p-4">
                        <h4 class="font-semibold text-secondary mb-2 line-clamp-1">
                            <?= htmlspecialchars($template['title']) ?>
                        </h4>
                        
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            <?= htmlspecialchars($template['description']) ?>
                        </p>
                        
                        <!-- Template Meta -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span class="flex items-center">
                                <i class="ri-folder-line mr-1"></i>
                                <?= htmlspecialchars($template['category_name'] ?? 'Uncategorized') ?>
                            </span>
                            <span class="flex items-center">
                                <i class="ri-file-line mr-1"></i>
                                <?= strtoupper($template['technology'] ?? 'N/A') ?>
                            </span>
                        </div>
                        
                        <!-- Price & Rating -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-xl font-bold text-secondary">
                                <?= $template['price'] > 0 ? '$' . number_format($template['price'], 2) : 'Free' ?>
                            </div>
                            
                            <?php if ($template['rating'] > 0): ?>
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="ri-star-<?= $i <= $template['rating'] ? 'fill' : 'line' ?> text-yellow-400 text-sm"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-xs text-gray-500 ml-1">
                                    (<?= $template['reviews_count'] ?>)
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <button onclick="editTemplate(<?= $template['id'] ?>)" 
                                    class="flex-1 px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center">
                                Edit
                            </button>
                            
                            <button onclick="viewTemplateAnalytics(<?= $template['id'] ?>)" 
                                    class="flex-1 px-3 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-center">
                                Analytics
                            </button>
                            
                            <div class="relative">
                                <button onclick="toggleTemplateMenu(<?= $template['id'] ?>)" 
                                        class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="ri-more-line"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="templateMenu<?= $template['id'] ?>" 
                                     class="hidden absolute right-0 bottom-full mb-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 z-10">
                                    <div class="py-1">
                                        <button onclick="duplicateTemplate(<?= $template['id'] ?>)" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="ri-file-copy-line mr-2"></i>Duplicate
                                        </button>
                                        <button onclick="downloadTemplate(<?= $template['id'] ?>)" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="ri-download-line mr-2"></i>Download
                                        </button>
                                        <button onclick="shareTemplate(<?= $template['id'] ?>)" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="ri-share-line mr-2"></i>Share
                                        </button>
                                        <hr class="my-1">
                                        <button onclick="deleteTemplate(<?= $template['id'] ?>)" 
                                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="ri-delete-bin-line mr-2"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- List View -->
        <div id="listView" class="hidden">
            <div class="divide-y divide-gray-100">
                <?php foreach ($sellerTemplates as $template): ?>
                <div class="p-6 template-item" data-status="<?= $template['status'] ?>" data-category="<?= $template['category_id'] ?>">
                    <div class="flex items-center space-x-4">
                        <!-- Template Preview -->
                        <div class="w-24 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <?php if ($template['preview_image']): ?>
                            <img src="<?= htmlspecialchars($template['preview_image']) ?>" 
                                 alt="<?= htmlspecialchars($template['title']) ?>"
                                 class="w-full h-full object-cover">
                            <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ri-layout-line text-xl text-gray-400"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Template Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-secondary mb-1">
                                        <?= htmlspecialchars($template['title']) ?>
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-1">
                                        <?= htmlspecialchars($template['description']) ?>    
                                    </p>
                                    
                                    <!-- Template Meta -->
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span><?= htmlspecialchars($template['category_name']) ?></span>
                                        <span><?= strtoupper($template['file_format']) ?></span>
                                        <span><?= number_format($template['downloads']) ?> downloads</span>
                                        <span>Updated <?= date('M j, Y', strtotime($template['updated_at'])) ?></span>
                                    </div>
                                </div>
                                
                                <!-- Price & Actions -->
                                <div class="text-right ml-4">
                                    <div class="text-xl font-bold text-secondary mb-2">
                                        <?= $template['price'] > 0 ? '$' . number_format($template['price'], 2) : 'Free' ?>
                                    </div>
                                    
                                    <!-- Status -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?> mb-2">
                                        <?= ucfirst($template['status']) ?>
                                    </span>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editTemplate(<?= $template['id'] ?>)" 
                                                class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                            Edit
                                        </button>
                                        <button onclick="viewTemplateAnalytics(<?= $template['id'] ?>)" 
                                                class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                            Analytics
                                        </button>
                                        <button onclick="toggleTemplateMenu(<?= $template['id'] ?>)" 
                                                class="px-2 py-1 text-xs text-gray-500 hover:text-gray-700">
                                            <i class="ri-more-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalTemplatePages > 1): ?>
        <div class="p-6 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing <?= ($currentTemplatePage - 1) * $perPage + 1 ?> to <?= min($currentTemplatePage * $perPage, $totalTemplates) ?> of <?= $totalTemplates ?> templates
                </div>
                
                <div class="flex items-center space-x-2">
                    <?php if ($currentTemplatePage > 1): ?>
                    <a href="?section=templates&page=<?= $currentTemplatePage - 1 ?>" 
                       class="px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg hover:bg-gray-50">
                        <i class="ri-arrow-left-line"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $currentTemplatePage - 2); $i <= min($totalTemplatePages, $currentTemplatePage + 2); $i++): ?>
                    <a href="?section=templates&page=<?= $i ?>" 
                       class="px-3 py-2 text-sm rounded-lg <?= $i === $currentTemplatePage ? 'bg-primary text-white' : 'text-gray-600 hover:text-primary hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($currentTemplatePage < $totalTemplatePages): ?>
                    <a href="?section=templates&page=<?= $currentTemplatePage + 1 ?>" 
                       class="px-3 py-2 text-sm text-gray-600 hover:text-primary rounded-lg hover:bg-gray-50">
                        <i class="ri-arrow-right-line"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Template Management Scripts -->
<script>
// View switching
function switchView(view) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.classList.add('bg-white', 'shadow-sm', 'text-primary');
        gridBtn.classList.remove('text-gray-600');
        listBtn.classList.remove('bg-white', 'shadow-sm', 'text-primary');
        listBtn.classList.add('text-gray-600');
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.classList.add('bg-white', 'shadow-sm', 'text-primary');
        listBtn.classList.remove('text-gray-600');
        gridBtn.classList.remove('bg-white', 'shadow-sm', 'text-primary');
        gridBtn.classList.add('text-gray-600');
    }
    
    localStorage.setItem('templateView', view);
}

// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Restore view preference
    const savedView = localStorage.getItem('templateView') || 'grid';
    switchView(savedView);
    
    const searchInput = document.getElementById('templateSearch');
    const categoryFilter = document.getElementById('templateCategoryFilter');
    const statusFilter = document.getElementById('templateStatusFilter');
    const templateItems = document.querySelectorAll('.template-item');
    
    function filterTemplates() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryFilter_val = categoryFilter.value;
        const statusFilter_val = statusFilter.value;
        
        templateItems.forEach(item => {
            const title = item.querySelector('h4').textContent.toLowerCase();
            const description = item.querySelector('p').textContent.toLowerCase();
            const status = item.dataset.status;
            const category = item.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !categoryFilter_val || category === categoryFilter_val;
            const matchesStatus = !statusFilter_val || status === statusFilter_val;
            
            if (matchesSearch && matchesCategory && matchesStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterTemplates);
    categoryFilter.addEventListener('change', filterTemplates);
    statusFilter.addEventListener('change', filterTemplates);
});

// Template management functions
function toggleTemplateMenu(templateId) {
    const menu = document.getElementById('templateMenu' + templateId);
    
    // Close all other menus
    document.querySelectorAll('[id^="templateMenu"]').forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });
    
    menu.classList.toggle('hidden');
}

function editTemplate(templateId) {
    window.location.href = `template-editor.php?id=${templateId}`;
}

function previewTemplate(templateId) {
    window.open(`template-detail.php?id=${templateId}`, '_blank');
}

function viewTemplateAnalytics(templateId) {
    window.location.href = `?section=analytics&template_id=${templateId}`;
}

function duplicateTemplate(templateId) {
    if (confirm('Are you sure you want to duplicate this template?')) {
        fetch('api/seller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'duplicate_template',
                template_id: templateId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Template duplicated successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

function downloadTemplate(templateId) {
    window.open(`api/seller.php?action=download_template&template_id=${templateId}`, '_blank');
}

function shareTemplate(templateId) {
    const url = `${window.location.origin}/template-detail.php?id=${templateId}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Check out this template',
            url: url
        });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Template link copied to clipboard', 'success');
        });
    }
}

function deleteTemplate(templateId) {
    if (confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
        fetch('api/seller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete_template',
                template_id: templateId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Template deleted successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        });
    }
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="templateMenu"]') && !event.target.closest('button[onclick*="toggleTemplateMenu"]')) {
        document.querySelectorAll('[id^="templateMenu"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
</script>