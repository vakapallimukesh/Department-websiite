<?php
/**
 * Security Configuration and Helper Functions
 * Enhanced security for the student portal system
 */

class Security {
    
    // Security constants
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 900; // 15 minutes in seconds
    const SESSION_TIMEOUT = 3600; // 1 hour
    const PASSWORD_MIN_LENGTH = 8;
    
    // CSRF Token management
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Input validation and sanitization
    public static function sanitizeInput($input, $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::sanitizeInput($item, $type);
            }, $input);
        }
        
        $input = trim($input);
        
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'string':
            default:
                return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
    }
    
    public static function validateInput($input, $type, $options = []) {
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
            case 'int':
                $min = $options['min'] ?? null;
                $max = $options['max'] ?? null;
                $flags = [];
                if ($min !== null) $flags['min_range'] = $min;
                if ($max !== null) $flags['max_range'] = $max;
                return filter_var($input, FILTER_VALIDATE_INT, ['options' => $flags]) !== false;
            case 'password':
                return strlen($input) >= self::PASSWORD_MIN_LENGTH && 
                       preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $input);
            case 'student_id':
                return preg_match('/^[0-9]{2}[A-Z]{2}[0-9]{4}$/', $input);
            case 'phone':
                return preg_match('/^[6-9]\d{9}$/', $input);
            default:
                return true;
        }
    }
    
    // Rate limiting for login attempts
    public static function checkLoginAttempts($identifier) {
        $attempts = $_SESSION['login_attempts'][$identifier] ?? [];
        $current_time = time();
        
        // Clean old attempts
        $attempts = array_filter($attempts, function($timestamp) use ($current_time) {
            return ($current_time - $timestamp) < self::LOCKOUT_DURATION;
        });
        
        $_SESSION['login_attempts'][$identifier] = $attempts;
        
        return count($attempts) < self::MAX_LOGIN_ATTEMPTS;
    }
    
    public static function recordLoginAttempt($identifier) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        if (!isset($_SESSION['login_attempts'][$identifier])) {
            $_SESSION['login_attempts'][$identifier] = [];
        }
        
        $_SESSION['login_attempts'][$identifier][] = time();
    }
    
    public static function clearLoginAttempts($identifier) {
        if (isset($_SESSION['login_attempts'][$identifier])) {
            unset($_SESSION['login_attempts'][$identifier]);
        }
    }
    
    // Session security
    public static function initSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
            
            // Check session timeout
            if (isset($_SESSION['last_activity']) && 
                (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT)) {
                session_unset();
                session_destroy();
                session_start();
            }
            
            $_SESSION['last_activity'] = time();
            
            // Regenerate session ID periodically
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > 300) { // 5 minutes
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }
    
    // Password hashing
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,  // 64 MB
            'time_cost' => 4,        // 4 iterations
            'threads' => 3,          // 3 threads
        ]);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Security headers
    public static function setSecurityHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        if (isset($_SERVER['HTTPS'])) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self';";
        
        header("Content-Security-Policy: $csp");
    }
    
    // Log security events
    public static function logSecurityEvent($event, $details = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'event' => $event,
            'details' => $details
        ];
        
        error_log(json_encode($log_entry), 3, __DIR__ . '/../logs/security.log');
    }
    
    // File upload security
    public static function validateFileUpload($file, $allowed_types = [], $max_size = 5242880) { // 5MB default
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error: ' . $file['error'];
            return $errors;
        }
        
        if ($file['size'] > $max_size) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!empty($allowed_types) && !in_array($mime_type, $allowed_types)) {
            $errors[] = 'File type not allowed';
        }
        
        // Check file content
        $file_content = file_get_contents($file['tmp_name']);
        if (strpos($file_content, '<?php') !== false || strpos($file_content, '<script') !== false) {
            $errors[] = 'Malicious content detected';
        }
        
        return $errors;
    }
}

// Initialize security headers on every request
Security::setSecurityHeaders();
?>