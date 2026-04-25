<?php
/**
 * Modern REST API for Student Portal
 * Provides comprehensive API endpoints for all student operations
 */

require_once dirname(__DIR__) . '/includes/DatabaseManager.php';
require_once dirname(__DIR__) . '/includes/SecurityManager.php';
require_once dirname(__DIR__) . '/includes/NotificationManager.php';

class StudentAPI {
    private $db;
    private $security;
    private $notifications;
    
    public function __construct() {
        $this->db = new DatabaseManager();
        $this->security = new SecurityManager();
        $this->notifications = new NotificationManager($this->db);
        
        // Set JSON headers
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
    
    /**
     * Main API router
     */
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $path = $_GET['endpoint'] ?? '';
            
            // Parse path segments
            $segments = explode('/', trim($path, '/'));
            $resource = $segments[0] ?? '';
            $id = $segments[1] ?? null;
            $action = $segments[2] ?? null;
            
            // Route to appropriate handler
            switch ($resource) {
                case 'profile':
                    return $this->handleProfile($method, $id, $action);
                case 'attendance':
                    return $this->handleAttendance($method, $id, $action);
                case 'notifications':
                    return $this->handleNotifications($method, $id, $action);
                case 'events':
                    return $this->handleEvents($method, $id, $action);
                case 'leaves':
                    return $this->handleLeaves($method, $id, $action);
                case 'dashboard':
                    return $this->handleDashboard($method, $id, $action);
                default:
                    throw new Exception('Invalid endpoint', 404);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Handle profile-related requests
     */
    private function handleProfile($method, $id, $action) {
        $student_id = $this->authenticate();
        
        switch ($method) {
            case 'GET':
                if ($action === 'stats') {
                    return $this->getProfileStats($student_id);
                }
                return $this->getProfile($student_id);
                
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                return $this->updateProfile($student_id, $data);
                
            case 'POST':
                if ($action === 'upload-avatar') {
                    return $this->uploadAvatar($student_id);
                }
                break;
                
            default:
                throw new Exception('Method not allowed', 405);
        }
    }
    
    /**
     * Handle attendance-related requests
     */
    private function handleAttendance($method, $id, $action) {
        $student_id = $this->authenticate();
        
        switch ($method) {
            case 'GET':
                if ($action === 'monthly') {
                    $month = $_GET['month'] ?? date('Y-m');
                    return $this->getMonthlyAttendance($student_id, $month);
                } elseif ($action === 'stats') {
                    return $this->getAttendanceStats($student_id);
                }
                return $this->getAttendance($student_id, $id);
                
            default:
                throw new Exception('Method not allowed', 405);
        }
    }
    
    /**
     * Handle notification-related requests
     */
    private function handleNotifications($method, $id, $action) {
        $student_id = $this->authenticate();
        
        switch ($method) {
            case 'GET':
                if ($action === 'unread') {
                    return $this->getUnreadNotifications($student_id);
                }
                return $this->getNotifications($student_id);
                
            case 'PUT':
                if ($id && $action === 'read') {
                    return $this->markNotificationAsRead($student_id, $id);
                }
                break;
                
            case 'POST':
                if ($action === 'mark-all-read') {
                    return $this->markAllNotificationsAsRead($student_id);
                }
                break;
                
            default:
                throw new Exception('Method not allowed', 405);
        }
    }
    
    /**
     * Handle dashboard data requests
     */
    private function handleDashboard($method, $id, $action) {
        $student_id = $this->authenticate();
        
        if ($method === 'GET') {
            return $this->getDashboardData($student_id);
        }
        
        throw new Exception('Method not allowed', 405);
    }
    
    /**
     * Authenticate user and return student ID
     */
    private function authenticate() {
        session_start();
        
        if (!isset($_SESSION['student_logged_in']) || !$_SESSION['student_logged_in']) {
            throw new Exception('Authentication required', 401);
        }
        
        if (!$this->security->validateSession()) {
            throw new Exception('Invalid session', 401);
        }
        
        return $_SESSION['student_id'];
    }
    
    /**
     * Get complete profile data
     */
    private function getProfile($student_id) {
        $query = "
            SELECT s.*, c.year, c.semester, c.academic_year, c.branch, c.section,
                   h.name as house_name, h.color as house_color,
                   sp.summary, sp.skills, sp.social_links, sp.cgpa,
                   sper.personal_number, sper.address, sper.blood_group, sper.dob
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.class_id
            LEFT JOIN houses h ON s.hid = h.hid
            LEFT JOIN student_profile sp ON s.student_id = sp.student_id
            LEFT JOIN student_personal sper ON s.student_id = sper.student_id
            WHERE s.student_id = ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        
        if (!$profile) {
            throw new Exception('Profile not found', 404);
        }
        
        // Parse JSON fields
        $profile['skills'] = json_decode($profile['skills'] ?? '[]', true);
        $profile['social_links'] = json_decode($profile['social_links'] ?? '[]', true);
        
        return $this->sendSuccess($profile);
    }
    
    /**
     * Get profile statistics
     */
    private function getProfileStats($student_id) {
        // Get attendance stats
        $attendance_query = "
            SELECT 
                COUNT(*) as total_sessions,
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_sessions
            FROM student_attendance 
            WHERE student_id = ?
        ";
        
        $stmt = $this->db->prepare($attendance_query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $attendance_result = $stmt->get_result()->fetch_assoc();
        
        $attendance_percentage = $attendance_result['total_sessions'] > 0 
            ? round(($attendance_result['present_sessions'] / $attendance_result['total_sessions']) * 100, 2) 
            : 0;
        
        // Get event participation stats
        $events_query = "
            SELECT 
                (SELECT COUNT(*) FROM participants WHERE student_id = ?) as participated_events,
                (SELECT COUNT(*) FROM organizers WHERE student_id = ?) as organized_events,
                (SELECT COUNT(*) FROM winners WHERE student_id = ?) as won_events
        ";
        
        $stmt = $this->db->prepare($events_query);
        $stmt->bind_param("sss", $student_id, $student_id, $student_id);
        $stmt->execute();
        $events_result = $stmt->get_result()->fetch_assoc();
        
        $stats = [
            'attendance' => [
                'percentage' => $attendance_percentage,
                'present_sessions' => $attendance_result['present_sessions'],
                'total_sessions' => $attendance_result['total_sessions']
            ],
            'events' => $events_result
        ];
        
        return $this->sendSuccess($stats);
    }
    
    /**
     * Get dashboard data with all necessary information
     */
    private function getDashboardData($student_id) {
        // Get basic profile info
        $profile = $this->getProfileData($student_id);
        
        // Get recent attendance (last 7 days)
        $recent_attendance = $this->getRecentAttendance($student_id, 7);
        
        // Get upcoming events
        $upcoming_events = $this->getUpcomingEvents($student_id);
        
        // Get unread notifications count
        $unread_count = $this->getUnreadNotificationsCount($student_id);
        
        // Get recent leave applications
        $recent_leaves = $this->getRecentLeaves($student_id);
        
        $dashboard_data = [
            'profile' => $profile,
            'recent_attendance' => $recent_attendance,
            'upcoming_events' => $upcoming_events,
            'unread_notifications' => $unread_count,
            'recent_leaves' => $recent_leaves,
            'timestamp' => date('c')
        ];
        
        return $this->sendSuccess($dashboard_data);
    }
    
    /**
     * Get unread notifications
     */
    private function getUnreadNotifications($student_id) {
        $query = "
            SELECT id, type, title, message, created_at, data
            FROM notifications 
            WHERE student_id = ? AND is_read = 0 
            ORDER BY created_at DESC 
            LIMIT 10
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $row['data'] = json_decode($row['data'] ?? '{}', true);
            $notifications[] = $row;
        }
        
        return $this->sendSuccess($notifications);
    }
    
    /**
     * Mark notification as read
     */
    private function markNotificationAsRead($student_id, $notification_id) {
        $query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND student_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $notification_id, $student_id);
        
        if ($stmt->execute()) {
            return $this->sendSuccess(['message' => 'Notification marked as read']);
        } else {
            throw new Exception('Failed to update notification', 500);
        }
    }
    
    /**
     * Helper method to get profile data
     */
    private function getProfileData($student_id) {
        $query = "
            SELECT s.name, s.email, s.student_id, s.profile_picture,
                   c.branch, c.section, c.year, c.semester,
                   h.name as house_name, h.color as house_color
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.class_id
            LEFT JOIN houses h ON s.hid = h.hid
            WHERE s.student_id = ?
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get recent attendance data
     */
    private function getRecentAttendance($student_id, $days = 7) {
        $query = "
            SELECT attendance_date, session, status
            FROM student_attendance 
            WHERE student_id = ? AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            ORDER BY attendance_date DESC, session ASC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $student_id, $days);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $attendance = [];
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }
        
        return $attendance;
    }
    
    /**
     * Get upcoming events
     */
    private function getUpcomingEvents($student_id) {
        $query = "
            SELECT e.event_id, e.title, e.event_date, e.venue, e.description
            FROM events e
            WHERE e.event_date >= CURDATE()
            ORDER BY e.event_date ASC
            LIMIT 5
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        
        return $events;
    }
    
    /**
     * Get unread notifications count
     */
    private function getUnreadNotificationsCount($student_id) {
        $query = "SELECT COUNT(*) as count FROM notifications WHERE student_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc()['count'];
    }
    
    /**
     * Get recent leave applications
     */
    private function getRecentLeaves($student_id) {
        $query = "
            SELECT leave_type, start_date, end_date, status, applied_at
            FROM leave_applications 
            WHERE student_id = ?
            ORDER BY applied_at DESC
            LIMIT 3
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $leaves = [];
        while ($row = $result->fetch_assoc()) {
            $leaves[] = $row;
        }
        
        return $leaves;
    }
    
    /**
     * Send success response
     */
    private function sendSuccess($data = null, $message = 'Success') {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ]);
        exit();
    }
    
    /**
     * Send error response
     */
    private function sendError($message = 'An error occurred', $code = 500) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'timestamp' => date('c')
        ]);
        exit();
    }
}

// Initialize and handle the API request
$api = new StudentAPI();
$api->handleRequest();
?>