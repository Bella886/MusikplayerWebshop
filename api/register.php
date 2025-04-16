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
$response["errors"] = array();

// Validate input data
$valid = true;

// Check if required fields are provided
if(empty($data->title) || !in_array($data->title, ["Mr", "Mrs", "Ms"])) {
    $valid = false;
    $response["errors"]["title"] = "Title is required and must be one of: Mr, Mrs, Ms";
}

if(empty($data->first_name)) {
    $valid = false;
    $response["errors"]["first_name"] = "First name is required";
}

if(empty($data->last_name)) {
    $valid = false;
    $response["errors"]["last_name"] = "Last name is required";
}

if(empty($data->address)) {
    $valid = false;
    $response["errors"]["address"] = "Address is required";
}

if(empty($data->postal_code)) {
    $valid = false;
    $response["errors"]["postal_code"] = "Postal code is required";
}

if(empty($data->city)) {
    $valid = false;
    $response["errors"]["city"] = "City is required";
}

if(empty($data->email)) {
    $valid = false;
    $response["errors"]["email"] = "Email is required";
} else if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    $valid = false;
    $response["errors"]["email"] = "Invalid email format";
}

if(empty($data->username)) {
    $valid = false;
    $response["errors"]["username"] = "Username is required";
} else if(strlen($data->username) < 3) {
    $valid = false;
    $response["errors"]["username"] = "Username must be at least 3 characters long";
}

if(empty($data->password)) {
    $valid = false;
    $response["errors"]["password"] = "Password is required";
} else if(strlen($data->password) < 6) {
    $valid = false;
    $response["errors"]["password"] = "Password must be at least 6 characters long";
}

if(empty($data->confirm_password)) {
    $valid = false;
    $response["errors"]["confirm_password"] = "Password confirmation is required";
} else if($data->password !== $data->confirm_password) {
    $valid = false;
    $response["errors"]["confirm_password"] = "Passwords do not match";
}

// Process if all validations pass
if($valid) {
    // Set user property values
    $user->title = $data->title;
    $user->first_name = $data->first_name;
    $user->last_name = $data->last_name;
    $user->address = $data->address;
    $user->postal_code = $data->postal_code;
    $user->city = $data->city;
    $user->email = $data->email;
    $user->username = $data->username;
    $user->password = $data->password;
    
    // Check if email or username already exists
    if($user->emailOrUsernameExists()) {
        $response["message"] = "User with this email or username already exists.";
    } else {
        // Create the user
        if($user->create()) {
            $response["success"] = true;
            $response["message"] = "User was created successfully.";
        } else {
            $response["message"] = "Unable to create user.";
        }
    }
} else {
    $response["message"] = "Please provide valid input data.";
}

// Return response
echo json_encode($response);
?> 