<?php
// Set page title
$page_title = "MusikplayerWebshop - Home";

// Include header
include_once 'views/header.php';
?>

<!-- Hero Section -->
<section class="hero-section mb-5">
    <div class="row">
        <div class="col-md-6">
            <h1>Welcome to MusikplayerWebshop</h1>
            <p class="lead">Discover the best portable music players and audio accessories for audiophiles.</p>
            <p>Whether you're looking for high-resolution audio players, premium headphones, or high-quality accessories, we have everything to enhance your music experience.</p>
            <a href="products.php" class="btn btn-primary">Shop Now</a>
        </div>
        <div class="col-md-6">
            <img src="assets/images/hero-image.jpg" alt="Music Player Collection" class="img-fluid rounded">
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="featured-categories mb-5">
    <h2 class="section-title">Shop by Category</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="category-card">
                <img src="assets/images/category-players.jpg" alt="Music Players" class="img-fluid rounded">
                <h3>Music Players</h3>
                <p>High-quality portable audio players for the ultimate listening experience.</p>
                <a href="products.php?category=1" class="btn btn-outline-primary">View Products</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="category-card">
                <img src="assets/images/category-headphones.jpg" alt="Headphones" class="img-fluid rounded">
                <h3>Headphones</h3>
                <p>Premium over-ear headphones and earbuds with superior sound quality.</p>
                <a href="products.php?category=2" class="btn btn-outline-primary">View Products</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="category-card">
                <img src="assets/images/category-accessories.jpg" alt="Accessories" class="img-fluid rounded">
                <h3>Accessories</h3>
                <p>Essential accessories including cables, cases, and more for your devices.</p>
                <a href="products.php?category=3" class="btn btn-outline-primary">View Products</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products mb-5">
    <h2 class="section-title">Featured Products</h2>
    <div class="row" id="featuredProductsContainer">
        <!-- Featured products will be loaded here via AJAX -->
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-choose-us mb-5">
    <h2 class="section-title">Why Choose Us</h2>
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="feature-card text-center">
                <i class="fas fa-truck fa-3x mb-3 text-primary"></i>
                <h3>Fast Delivery</h3>
                <p>Quick and reliable shipping to your doorstep.</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="feature-card text-center">
                <i class="fas fa-shield-alt fa-3x mb-3 text-primary"></i>
                <h3>Secure Payment</h3>
                <p>Your payments are secure and protected.</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="feature-card text-center">
                <i class="fas fa-headset fa-3x mb-3 text-primary"></i>
                <h3>24/7 Support</h3>
                <p>Customer support available round the clock.</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="feature-card text-center">
                <i class="fas fa-undo-alt fa-3x mb-3 text-primary"></i>
                <h3>Easy Returns</h3>
                <p>Hassle-free return policy for all products.</p>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // Load featured products
        $.ajax({
            url: 'api/products.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.products.length > 0) {
                    // Clear loading indicator
                    $('#featuredProductsContainer').empty();
                    
                    // Display random 4 products as featured
                    const shuffled = response.products.sort(() => 0.5 - Math.random());
                    const featured = shuffled.slice(0, 4);
                    
                    featured.forEach(product => {
                        const productCard = `
                            <div class="col-md-3 mb-4">
                                <div class="product-card">
                                    <div class="product-image">
                                        <img src="assets/images/products/${product.image || 'placeholder.jpg'}" alt="${product.name}">
                                    </div>
                                    <div class="product-details">
                                        <h3 class="product-title">${product.name}</h3>
                                        <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
                                        <div class="product-rating">Rating: ${product.rating}/5</div>
                                        <div class="product-action">
                                            <button class="btn-add-to-cart" data-product-id="${product.id}">Add to Cart</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        $('#featuredProductsContainer').append(productCard);
                    });
                    
                    // Add event listeners to "Add to Cart" buttons
                    $('.btn-add-to-cart').on('click', function() {
                        const productId = $(this).data('product-id');
                        addToCart(productId);
                    });
                } else {
                    $('#featuredProductsContainer').html('<div class="col-12"><p class="text-center">No featured products available.</p></div>');
                }
            },
            error: function(xhr, status, error) {
                $('#featuredProductsContainer').html('<div class="col-12"><p class="text-center text-danger">Failed to load featured products.</p></div>');
                console.error('Error loading featured products:', error);
            }
        });
    });
</script>

<?php
// Include footer
include_once 'views/footer.php';
?> 