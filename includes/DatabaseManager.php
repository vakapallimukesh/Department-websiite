<?php
/**
 * DatabaseManager Class
 * Provides a mysqli-like interface for database operations using the global connection.
 */

require_once __DIR__ . '/../connect.php'; // Use existing mysqli connection

class DatabaseManager {
    private $conn;
    
    public function __construct() {
        global $conn;
        if (!$conn) {
            throw new Exception('Database connection not established');
        }
        $this->conn = $conn;
    }
    
    public function prepare($query) {
        return $this->conn->prepare($query);
    }
    
    public function error() {
        return $this->conn->error;
    }
    
    // Additional methods can be added as needed for other operations
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function escape_string($string) {
        return $this->conn->real_escape_string($string);
    }
    
    public function close() {
        $this->conn->close();
    }
}
?>
