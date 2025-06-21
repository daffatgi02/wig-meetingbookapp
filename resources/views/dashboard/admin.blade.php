{{-- resources/views/dashboard/admin.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Page Header -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="text-dark mb-1">Admin Dashboard</h3>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar-day me-1"></i>
                    {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Create Booking
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <!-- Pending Approvals -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-clock text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $adminStats['pendingApprovals'] }}</h4>
                                <p class="text-muted mb-0 small">Pending Approvals</p>
                                @if($adminStats['pendingApprovals'] > 0)
                                    <a href="{{ route('admin.bookings.pending') }}" class="text-warning small">
                                        <i class="fas fa-arrow-right me-1"></i>Review Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Bookings -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-calendar-day text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $adminStats['todayBookingsCount'] }}</h4>
                                <p class="text-muted mb-0 small">Today's Bookings</p>
                                <span class="text-primary small">
                                    <i class="fas fa-chart-line me-1"></i>Active Sessions
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Rooms -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-door-open text-success" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $adminStats['activeRooms'] }}/{{ $adminStats['totalRooms'] }}</h4>
                                <p class="text-muted mb-0 small">Active Rooms</p>
                                <a href="{{ route('admin.rooms.index') }}" class="text-success small">
                                    <i class="fas fa-cog me-1"></i>Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-users text-info" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $adminStats['activeUsers'] }}/{{ $adminStats['totalUsers'] }}</h4>
                                <p class="text-muted mb-0 small">Active Users</p>
                                <a href="{{ route('admin.users.index') }}" class="text-info small">
                                    <i class="fas fa-user-cog me-1"></i>Manage
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="col-lg-8 mb-4">
        <!-- Pending Approvals Section -->
        @if($adminStats['pendingApprovals'] > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Pending Approvals ({{ $adminStats['pendingApprovals'] }})
                    </h5>
                    <a href="{{ route('admin.bookings.pending') }}" class="btn btn-warning btn-sm">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @php
                        $pendingBookings = \App\Models\Booking::with(['user', 'room'])
                            ->where('status', 'pending')
                            ->orderBy('created_at', 'asc')
                            ->limit(5)
                            ->get();
                    @endphp

                    <div class="list-group list-group-flush">
                        @foreach($pendingBookings as $booking)
                            <div class="list-group-item border-0 px-4 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $booking->title }}</h6>
                                        <div class="row text-muted small">
                                            <div class="col-md-6">
                                                <i class="fas fa-user me-1"></i>{{ $booking->user->name }}
                                            </div>
                                            <div class="col-md-6">
                                                <i class="fas fa-door-open me-1"></i>{{ $booking->room->name }}
                                            </div>
                                        </div>
                                        <div class="row text-muted small mt-1">
                                            <div class="col-md-6">
                                                <i class="fas fa-calendar me-1"></i>{{ $booking->booking_date->format('d/m/Y') }}
                                            </div>
                                            <div class="col-md-6">
                                                <i class="fas fa-clock me-1"></i>{{ $booking->start_time }} - {{ $booking->end_time }}
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Requested {{ $booking->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.bookings.show', $booking) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-success" 
                                                onclick="approveBooking({{ $booking->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger" 
                                                onclick="rejectBooking({{ $booking->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Bookings -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>
                    Recent Bookings
                </h5>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>User</th>
                                <th>Room</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adminStats['recentBookings'] as $booking)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $booking->title }}</div>
                                        <small class="text-muted">{{ Str::limit($booking->purpose, 30) }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold small">{{ $booking->user->name }}</div>
                                                <small class="text-muted">{{ $booking->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-door-open me-1 text-primary"></i>
                                        {{ $booking->room->name }}
                                        <br>
                                        <small class="text-muted">{{ $booking->room->location }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $booking->booking_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $booking->start_time }} - {{ $booking->end_time }}</small>
                                    </td>
                                    <td>
                                        @include('components.status-badge', ['status' => $booking->status])
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($booking->status === 'pending')
                                                <button class="btn btn-outline-success" 
                                                        onclick="approveBooking({{ $booking->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Today's Schedule -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                    Today's Schedule
                </h6>
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                @if($todayBookings->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todayBookings as $booking)
                            <div class="list-group-item border-0 px-3 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <small class="text-primary fw-bold me-2">
                                                {{ $booking->start_time }}
                                            </small>
                                            @include('components.status-badge', ['status' => $booking->status])
                                        </div>
                                        
                                        <h6 class="mb-1 small">{{ $booking->title }}</h6>
                                        
                                        <div class="small text-muted">
                                            <i class="fas fa-door-open me-1"></i>
                                            {{ $booking->room->name }}
                                        </div>
                                        
                                        <div class="small text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $booking->user->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-day text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 small">No bookings today</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick System Status -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-server me-2 text-primary"></i>
                    System Status
                </h6>
            </div>
            <div class="card-body">
                @php
                    $systemStats = [
                        'database' => true,
                        'notifications' => \App\Models\Setting::get('notification_website_enabled', true),
                        'rooms_active' => $adminStats['activeRooms'] > 0,
                        'users_active' => $adminStats['activeUsers'] > 0,
                    ];
                @endphp

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <small>Database Connection</small>
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>Active
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <small>Website Notifications</small>
                        <span class="badge {{ $systemStats['notifications'] ? 'bg-success' : 'bg-warning' }}">
                            <i class="fas {{ $systemStats['notifications'] ? 'fa-check' : 'fa-exclamation' }} me-1"></i>
                            {{ $systemStats['notifications'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <small>Active Rooms</small>
                        <span class="badge {{ $systemStats['rooms_active'] ? 'bg-success' : 'bg-danger' }}">
                            {{ $adminStats['activeRooms'] }} Available
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                        <small>Active Users</small>
                        <span class="badge {{ $systemStats['users_active'] ? 'bg-success' : 'bg-warning' }}">
                            {{ $adminStats['activeUsers'] }} Online
                        </span>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-cogs me-2"></i>
                        System Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Create Booking
                    </a>
                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-door-open me-2"></i>
                        Add New Room
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user-plus me-2"></i>
                        Add New User
                    </a>
                    <a href="{{ route('calendar.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-calendar me-2"></i>
                        Calendar View
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
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
                <form id="rejectForm">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            Reason for Rejection <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="3" required 
                                  placeholder="Explain why this booking is being rejected..."></textarea>
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
@endsection

@push('scripts')
<script>
let currentBookingId = null;

$(document).ready(function() {
    // Auto-refresh dashboard every 2 minutes
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 120000);
});

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
    currentBookingId = bookingId;
    $('#rejection_reason').val('');
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    rejectModal.show();
}

$('#confirmReject').click(function() {
    const reason = $('#rejection_reason').val().trim();
    
    if (!reason) {
        showToast('error', 'Please provide a reason for rejection');
        return;
    }

    $.post(`/admin/bookings/${currentBookingId}/reject`, {
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
</script>
@endpush