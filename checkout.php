<?php
// Set page title
$page_title = "MusikplayerWebshop - Checkout";

// Check if user is logged in, redirect to login if not
session_start();
if (!isset($_SESSION['user_id'])) {
    // Store the current page as intended destination after login
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

// Include header
include_once 'views/header.php';
?>

<!-- Checkout Section -->
<section class="checkout-section mb-5">
    <h1 class="section-title">Checkout</h1>
    
    <div class="row">
        <div class="col-md-8">
            <!-- Shipping Information -->
            <div class="shipping-info card mb-4">
                <div class="card-header">
                    <h3>Shipping Information</h3>
                </div>
                <div class="card-body">
                    <form id="shippingForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zipCode" class="form-label">Zip Code</label>
                                <input type="text" class="form-control" id="zipCode" name="zipCode" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Payment Information -->
            <div class="payment-info card mb-4">
                <div class="card-header">
                    <h3>Payment Information</h3>
                </div>
                <div class="card-body">
                    <form id="paymentForm">
                        <div class="mb-3">
                            <label for="cardName" class="form-label">Name on Card</label>
                            <input type="text" class="form-control" id="cardName" name="cardName" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cardNumber" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="XXXX XXXX XXXX XXXX" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expiryDate" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" id="expiryDate" name="expiryDate" placeholder="MM/YY" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" placeholder="XXX" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="order-summary card mb-4">
                <div class="card-header">
                    <h3>Order Summary</h3>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        <!-- Order items will be loaded here via JavaScript -->
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="order-totals">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="orderSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount:</span>
                            <span id="orderDiscount">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span id="orderShipping">$5.00</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span id="orderTotal">$0.00</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button id="placeOrderBtn" class="btn btn-primary w-100">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Order Confirmation Modal -->
<div class="modal fade" id="orderConfirmationModal" tabindex="-1" aria-labelledby="orderConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderConfirmationModalLabel">Order Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h3 class="mt-3">Thank You for Your Order!</h3>
                <p>Your order has been placed successfully.</p>
                <p>Order #: <span id="orderNumber"></span></p>
                <p>A confirmation email has been sent to your email address.</p>
            </div>
            <div class="modal-footer">
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Cart items array
    let cartItems = [];
    let discount = 0;
    const shippingCost = 5.00;
    
    $(document).ready(function() {
        // Initialize checkout page
        loadCartForCheckout();
        
        // Handle form submission
        $('#placeOrderBtn').on('click', function() {
            if (validateForms()) {
                placeOrder();
            } else {
                alert('Please fill in all required fields');
            }
        });
        
        // Form validation for card fields
        $('#cardNumber').on('input', function() {
            this.value = this.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
        });
        
        $('#expiryDate').on('input', function() {
            this.value = this.value.replace(/[^\d]/g, '').replace(/(.{2})/, '$1/').substr(0, 5);
        });
        
        $('#cvv').on('input', function() {
            this.value = this.value.replace(/[^\d]/g, '').substr(0, 3);
        });
    });
    
    // Load cart contents for checkout
    function loadCartForCheckout() {
        // Get cart from localStorage
        const savedCart = localStorage.getItem('musicShopCart');
        
        if (savedCart) {
            cartItems = JSON.parse(savedCart);
            
            // If cart has items
            if (cartItems.length > 0) {
                renderOrderItems();
                updateOrderSummary();
            } else {
                // Redirect to cart page if cart is empty
                window.location.href = 'cart.php';
            }
        } else {
            // Redirect to cart page if cart is empty
            window.location.href = 'cart.php';
        }
    }
    
    // Render order items
    function renderOrderItems() {
        const orderItemsContainer = $('#orderItems');
        orderItemsContainer.empty();
        
        cartItems.forEach(item => {
            const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
            
            const orderItem = $(`
                <div class="order-item mb-3">
                    <div class="d-flex align-items-center">
                        <img src="assets/images/products/${item.image || 'placeholder.jpg'}" alt="${item.name}" class="order-item-image me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <div class="order-item-name">${item.name}</div>
                            <div class="order-item-details">
                                <span class="order-item-price">$${parseFloat(item.price).toFixed(2)}</span>
                                <span class="order-item-quantity">x ${item.quantity}</span>
                            </div>
                        </div>
                        <div class="order-item-total">
                            $${itemTotal.toFixed(2)}
                        </div>
                    </div>
                </div>
            `);
            
            orderItemsContainer.append(orderItem);
        });
    }
    
    // Update order summary
    function updateOrderSummary() {
        let subtotal = 0;
        
        // Calculate subtotal
        cartItems.forEach(item => {
            subtotal += parseFloat(item.price) * parseInt(item.quantity);
        });
        
        // Calculate total
        const total = subtotal - discount + shippingCost;
        
        // Update summary display
        $('#orderSubtotal').text('$' + subtotal.toFixed(2));
        $('#orderDiscount').text('$' + discount.toFixed(2));
        $('#orderShipping').text('$' + shippingCost.toFixed(2));
        $('#orderTotal').text('$' + total.toFixed(2));
    }
    
    // Validate forms
    function validateForms() {
        // Check if all required fields are filled
        const shippingFormValid = document.getElementById('shippingForm').checkValidity();
        const paymentFormValid = document.getElementById('paymentForm').checkValidity();
        
        // Validate forms
        if (!shippingFormValid) {
            $('#shippingForm :invalid').first().focus();
        } else if (!paymentFormValid) {
            $('#paymentForm :invalid').first().focus();
        }
        
        return shippingFormValid && paymentFormValid;
    }
    
    // Place order
    function placeOrder() {
        // Get form data
        const shippingData = {
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            email: $('#email').val(),
            address: $('#address').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            zipCode: $('#zipCode').val(),
            phone: $('#phone').val()
        };
        
        // Get cart data
        let subtotal = 0;
        cartItems.forEach(item => {
            subtotal += parseFloat(item.price) * parseInt(item.quantity);
        });
        const total = subtotal - discount + shippingCost;
        
        // Create order object
        const orderData = {
            customer: shippingData,
            items: cartItems,
            summary: {
                subtotal: subtotal,
                discount: discount,
                shipping: shippingCost,
                total: total
            }
        };
        
        // Send order to server
        $.ajax({
            url: 'api/orders.php',
            type: 'POST',
            data: JSON.stringify(orderData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Clear cart
                    localStorage.removeItem('musicShopCart');
                    
                    // Show confirmation modal
                    $('#orderNumber').text(response.order_id);
                    
                    // Update cart count in header
                    $('.cart-count').text('0');
                    
                    // Open confirmation modal
                    const confirmationModal = new bootstrap.Modal(document.getElementById('orderConfirmationModal'));
                    confirmationModal.show();
                } else {
                    alert('Error placing order: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error placing order:', error);
                alert('Error placing order. Please try again.');
            }
        });
    }
</script>

<?php
// Include footer
include_once 'views/footer.php';
?> 