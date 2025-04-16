<?php
/**
 * User Model
 * Handles user-related operations like registration, login, etc.
 */
class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";
    
    // Object properties
    public $id;
    public $title;
    public $first_name;
    public $last_name;
    public $address;
    public $postal_code;
    public $city;
    public $email;
    public $username;
    public $password;
    public $is_admin;
    public $is_active;
    public $created_at;
    
    /**
     * Constructor with database connection
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create new user (registration)
     * @return boolean Success or failure
     */
    public function create() {
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Query to insert new user
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title = :title, 
                      first_name = :first_name, 
                      last_name = :last_name, 
                      address = :address, 
                      postal_code = :postal_code, 
                      city = :city, 
                      email = :email, 
                      username = :username, 
                      password = :password";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->postal_code = htmlspecialchars(strip_tags($this->postal_code));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->username = htmlspecialchars(strip_tags($this->username));
        
        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":postal_code", $this->postal_code);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $password_hash);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if email or username already exists
     * @return boolean True if user exists, false otherwise
     */
    public function emailOrUsernameExists() {
        // Query to check if email or username exists
        $query = "SELECT id, username, password, is_admin, is_active 
                  FROM " . $this->table_name . " 
                  WHERE email = :email OR username = :username 
                  LIMIT 1";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->username = htmlspecialchars(strip_tags($this->username));
        
        // Bind values
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":username", $this->username);
        
        // Execute query
        $stmt->execute();
        
        // Get number of rows
        if($stmt->rowCount() > 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Login user by checking credentials
     * @return boolean True on success, false otherwise
     */
    public function login() {
        // Query to find user by username or email
        $query = "SELECT id, username, password, is_admin, is_active, first_name, last_name 
                  FROM " . $this->table_name . " 
                  WHERE (username = :username OR email = :email) 
                  LIMIT 1";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->username); // Username field might contain email
        
        // Execute query
        $stmt->execute();
        
        // Check if user exists
        if($stmt->rowCount() > 0) {
            // Get user data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if(password_verify($this->password, $row['password'])) {
                // Check if account is active
                if($row['is_active'] == 0) {
                    return false; // Account is disabled
                }
                
                // Set user properties
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->is_admin = $row['is_admin'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Create remember me token
     * @param int $user_id User ID
     * @return string|boolean Token or false on failure
     */
    public function createRememberMeToken($user_id) {
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        $hashed_token = password_hash($token, PASSWORD_DEFAULT);
        
        // Set expiry date (30 days from now)
        $expiry_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Query to insert token
        $query = "INSERT INTO session_tokens SET 
                  user_id = :user_id, 
                  token = :token, 
                  expires_at = :expires_at";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":token", $hashed_token);
        $stmt->bindParam(":expires_at", $expiry_date);
        
        // Execute query
        if($stmt->execute()) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * Verify remember me token
     * @param int $user_id User ID
     * @param string $token Token to verify
     * @return boolean True if valid, false otherwise
     */
    public function verifyRememberMeToken($user_id, $token) {
        // Query to get token
        $query = "SELECT * FROM session_tokens 
                  WHERE user_id = :user_id 
                  AND expires_at > NOW() 
                  ORDER BY created_at DESC 
                  LIMIT 1";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bindParam(":user_id", $user_id);
        
        // Execute query
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify token
            if(password_verify($token, $row['token'])) {
                // Get user data
                $query = "SELECT id, username, is_admin, first_name, last_name 
                          FROM " . $this->table_name . " 
                          WHERE id = :id 
                          AND is_active = 1 
                          LIMIT 1";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id", $user_id);
                $stmt->execute();
                
                if($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Set user properties
                    $this->id = $user['id'];
                    $this->username = $user['username'];
                    $this->is_admin = $user['is_admin'];
                    $this->first_name = $user['first_name'];
                    $this->last_name = $user['last_name'];
                    
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Delete remember me token
     * @param int $user_id User ID
     * @return boolean Success or failure
     */
    public function deleteRememberMeToken($user_id) {
        // Query to delete token
        $query = "DELETE FROM session_tokens 
                  WHERE user_id = :user_id";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bindParam(":user_id", $user_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get user details by ID
     * @param int $id User ID
     * @return boolean Success or failure
     */
    public function readOne($id) {
        // Query to read user details
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 1";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bindParam(":id", $id);
        
        // Execute query
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set user properties
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->address = $row['address'];
            $this->postal_code = $row['postal_code'];
            $this->city = $row['city'];
            $this->email = $row['email'];
            $this->username = $row['username'];
            $this->is_admin = $row['is_admin'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            
            return true;
        }
        
        return false;
    }
}
?> 