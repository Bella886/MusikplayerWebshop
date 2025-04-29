<?php
// Set page title
$page_title = "MusikplayerWebshop - Shopping Cart";

// Include header
include_once 'views/header.php';
?>

<!-- Shopping Cart Section -->
<section class="cart-section mb-5">
    <h1 class="section-title">Shopping Cart</h1>
    
    <div id="cartContents">
        <!-- Cart contents will be loaded here via JavaScript -->
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    
    <div id="cartSummary" class="mt-4" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="coupon-section">
                    <h3>Coupon Code</h3>
                    <div class="input-group mb-3">
                        <input type="text" id="couponCode" class="form-control" placeholder="Enter coupon code">
                        <button class="btn btn-outline-secondary" type="button" id="applyCoupon">Apply</button>
                    </div>
                    <div id="couponMessage"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="cart-totals">
                    <h3>Cart Totals</h3>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Subtotal</td>
                                <td id="cartSubtotal">$0.00</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td id="cartDiscount">$0.00</td>
                            </tr>
                            <tr>
                                <td>Shipping</td>
                                <td id="cartShipping">$5.00</td>
                            </tr>
                            <tr class="total-row">
                                <td><strong>Total</strong></td>
                                <td id="cartTotal"><strong>$0.00</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="checkout-button">
                        <a href="checkout.php" id="proceedToCheckout" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="emptyCart" class="text-center py-5" style="display: none;">
        <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added any products to your cart yet.</p>
        <a href="products.php" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>
</section>

<script>
    // Cart items array
    let cartItems = [];
    let discount = 0;
    const shippingCost = 5.00;
    
    $(document).ready(function() {
        // Initialize cart
        loadCart();
        
        // Event handler for the apply coupon button
        $('#applyCoupon').on('click', function() {
            applyCoupon();
        });
    });
    
    // Load cart contents
    function loadCart() {
        // Get cart from localStorage
        const savedCart = localStorage.getItem('musicShopCart');
        
        if (savedCart) {
            cartItems = JSON.parse(savedCart);
            
            // If cart has items
            if (cartItems.length > 0) {
                renderCart();
                updateCartSummary();
                $('#cartSummary').show();
                $('#emptyCart').hide();
            } else {
                // Show empty cart message
                $('#cartContents').hide();
                $('#cartSummary').hide();
                $('#emptyCart').show();
            }
        } else {
            // Show empty cart message
            $('#cartContents').hide();
            $('#cartSummary').hide();
            $('#emptyCart').show();
        }
    }
    
    // Render cart items
    function renderCart() {
        const cartContainer = $('#cartContents');
        cartContainer.empty();
        
        // Create cart table
        const cartTable = $(`
            <table class="table cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartItemsContainer"></tbody>
            </table>
        `);
        
        cartContainer.append(cartTable);
        const cartItemsContainer = $('#cartItemsContainer');
        
        // Add each item to the table
        cartItems.forEach((item, index) => {
            const cartRow = $(`
                <tr data-item-id="${item.id}">
                    <td class="product-info">
                        <img src="assets/images/products/${item.image || 'placeholder.jpg'}" alt="${item.name}" class="cart-item-image">
                        <span>${item.name}</span>
                    </td>
                    <td>$${parseFloat(item.price).toFixed(2)}</td>
                    <td class="quantity-cell">
                        <div class="quantity-controls">
                            <button class="btn-decrease-quantity" data-index="${index}">-</button>
                            <input type="number" class="item-quantity" value="${item.quantity}" min="1" max="99" data-index="${index}">
                            <button class="btn-increase-quantity" data-index="${index}">+</button>
                        </div>
                    </td>
                    <td>$${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
                    <td>
                        <button class="btn-remove-item" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            
            cartItemsContainer.append(cartRow);
        });
        
        // Add event listeners
        $('.btn-decrease-quantity').on('click', function() {
            const index = $(this).data('index');
            decreaseQuantity(index);
        });
        
        $('.btn-increase-quantity').on('click', function() {
            const index = $(this).data('index');
            increaseQuantity(index);
        });
        
        $('.item-quantity').on('change', function() {
            const index = $(this).data('index');
            const newQuantity = parseInt($(this).val());
            updateItemQuantity(index, newQuantity);
        });
        
        $('.btn-remove-item').on('click', function() {
            const index = $(this).data('index');
            removeCartItem(index);
        });
    }
    
    // Increase item quantity
    function increaseQuantity(index) {
        if (cartItems[index].quantity < 99) {
            cartItems[index].quantity++;
            updateCart();
        }
    }
    
    // Decrease item quantity
    function decreaseQuantity(index) {
        if (cartItems[index].quantity > 1) {
            cartItems[index].quantity--;
            updateCart();
        }
    }
    
    // Update item quantity
    function updateItemQuantity(index, quantity) {
        if (quantity >= 1 && quantity <= 99) {
            cartItems[index].quantity = quantity;
            updateCart();
        }
    }
    
    // Remove item from cart
    function removeCartItem(index) {
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            cartItems.splice(index, 1);
            updateCart();
            
            // If cart is now empty, show empty message
            if (cartItems.length === 0) {
                $('#cartContents').hide();
                $('#cartSummary').hide();
                $('#emptyCart').show();
            }
        }
    }
    
    // Update cart
    function updateCart() {
        // Save to localStorage
        localStorage.setItem('musicShopCart', JSON.stringify(cartItems));
        
        // Update UI
        renderCart();
        updateCartSummary();
        updateCartCount();
    }
    
    // Update cart count in header
    function updateCartCount() {
        $('.cart-count').text(cartItems.length);
    }
    
    // Update cart summary
    function updateCartSummary() {
        let subtotal = 0;
        
        // Calculate subtotal
        cartItems.forEach(item => {
            subtotal += parseFloat(item.price) * parseInt(item.quantity);
        });
        
        // Calculate total
        const total = subtotal - discount + shippingCost;
        
        // Update summary display
        $('#cartSubtotal').text('$' + subtotal.toFixed(2));
        $('#cartDiscount').text('$' + discount.toFixed(2));
        $('#cartShipping').text('$' + shippingCost.toFixed(2));
        $('#cartTotal').text('$' + total.toFixed(2));
    }
    
    // Apply coupon code
    function applyCoupon() {
        const couponCode = $('#couponCode').val().trim();
        
        if (couponCode === '') {
            showCouponMessage('Please enter a coupon code', 'warning');
            return;
        }
        
        // Send AJAX request to verify coupon
        $.ajax({
            url: 'api/coupons.php',
            type: 'POST',
            data: { code: couponCode },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    discount = response.discount;
                    updateCartSummary();
                    showCouponMessage(`Coupon applied! ${response.message}`, 'success');
                } else {
                    showCouponMessage(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error applying coupon:', error);
                showCouponMessage('Error applying coupon. Please try again.', 'error');
            }
        });
    }
    
    // Show coupon message
    function showCouponMessage(message, type) {
        const couponMessage = $('#couponMessage');
        
        couponMessage.removeClass('text-success text-danger text-warning');
        
        switch(type) {
            case 'success':
                couponMessage.addClass('text-success');
                break;
            case 'error':
                couponMessage.addClass('text-danger');
                break;
            case 'warning':
                couponMessage.addClass('text-warning');
                break;
        }
        
        couponMessage.text(message);
    }
</script>

<?php
// Include footer
include_once 'views/footer.php';
?> 