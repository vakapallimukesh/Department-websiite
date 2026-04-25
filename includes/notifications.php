<?php
/**
 * Notification System
 * Real-time notifications with database storage and WebSocket support
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

class NotificationSystem {
    
    private $db;
    
    const NOTIFICATION_TYPES = [
        'info' => 'info',
        'success' => 'success', 
        'warning' => 'warning',
        'error' => 'error',
        'attendance' => 'attendance',
        'leave' => 'leave',
        'house_points' => 'house_points',
        'announcement' => 'announcement'
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new notification
     */
    public function createNotification($data) {
        // Validate input
        $required_fields = ['user_id', 'user_type', 'title', 'message', 'type'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
        
        // Sanitize input
        $notification = [
            'user_id' => Security::sanitizeInput($data['user_id'], 'int'),
            'user_type' => Security::sanitizeInput($data['user_type'], 'string'),
            'title' => Security::sanitizeInput($data['title'], 'string'),
            'message' => Security::sanitizeInput($data['message'], 'string'),
            'type' => Security::sanitizeInput($data['type'], 'string'),
            'priority' => Security::sanitizeInput($data['priority'] ?? 'normal', 'string'),
            'action_url' => Security::sanitizeInput($data['action_url'] ?? null, 'url'),
            'metadata' => json_encode($data['metadata'] ?? []),
            'created_at' => date('Y-m-d H:i:s'),
            'is_read' => 0
        ];
        
        // Validate notification type
        if (!isset(self::NOTIFICATION_TYPES[$notification['type']])) {
            throw new Exception("Invalid notification type");
        }
        
        $notification_id = $this->db->insert('notifications', $notification);
        
        // Send real-time notification
        $this->sendRealTimeNotification($notification_id, $notification);
        
        return $notification_id;
    }
    
    /**
     * Get notifications for a user
     */
    public function getUserNotifications($user_id, $user_type, $limit = 50, $offset = 0, $unread_only = false) {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ? AND user_type = ?";
        $params = [$user_id, $user_type];
        
        if ($unread_only) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id, $user_id = null) {
        $conditions = ['id' => $notification_id];
        if ($user_id) {
            $conditions['user_id'] = $user_id;
        }
        
        return $this->db->update('notifications', ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')], $conditions);
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($user_id, $user_type) {
        return $this->db->update('notifications', 
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')], 
            ['user_id' => $user_id, 'user_type' => $user_type]
        );
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount($user_id, $user_type) {
        $result = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM notifications 
             WHERE user_id = ? AND user_type = ? AND is_read = 0",
            [$user_id, $user_type]
        );
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Delete notification
     */
    public function deleteNotification($notification_id, $user_id = null) {
        $conditions = ['id' => $notification_id];
        if ($user_id) {
            $conditions['user_id'] = $user_id;
        }
        
        return $this->db->delete('notifications', $conditions);
    }
    
    /**
     * Send bulk notifications
     */
    public function sendBulkNotifications($users, $notification_data) {
        $this->db->beginTransaction();
        
        try {
            $notification_ids = [];
            
            foreach ($users as $user) {
                $data = array_merge($notification_data, [
                    'user_id' => $user['id'],
                    'user_type' => $user['type']
                ]);
                
                $notification_ids[] = $this->createNotification($data);
            }
            
            $this->db->commit();
            return $notification_ids;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Send real-time notification via WebSocket
     */
    private function sendRealTimeNotification($notification_id, $notification_data) {
        // This would connect to a WebSocket server
        // For now, we'll create a simple file-based approach for real-time updates
        
        $realtime_data = [
            'id' => $notification_id,
            'user_id' => $notification_data['user_id'],
            'user_type' => $notification_data['user_type'],
            'title' => $notification_data['title'],
            'message' => $notification_data['message'],
            'type' => $notification_data['type'],
            'timestamp' => time()
        ];
        
        // Store in session for immediate display
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['pending_notifications'])) {
            $_SESSION['pending_notifications'] = [];
        }
        
        $_SESSION['pending_notifications'][] = $realtime_data;
        
        // Also write to a temporary file for WebSocket pickup
        $temp_file = __DIR__ . '/../temp/notifications_' . $notification_data['user_id'] . '_' . $notification_data['user_type'] . '.json';
        
        if (!is_dir(dirname($temp_file))) {
            mkdir(dirname($temp_file), 0755, true);
        }
        
        file_put_contents($temp_file, json_encode($realtime_data) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get pending notifications from session
     */
    public static function getPendingNotifications() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $notifications = $_SESSION['pending_notifications'] ?? [];
        unset($_SESSION['pending_notifications']);
        
        return $notifications;
    }
    
    /**
     * Cleanup old notifications
     */
    public function cleanup($days = 30) {
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->db->delete('notifications', [
            'created_at <' => $cutoff_date,
            'is_read' => 1
        ]);
    }
}

// Predefined notification templates
class NotificationTemplates {
    
    public static function attendanceMarked($student_name, $date, $status) {
        return [
            'type' => 'attendance',
            'title' => 'Attendance Updated',
            'message' => "Attendance for {$student_name} on {$date} marked as {$status}",
            'priority' => 'normal'
        ];
    }
    
    public static function leaveApproved($student_name, $leave_dates) {
        return [
            'type' => 'leave',
            'title' => 'Leave Application Approved',
            'message' => "Leave application for {$student_name} ({$leave_dates}) has been approved",
            'priority' => 'normal'
        ];
    }
    
    public static function housePointsAwarded($house_name, $points, $reason) {
        return [
            'type' => 'house_points',
            'title' => 'House Points Awarded',
            'message' => "{$house_name} house awarded {$points} points for {$reason}",
            'priority' => 'normal'
        ];
    }
    
    public static function systemAnnouncement($title, $message) {
        return [
            'type' => 'announcement',
            'title' => $title,
            'message' => $message,
            'priority' => 'high'
        ];
    }
    
    public static function lowAttendance($student_name, $percentage) {
        return [
            'type' => 'warning',
            'title' => 'Low Attendance Alert',
            'message' => "{$student_name}'s attendance has dropped to {$percentage}%. Please take necessary action.",
            'priority' => 'high'
        ];
    }
}
?>