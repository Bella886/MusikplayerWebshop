<?php
/**
 * Database configuration file
 * Contains connection parameters for MySQL database
 */
class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "musikplayer_webshop";
    private $username = "root";
    private $password = "";
    public $conn;
    
    /**
     * Get database connection
     * @return PDO|null Database connection or null on failure
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?> 