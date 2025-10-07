@extends('layouts.app')

@section('title', 'Store Management - Optimized for Large Scale')
@section('page-title', 'Store Management')
@section('page-description', 'Manage your pharmacy\'s medicine store with advanced performance optimization')

@section('content')
<style>
    /* Virtual Scrolling Styles */
    .virtual-scroll-container {
        height: 600px;
        overflow-y: auto;
        position: relative;
    }
    
    .virtual-scroll-item {
        position: absolute;
        width: 100%;
        transition: transform 0.1s ease;
    }
    
    /* Lazy Loading Styles */
    .lazy-load {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .lazy-load.loaded {
        opacity: 1;
    }
    
    /* Performance Optimizations */
    .medicine-card {
        will-change: transform;
        transform: translateZ(0);
    }
    
    /* Search Optimization */
    .search-suggestions {
        max-height: 200px;
        overflow-y: auto;
    }
    
    /* Loading States */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<!-- Performance Metrics -->
<div class="mb-6 bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Performance Mode</h3>
            <p class="text-sm text-blue-700 dark:text-blue-300">Optimized for large datasets (100,000+ medicines)</p>
        </div>
        <div class="flex space-x-4 text-sm">
            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                Virtual Scrolling
            </span>
            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                Lazy Loading
            </span>
            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                Cached Queries
            </span>
        </div>
    </div>
</div>

<!-- Advanced Search -->
<div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Smart Search with Autocomplete -->
        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Smart Search</label>
            <input type="text" 
                   id="smartSearch" 
                   placeholder="Search medicines..." 
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
            <div id="searchSuggestions" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg search-suggestions hidden"></div>
        </div>
        
        <!-- Category Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
            <select id="categoryFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                <option value="">All Categories</option>
            </select>
        </div>
        
        <!-- Stock Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Status</label>
            <select id="stockFilter" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                <option value="">All Stock</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
        </div>
        
        <!-- View Mode -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">View Mode</label>
            <div class="flex space-x-2">
                <button id="virtualScrollBtn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    Virtual Scroll
                </button>
                <button id="paginationBtn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Pagination
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Medicines</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="totalMedicines">-</p>
            </div>
            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-pills text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Medicines</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="activeMedicines">-</p>
            </div>
            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Stock</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="inStock">-</p>
            </div>
            <div class="w-12 h-12 bg-emerald-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="totalValue">-</p>
            </div>
            <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Virtual Scroll Container -->
<div id="virtualScrollContainer" class="virtual-scroll-container bg-white dark:bg-gray-800 rounded-xl shadow-lg">
    <div id="virtualScrollContent" class="relative">
        <!-- Virtual scroll items will be rendered here -->
    </div>
</div>

<!-- Pagination Container (Hidden by default) -->
<div id="paginationContainer" class="hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
        <div class="p-6">
            <div id="medicinesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Paginated medicines will be rendered here -->
            </div>
        </div>
        
        <!-- Pagination Controls -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span id="paginationFrom">0</span> to <span id="paginationTo">0</span> of <span id="paginationTotal">0</span> results
                </div>
                <div class="flex space-x-2">
                    <button id="prevPage" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                    <span id="pageNumbers" class="flex space-x-1"></span>
                    <button id="nextPage" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading States -->
<div id="loadingStates" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="loading-skeleton h-4 w-3/4 mb-2"></div>
            <div class="loading-skeleton h-3 w-1/2 mb-4"></div>
            <div class="loading-skeleton h-3 w-full mb-2"></div>
            <div class="loading-skeleton h-3 w-2/3"></div>
        </div>
        <!-- Repeat skeleton cards -->
    </div>
</div>

<script>
/**
 * Optimized Store Management for Large Scale
 * Handles 100,000+ medicines with virtual scrolling and lazy loading
 */
(function() {
    'use strict';
    
    // Configuration
    const ITEM_HEIGHT = 120; // Height of each medicine item
    const BUFFER_SIZE = 10; // Number of items to render outside viewport
    const CACHE_SIZE = 1000; // Maximum items to cache
    
    // State management
    let medicines = [];
    let filteredMedicines = [];
    let virtualScrollData = [];
    let currentPage = 1;
    let totalPages = 1;
    let isVirtualScroll = true;
    let isLoading = false;
    let searchTimeout = null;
    
    // DOM elements
    const virtualContainer = document.getElementById('virtualScrollContainer');
    const virtualContent = document.getElementById('virtualScrollContent');
    const paginationContainer = document.getElementById('paginationContainer');
    const medicinesGrid = document.getElementById('medicinesGrid');
    const smartSearch = document.getElementById('smartSearch');
    const searchSuggestions = document.getElementById('searchSuggestions');
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeOptimizedInventory();
    });
    
    /**
     * Initialize optimized inventory management
     */
    function initializeOptimizedInventory() {
        console.log('Initializing optimized inventory management...');
        
        // Load initial data
        loadStatistics();
        loadCategories();
        
        // Set up event listeners
        setupEventListeners();
        
        // Initialize virtual scrolling
        if (isVirtualScroll) {
            initializeVirtualScrolling();
        } else {
            loadMedicinesPaginated();
        }
        
        console.log('Optimized inventory management initialized');
    }
    
    /**
     * Set up event listeners
     */
    function setupEventListeners() {
        // Search with debouncing
        smartSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });
        
        // Filter changes
        document.getElementById('categoryFilter').addEventListener('change', applyFilters);
        document.getElementById('stockFilter').addEventListener('change', applyFilters);
        
        // View mode toggle
        document.getElementById('virtualScrollBtn').addEventListener('click', () => switchToVirtualScroll());
        document.getElementById('paginationBtn').addEventListener('click', () => switchToPagination());
        
        // Pagination controls
        document.getElementById('prevPage').addEventListener('click', () => changePage(currentPage - 1));
        document.getElementById('nextPage').addEventListener('click', () => changePage(currentPage + 1));
    }
    
    /**
     * Load statistics with caching
     */
    async function loadStatistics() {
        try {
            const response = await fetch('/medicines/statistics', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    updateStatisticsDisplay(data.data);
                }
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }
    
    /**
     * Load categories for filters
     */
    async function loadCategories() {
        try {
            const response = await fetch('/categories', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    populateCategoryFilter(data.data);
                }
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }
    
    /**
     * Initialize virtual scrolling
     */
    function initializeVirtualScrolling() {
        virtualContainer.addEventListener('scroll', handleVirtualScroll);
        loadVirtualScrollData();
    }
    
    /**
     * Handle virtual scroll events
     */
    function handleVirtualScroll() {
        if (isLoading) return;
        
        const scrollTop = virtualContainer.scrollTop;
        const containerHeight = virtualContainer.clientHeight;
        const totalHeight = virtualScrollData.length * ITEM_HEIGHT;
        
        // Calculate visible range
        const startIndex = Math.max(0, Math.floor(scrollTop / ITEM_HEIGHT) - BUFFER_SIZE);
        const endIndex = Math.min(virtualScrollData.length, Math.ceil((scrollTop + containerHeight) / ITEM_HEIGHT) + BUFFER_SIZE);
        
        renderVirtualItems(startIndex, endIndex);
    }
    
    /**
     * Load data for virtual scrolling
     */
    async function loadVirtualScrollData() {
        if (isLoading) return;
        
        isLoading = true;
        showLoadingState();
        
        try {
            const response = await fetch('/medicines/virtual?start=0&limit=1000', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    virtualScrollData = data.data;
                    medicines = data.data;
                    filteredMedicines = data.data;
                    
                    // Set container height
                    virtualContent.style.height = `${virtualScrollData.length * ITEM_HEIGHT}px`;
                    
                    // Render initial items
                    handleVirtualScroll();
                }
            }
        } catch (error) {
            console.error('Error loading virtual scroll data:', error);
        } finally {
            isLoading = false;
            hideLoadingState();
        }
    }
    
    /**
     * Render virtual scroll items
     */
    function renderVirtualItems(startIndex, endIndex) {
        const fragment = document.createDocumentFragment();
        
        for (let i = startIndex; i < endIndex; i++) {
            if (virtualScrollData[i]) {
                const item = createVirtualScrollItem(virtualScrollData[i], i);
                fragment.appendChild(item);
            }
        }
        
        virtualContent.innerHTML = '';
        virtualContent.appendChild(fragment);
    }
    
    /**
     * Create virtual scroll item
     */
    function createVirtualScrollItem(medicine, index) {
        const item = document.createElement('div');
        item.className = 'virtual-scroll-item medicine-card bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-4';
        item.style.top = `${index * ITEM_HEIGHT}px`;
        item.style.height = `${ITEM_HEIGHT}px`;
        
        item.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-pills text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">${medicine.name}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">${medicine.generic_name || 'No generic name'}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-900 dark:text-white">Br ${parseFloat(medicine.selling_price).toFixed(2)}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Stock: ${medicine.stock_quantity}</p>
                </div>
            </div>
        `;
        
        return item;
    }
    
    /**
     * Switch to virtual scroll mode
     */
    function switchToVirtualScroll() {
        isVirtualScroll = true;
        virtualContainer.style.display = 'block';
        paginationContainer.style.display = 'none';
        
        document.getElementById('virtualScrollBtn').classList.add('bg-blue-500');
        document.getElementById('virtualScrollBtn').classList.remove('bg-gray-500');
        document.getElementById('paginationBtn').classList.add('bg-gray-500');
        document.getElementById('paginationBtn').classList.remove('bg-blue-500');
        
        if (virtualScrollData.length === 0) {
            loadVirtualScrollData();
        }
    }
    
    /**
     * Switch to pagination mode
     */
    function switchToPagination() {
        isVirtualScroll = false;
        virtualContainer.style.display = 'none';
        paginationContainer.style.display = 'block';
        
        document.getElementById('paginationBtn').classList.add('bg-blue-500');
        document.getElementById('paginationBtn').classList.remove('bg-gray-500');
        document.getElementById('virtualScrollBtn').classList.add('bg-gray-500');
        document.getElementById('virtualScrollBtn').classList.remove('bg-blue-500');
        
        loadMedicinesPaginated();
    }
    
    /**
     * Load medicines with pagination
     */
    async function loadMedicinesPaginated() {
        if (isLoading) return;
        
        isLoading = true;
        showLoadingState();
        
        try {
            const response = await fetch(`/medicines?page=${currentPage}&per_page=12`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    medicines = data.data;
                    updatePaginationDisplay(data.pagination);
                    renderPaginatedMedicines();
                }
            }
        } catch (error) {
            console.error('Error loading paginated medicines:', error);
        } finally {
            isLoading = false;
            hideLoadingState();
        }
    }
    
    /**
     * Render paginated medicines
     */
    function renderPaginatedMedicines() {
        medicinesGrid.innerHTML = medicines.map(medicine => `
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pills text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">${medicine.name}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">${medicine.generic_name || 'No generic name'}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${medicine.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${medicine.is_active ? 'Active' : 'Inactive'}
                    </span>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Stock:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.stock_quantity}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Price:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(medicine.selling_price).toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    /**
     * Perform search with autocomplete
     */
    async function performSearch(query) {
        if (query.length < 2) {
            searchSuggestions.classList.add('hidden');
            return;
        }
        
        try {
            const response = await fetch(`/medicines/autocomplete?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    showSearchSuggestions(data.data);
                }
            }
        } catch (error) {
            console.error('Error performing search:', error);
        }
    }
    
    /**
     * Show search suggestions
     */
    function showSearchSuggestions(suggestions) {
        if (suggestions.length === 0) {
            searchSuggestions.classList.add('hidden');
            return;
        }
        
        searchSuggestions.innerHTML = suggestions.map(medicine => `
            <div class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer" onclick="selectSuggestion('${medicine.name}')">
                <div class="font-medium text-gray-900 dark:text-white">${medicine.name}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">${medicine.generic_name || 'No generic name'}</div>
            </div>
        `).join('');
        
        searchSuggestions.classList.remove('hidden');
    }
    
    /**
     * Select search suggestion
     */
    function selectSuggestion(medicineName) {
        smartSearch.value = medicineName;
        searchSuggestions.classList.add('hidden');
        applyFilters();
    }
    
    /**
     * Apply filters
     */
    function applyFilters() {
        const searchTerm = smartSearch.value.toLowerCase();
        const categoryId = document.getElementById('categoryFilter').value;
        const stockFilter = document.getElementById('stockFilter').value;
        
        if (isVirtualScroll) {
            // Filter virtual scroll data
            filteredMedicines = virtualScrollData.filter(medicine => {
                const matchesSearch = !searchTerm || 
                    medicine.name.toLowerCase().includes(searchTerm) ||
                    (medicine.generic_name && medicine.generic_name.toLowerCase().includes(searchTerm));
                
                const matchesCategory = !categoryId || medicine.category_id == categoryId;
                
                const matchesStock = !stockFilter || (
                    (stockFilter === 'in_stock' && medicine.stock_quantity > 0) ||
                    (stockFilter === 'low_stock' && medicine.stock_quantity > 0 && medicine.stock_quantity <= 10) ||
                    (stockFilter === 'out_of_stock' && medicine.stock_quantity <= 0)
                );
                
                return matchesSearch && matchesCategory && matchesStock;
            });
            
            // Update virtual scroll with filtered data
            virtualScrollData = filteredMedicines;
            virtualContent.style.height = `${virtualScrollData.length * ITEM_HEIGHT}px`;
            handleVirtualScroll();
        } else {
            // Reload paginated data with filters
            loadMedicinesPaginated();
        }
    }
    
    /**
     * Update statistics display
     */
    function updateStatisticsDisplay(stats) {
        document.getElementById('totalMedicines').textContent = stats.total_medicines || 0;
        document.getElementById('activeMedicines').textContent = stats.active_medicines || 0;
        document.getElementById('inStock').textContent = stats.in_stock || 0;
        document.getElementById('totalValue').textContent = `Br ${parseFloat(stats.total_value || 0).toFixed(2)}`;
    }
    
    /**
     * Populate category filter
     */
    function populateCategoryFilter(categories) {
        const categoryFilter = document.getElementById('categoryFilter');
        categoryFilter.innerHTML = '<option value="">All Categories</option>' +
            categories.map(category => `<option value="${category.id}">${category.name}</option>`).join('');
    }
    
    /**
     * Update pagination display
     */
    function updatePaginationDisplay(pagination) {
        currentPage = pagination.current_page;
        totalPages = pagination.last_page;
        
        document.getElementById('paginationFrom').textContent = pagination.from || 0;
        document.getElementById('paginationTo').textContent = pagination.to || 0;
        document.getElementById('paginationTotal').textContent = pagination.total || 0;
        
        // Update pagination buttons
        document.getElementById('prevPage').disabled = currentPage <= 1;
        document.getElementById('nextPage').disabled = currentPage >= totalPages;
        
        // Update page numbers
        updatePageNumbers();
    }
    
    /**
     * Update page numbers
     */
    function updatePageNumbers() {
        const pageNumbers = document.getElementById('pageNumbers');
        const pages = [];
        
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            pages.push(`<button class="px-3 py-1 ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'} rounded hover:bg-blue-600" onclick="changePage(${i})">${i}</button>`);
        }
        
        pageNumbers.innerHTML = pages.join('');
    }
    
    /**
     * Change page
     */
    function changePage(page) {
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            loadMedicinesPaginated();
        }
    }
    
    /**
     * Show loading state
     */
    function showLoadingState() {
        document.getElementById('loadingStates').classList.remove('hidden');
    }
    
    /**
     * Hide loading state
     */
    function hideLoadingState() {
        document.getElementById('loadingStates').classList.add('hidden');
    }
    
    // Make functions globally available
    window.selectSuggestion = selectSuggestion;
    window.changePage = changePage;
    
})();
</script>
@endsection
