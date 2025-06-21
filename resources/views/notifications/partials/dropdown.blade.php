{{-- resources/views/notifications/partials/dropdown.blade.php --}}
<div class="dropdown-header d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Notifications</h6>
    @if($unreadNotifications > 0)
        <button class="btn btn-link btn-sm text-primary p-0" id="markAllRead">
            Mark all read
        </button>
    @endif
</div>

<div class="dropdown-divider"></div>

<div class="notification-list" style="max-height: 300px; overflow-y: auto;">
    @php
        $recentNotifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    @endphp

    @if($recentNotifications->count() > 0)
        @foreach($recentNotifications as $notification)
            <div class="dropdown-item-text notification-item {{ $notification->is_read ? '' : 'bg-light' }} p-3" 
                 data-notification-id="{{ $notification->id }}">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        @switch($notification->type)
                            @case('booking_approved')
                                <i class="fas fa-check-circle text-success"></i>
                                @break
                            @case('booking_rejected')
                                <i class="fas fa-times-circle text-danger"></i>
                                @break
                            @case('booking_moved')
                                <i class="fas fa-arrows-alt text-warning"></i>
                                @break
                            @case('booking_reminder')
                                <i class="fas fa-bell text-info"></i>
                                @break
                            @default
                                <i class="fas fa-info-circle text-primary"></i>
                        @endswitch
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 small">{{ $notification->title }}</h6>
                        <p class="mb-1 small text-muted">{{ Str::limit($notification->message, 80) }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        
                        @if(!$notification->is_read)
                            <span class="badge bg-primary ms-2">New</span>
                        @endif
                    </div>
                    @if(!$notification->is_read)
                        <div class="flex-shrink-0">
                            <button class="btn btn-link btn-sm text-muted p-0 mark-read" 
                                    data-id="{{ $notification->id }}">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    @endif
                </div>

                @if(isset($notification->data['booking_id']))
                    <div class="mt-2">
                        <a href="{{ route('bookings.show', $notification->data['booking_id']) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>
                            View Booking
                        </a>
                    </div>
                @endif
            </div>
            @if(!$loop->last)
                <div class="dropdown-divider"></div>
            @endif
        @endforeach
    @else
        <div class="dropdown-item-text text-center py-4">
            <i class="fas fa-bell-slash text-muted mb-2" style="font-size: 2rem;"></i>
            <p class="text-muted mb-0">No notifications yet</p>
        </div>
    @endif
</div>

@if($recentNotifications->count() > 0)
    <div class="dropdown-divider"></div>
    <div class="dropdown-footer text-center">
        <a href="{{ route('notifications.index') }}" class="text-primary text-decoration-none small">
            View All Notifications
        </a>
    </div>
@endif

<script>
$(document).ready(function() {
    // Mark single notification as read
    $('.mark-read').click(function(e) {
        e.preventDefault();
        const notificationId = $(this).data('id');
        const notificationItem = $(this).closest('.notification-item');
        
        $.post(`/notifications/${notificationId}/read`)
            .done(function() {
                notificationItem.removeClass('bg-light');
                notificationItem.find('.badge').remove();
                notificationItem.find('.mark-read').remove();
                updateNotificationCount();
            });
    });

    // Mark all as read
    $('#markAllRead').click(function(e) {
        e.preventDefault();
        
        $.post('/notifications/mark-all-read')
            .done(function() {
                $('.notification-item').removeClass('bg-light');
                $('.notification-item .badge').remove();
                $('.mark-read').remove();
                $('#markAllRead').hide();
                updateNotificationCount();
            });
    });

    // Update notification count in navbar
    function updateNotificationCount() {
        $.get('/notifications/count')
            .done(function(data) {
                const badge = $('.notification-badge');
                if (data.count > 0) {
                    badge.text(data.count > 99 ? '99+' : data.count).show();
                } else {
                    badge.hide();
                }
            });
    }
});
</script>