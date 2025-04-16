<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user model
include_once '../config/database.php';
include_once '../models/User.php';

// Start session
session_start();

// Response array
$response = array();
$response["logged_in"] = false;
$response["user"] = null;

// Check if user is logged in via session
if(isset($_SESSION["user_id"])) {
    $response["logged_in"] = true;
    
    // Return user data
    $response["user"] = array(
        "id" => $_SESSION["user_id"],
        "username" => $_SESSION["username"],
        "first_name" => $_SESSION["first_name"],
        "last_name" => $_SESSION["last_name"],
        "is_admin" => $_SESSION["is_admin"]
    );
} 
// If not logged in via session, check for remember me cookie
else if(isset($_COOKIE["remember_user_id"]) && isset($_COOKIE["remember_token"])) {
    $user_id = $_COOKIE["remember_user_id"];
    $token = $_COOKIE["remember_token"];
    
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize user object
    $user = new User($db);
    
    // Verify remember me token
    if($user->verifyRememberMeToken($user_id, $token)) {
        // Set session variables
        $_SESSION["user_id"] = $user->id;
        $_SESSION["username"] = $user->username;
        $_SESSION["is_admin"] = $user->is_admin;
        $_SESSION["first_name"] = $user->first_name;
        $_SESSION["last_name"] = $user->last_name;
        
        $response["logged_in"] = true;
        
        // Return user data
        $response["user"] = array(
            "id" => $user->id,
            "username" => $user->username,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "is_admin" => $user->is_admin
        );
    } else {
        // Invalid or expired token, clear cookies
        setcookie("remember_user_id", "", time() - 3600, "/");
        setcookie("remember_token", "", time() - 3600, "/");
    }
}

// Return response
echo json_encode($response);
?> 