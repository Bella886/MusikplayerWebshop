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

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize user object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Response array
$response = array();
$response["success"] = false;
$response["message"] = "";
$response["user"] = null;
$response["token"] = null;

// Validate input data
if(!empty($data->username) && !empty($data->password)) {
    // Set user property values
    $user->username = $data->username;
    $user->email = $data->username; // Username might be email
    $user->password = $data->password;
    
    // Attempt to log in
    if($user->login()) {
        // Start session
        session_start();
        
        // Set session variables
        $_SESSION["user_id"] = $user->id;
        $_SESSION["username"] = $user->username;
        $_SESSION["is_admin"] = $user->is_admin;
        $_SESSION["first_name"] = $user->first_name;
        $_SESSION["last_name"] = $user->last_name;
        
        // Check if remember me is set
        if(isset($data->remember_me) && $data->remember_me == true) {
            // Create remember me token
            $token = $user->createRememberMeToken($user->id);
            
            if($token) {
                $response["token"] = $token;
            }
        }
        
        // Return user data (excluding sensitive information)
        $user_data = array(
            "id" => $user->id,
            "username" => $user->username,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "is_admin" => $user->is_admin
        );
        
        $response["success"] = true;
        $response["message"] = "Login successful.";
        $response["user"] = $user_data;
    } else {
        $response["message"] = "Invalid username/email or password.";
    }
} else {
    $response["message"] = "Username and password are required.";
}

// Return response
echo json_encode($response);
?> 