<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database
include_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Response array
$response = array();
$response["success"] = false;

// Check if request is for a single product or a list
if (isset($_GET['id'])) {
    // Get one product
    $product_id = intval($_GET['id']);
    
    // Query to get product details
    $query = "SELECT p.*, c.name as category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE p.id = :id
              LIMIT 1";
    
    // Prepare query
    $stmt = $db->prepare($query);
    
    // Bind value
    $stmt->bindParam(':id', $product_id);
    
    // Execute query
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $product = array(
            "id" => $row['id'],
            "name" => $row['name'],
            "description" => $row['description'],
            "price" => $row['price'],
            "rating" => $row['rating'],
            "image" => $row['image'],
            "category_id" => $row['category_id'],
            "category_name" => $row['category_name']
        );
        
        $response["success"] = true;
        $response["product"] = $product;
    } else {
        $response["message"] = "Product not found.";
    }
} else {
    // Get products by category
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
    
    // Query to fetch products
    $query = "SELECT p.*, c.name as category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id";
    
    // If category is specified, filter by it
    if ($category_id > 0) {
        $query .= " WHERE p.category_id = :category_id";
    }
    
    $query .= " ORDER BY p.name ASC";
    
    // Prepare query
    $stmt = $db->prepare($query);
    
    // Bind value if category filter is active
    if ($category_id > 0) {
        $stmt->bindParam(':category_id', $category_id);
    }
    
    // Execute query
    $stmt->execute();
    
    // Check if products were found
    if ($stmt->rowCount() > 0) {
        $products = array();
        
        // Fetch all products
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "description" => $row['description'],
                "price" => $row['price'],
                "rating" => $row['rating'],
                "image" => $row['image'],
                "category_id" => $row['category_id'],
                "category_name" => $row['category_name']
            );
            
            $products[] = $product;
        }
        
        $response["success"] = true;
        $response["products"] = $products;
    } else {
        $response["message"] = "No products found.";
        $response["products"] = array();
    }
}

// Return response
echo json_encode($response);
?> 