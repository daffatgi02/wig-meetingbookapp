{{-- resources/views/admin/bookings/partials/actions.blade.php --}}
<div class="btn-group" role="group">
    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" 
            data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-cog"></i>
    </button>
    <ul class="dropdown-menu">
        <!-- View Details -->
        <li>
            <a class="dropdown-item" href="{{ route('admin.bookings.show', $booking) }}">
                <i class="fas fa-eye me-2 text-info"></i>
                View Details
            </a>
        </li>

        <!-- Edit Booking -->
        @if($booking->isEditable() || auth()->user()->isAdmin())
            <li>
                <a class="dropdown-item" href="{{ route('admin.bookings.edit', $booking) }}">
                    <i class="fas fa-edit me-2 text-warning"></i>
                    Edit Booking
                </a>
            </li>
        @endif

        <li><hr class="dropdown-divider"></li>

        <!-- Approval Actions -->
        @if($booking->status === 'pending')
            <li>
                <a class="dropdown-item text-success" href="#" 
                   onclick="approveBooking({{ $booking->id }})">
                    <i class="fas fa-check me-2"></i>
                    Approve
                </a>
            </li>
            <li>
                <a class="dropdown-item text-danger" href="#" 
                   onclick="rejectBooking({{ $booking->id }})">
                    <i class="fas fa-times me-2"></i>
                    Reject
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
        @endif

        <!-- Status Actions -->
        @if(in_array($booking->status, ['approved', 'ongoing']))
            @if($booking->status === 'approved' && $booking->start_date_time->isPast())
                <li>
                    <a class="dropdown-item text-info" href="#" 
                       onclick="markAsOngoing({{ $booking->id }})">
                        <i class="fas fa-play me-2"></i>
                        Mark as Ongoing
                    </a>
                </li>
            @endif

            @if($booking->status === 'ongoing' && $booking->end_date_time->isPast())
                <li>
                    <a class="dropdown-item text-success" href="#" 
                       onclick="markAsCompleted({{ $booking->id }})">
                        <i class="fas fa-check-double me-2"></i>
                        Mark as Completed
                    </a>
                </li>
            @endif
        @endif

        <!-- Cancel/Delete Actions -->
        @if($booking->isCancellable())
            <li>
                <a class="dropdown-item text-danger" href="#" 
                   onclick="cancelBooking({{ $booking->id }})">
                    <i class="fas fa-ban me-2"></i>
                    Cancel Booking
                </a>
            </li>
        @endif

        @if(in_array($booking->status, ['cancelled', 'rejected', 'completed']))
            <li>
                <a class="dropdown-item text-danger" href="#" 
                   onclick="deleteBooking({{ $booking->id }})">
                    <i class="fas fa-trash me-2"></i>
                    Delete Permanently
                </a>
            </li>
        @endif

        <li><hr class="dropdown-divider"></li>

        <!-- Other Actions -->
        <li>
            <a class="dropdown-item" href="#" onclick="duplicateBooking({{ $booking->id }})">
                <i class="fas fa-copy me-2 text-secondary"></i>
                Duplicate
            </a>
        </li>
        
        <li>
            <a class="dropdown-item" href="#" onclick="printBooking({{ $booking->id }})">
                <i class="fas fa-print me-2 text-secondary"></i>
                Print Details
            </a>
        </li>
    </ul>
</div>

@if($booking->status === 'pending')
    <!-- Quick Action Buttons for Pending -->
    <div class="btn-group ms-2" role="group">
        <button class="btn btn-success btn-sm" onclick="approveBooking({{ $booking->id }})" 
                data-bs-toggle="tooltip" title="Quick Approve">
            <i class="fas fa-check"></i>
        </button>
        <button class="btn btn-danger btn-sm" onclick="rejectBooking({{ $booking->id }})" 
                data-bs-toggle="tooltip" title="Quick Reject">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<script>
function approveBooking(bookingId) {
    confirmAction(
        'Approve Booking?',
        'Are you sure you want to approve this booking?',
        'Yes, Approve!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.post(`/admin/bookings/${bookingId}/approve`)
                .done(function(response) {
                    showToast('success', 'Booking approved successfully');
                    setTimeout(() => location.reload(), 1500);
                })
                .fail(function(xhr) {
                    let message = 'Failed to approve booking';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast('error', message);
                });
        }
    });
}

function rejectBooking(bookingId) {
    // Set current booking ID for modal
    window.currentBookingId = bookingId;
    
    // Clear previous reason
    $('#rejection_reason').val('');
    
    // Show reject modal
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    rejectModal.show();
}

function cancelBooking(bookingId) {
    confirmAction(
        'Cancel Booking?',
        'Are you sure you want to cancel this booking? This action cannot be undone.',
        'Yes, Cancel!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.post(`/admin/bookings/${bookingId}/cancel`)
                .done(function(response) {
                    showToast('success', 'Booking cancelled successfully');
                    setTimeout(() => location.reload(), 1500);
                })
                .fail(function(xhr) {
                    showToast('error', 'Failed to cancel booking');
                });
        }
    });
}

function deleteBooking(bookingId) {
    confirmAction(
        'Delete Booking Permanently?',
        'This will permanently delete the booking and all its data. This action cannot be undone.',
        'Yes, Delete Permanently!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/bookings/${bookingId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function(response) {
                showToast('success', 'Booking deleted permanently');
                setTimeout(() => location.reload(), 1500);
            })
            .fail(function(xhr) {
                showToast('error', 'Failed to delete booking');
            });
        }
    });
}

function duplicateBooking(bookingId) {
    window.location.href = `/admin/bookings/create?duplicate=${bookingId}`;
}

function printBooking(bookingId) {
    window.open(`/admin/bookings/${bookingId}?print=1`, '_blank');
}

function markAsOngoing(bookingId) {
    $.post(`/admin/bookings/${bookingId}/mark-ongoing`)
        .done(function(response) {
            showToast('success', 'Marked as ongoing');
            setTimeout(() => location.reload(), 1500);
        })
        .fail(function(xhr) {
            showToast('error', 'Failed to update status');
        });
}

function markAsCompleted(bookingId) {
    $.post(`/admin/bookings/${bookingId}/mark-completed`)
        .done(function(response) {
            showToast('success', 'Marked as completed');
            setTimeout(() => location.reload(), 1500);
        })
        .fail(function(xhr) {
            showToast('error', 'Failed to update status');
        });
}
</script>