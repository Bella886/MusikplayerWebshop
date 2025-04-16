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
$response["products"] = array();

// Get search term from request
$search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';

if (empty($search_term)) {
    $response["message"] = "Search term is required.";
    echo json_encode($response);
    exit;
}

// Search products by name or description
$query = "SELECT p.*, c.name as category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.name LIKE :search_term
          OR p.description LIKE :search_term
          ORDER BY p.name ASC";

// Prepare query
$stmt = $db->prepare($query);

// Bind search term with wildcards
$search_param = "%{$search_term}%";
$stmt->bindParam(':search_term', $search_param);

// Execute query
$stmt->execute();

// Check if products were found
if ($stmt->rowCount() > 0) {
    $products = array();
    
    // Fetch all matching products
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
    $response["message"] = "No products found matching your search.";
}

// Return response
echo json_encode($response);
?> 