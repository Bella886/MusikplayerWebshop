<?php
// Set page title
$page_title = "MusikplayerWebshop - My Orders";

// Check if user is logged in, redirect to login if not
session_start();
if (!isset($_SESSION['user_id'])) {
    // Store the current page as intended destination after login
    $_SESSION['redirect_after_login'] = 'orders.php';
    header("Location: login.php");
    exit;
}

// Include header
include_once 'views/header.php';
?>

<!-- Orders Section -->
<section class="orders-section mb-5">
    <h1 class="section-title">My Orders</h1>
    
    <div id="ordersContainer">
        <!-- Orders will be loaded here via JavaScript -->
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    
    <div id="noOrders" class="text-center py-5" style="display: none;">
        <i class="fas fa-shopping-bag fa-4x mb-3 text-muted"></i>
        <h2>No orders yet</h2>
        <p>You haven't placed any orders yet.</p>
        <a href="products.php" class="btn btn-primary mt-3">Start Shopping</a>
    </div>
</section>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Order details will be loaded here -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Load orders
        loadOrders();
        
        // Event delegation for view order details buttons
        $(document).on('click', '.btn-view-order', function() {
            const orderId = $(this).data('order-id');
            loadOrderDetails(orderId);
        });
    });
    
    // Load user orders
    function loadOrders() {
        $.ajax({
            url: 'api/orders.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.orders && response.orders.length > 0) {
                    renderOrders(response.orders);
                } else {
                    // Show no orders message
                    $('#ordersContainer').hide();
                    $('#noOrders').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading orders:', error);
                $('#ordersContainer').html('<div class="alert alert-danger">Error loading orders. Please try again.</div>');
            }
        });
    }
    
    // Render orders table
    function renderOrders(orders) {
        const ordersContainer = $('#ordersContainer');
        ordersContainer.empty();
        
        // Create orders table
        const ordersTable = $(`
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody"></tbody>
            </table>
        `);
        
        ordersContainer.append(ordersTable);
        const ordersTableBody = $('#ordersTableBody');
        
        // Add each order to the table
        orders.forEach(order => {
            const orderDate = new Date(order.order_date).toLocaleDateString();
            const orderRow = $(`
                <tr>
                    <td>#${order.id}</td>
                    <td>${orderDate}</td>
                    <td>${order.item_count}</td>
                    <td>$${parseFloat(order.total).toFixed(2)}</td>
                    <td><span class="badge bg-${getStatusBadgeClass(order.status)}">${capitalizeFirstLetter(order.status)}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-view-order" data-order-id="${order.id}" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">
                            View Details
                        </button>
                    </td>
                </tr>
            `);
            
            ordersTableBody.append(orderRow);
        });
    }
    
    // Get appropriate badge class for order status
    function getStatusBadgeClass(status) {
        switch(status) {
            case 'pending':
                return 'warning';
            case 'processing':
                return 'info';
            case 'shipped':
                return 'primary';
            case 'delivered':
                return 'success';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }
    
    // Capitalize first letter of a string
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    
    // Load order details
    function loadOrderDetails(orderId) {
        $.ajax({
            url: 'api/orders.php',
            type: 'GET',
            data: { id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.order) {
                    renderOrderDetails(response.order);
                } else {
                    $('#orderDetailsContent').html('<div class="alert alert-danger">Error loading order details.</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading order details:', error);
                $('#orderDetailsContent').html('<div class="alert alert-danger">Error loading order details. Please try again.</div>');
            }
        });
    }
    
    // Render order details
    function renderOrderDetails(order) {
        const orderDetailsContent = $('#orderDetailsContent');
        orderDetailsContent.empty();
        
        // Format date
        const orderDate = new Date(order.order_date).toLocaleString();
        
        // Create order details HTML
        const orderDetails = $(`
            <div class="order-details">
                <div class="order-header d-flex justify-content-between mb-3">
                    <div>
                        <h5>Order #${order.id}</h5>
                        <p>Placed on: ${orderDate}</p>
                    </div>
                    <div>
                        <span class="badge bg-${getStatusBadgeClass(order.status)}">${capitalizeFirstLetter(order.status)}</span>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Shipping Address</h6>
                        <p>${order.shipping_address}<br>
                        ${order.shipping_city}, ${order.shipping_state} ${order.shipping_zip}</p>
                    </div>
                </div>
                
                <h6>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsTableBody"></tbody>
                    </table>
                </div>
                
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <table class="table table-sm order-summary">
                            <tbody>
                                <tr>
                                    <td>Subtotal</td>
                                    <td>$${parseFloat(order.subtotal).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td>$${parseFloat(order.discount).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <td>Shipping</td>
                                    <td>$${parseFloat(order.shipping_cost).toFixed(2)}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td>$${parseFloat(order.total).toFixed(2)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `);
        
        orderDetailsContent.append(orderDetails);
        
        // Add order items to the table
        const orderItemsTableBody = $('#orderItemsTableBody');
        
        order.items.forEach(item => {
            const itemRow = $(`
                <tr>
                    <td>${item.product_name}</td>
                    <td>$${parseFloat(item.price).toFixed(2)}</td>
                    <td>${item.quantity}</td>
                    <td>$${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>
            `);
            
            orderItemsTableBody.append(itemRow);
        });
    }
</script>

<?php
// Include footer
include_once 'views/footer.php';
?> 