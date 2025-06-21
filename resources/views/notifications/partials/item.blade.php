{{-- resources/views/notifications/partials/item.blade.php --}}
<div class="notification-item {{ $notification->is_read ? '' : 'bg-light' }}" 
     data-notification-id="{{ $notification->id }}">
    <div class="d-flex align-items-start p-3">
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
            <p class="mb-1 small text-muted">{{ Str::limit($notification->message, 100) }}</p>
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
        <div class="px-3 pb-3">
            <a href="{{ route('bookings.show', $notification->data['booking_id']) }}" 
               class="btn btn-outline-primary btn-sm">
                <i class="fas fa-eye me-1"></i>
                View Booking
            </a>
        </div>
    @endif
</div>