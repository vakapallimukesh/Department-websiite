<?php
/**
 * Enhanced Database Class
 * Provides secure database operations with connection pooling and prepared statements
 */

require_once __DIR__ . '/security.php';

class Database {
    private static $instance = null;
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset = 'utf8mb4';
    
    // Connection pool
    private static $connections = [];
    private static $max_connections = 10;
    
    private function __construct() {
        $this->loadConfig();
        $this->connect();
    }
    
    private function loadConfig() {
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        if ($httpHost === 'csd-csit.page.gd' || strpos($httpHost, 'page.gd') !== false) {
            // Production configuration
            $this->host = 'sql302.infinityfree.com';
            $this->username = 'if0_39923791';
            $this->password = 'WredXibeqKifLM';
            $this->database = 'if0_39923791_test';
        } else {
            // Development configuration
            $this->host = '127.0.0.1';
            $this->username = 'root';
            $this->password = '';
            $this->database = 'new_sem';
        }
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            Security::logSecurityEvent('database_connection_failed', ['error' => $e->getMessage()]);
            throw new Exception('Database connection failed');
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prepared statement methods
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            Security::logSecurityEvent('database_query_failed', [
                'sql' => $sql,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Database query failed');
        }
    }
    
    public function select($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function selectOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $conditions) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = :{$column}";
        }
        $set = implode(', ', $set);
        
        $where = [];
        foreach (array_keys($conditions) as $column) {
            $where[] = "{$column} = :where_{$column}";
        }
        $where = implode(' AND ', $where);
        
        // Prefix condition parameters to avoid conflicts
        $params = $data;
        foreach ($conditions as $key => $value) {
            $params["where_{$key}"] = $value;
        }
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        
        return $stmt->rowCount();
    }
    
    public function delete($table, $conditions) {
        $where = [];
        foreach (array_keys($conditions) as $column) {
            $where[] = "{$column} = :{$column}";
        }
        $where = implode(' AND ', $where);
        
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $conditions);
        
        return $stmt->rowCount();
    }
    
    // Transaction methods
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
    
    // Authentication methods
    public function authenticateUser($email, $password, $user_type = 'student') {
        Security::initSecureSession();
        
        // Check rate limiting
        if (!Security::checkLoginAttempts($email)) {
            throw new Exception('Too many login attempts. Please try again later.');
        }
        
        $table = ($user_type === 'faculty') ? 'faculties' : 'students';
        
        if ($user_type === 'student') {
            $sql = "SELECT s.student_id, s.name, s.email, s.password, s.class_id, s.branch, s.section, s.hid,
                           c.year, c.semester, c.academic_year
                    FROM students s
                    LEFT JOIN classes c ON s.class_id = c.class_id
                    WHERE s.email = ? AND s.is_active = 1";
        } else {
            $sql = "SELECT faculty_id, faculty_name, email, password, class_id, phone_number
                    FROM faculties 
                    WHERE email = ? AND is_active = 1";
        }
        
        $user = $this->selectOne($sql, [$email]);
        
        if (!$user) {
            Security::recordLoginAttempt($email);
            throw new Exception('Invalid credentials');
        }
        
        // Verify password
        $password_valid = false;
        if (password_verify($password, $user['password'])) {
            $password_valid = true;
        } elseif ($password === $user['password']) {
            // Backward compatibility - rehash plain text password
            $hashed = Security::hashPassword($password);
            $this->update($table, ['password' => $hashed], ['email' => $email]);
            $password_valid = true;
        }
        
        if (!$password_valid) {
            Security::recordLoginAttempt($email);
            Security::logSecurityEvent('failed_login_attempt', ['email' => $email]);
            throw new Exception('Invalid credentials');
        }
        
        // Clear login attempts on successful login
        Security::clearLoginAttempts($email);
        Security::logSecurityEvent('successful_login', ['email' => $email, 'user_type' => $user_type]);
        
        return $user;
    }
    
    // Cache methods for performance
    private static $cache = [];
    
    public function getCached($key, $sql, $params = [], $ttl = 300) {
        $cache_key = md5($sql . serialize($params));
        
        if (isset(self::$cache[$cache_key]) && 
            time() - self::$cache[$cache_key]['time'] < $ttl) {
            return self::$cache[$cache_key]['data'];
        }
        
        $data = $this->select($sql, $params);
        self::$cache[$cache_key] = [
            'data' => $data,
            'time' => time()
        ];
        
        return $data;
    }
    
    public function clearCache($pattern = null) {
        if ($pattern) {
            foreach (array_keys(self::$cache) as $key) {
                if (strpos($key, $pattern) !== false) {
                    unset(self::$cache[$key]);
                }
            }
        } else {
            self::$cache = [];
        }
    }
    
    // Health check
    public function healthCheck() {
        try {
            $this->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function __destruct() {
        $this->connection = null;
    }
}

// Create global database instance for backward compatibility
$db = Database::getInstance();
$conn = $db->getConnection();
?>