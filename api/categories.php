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
$response["categories"] = array();

// Query to fetch all categories
$query = "SELECT id, name, description FROM categories ORDER BY name ASC";

// Prepare query
$stmt = $db->prepare($query);

// Execute query
$stmt->execute();

// Check if categories were found
if ($stmt->rowCount() > 0) {
    $categories = array();
    
    // Fetch all categories
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category = array(
            "id" => $row['id'],
            "name" => $row['name'],
            "description" => $row['description']
        );
        
        $categories[] = $category;
    }
    
    $response["success"] = true;
    $response["categories"] = $categories;
} else {
    $response["message"] = "No categories found.";
}

// Return response
echo json_encode($response);
?> 