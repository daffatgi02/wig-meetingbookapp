// Add to main layout for real-time notifications
// resources/js/notifications.js

class NotificationManager {
    constructor() {
        this.unreadCount = 0;
        this.lastCheck = new Date();
        this.checkInterval = 60000; // Check every minute
        this.init();
    }

    init() {
        this.updateCount();
        this.startPolling();
        this.bindEvents();
    }

    startPolling() {
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                this.checkNewNotifications();
            }
        }, this.checkInterval);
    }

    bindEvents() {
        // Mark notification as read when clicked
        $(document).on('click', '.notification-item', function () {
            const notificationId = $(this).data('notification-id');
            if (notificationId) {
                notificationManager.markAsRead(notificationId);
            }
        });

        // Handle visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.checkNewNotifications();
            }
        });
    }

    async checkNewNotifications() {
        try {
            const response = await fetch('/notifications/unread');
            const data = await response.json();

            if (data.notifications && data.notifications.length > 0) {
                // Check for new notifications since last check
                const newNotifications = data.notifications.filter(notification =>
                    new Date(notification.created_at) > this.lastCheck
                );

                if (newNotifications.length > 0) {
                    this.showNewNotificationAlert(newNotifications);
                }
            }

            this.unreadCount = data.count;
            this.updateBadge();
            this.lastCheck = new Date();

        } catch (error) {
            console.error('Failed to check notifications:', error);
        }
    }

    showNewNotificationAlert(notifications) {
        notifications.forEach(notification => {
            // Show toast notification
            showToast('info', notification.title, {
                timer: 5000,
                showConfirmButton: true,
                confirmButtonText: 'View',
                cancelButtonText: 'Dismiss',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed && notification.data && notification.data.booking_id) {
                    window.location.href = `/bookings/${notification.data.booking_id}`;
                }
            });
        });
    }

    async updateCount() {
        try {
            const response = await fetch('/notifications/count');
            const data = await response.json();
            this.unreadCount = data.count;
            this.updateBadge();
        } catch (error) {
            console.error('Failed to update notification count:', error);
        }
    }

    updateBadge() {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (this.unreadCount > 0) {
                badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });

            this.unreadCount = Math.max(0, this.unreadCount - 1);
            this.updateBadge();

        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            this.unreadCount = 0;
            this.updateBadge();

            return data.marked_count;

        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
            throw error;
        }
    }
}

// Initialize notification manager when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    if (typeof notificationManager === 'undefined') {
        window.notificationManager = new NotificationManager();
    }
});