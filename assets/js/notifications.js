/**
 * Modern Notification System
 * Handles real-time notifications with elegant animations and user interaction
 */

class NotificationManager {
    constructor() {
        this.container = this.createContainer();
        this.notifications = new Map();
        this.defaultDuration = 5000;
        this.maxNotifications = 5;
        
        // Initialize real-time polling
        this.initRealtimeUpdates();
    }

    createContainer() {
        let container = document.getElementById('notification-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'notification-container';
            document.body.appendChild(container);
        }
        return container;
    }

    show(options = {}) {
        const notification = {
            id: this.generateId(),
            title: options.title || 'Notification',
            message: options.message || '',
            type: options.type || 'info',
            duration: options.duration || this.defaultDuration,
            persistent: options.persistent || false,
            actions: options.actions || [],
            ...options
        };

        this.render(notification);
        this.notifications.set(notification.id, notification);

        // Auto-remove if not persistent
        if (!notification.persistent && notification.duration > 0) {
            setTimeout(() => {
                this.remove(notification.id);
            }, notification.duration);
        }

        // Limit max notifications
        if (this.notifications.size > this.maxNotifications) {
            const oldestId = this.notifications.keys().next().value;
            this.remove(oldestId);
        }

        return notification.id;
    }

    render(notification) {
        const element = document.createElement('div');
        element.className = `notification ${notification.type}`;
        element.id = `notification-${notification.id}`;
        element.setAttribute('role', 'alert');
        element.setAttribute('aria-live', 'polite');

        const icon = this.getIcon(notification.type);
        const actionsHtml = this.renderActions(notification.actions, notification.id);
        
        element.innerHTML = `
            <div class="notification-content">
                <div class="notification-header">
                    <div class="notification-icon">
                        <i class="${icon}"></i>
                    </div>
                    <div class="notification-text">
                        <div class="notification-title">${this.escapeHtml(notification.title)}</div>
                        <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                    </div>
                    <button class="notification-close" onclick="notificationManager.remove('${notification.id}')" aria-label="Close notification">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                ${actionsHtml ? `<div class="notification-actions">${actionsHtml}</div>` : ''}
            </div>
            <div class="notification-progress" style="animation-duration: ${notification.duration}ms"></div>
        `;

        // Add event listeners
        element.addEventListener('mouseenter', () => {
            element.style.animationPlayState = 'paused';
        });

        element.addEventListener('mouseleave', () => {
            element.style.animationPlayState = 'running';
        });

        // Add to container with animation
        this.container.appendChild(element);
        
        // Trigger entrance animation
        requestAnimationFrame(() => {
            element.classList.add('notification-enter');
        });

        // Add swipe gesture support for mobile
        this.addSwipeSupport(element, notification.id);
    }

    renderActions(actions, notificationId) {
        if (!actions || !actions.length) return '';
        
        return actions.map(action => `
            <button class="notification-action btn btn-sm ${action.type || 'btn-secondary'}" 
                    onclick="${action.onclick ? action.onclick : `notificationManager.handleAction('${notificationId}', '${action.id}')`}">
                ${action.icon ? `<i class="${action.icon}"></i>` : ''}
                ${this.escapeHtml(action.text)}
            </button>
        `).join('');
    }

    handleAction(notificationId, actionId) {
        const notification = this.notifications.get(notificationId);
        if (!notification) return;

        const action = notification.actions.find(a => a.id === actionId);
        if (action && action.handler) {
            action.handler(notificationId, actionId);
        }

        // Remove notification after action (unless specified otherwise)
        if (!action || action.removeAfter !== false) {
            this.remove(notificationId);
        }
    }

    remove(notificationId) {
        const element = document.getElementById(`notification-${notificationId}`);
        if (!element) return;

        element.classList.add('notification-exit');
        
        element.addEventListener('animationend', () => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        }, { once: true });

        this.notifications.delete(notificationId);
    }

    removeAll() {
        this.notifications.forEach((_, id) => this.remove(id));
    }

    getIcon(type) {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle',
            attendance: 'fas fa-calendar-check',
            leave: 'fas fa-calendar-times',
            house_points: 'fas fa-trophy',
            announcement: 'fas fa-bullhorn'
        };
        return icons[type] || icons.info;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    addSwipeSupport(element, notificationId) {
        let startX, startY, distX, distY;
        const threshold = 100;

        element.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            startX = touch.pageX;
            startY = touch.pageY;
        });

        element.addEventListener('touchmove', (e) => {
            if (!startX || !startY) return;

            const touch = e.touches[0];
            distX = touch.pageX - startX;
            distY = touch.pageY - startY;

            if (Math.abs(distX) > Math.abs(distY)) {
                e.preventDefault();
                element.style.transform = `translateX(${distX}px)`;
                element.style.opacity = Math.max(0, 1 - Math.abs(distX) / 200);
            }
        });

        element.addEventListener('touchend', () => {
            if (Math.abs(distX) > threshold) {
                this.remove(notificationId);
            } else {
                element.style.transform = '';
                element.style.opacity = '';
            }
            startX = startY = distX = distY = null;
        });
    }

    // Real-time notification polling
    initRealtimeUpdates() {
        // Check for new notifications every 30 seconds
        setInterval(() => {
            this.fetchNewNotifications();
        }, 30000);

        // Check immediately
        this.fetchNewNotifications();
    }

    async fetchNewNotifications() {
        try {
            const response = await fetch('api/notifications.php?action=poll', {
                method: 'GET',
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        this.show({
                            title: notification.title,
                            message: notification.message,
                            type: notification.type,
                            duration: 8000 // Longer duration for real-time notifications
                        });
                    });
                }
            }
        } catch (error) {
            console.warn('Failed to fetch notifications:', error);
        }
    }

    // Predefined notification types
    success(message, title = 'Success') {
        return this.show({ type: 'success', title, message });
    }

    error(message, title = 'Error') {
        return this.show({ type: 'error', title, message, duration: 8000 });
    }

    warning(message, title = 'Warning') {
        return this.show({ type: 'warning', title, message, duration: 6000 });
    }

    info(message, title = 'Information') {
        return this.show({ type: 'info', title, message });
    }

    // System-specific notifications
    attendanceUpdate(studentName, status, date) {
        return this.show({
            type: 'attendance',
            title: 'Attendance Updated',
            message: `${studentName}'s attendance for ${date} marked as ${status}`,
            actions: [
                {
                    id: 'view',
                    text: 'View Details',
                    type: 'btn-primary',
                    onclick: `window.location.href='attendance_detail.php?date=${date}'`
                }
            ]
        });
    }

    leaveNotification(type, studentName, dates) {
        const messages = {
            approved: `Leave application for ${studentName} (${dates}) has been approved`,
            rejected: `Leave application for ${studentName} (${dates}) has been rejected`,
            pending: `New leave application from ${studentName} for ${dates} requires approval`
        };

        return this.show({
            type: type === 'approved' ? 'success' : type === 'rejected' ? 'error' : 'info',
            title: 'Leave Application Update',
            message: messages[type] || messages.pending,
            actions: type === 'pending' ? [
                {
                    id: 'approve',
                    text: 'Approve',
                    type: 'btn-success',
                    handler: (notificationId) => {
                        // Handle approval logic
                        this.success('Leave application approved');
                    }
                },
                {
                    id: 'reject',
                    text: 'Reject', 
                    type: 'btn-error',
                    handler: (notificationId) => {
                        // Handle rejection logic
                        this.error('Leave application rejected');
                    }
                }
            ] : undefined
        });
    }

    housePointsUpdate(houseName, points, reason) {
        return this.show({
            type: 'house_points',
            title: 'House Points Awarded!',
            message: `${houseName} house earned ${points} points for ${reason}`,
            duration: 7000,
            actions: [
                {
                    id: 'leaderboard',
                    text: 'View Leaderboard',
                    type: 'btn-primary',
                    onclick: "window.location.href='house_points.php'"
                }
            ]
        });
    }

    systemAnnouncement(title, message, priority = 'normal') {
        return this.show({
            type: 'announcement',
            title: title,
            message: message,
            duration: priority === 'high' ? 10000 : 6000,
            persistent: priority === 'critical'
        });
    }
}

// Performance monitoring
class PerformanceMonitor {
    constructor() {
        this.metrics = {};
        this.thresholds = {
            loadTime: 3000, // 3 seconds
            apiResponse: 2000, // 2 seconds
            renderTime: 100 // 100ms
        };
    }

    startTimer(label) {
        this.metrics[label] = { start: performance.now() };
    }

    endTimer(label) {
        if (this.metrics[label]) {
            const duration = performance.now() - this.metrics[label].start;
            this.metrics[label].duration = duration;
            
            // Check thresholds
            if (this.thresholds[label] && duration > this.thresholds[label]) {
                console.warn(`Performance warning: ${label} took ${duration.toFixed(2)}ms`);
            }
            
            return duration;
        }
    }

    getMetrics() {
        return this.metrics;
    }
}

// Initialize global instances
const notificationManager = new NotificationManager();
const performanceMonitor = new PerformanceMonitor();

// Add CSS for notifications if not already present
if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        .notification-content {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .notification-header {
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-md);
        }

        .notification-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-text {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
            color: var(--secondary-900);
        }

        .notification-message {
            font-size: 0.875rem;
            color: var(--secondary-700);
            line-height: 1.4;
        }

        .notification-close {
            background: none;
            border: none;
            color: var(--secondary-500);
            cursor: pointer;
            padding: var(--spacing-xs);
            border-radius: var(--radius-sm);
            transition: all var(--transition-fast);
            flex-shrink: 0;
        }

        .notification-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: var(--secondary-700);
        }

        .notification-actions {
            display: flex;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-sm);
            padding-top: var(--spacing-sm);
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .notification-action {
            font-size: 0.8rem;
            padding: var(--spacing-xs) var(--spacing-md);
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            animation: progressBar linear;
            transform-origin: left;
        }

        @keyframes progressBar {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }

        .notification-enter {
            animation: slideInRight 0.3s ease-out forwards;
        }

        .notification-exit {
            animation: slideOutRight 0.2s ease-in forwards;
        }

        @keyframes slideOutRight {
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Mobile optimizations */
        @media (max-width: 480px) {
            .notification {
                margin: 0 var(--spacing-sm) var(--spacing-sm) var(--spacing-sm);
                max-width: none;
            }

            .notification-actions {
                flex-direction: column;
            }

            .notification-action {
                width: 100%;
                justify-content: center;
            }
        }
    `;
    document.head.appendChild(style);
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { NotificationManager, PerformanceMonitor };
}