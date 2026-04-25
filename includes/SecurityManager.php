<?php
/**
 * SecurityManager Class
 * Wraps the Security class and provides session validation.
 */

require_once __DIR__ . '/../config/security.php';

class SecurityManager {
    private $security;
    
    public function __construct() {
        $this->security = new Security();
    }
    
    public function generateCSRFToken() {
        return Security::generateCSRFToken();
    }
    
    public function validateSession() {
        Security::initSecureSession();
        
        // Check if student is logged in
        if (!isset($_SESSION['student_logged_in']) || !$_SESSION['student_logged_in']) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > Security::SESSION_TIMEOUT)) {
            session_destroy();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    // Delegate other methods to Security class
    public function __call($method, $args) {
        if (method_exists($this->security, $method)) {
            return call_user_func_array([$this->security, $method], $args);
        }
        throw new Exception("Method $method does not exist in SecurityManager");
    }
}
?>
