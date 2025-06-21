{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-dark mb-1">Notifications</h3>
                    <p class="text-muted mb-0">Stay updated with your booking activities</p>
                </div>

                @if ($notifications->where('is_read', false)->count() > 0)
                    <button class="btn btn-outline-primary" id="markAllRead">
                        <i class="fas fa-check-double me-2"></i>
                        Mark All as Read
                    </button>
                @endif
            </div>

            <!-- Notification Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body text-center py-3">
                            <i class="fas fa-bell text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <h4 class="text-primary mb-0">{{ $notifications->count() }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body text-center py-3">
                            <i class="fas fa-envelope text-warning mb-2" style="font-size: 1.5rem;"></i>
                            <h4 class="text-warning mb-0">{{ $notifications->where('is_read', false)->count() }}</h4>
                            <small class="text-muted">Unread</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body text-center py-3">
                            <i class="fas fa-envelope-open text-success mb-2" style="font-size: 1.5rem;"></i>
                            <h4 class="text-success mb-0">{{ $notifications->where('is_read', true)->count() }}</h4>
                            <small class="text-muted">Read</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <ul class="nav nav-pills nav-fill" id="notificationTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-filter="all" href="#">
                                <i class="fas fa-list me-2"></i>All Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-filter="unread" href="#">
                                <i class="fas fa-envelope me-2"></i>Unread
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-filter="booking_approved" href="#">
                                <i class="fas fa-check-circle me-2"></i>Approved
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-filter="booking_rejected" href="#">
                                <i class="fas fa-times-circle me-2"></i>Rejected
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if ($notifications->count() > 0)
                        <div class="list-group list-group-flush" id="notificationsList">
                            @foreach ($notifications as $notification)
                                <div class="list-group-item notification-item {{ $notification->is_read ? '' : 'bg-light border-start border-primary border-3' }}"
                                    data-notification-id="{{ $notification->id }}" data-type="{{ $notification->type }}"
                                    data-read="{{ $notification->is_read ? 'true' : 'false' }}">

                                    <div class="d-flex align-items-start">
                                        <!-- Notification Icon -->
                                        <div class="flex-shrink-0 me-3">
                                            <div
                                                class="notification-icon bg-{{ $notification->is_read ? 'secondary' : 'primary' }} bg-opacity-10 rounded-circle p-3">
                                                @switch($notification->type)
                                                    @case('booking_approved')
                                                        <i class="fas fa-check-circle text-success" style="font-size: 1.25rem;"></i>
                                                    @break

                                                    @case('booking_rejected')
                                                        <i class="fas fa-times-circle text-danger" style="font-size: 1.25rem;"></i>
                                                    @break

                                                    @case('booking_moved')
                                                        <i class="fas fa-arrows-alt text-warning" style="font-size: 1.25rem;"></i>
                                                    @break

                                                    @case('booking_reminder')
                                                        <i class="fas fa-bell text-info" style="font-size: 1.25rem;"></i>
                                                    @break

                                                    @case('booking_updated')
                                                        <i class="fas fa-edit text-warning" style="font-size: 1.25rem;"></i>
                                                    @break

                                                    @case('booking_cancelled')
                                                        <i class="fas fa-ban text-secondary" style="font-size: 1.25rem;"></i>
                                                    @break

                                                    @default
                                                        <i class="fas fa-info-circle text-primary" style="font-size: 1.25rem;"></i>
                                                @endswitch
                                            </div>
                                        </div>

                                        <!-- Notification Content -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 {{ $notification->is_read ? 'text-muted' : 'text-dark' }}">
                                                    {{ $notification->title }}
                                                </h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if (!$notification->is_read)
                                                        <span class="badge bg-primary">New</span>
                                                    @endif
                                                    <small
                                                        class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>

                                            <p class="mb-2 {{ $notification->is_read ? 'text-muted' : 'text-dark' }}">
                                                {{ $notification->message }}
                                            </p>

                                            <!-- Action Buttons -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex gap-2">
                                                    @if (isset($notification->data['booking_id']))
                                                        <a href="{{ route('bookings.show', $notification->data['booking_id']) }}"
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>
                                                            View Booking
                                                        </a>
                                                    @endif

                                                    @if (!$notification->is_read)
                                                        <button class="btn btn-outline-success btn-sm mark-read"
                                                            data-id="{{ $notification->id }}">
                                                            <i class="fas fa-check me-1"></i>
                                                            Mark as Read
                                                        </button>
                                                    @endif
                                                </div>

                                                <div class="dropdown">
                                                    <button class="btn btn-link btn-sm text-muted p-0" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if (!$notification->is_read)
                                                            <li>
                                                                <a class="dropdown-item mark-read" href="#"
                                                                    data-id="{{ $notification->id }}">
                                                                    <i class="fas fa-check me-2"></i>Mark as Read
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a class="dropdown-item mark-unread" href="#"
                                                                    data-id="{{ $notification->id }}">
                                                                    <i class="fas fa-envelope me-2"></i>Mark as Unread
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <form
                                                                action="{{ route('notifications.destroy', $notification) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger delete-notification">
                                                                    <i class="fas fa-trash me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash text-muted mb-3" style="font-size: 4rem;"></i>
                            <h5 class="text-muted">No notifications yet</h5>
                            <p class="text-muted">You'll receive notifications here when there are updates to your bookings
                            </p>
                        </div>
                    @endif
                </div>

                @if ($notifications->hasPages())
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-center">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Filter functionality
            $('#notificationTabs .nav-link').click(function(e) {
                e.preventDefault();

                // Update active tab
                $('#notificationTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                const filter = $(this).data('filter');
                filterNotifications(filter);
            });

            // Mark single notification as read
            $('.mark-read').click(function(e) {
                e.preventDefault();
                const notificationId = $(this).data('id');
                const notificationItem = $(this).closest('.notification-item');

                $.post(`/notifications/${notificationId}/read`)
                    .done(function() {
                        notificationItem.removeClass('bg-light border-start border-primary border-3');
                        notificationItem.find('.badge').remove();
                        notificationItem.find('.mark-read').remove();
                        notificationItem.attr('data-read', 'true');

                        // Update notification icon
                        notificationItem.find('.notification-icon')
                            .removeClass('bg-primary')
                            .addClass('bg-secondary');

                        // Update text color
                        notificationItem.find('h6, p').removeClass('text-dark').addClass('text-muted');

                        updateNotificationCount();
                        showToast('success', 'Notification marked as read');
                    })
                    .fail(function() {
                        showToast('error', 'Failed to mark notification as read');
                    });
            });

            // Mark notification as unread
            $('.mark-unread').click(function(e) {
                e.preventDefault();
                const notificationId = $(this).data('id');
                const notificationItem = $(this).closest('.notification-item');

                $.post(`/notifications/${notificationId}/unread`)
                    .done(function() {
                        notificationItem.addClass('bg-light border-start border-primary border-3');
                        notificationItem.attr('data-read', 'false');

                        // Update notification icon
                        notificationItem.find('.notification-icon')
                            .removeClass('bg-secondary')
                            .addClass('bg-primary');

                        // Update text color
                        notificationItem.find('h6, p').removeClass('text-muted').addClass('text-dark');

                        // Add new badge
                        notificationItem.find('.badge').remove();
                        notificationItem.find('small').before(
                            '<span class="badge bg-primary">New</span>');

                        updateNotificationCount();
                        showToast('success', 'Notification marked as unread');
                    })
                    .fail(function() {
                        showToast('error', 'Failed to mark notification as unread');
                    });
            });

            // Mark all as read
            $('#markAllRead').click(function() {
                confirmAction(
                    'Mark All as Read?',
                    'Are you sure you want to mark all notifications as read?',
                    'Yes, Mark All!'
                ).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/notifications/mark-all-read')
                            .done(function(response) {
                                $('.notification-item').removeClass(
                                    'bg-light border-start border-primary border-3');
                                $('.notification-item .badge').remove();
                                $('.mark-read').remove();
                                $('.notification-item').attr('data-read', 'true');

                                // Update all icons and text
                                $('.notification-icon').removeClass('bg-primary').addClass(
                                    'bg-secondary');
                                $('.notification-item h6, .notification-item p').removeClass(
                                    'text-dark').addClass('text-muted');

                                $('#markAllRead').hide();
                                updateNotificationCount();
                                showToast('success',
                                    `${response.marked_count} notifications marked as read`);
                            })
                            .fail(function() {
                                showToast('error', 'Failed to mark notifications as read');
                            });
                    }
                });
            });

            // Delete notification with confirmation
            $('.delete-notification').click(function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                confirmAction(
                    'Delete Notification?',
                    'Are you sure you want to delete this notification? This action cannot be undone.',
                    'Yes, Delete!'
                ).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Auto-refresh notifications every 2 minutes
            setInterval(function() {
                if (document.visibilityState === 'visible') {
                    updateNotificationCount();
                }
            }, 120000);
        });

        function filterNotifications(filter) {
            const notifications = $('.notification-item');

            notifications.each(function() {
                const notification = $(this);
                const type = notification.data('type');
                const isRead = notification.data('read') === 'true';

                let show = false;

                switch (filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'unread':
                        show = !isRead;
                        break;
                    case 'booking_approved':
                        show = type === 'booking_approved';
                        break;
                    case 'booking_rejected':
                        show = type === 'booking_rejected';
                        break;
                    default:
                        show = type === filter;
                }

                if (show) {
                    notification.show();
                } else {
                    notification.hide();
                }
            });

            // Show/hide empty state
            const visibleNotifications = $('.notification-item:visible');
            if (visibleNotifications.length === 0) {
                if ($('#emptyState').length === 0) {
                    $('#notificationsList').after(`
               <div id="emptyState" class="text-center py-5">
                   <i class="fas fa-filter text-muted mb-3" style="font-size: 3rem;"></i>
                   <h6 class="text-muted">No notifications found</h6>
                   <p class="text-muted">No notifications match the current filter</p>
               </div>
           `);
                }
                $('#emptyState').show();
            } else {
                $('#emptyState').hide();
            }
        }

        function updateNotificationCount() {
            $.get('/notifications/count')
                .done(function(data) {
                    const badge = $('.notification-badge');
                    if (data.count > 0) {
                        badge.text(data.count > 99 ? '99+' : data.count).show();
                    } else {
                        badge.hide();
                    }

                    // Update stats cards
                    const unreadCount = $('.notification-item[data-read="false"]').length;
                    $('.bg-warning .h4').text(unreadCount);
                    $('.bg-success .h4').text($('.notification-item[data-read="true"]').length);

                    // Hide mark all read button if no unread notifications
                    if (unreadCount === 0) {
                        $('#markAllRead').hide();
                    }
                });
        }
    </script>
@endpush
