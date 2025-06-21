{{-- resources/views/bookings/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Booking Detail')

@section('content')
    <div class="row">
        <!-- Main Booking Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $booking->title }}</h5>
                        <small class="text-muted">Booking ID: #{{ $booking->id }}</small>
                    </div>
                    @include('components.status-badge', ['status' => $booking->status])
                </div>
                <div class="card-body">
                    <!-- Meeting Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Meeting Information
                            </h6>

                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="35%">
                                        <i class="fas fa-heading me-2"></i>Title:
                                    </td>
                                    <td class="fw-bold">{{ $booking->title }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-bullseye me-2"></i>Purpose:
                                    </td>
                                    <td>{{ $booking->purpose }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-calendar me-2"></i>Date:
                                    </td>
                                    <td>
                                        {{ $booking->booking_date->format('l, d F Y') }}
                                        <span class="badge bg-light text-dark ms-2">
                                            {{ $booking->booking_date->diffForHumans() }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-clock me-2"></i>Time:
                                    </td>
                                    <td>
                                        {{ $booking->start_time }} - {{ $booking->end_time }}
                                        <span class="badge bg-info text-white ms-2">
                                            {{ $booking->getDurationInMinutes() }} min
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">
                                        <i class="fas fa-users me-2"></i>Participants:
                                    </td>
                                    <td>{{ $booking->participant_count }} people</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-door-open me-2"></i>
                                Room Details
                            </h6>

                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <h6 class="card-title text-primary">{{ $booking->room->name }}</h6>
                                    <p class="card-text mb-2">
                                        <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                        {{ $booking->room->location }}
                                    </p>
                                    <p class="card-text mb-2">
                                        <i class="fas fa-users me-2 text-muted"></i>
                                        Capacity: {{ $booking->room->capacity }} people
                                    </p>

                                    @if ($booking->room->facilities && count($booking->room->facilities) > 0)
                                        <div class="mt-3">
                                            <small class="text-muted d-block mb-2">Available Facilities:</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($booking->room->facilities as $facility)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        {{ $facility }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    @if ($booking->description)
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="fas fa-sticky-note me-2"></i>
                                Additional Notes
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $booking->description }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Participants List -->
                    @if ($booking->participants->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="fas fa-user-friends me-2"></i>
                                Meeting Participants ({{ $booking->participants->count() }})
                            </h6>

                            <div class="row">
                                @foreach ($booking->participants as $participant)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="d-flex align-items-center p-2 bg-light rounded">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px; font-size: 16px;">
                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold small">{{ $participant->name }}</div>
                                                @if ($participant->email)
                                                    <div class="small text-muted">{{ $participant->email }}</div>
                                                @endif
                                                @if ($participant->isRegisteredUser())
                                                    <span class="badge bg-success badge-sm">Registered User</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Booking Status Information -->
                    <div class="mb-4">
                        <h6 class="text-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            Booking Status Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Current Status:</span>
                                        @include('components.status-badge', ['status' => $booking->status])
                                    </div>

                                    @if ($booking->status === 'rejected' && $booking->rejection_reason)
                                        <div class="mt-2">
                                            <small class="text-muted">Rejection Reason:</small>
                                            <p class="text-danger small mb-0">{{ $booking->rejection_reason }}</p>
                                        </div>
                                    @endif

                                    @if ($booking->requires_reapproval)
                                        <div class="mt-2">
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Requires Re-approval
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="small text-muted mb-1">Created:</div>
                                    <div class="fw-bold">{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="small text-muted">{{ $booking->created_at->diffForHumans() }}</div>

                                    @if ($booking->approved_at)
                                        <div class="small text-muted mb-1 mt-2">
                                            {{ $booking->status === 'rejected' ? 'Rejected:' : 'Approved:' }}
                                        </div>
                                        <div class="fw-bold">{{ $booking->approved_at->format('d/m/Y H:i') }}</div>
                                        @if ($booking->approver)
                                            <div class="small text-muted">by {{ $booking->approver->name }}</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        <div>
                            <a href="{{ auth()->user()->isAdmin() ? route('admin.bookings.index') : route('bookings.index') }}"
                                class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Bookings
                            </a>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            @can('update', $booking)
                                @if ($booking->isEditable())
                                    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>
                                        Edit Booking
                                    </a>
                                @endif
                            @endcan

                            @if (auth()->user()->isAdmin())
                                @if ($booking->status === 'pending')
                                    <button class="btn btn-success" onclick="approveBooking({{ $booking->id }})">
                                        <i class="fas fa-check me-2"></i>
                                        Approve
                                    </button>
                                    <button class="btn btn-danger" onclick="rejectBooking({{ $booking->id }})">
                                        <i class="fas fa-times me-2"></i>
                                        Reject
                                    </button>
                                @endif

                                @if (in_array($booking->status, ['approved', 'pending']))
                                    <button class="btn btn-outline-info" onclick="moveBooking({{ $booking->id }})">
                                        <i class="fas fa-arrows-alt me-2"></i>
                                        Move/Reschedule
                                    </button>
                                @endif
                            @endif

                            @can('delete', $booking)
                                @if ($booking->isCancellable())
                                    <button class="btn btn-outline-danger" onclick="cancelBooking({{ $booking->id }})">
                                        <i class="fas fa-ban me-2"></i>
                                        Cancel Booking
                                    </button>
                                @endif
                            @endcan

                            <!-- Print/Export -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-2"></i>
                                    Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="printBooking()">
                                            <i class="fas fa-print me-2"></i>Print Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="exportToPDF()">
                                            <i class="fas fa-file-pdf me-2"></i>Export to PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="addToCalendar()">
                                            <i class="fas fa-calendar-plus me-2"></i>Add to Calendar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- Booking Summary -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        Meeting Timeline
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $now = now();
                        $bookingDateTime = $booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time;
                        $startDateTime = \Carbon\Carbon::parse($bookingDateTime);
                        $endDateTime = \Carbon\Carbon::parse(
                            $booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time,
                        );
                    @endphp

                    <div class="timeline-status">
                        @if ($booking->status === 'approved')
                            @if ($now->lt($startDateTime))
                                <div class="alert alert-info">
                                    <i class="fas fa-hourglass-start me-2"></i>
                                    <strong>Upcoming Meeting</strong><br>
                                    Starts in
                                    {{ $now->diffInHours($startDateTime) > 24 ? $startDateTime->diffForHumans() : $startDateTime->diffForHumans() }}
                                </div>
                            @elseif($now->between($startDateTime, $endDateTime))
                                <div class="alert alert-success">
                                    <i class="fas fa-play-circle me-2"></i>
                                    <strong>Meeting in Progress</strong><br>
                                    Started {{ $startDateTime->diffForHumans() }}<br>
                                    Ends {{ $endDateTime->diffForHumans() }}
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <i class="fas fa-check-double me-2"></i>
                                    <strong>Meeting Completed</strong><br>
                                    Ended {{ $endDateTime->diffForHumans() }}
                                </div>
                            @endif
                        @elseif($booking->status === 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Awaiting Approval</strong><br>
                                Submitted {{ $booking->created_at->diffForHumans() }}
                            </div>
                        @elseif($booking->status === 'rejected')
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Booking Rejected</strong><br>
                                {{ $booking->approved_at->diffForHumans() }}
                            </div>
                        @endif
                    </div>

                    <!-- Quick Info -->
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="h5 text-primary mb-0">{{ $booking->getDurationInMinutes() }}</div>
                                    <small class="text-muted">Minutes</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="h5 text-success mb-0">{{ $booking->participant_count }}</div>
                                    <small class="text-muted">Participants</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="h5 text-info mb-0">{{ $booking->room->capacity }}</div>
                                <small class="text-muted">Capacity</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Availability for This Date -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-day me-2 text-primary"></i>
                        {{ $booking->room->name }} - {{ $booking->booking_date->format('d/m/Y') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div id="roomSchedule">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Loading schedule...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizer Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2 text-primary"></i>
                        Organizer
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px; font-size: 20px;">
                            {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $booking->user->name }}</h6>
                            <p class="text-muted mb-1">{{ $booking->user->email }}</p>
                            @if ($booking->user->phone)
                                <p class="text-muted mb-0">
                                    <i class="fas fa-phone me-1"></i>{{ $booking->user->phone }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($booking->adminCreator && $booking->adminCreator->id !== $booking->user->id)
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Created by admin: {{ $booking->adminCreator->name }}
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        Reject Booking
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this booking?</p>
                    <form id="rejectForm">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">
                                Reason for Rejection <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required
                                placeholder="Please explain why this booking is being rejected..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">
                        <i class="fas fa-times me-2"></i>
                        Reject Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Move/Reschedule Modal -->
    <div class="modal fade" id="moveModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-info">
                        <i class="fas fa-arrows-alt me-2"></i>
                        Move/Reschedule Booking
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="moveForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="move_room_id" class="form-label">Room</label>
                                <select class="form-select" id="move_room_id" name="room_id" required>
                                    @foreach ($rooms ?? [] as $room)
                                        <option value="{{ $room->id }}"
                                            {{ $room->id == $booking->room_id ? 'selected' : '' }}>
                                            {{ $room->name }} - {{ $room->location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="move_booking_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="move_booking_date" name="booking_date"
                                    value="{{ $booking->booking_date->format('Y-m-d') }}" required
                                    min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="move_start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="move_start_time" name="start_time"
                                    value="{{ $booking->start_time }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="move_end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="move_end_time" name="end_time"
                                    value="{{ $booking->end_time }}" required>
                            </div>
                        </div>
                        <div id="moveAvailabilityCheck"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" id="confirmMove">
                        <i class="fas fa-arrows-alt me-2"></i>
                        Move Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load room schedule for the booking date
            loadRoomSchedule();

            // Update booking status based on time
            updateBookingStatus();
        });

        function loadRoomSchedule() {
            const roomId = {{ $booking->room_id }};
            const date = '{{ $booking->booking_date->format('Y-m-d') }}';

            $.get('{{ route('room.availability') }}', {
                    date: date
                })
                .done(function(response) {
                    const roomData = response.find(r => r.room.id === roomId);

                    if (roomData && roomData.bookings.length > 0) {
                        let scheduleHtml = '';
                        roomData.bookings.forEach(booking => {
                            const isCurrentBooking = booking.id === {{ $booking->id }};
                            scheduleHtml += `
                    <div class="d-flex justify-content-between align-items-center py-2 ${isCurrentBooking ? 'bg-primary bg-opacity-10 rounded px-2' : ''}">
                        <div>
                            <div class="fw-bold small">${booking.title}</div>
                            <div class="small text-muted">${booking.user.name}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-primary">${booking.start_time} - ${booking.end_time}</div>
                            ${isCurrentBooking ? '<span class="badge bg-primary">Current</span>' : ''}
                        </div>
                    </div>
                    ${!isCurrentBooking ? '<hr class="my-1">' : ''}
                `;
                        });
                        $('#roomSchedule').html(scheduleHtml);
                    } else {
                        $('#roomSchedule').html(`
                <div class="text-center text-muted">
                    <i class="fas fa-calendar-check mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0">Only this booking for today</p>
                </div>
            `);
                    }
                })
                .fail(function() {
                    $('#roomSchedule').html(`
            <div class="text-center text-danger">
                <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                <p class="mb-0">Unable to load schedule</p>
            </div>
        `);
                });
        }

        function updateBookingStatus() {
            // Auto-refresh page if booking status might have changed
            const now = new Date();
            const bookingDate = '{{ $booking->booking_date->format('Y-m-d') }}';
            const startTime = '{{ $booking->start_time }}';
            const endTime = '{{ $booking->end_time }}';

            const startDateTime = new Date(bookingDate + ' ' + startTime);
            const endDateTime = new Date(bookingDate + ' ' + endTime);

            // Check if we need to refresh for status update
            if ('{{ $booking->status }}' === 'approved') {
                if (now >= startDateTime && now <= endDateTime) {
                    // Should be ongoing
                    setTimeout(() => location.reload(), 30000); // Refresh in 30 seconds
                } else if (now > endDateTime) {
                    // Should be completed
                    setTimeout(() => location.reload(), 5000); // Refresh in 5 seconds
                }
            }
        }

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
                            showToast('error', 'Failed to approve booking');
                        });
                }
            });
        }

        function rejectBooking(bookingId) {
            $('#rejection_reason').val('');
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
            rejectModal.show();

            $('#confirmReject').off('click').on('click', function() {
                const reason = $('#rejection_reason').val().trim();

                if (!reason) {
                    showToast('error', 'Please provide a reason for rejection');
                    return;
                }

                $.post(`/admin/bookings/${bookingId}/reject`, {
                        rejection_reason: reason,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(function(response) {
                        showToast('success', 'Booking rejected successfully');
                        $('#rejectModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    })
                    .fail(function(xhr) {
                        showToast('error', 'Failed to reject booking');
                    });
            });
        }

        function moveBooking(bookingId) {
            const moveModal = new bootstrap.Modal(document.getElementById('moveModal'));
            moveModal.show();

            // Check availability when inputs change
            $('#move_room_id, #move_booking_date, #move_start_time, #move_end_time').on('change', function() {
                checkMoveAvailability();
            });

            $('#confirmMove').off('click').on('click', function() {
                const formData = {
                    room_id: $('#move_room_id').val(),
                    booking_date: $('#move_booking_date').val(),
                    start_time: $('#move_start_time').val(),
                    end_time: $('#move_end_time').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.post(`/admin/bookings/${bookingId}/move`, formData)
                    .done(function(response) {
                        showToast('success', 'Booking moved successfully');
                        $('#moveModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    })
                    .fail(function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Failed to move booking';
                        showToast('error', errorMsg);
                    });
            });
        }

        function checkMoveAvailability() {
            const roomId = $('#move_room_id').val();
            const date = $('#move_booking_date').val();
            const startTime = $('#move_start_time').val();
            const endTime = $('#move_end_time').val();

            if (!roomId || !date || !startTime || !endTime) {
                $('#moveAvailabilityCheck').empty();
                return;
            }

            $('#moveAvailabilityCheck').html(`
        <div class="alert alert-info d-flex align-items-center">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Checking availability...
        </div>
    `);

            $.post('{{ route('bookings.check-availability') }}', {
                    room_id: roomId,
                    date: date,
                    start_time: startTime,
                    end_time: endTime,
                    exclude_booking_id: {{ $booking->id }},
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.available) {
                        $('#moveAvailabilityCheck').html(`
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    New time slot is available
                </div>
            `);
                        $('#confirmMove').prop('disabled', false);
                    } else {
                        $('#moveAvailabilityCheck').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>
                    Time slot is not available. Please choose a different time.
                </div>
            `);
                        $('#confirmMove').prop('disabled', true);
                    }
                });
        }

        function cancelBooking(bookingId) {
            confirmAction(
                'Cancel Booking?',
                'Are you sure you want to cancel this booking? This action cannot be undone.',
                'Yes, Cancel!'
            ).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/bookings/${bookingId}/cancel`, {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        })
                        .done(function(response) {
                            showToast('success', 'Booking cancelled successfully');
                            setTimeout(() => location.reload(), 1500);
                        })
                        .fail(function(xhr) {
                            const errorMsg = xhr.responseJSON?.message || 'Failed to cancel booking';
                            showToast('error', errorMsg);
                        });
                }
            });
        }

        function printBooking() {
            const printContent = `
       <div style="font-family: Arial, sans-serif; padding: 20px;">
           <h2 style="color: #dc2626; border-bottom: 2px solid #dc2626; padding-bottom: 10px;">
               Meeting Booking Details
           </h2>
           
           <div style="margin: 20px 0;">
               <h3>{{ $booking->title }}</h3>
               <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
               <p><strong>Status:</strong> {{ $booking->getStatusLabel() }}</p>
           </div>
           
           <div style="margin: 20px 0;">
               <h4>Meeting Information</h4>
               <table style="width: 100%; border-collapse: collapse;">
                   <tr>
                       <td style="padding: 8px; border: 1px solid #ddd;"><strong>Purpose:</strong></td>
                       <td style="padding: 8px; border: 1px solid #ddd;">{{ $booking->purpose }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 8px; border: 1px solid #ddd;"><strong>Date:</strong></td>
                       <td style="padding: 8px; border: 1px solid #ddd;">{{ $booking->booking_date->format('l, d F Y') }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 8px; border: 1px solid #ddd;"><strong>Time:</strong></td>
                       <td style="padding: 8px; border: 1px solid #ddd;">{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 8px; border: 1px solid #ddd;"><strong>Room:</strong></td>
                       <td style="padding: 8px; border: 1px solid #ddd;">{{ $booking->room->name }} - {{ $booking->room->location }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 8px; border: 1px solid #ddd;"><strong>Participants:</strong></td>
                       <td style="padding: 8px; border: 1px solid #ddd;">{{ $booking->participant_count }} people</td>
                   </tr>
                   <tr>
                       <td style="padding: 8px; border: 1px solid #ddd;"><strong>Organizer:</strong></td>
                       <td style="padding: 8px; border: 1px solid #ddd;">{{ $booking->user->name }} ({{ $booking->user->email }})</td>
                   </tr>
               </table>
           </div>
           
           @if ($booking->participants->count() > 0)
           <div style="margin: 20px 0;">
               <h4>Participants</h4>
               <ul>
                   @foreach ($booking->participants as $participant)
                   <li>{{ $participant->name }}{{ $participant->email ? ' (' . $participant->email . ')' : '' }}</li>
                   @endforeach
               </ul>
           </div>
           @endif
           
           @if ($booking->description)
           <div style="margin: 20px 0;">
               <h4>Additional Notes</h4>
               <p>{{ $booking->description }}</p>
           </div>
           @endif
           
           <div style="margin-top: 30px; text-align: center; color: #666;">
               <p>Printed on {{ now()->format('d/m/Y H:i') }}</p>
           </div>
       </div>
   `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        function exportToPDF() {
            // This would typically use a library like jsPDF or call a backend endpoint
            showToast('info', 'PDF export feature coming soon');
        }

        function addToCalendar() {
            const startDate = '{{ $booking->booking_date->format('Y-m-d') }}';
            const startTime = '{{ $booking->start_time }}';
            const endTime = '{{ $booking->end_time }}';

            const startDateTime = new Date(startDate + 'T' + startTime);
            const endDateTime = new Date(startDate + 'T' + endTime);

            // Format for Google Calendar
            const formatDate = (date) => {
                return date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
            };

            const calendarUrl =
                `https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($booking->title) }}&dates=${formatDate(startDateTime)}/${formatDate(endDateTime)}&details={{ urlencode($booking->description ?: $booking->purpose) }}&location={{ urlencode($booking->room->name . ' - ' . $booking->room->location) }}`;

            window.open(calendarUrl, '_blank');
        }
    </script>
@endpush
