<?php
/**
 * Notifications API Endpoint
 * Handles real-time notification polling and management
 */

require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/notifications.php';

// Initialize secure session
Security::initSecureSession();

// Set JSON response headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if user is logged in
if (!isset($_SESSION['student_logged_in']) && !isset($_SESSION['faculty_logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$db = Database::getInstance();
$notifications = new NotificationSystem();

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['student_id'] ?? $_SESSION['faculty_id'] ?? null;
$user_type = isset($_SESSION['student_logged_in']) ? 'student' : 'faculty';

try {
    switch ($action) {
        case 'poll':
            // Get unread notifications for the user
            $unread = $notifications->getUnreadNotifications($user_id, $user_type);
            echo json_encode([
                'success' => true,
                'notifications' => $unread,
                'count' => count($unread)
            ]);
            break;

        case 'mark_read':
            $notification_id = $_POST['notification_id'] ?? null;
            if ($notification_id) {
                $notifications->markAsRead($notification_id, $user_id);
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Notification ID required');
            }
            break;

        case 'mark_all_read':
            $notifications->markAllAsRead($user_id, $user_type);
            echo json_encode(['success' => true]);
            break;

        case 'get_recent':
            $limit = min(50, (int)($_GET['limit'] ?? 20));
            $recent = $notifications->getRecentNotifications($user_id, $user_type, $limit);
            echo json_encode([
                'success' => true,
                'notifications' => $recent
            ]);
            break;

        case 'delete':
            $notification_id = $_POST['notification_id'] ?? null;
            if ($notification_id) {
                $notifications->deleteNotification($notification_id, $user_id);
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Notification ID required');
            }
            break;

        case 'stats':
            // Get notification statistics
            $stats = [
                'total' => $notifications->getTotalCount($user_id, $user_type),
                'unread' => $notifications->getUnreadCount($user_id, $user_type),
                'recent_types' => $notifications->getRecentTypeDistribution($user_id, $user_type)
            ];
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
    // Log error for debugging
    Security::logSecurityEvent('api_error', [
        'endpoint' => 'notifications',
        'action' => $action,
        'user_id' => $user_id,
        'error' => $e->getMessage()
    ]);
}
?>