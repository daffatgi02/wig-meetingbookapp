{{-- resources/views/admin/bookings/pending.blade.php --}}
@extends('layouts.admin')

@section('title', 'Pending Approvals')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-dark mb-1">
                    <i class="fas fa-clock text-warning me-2"></i>
                    Pending Approvals
                </h3>
                <p class="text-muted mb-0">Review and approve booking requests waiting for your decision</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success" onclick="approveAllPending()" 
                        {{ $bookings->count() === 0 ? 'disabled' : '' }}>
                    <i class="fas fa-check-double me-2"></i>
                    Approve All ({{ $bookings->count() }})
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-2"></i>
                    All Bookings
                </a>
            </div>
        </div>

        @if($bookings->count() > 0)
            <!-- Bulk Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-bold" for="selectAll">
                                    Select All
                                </label>
                            </div>
                            <span class="text-muted" id="selectedCount">0 selected</span>
                        </div>
                        <div class="d-flex gap-2" id="bulkActions" style="display: none;">
                            <button class="btn btn-success btn-sm" onclick="bulkApprove()">
                                <i class="fas fa-check me-1"></i>
                                Approve Selected
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="bulkReject()">
                                <i class="fas fa-times me-1"></i>
                                Reject Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Bookings Cards -->
            <div class="row g-4">
                @foreach($bookings as $booking)
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 booking-card" data-booking-id="{{ $booking->id }}">
                            <div class="card-header bg-warning bg-opacity-10 border-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input booking-checkbox" type="checkbox" 
                                                   value="{{ $booking->id }}" id="booking_{{ $booking->id }}">
                                            <label class="form-check-label" for="booking_{{ $booking->id }}"></label>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-primary">{{ $booking->title }}</h6>
                                            <div class="small text-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                Pending • {{ $booking->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.bookings.show', $booking) }}">
                                                    <i class="fas fa-eye me-2"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.bookings.edit', $booking) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit Before Approve
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <!-- Meeting Info -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center text-muted small mb-2">
                                            <i class="fas fa-door-open me-2 text-primary"></i>
                                            <strong>Room:</strong>
                                        </div>
                                        <div class="fw-bold">{{ $booking->room->name }}</div>
                                        <div class="small text-muted">{{ $booking->room->location }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center text-muted small mb-2">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            <strong>Organizer:</strong>
                                        </div>
                                        <div class="fw-bold">{{ $booking->user->name }}</div>
                                        <div class="small text-muted">{{ $booking->user->email }}</div>
                                    </div>
                                </div>

                                <!-- Schedule Info -->
                                <div class="bg-light p-3 rounded mb-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="small text-muted mb-1">
                                                <i class="fas fa-calendar me-1"></i>Date
                                            </div>
                                            <div class="fw-bold text-primary">
                                                {{ $booking->booking_date->format('D, d M Y') }}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted mb-1">
                                                <i class="fas fa-clock me-1"></i>Time
                                            </div>
                                            <div class="fw-bold text-primary">
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purpose & Details -->
                                <div class="mb-3">
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-bullseye me-1"></i>Purpose
                                    </div>
                                    <div class="fw-bold">{{ $booking->purpose }}</div>
                                </div>

                                @if($booking->description)
                                    <div class="mb-3">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-sticky-note me-1"></i>Description
                                        </div>
                                        <div class="small text-dark">{{ $booking->description }}</div>
                                    </div>
                                @endif

                                <!-- Participants -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="small text-muted">
                                            <i class="fas fa-users me-1"></i>Participants
                                        </div>
                                        <span class="badge bg-info">{{ $booking->participant_count }} people</span>
                                    </div>
                                    
                                    @if($booking->participants->count() > 0)
                                        <div class="mt-2">
                                            @foreach($booking->participants->take(3) as $participant)
                                                <span class="badge bg-light text-dark me-1">{{ $participant->name }}</span>
                                            @endforeach
                                            @if($booking->participants->count() > 3)
                                                <span class="badge bg-secondary">+{{ $booking->participants->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Room Capacity Check -->
                                @if($booking->participant_count > $booking->room->capacity)
                                    <div class="alert alert-warning border-0 py-2 mb-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <small>
                                            <strong>Warning:</strong> Participants ({{ $booking->participant_count }}) 
                                            exceed room capacity ({{ $booking->room->capacity }})
                                        </small>
                                    </div>
                                @endif

                                <!-- Conflict Check -->
                                @php
                                    $conflicts = $booking->room->getConflictingBookings(
                                        $booking->booking_date->format('Y-m-d'), 
                                        $booking->start_time, 
                                        $booking->end_time, 
                                        $booking->id
                                    );
                                @endphp
                                
                                @if($conflicts->count() > 0)
                                    <div class="alert alert-danger border-0 py-2 mb-3">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <small>
                                            <strong>Conflict:</strong> Room is already booked during this time
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-white border-0 d-flex gap-2">
                                <button class="btn btn-success flex-fill" 
                                        onclick="approveBooking({{ $booking->id }})"
                                        {{ $conflicts->count() > 0 ? 'disabled title="Cannot approve due to conflict"' : '' }}>
                                    <i class="fas fa-check me-2"></i>
                                    Approve
                                </button>
                                <button class="btn btn-danger flex-fill" 
                                        onclick="rejectBooking({{ $booking->id }})">
                                    <i class="fas fa-times me-2"></i>
                                    Reject
                                </button>
                                <a href="{{ route('admin.bookings.show', $booking) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $bookings->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                        <h4 class="text-dark">All Caught Up!</h4>
                        <p class="text-muted mb-4">
                            There are no pending bookings waiting for approval at the moment.
                        </p>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>
                            View All Bookings
                        </a>
                        <a href="{{ route('admin.bookings.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>
                            Create New Booking
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Include Modals -->
@include('admin.bookings.partials.approval-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all functionality
    $('#selectAll').change(function() {
        $('.booking-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkActions();
    });

    // Individual checkbox change
    $('.booking-checkbox').change(function() {
        updateBulkActions();
        
        // Update select all checkbox
        const totalCheckboxes = $('.booking-checkbox').length;
        const checkedCheckboxes = $('.booking-checkbox:checked').length;
        
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    function updateBulkActions() {
        const selectedCount = $('.booking-checkbox:checked').length;
        $('#selectedCount').text(selectedCount + ' selected');
        
        if (selectedCount > 0) {
            $('#bulkActions').show();
        } else {
            $('#bulkActions').hide();
        }
    }

    // Auto-refresh every 30 seconds
    setInterval(function() {
        if (document.visibilityState === 'visible' && !$('.modal.show').length) {
            location.reload();
        }
    }, 30000);
});

function bulkApprove() {
    const selectedIds = $('.booking-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('warning', 'Please select bookings to approve');
        return;
    }

    confirmAction(
        'Approve Selected Bookings?',
        `Are you sure you want to approve ${selectedIds.length} selected booking(s)?`,
        'Yes, Approve All!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.post('/admin/bookings/bulk-approve', {
                booking_ids: selectedIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                showToast('success', `${selectedIds.length} booking(s) approved successfully`);
                setTimeout(() => location.reload(), 1500);
            })
            .fail(function(xhr) {
                showToast('error', 'Failed to approve selected bookings');
            });
        }
    });
}

function bulkReject() {
    const selectedIds = $('.booking-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (selectedIds.length === 0) {
        showToast('warning', 'Please select bookings to reject');
        return;
    }

    // For bulk reject, we need a common reason
    Swal.fire({
        title: 'Reject Selected Bookings',
        html: `
            <p>You are about to reject ${selectedIds.length} booking(s).</p>
            <textarea id="bulkRejectReason" class="form-control" rows="3" 
                     placeholder="Enter reason for rejection..." required></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Reject All',
        confirmButtonColor: '#dc3545',
        preConfirm: () => {
            const reason = document.getElementById('bulkRejectReason').value.trim();
            if (!reason) {
                Swal.showValidationMessage('Please provide a reason');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('/admin/bookings/bulk-reject', {
                booking_ids: selectedIds,
                rejection_reason: result.value,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                showToast('success', `${selectedIds.length} booking(s) rejected successfully`);
                setTimeout(() => location.reload(), 1500);
            })
            .fail(function(xhr) {
                showToast('error', 'Failed to reject selected bookings');
            });
        }
    });
}

function approveAllPending() {
    const totalPending = {{ $bookings->count() }};
    
    if (totalPending === 0) return;

    confirmAction(
        'Approve All Pending Bookings?',
        `Are you sure you want to approve all ${totalPending} pending booking(s)?`,
        'Yes, Approve All!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.post('/admin/bookings/approve-all-pending', {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                showToast('success', `All ${totalPending} pending booking(s) approved successfully`);
                setTimeout(() => location.reload(), 2000);
            })
            .fail(function(xhr) {
                showToast('error', 'Failed to approve all pending bookings');
            });
        }
    });
}

// Sound notification for new pending bookings (optional)
function playNotificationSound() {
    // Create audio context for notification sound
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.start();
    oscillator.stop(audioContext.currentTime + 0.5);
}
</script>
@endpush