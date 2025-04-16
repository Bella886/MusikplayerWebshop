/**
 * Main JavaScript file for MusikplayerWebshop
 */

// Global variables
let currentUser = null;
let cartItems = [];

// Document ready function
$(document).ready(function() {
    // Check if user is logged in
    checkLoginStatus();
    
    // Initialize cart from localStorage
    initializeCart();
    
    // Register form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        registerUser();
    });
    
    // Login form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        loginUser();
    });
    
    // Logout button click
    $('#logoutBtn').on('click', function(e) {
        e.preventDefault();
        logoutUser();
    });
    
    // Category tabs click
    $('.category-tab').on('click', function() {
        const categoryId = $(this).data('category-id');
        $('.category-tab').removeClass('active');
        $(this).addClass('active');
        loadProductsByCategory(categoryId);
    });
    
    // Product search
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        searchProducts();
    });
    
    // Real-time search
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().trim();
        if (searchTerm.length >= 2) {
            searchProducts();
        } else if (searchTerm.length === 0) {
            // If search field is cleared, show all products
            loadProductsByCategory($('.category-tab.active').data('category-id'));
        }
    });
    
    // Make products draggable (for drag-and-drop to cart)
    initializeDragAndDrop();
});

/**
 * Check if user is logged in and update UI accordingly
 */
function checkLoginStatus() {
    $.ajax({
        url: 'api/check_login.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.logged_in) {
                currentUser = response.user;
                updateUIForLoggedInUser();
            } else {
                updateUIForGuest();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking login status:', error);
        }
    });
}

/**
 * Update UI elements for logged in user
 */
function updateUIForLoggedInUser() {
    // Update navigation menu
    if (currentUser.is_admin) {
        // Admin menu
        $('.main-nav ul').html(`
            <li><a href="index.php">Home</a></li>
            <li><a href="admin/products.php">Edit Products</a></li>
            <li><a href="admin/customers.php">Manage Customers</a></li>
            <li><a href="admin/coupons.php">Manage Coupons</a></li>
            <li><a href="#" id="logoutBtn">Logout</a></li>
        `);
    } else {
        // Customer menu
        $('.main-nav ul').html(`
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="account.php">My Account</a></li>
            <li><a href="cart.php">Cart <span class="cart-icon">🛒<span class="cart-count">${cartItems.length}</span></span></a></li>
            <li><a href="#" id="logoutBtn">Logout</a></li>
        `);
    }
    
    // Show welcome message
    $('.user-welcome').html(`Welcome, ${currentUser.first_name}!`).show();
    
    // Re-attach logout event
    $('#logoutBtn').on('click', function(e) {
        e.preventDefault();
        logoutUser();
    });
}

/**
 * Update UI elements for guest user
 */
function updateUIForGuest() {
    // Update navigation menu for guest
    $('.main-nav ul').html(`
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="cart.php">Cart <span class="cart-icon">🛒<span class="cart-count">${cartItems.length}</span></span></a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    `);
    
    // Hide welcome message
    $('.user-welcome').hide();
}

/**
 * Initialize shopping cart from localStorage
 */
function initializeCart() {
    const savedCart = localStorage.getItem('musicShopCart');
    
    if (savedCart) {
        cartItems = JSON.parse(savedCart);
        updateCartCount();
    }
}

/**
 * Update cart count in the UI
 */
function updateCartCount() {
    $('.cart-count').text(cartItems.length);
}

/**
 * Register new user
 */
function registerUser() {
    // Get form data
    const formData = {
        title: $('#title').val(),
        first_name: $('#firstName').val(),
        last_name: $('#lastName').val(),
        address: $('#address').val(),
        postal_code: $('#postalCode').val(),
        city: $('#city').val(),
        email: $('#email').val(),
        username: $('#username').val(),
        password: $('#password').val(),
        confirm_password: $('#confirmPassword').val()
    };
    
    // Clear previous errors
    $('.error-message').remove();
    $('.form-control').removeClass('form-error');
    
    // Send AJAX request
    $.ajax({
        url: 'api/register.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: function(response) {
            if (response.success) {
                // Show success message
                $('#registerFormContainer').html(`
                    <div class="alert alert-success">
                        ${response.message} You can now <a href="login.php">login</a>.
                    </div>
                `);
            } else {
                // Show error message
                $('#registerAlert').html(`
                    <div class="alert alert-danger">
                        ${response.message}
                    </div>
                `).show();
                
                // Show field-specific errors
                if (response.errors) {
                    Object.keys(response.errors).forEach(field => {
                        const errorMessage = response.errors[field];
                        $(`#${field}`).addClass('form-error')
                            .after(`<span class="error-message">${errorMessage}</span>`);
                    });
                }
            }
        },
        error: function(xhr, status, error) {
            $('#registerAlert').html(`
                <div class="alert alert-danger">
                    An error occurred. Please try again later.
                </div>
            `).show();
            console.error('Registration error:', error);
        }
    });
}

/**
 * Login user
 */
function loginUser() {
    // Get form data
    const formData = {
        username: $('#username').val(),
        password: $('#password').val(),
        remember_me: $('#rememberMe').is(':checked')
    };
    
    // Send AJAX request
    $.ajax({
        url: 'api/login.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: function(response) {
            if (response.success) {
                currentUser = response.user;
                
                // Set remember me cookie if requested
                if (formData.remember_me && response.token) {
                    const expiry = new Date();
                    expiry.setDate(expiry.getDate() + 30);
                    document.cookie = `remember_user_id=${response.user.id}; expires=${expiry.toUTCString()}; path=/`;
                    document.cookie = `remember_token=${response.token}; expires=${expiry.toUTCString()}; path=/`;
                }
                
                // Redirect based on user role
                if (currentUser.is_admin) {
                    window.location.href = 'admin/dashboard.php';
                } else {
                    window.location.href = 'index.php';
                }
            } else {
                // Show error message
                $('#loginAlert').html(`
                    <div class="alert alert-danger">
                        ${response.message}
                    </div>
                `).show();
            }
        },
        error: function(xhr, status, error) {
            $('#loginAlert').html(`
                <div class="alert alert-danger">
                    An error occurred. Please try again later.
                </div>
            `).show();
            console.error('Login error:', error);
        }
    });
}

/**
 * Logout user
 */
function logoutUser() {
    // Send AJAX request
    $.ajax({
        url: 'api/logout.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify({ clear_token: true }),
        success: function(response) {
            // Clear cookies
            document.cookie = 'remember_user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'remember_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            
            // Reset current user
            currentUser = null;
            
            // Update UI
            updateUIForGuest();
            
            // Redirect to home page
            window.location.href = 'index.php';
        },
        error: function(xhr, status, error) {
            console.error('Logout error:', error);
        }
    });
}

/**
 * Load products by category
 * @param {number} categoryId Category ID to load
 */
function loadProductsByCategory(categoryId) {
    // Show loading indicator
    $('#productsContainer').html('<div class="loading">Loading products...</div>');
    
    // Send AJAX request
    $.ajax({
        url: 'api/products.php',
        type: 'GET',
        data: { category_id: categoryId },
        dataType: 'json',
        success: function(response) {
            // Clear loading indicator
            $('#productsContainer').empty();
            
            if (response.success && response.products.length > 0) {
                // Render products
                response.products.forEach(product => {
                    renderProductCard(product);
                });
                
                // Initialize drag and drop for new products
                initializeDragAndDrop();
            } else {
                $('#productsContainer').html('<div class="no-products">No products found in this category.</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#productsContainer').html('<div class="error">Failed to load products. Please try again later.</div>');
            console.error('Error loading products:', error);
        }
    });
}

/**
 * Render a product card
 * @param {Object} product Product object
 */
function renderProductCard(product) {
    const productCard = $(`
        <div class="product-card" data-product-id="${product.id}" draggable="true">
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
    `);
    
    // Add to cart button click
    productCard.find('.btn-add-to-cart').on('click', function() {
        const productId = $(this).data('product-id');
        addToCart(productId);
    });
    
    // Append to container
    $('#productsContainer').append(productCard);
}

/**
 * Search products
 */
function searchProducts() {
    const searchTerm = $('#searchInput').val().trim();
    
    if (searchTerm.length < 2) {
        return;
    }
    
    // Send AJAX request
    $.ajax({
        url: 'api/search_products.php',
        type: 'GET',
        data: { search_term: searchTerm },
        dataType: 'json',
        success: function(response) {
            // Clear product container
            $('#productsContainer').empty();
            
            if (response.success && response.products.length > 0) {
                // Render products
                response.products.forEach(product => {
                    renderProductCard(product);
                });
                
                // Initialize drag and drop for new products
                initializeDragAndDrop();
            } else {
                $('#productsContainer').html('<div class="no-products">No products found matching your search.</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#productsContainer').html('<div class="error">Failed to search products. Please try again later.</div>');
            console.error('Error searching products:', error);
        }
    });
}

/**
 * Add a product to cart
 * @param {number} productId Product ID to add to cart
 */
function addToCart(productId) {
    // Send AJAX request to get product details
    $.ajax({
        url: 'api/products.php',
        type: 'GET',
        data: { id: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.product) {
                const product = response.product;
                
                // Add to cart array
                cartItems.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image,
                    quantity: 1
                });
                
                // Save to localStorage
                localStorage.setItem('musicShopCart', JSON.stringify(cartItems));
                
                // Update cart count
                updateCartCount();
                
                // Show confirmation message
                showNotification(`${product.name} added to cart!`);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding to cart:', error);
            showNotification('Error adding product to cart. Please try again.', 'error');
        }
    });
}

/**
 * Initialize drag and drop functionality
 */
function initializeDragAndDrop() {
    // Make product cards draggable
    $('.product-card').attr('draggable', true);
    
    // Add drag start event
    $('.product-card').on('dragstart', function(e) {
        const productId = $(this).data('product-id');
        e.originalEvent.dataTransfer.setData('text/plain', productId);
        $(this).addClass('dragging');
    });
    
    // Add drag end event
    $('.product-card').on('dragend', function() {
        $(this).removeClass('dragging');
    });
    
    // Make cart icon a drop target
    $('.cart-icon').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });
    
    $('.cart-icon').on('dragleave', function() {
        $(this).removeClass('drag-over');
    });
    
    $('.cart-icon').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        
        const productId = e.originalEvent.dataTransfer.getData('text/plain');
        addToCart(productId);
    });
}

/**
 * Show a notification message
 * @param {string} message Message to display
 * @param {string} type Type of notification (success, error)
 */
function showNotification(message, type = 'success') {
    // Create notification element if it doesn't exist
    if ($('#notification').length === 0) {
        $('body').append('<div id="notification"></div>');
    }
    
    // Set message and type
    $('#notification')
        .text(message)
        .removeClass()
        .addClass(`notification ${type}`)
        .fadeIn();
    
    // Hide after 3 seconds
    setTimeout(function() {
        $('#notification').fadeOut();
    }, 3000);
} 