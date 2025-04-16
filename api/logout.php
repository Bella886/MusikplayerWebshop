<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user model
include_once '../config/database.php';
include_once '../models/User.php';

// Start session
session_start();

// Response array
$response = array();
$response["success"] = true;
$response["message"] = "Logged out successfully.";

// Process request to remove remember me token
$data = json_decode(file_get_contents("php://input"));

if(isset($data->clear_token) && $data->clear_token === true && isset($_SESSION["user_id"])) {
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize user object
    $user = new User($db);
    
    // Delete remember me token
    $user->deleteRememberMeToken($_SESSION["user_id"]);
}

// Unset all session variables
$_SESSION = array();

// Delete the session cookie
if(ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Return response
echo json_encode($response);
?> 