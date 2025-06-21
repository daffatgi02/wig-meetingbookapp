{{-- resources/views/admin/bookings/partials/approval-modal.blade.php --}}
<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 bg-danger bg-opacity-10">
                <h5 class="modal-title text-danger" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>
                    Reject Booking
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Important:</strong> Please provide a clear reason for rejection. The user will receive this message via notification.
                </div>

                <form id="rejectForm">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-bold">
                            <i class="fas fa-comment me-1"></i>
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="4" required maxlength="500"
                                  placeholder="Please explain why this booking request is being rejected. Be specific and professional..."></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/500 characters
                        </div>
                    </div>

                    <!-- Common rejection reasons -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Quick Reasons:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="Room is not available at the requested time.">
                                Room Unavailable
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="Insufficient information provided in the booking request.">
                                Insufficient Info
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="Room capacity exceeds the maximum allowed participants.">
                                Capacity Exceeded
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="Booking does not comply with company policies.">
                                Policy Violation
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmReject" disabled>
                    <i class="fas fa-ban me-2"></i>
                    Reject Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Move Booking Modal -->
<div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="moveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 bg-info bg-opacity-10">
                <h5 class="modal-title text-info" id="moveModalLabel">
                    <i class="fas fa-arrows-alt me-2"></i>
                    Move Booking
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="moveForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="move_room_id" class="form-label fw-bold">Room</label>
                            <select class="form-select" id="move_room_id" name="room_id" required>
                                <option value="">Select Room</option>
                                @foreach(\App\Models\Room::active()->orderBy('name')->get() as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->name }} - {{ $room->location }} ({{ $room->capacity }} people)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="move_booking_date" class="form-label fw-bold">Date</label>
                            <input type="date" class="form-control" id="move_booking_date" name="booking_date" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="move_start_time" class="form-label fw-bold">Start Time</label>
                            <input type="time" class="form-control" id="move_start_time" name="start_time" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="move_end_time" class="form-label fw-bold">End Time</label>
                            <input type="time" class="form-control" id="move_end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div id="moveAvailabilityCheck" class="alert" style="display: none;"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="confirmMove" disabled>
                    <i class="fas fa-arrows-alt me-2"></i>
                    Move Booking
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Character counter for rejection reason
    $('#rejection_reason').on('input', function() {
        const length = $(this).val().length;
        $('#charCount').text(length);
        
        // Enable/disable reject button
        $('#confirmReject').prop('disabled', length < 10);
    });

    // Quick reason buttons
    $('.quick-reason').click(function() {
        const reason = $(this).data('reason');
        $('#rejection_reason').val(reason).trigger('input');
    });

    // Confirm reject button
    $('#confirmReject').click(function() {
        const reason = $('#rejection_reason').val().trim();
        
        if (reason.length < 10) {
            showToast('error', 'Please provide a more detailed reason (minimum 10 characters)');
            return;
        }

        if (!window.currentBookingId) {
            showToast('error', 'No booking selected');
            return;
        }

        $.post(`/admin/bookings/${window.currentBookingId}/reject`, {
            rejection_reason: reason,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            showToast('success', 'Booking rejected successfully');
            $('#rejectModal').modal('hide');
            setTimeout(() => location.reload(), 1500);
        })
        .fail(function(xhr) {
            let message = 'Failed to reject booking';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
        });
    });

    // Move booking functionality
    $('#move_room_id, #move_booking_date, #move_start_time, #move_end_time').change(function() {
        checkMoveAvailability();
    });

    function checkMoveAvailability() {
        const roomId = $('#move_room_id').val();
        const date = $('#move_booking_date').val();
        const startTime = $('#move_start_time').val();
        const endTime = $('#move_end_time').val();

        if (roomId && date && startTime && endTime) {
            $.get('/api/rooms/' + roomId + '/availability', {
                date: date,
                start_time: startTime,
                end_time: endTime,
                exclude_booking_id: window.currentBookingId
            })
            .done(function(response) {
                if (response.available) {
                    $('#moveAvailabilityCheck')
                        .removeClass('alert-danger')
                        .addClass('alert-success')
                        .html('<i class="fas fa-check me-2"></i>Time slot is available')
                        .show();
                    $('#confirmMove').prop('disabled', false);
                } else {
                    $('#moveAvailabilityCheck')
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .html('<i class="fas fa-times me-2"></i>Time slot is not available')
                        .show();
                    $('#confirmMove').prop('disabled', true);
                }
            })
            .fail(function() {
                $('#moveAvailabilityCheck').hide();
                $('#confirmMove').prop('disabled', true);
            });
        } else {
            $('#moveAvailabilityCheck').hide();
            $('#confirmMove').prop('disabled', true);
        }
    }

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#move_booking_date').attr('min', today);
});

// Global function to open move modal
function moveBooking(bookingId) {
    window.currentBookingId = bookingId;
    
    // Reset form
    $('#moveForm')[0].reset();
    $('#moveAvailabilityCheck').hide();
    $('#confirmMove').prop('disabled', true);
    
    const moveModal = new bootstrap.Modal(document.getElementById('moveModal'));
    moveModal.show();
}
</script>