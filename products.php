<?php
// Set page title
$page_title = "MusikplayerWebshop - Products";

// Include header
include_once 'views/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h1>Our Products</h1>
        <p>Discover our range of high-quality music players and accessories.</p>
        
        <!-- Search Bar -->
        <div class="search-container">
            <form id="searchForm" class="search-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="Search products...">
                <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <!-- Category Tabs -->
        <div class="category-tabs" id="categoryTabs">
            <div class="category-tab active" data-category-id="0">All Products</div>
            <!-- Category tabs will be loaded here via AJAX -->
        </div>
        
        <!-- Products Container -->
        <div class="products-container" id="productsContainer">
            <!-- Products will be loaded here via AJAX -->
            <div class="loading">Loading products...</div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Load categories
        $.ajax({
            url: 'api/categories.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.categories.length > 0) {
                    // Render category tabs
                    response.categories.forEach(category => {
                        $('#categoryTabs').append(`
                            <div class="category-tab" data-category-id="${category.id}">${category.name}</div>
                        `);
                    });
                    
                    // Reattach click event for category tabs
                    $('.category-tab').on('click', function() {
                        const categoryId = $(this).data('category-id');
                        $('.category-tab').removeClass('active');
                        $(this).addClass('active');
                        loadProductsByCategory(categoryId);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading categories:', error);
            }
        });
        
        // Initial product load (all products)
        loadProductsByCategory(0);
        
        // Check if category parameter is in URL
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');
        
        if (categoryParam) {
            // Simulate click on the specified category tab
            setTimeout(function() {
                $(`.category-tab[data-category-id="${categoryParam}"]`).click();
            }, 500); // Small delay to ensure tabs are loaded
        }
    });
</script>

<?php
// Include footer
include_once 'views/footer.php';
?> 