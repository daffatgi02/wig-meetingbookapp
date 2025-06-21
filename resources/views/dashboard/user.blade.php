{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Section -->
    <div class="col-12 mb-4">
        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));">
            <div class="card-body text-white p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">
                            <i class="fas fa-sun me-2"></i>
                            Selamat datang, {{ auth()->user()->name }}!
                        </h4>
                        <p class="mb-0 opacity-75">
                            {{ now()->format('l, d F Y') }} • 
                            <span id="currentTime">{{ now()->format('H:i') }}</span>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('bookings.create') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            Book Room
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-12 mb-4">
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <div class="card text-center border-0 h-100">
                    <div class="card-body">
                        <div class="text-primary mb-2">
                            <i class="fas fa-calendar-check" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-primary mb-1">{{ $totalBookings }}</h4>
                        <small class="text-muted">Total Bookings</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card text-center border-0 h-100">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <i class="fas fa-clock" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-warning mb-1">{{ $pendingBookings }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card text-center border-0 h-100">
                    <div class="card-body">
                        <div class="text-success mb-2">
                            <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-success mb-1">{{ $approvedBookings }}</h4>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card text-center border-0 h-100">
                    <div class="card-body">
                        <div class="text-info mb-2">
                            <i class="fas fa-door-open" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="text-info mb-1">{{ $rooms->where('is_active', true)->count() }}</h4>
                        <small class="text-muted">Available Rooms</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Upcoming Bookings -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>
                    My Upcoming Bookings
                </h5>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                @if($myBookings->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($myBookings as $booking)
                            <div class="list-group-item border-0 px-4 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 me-3">{{ $booking->title }}</h6>
                                            @include('components.status-badge', ['status' => $booking->status])
                                        </div>
                                        
                                        <div class="row text-muted small">
                                            <div class="col-md-6">
                                                <i class="fas fa-door-open me-1"></i>
                                                {{ $booking->room->name }}
                                            </div>
                                            <div class="col-md-6">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $booking->booking_date->format('d/m/Y') }}
                                            </div>
                                        </div>
                                        
                                        <div class="row text-muted small mt-1">
                                            <div class="col-md-6">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </div>
                                            <div class="col-md-6">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $booking->participant_count }} peserta
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('bookings.show', $booking) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($booking->isEditable())
                                            <a href="{{ route('bookings.edit', $booking) }}" 
                                               class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">Belum ada booking upcoming</h6>
                        <p class="text-muted mb-3">Mulai booking ruangan untuk meeting Anda</p>
                        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Book Room Now
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Today's Schedule -->
    <div class="col-lg-4 mb-4">
        <!-- Quick Actions -->
        <div class="card border-0 mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Book New Room
                    </a>
                    <a href="{{ route('calendar.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-2"></i>
                        View Calendar
                    </a>
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-history me-2"></i>
                        My Booking History
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's All Bookings -->
        <div class="card border-0">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                    Today's Schedule
                </h6>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                @if($todayBookings->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todayBookings as $booking)
                            <div class="list-group-item border-0 px-3 py-2 {{ $booking->user_id === auth()->id() ? 'bg-light' : '' }}">
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
                                            @if($booking->user_id === auth()->id())
                                                <span class="badge bg-primary ms-1">You</span>
                                            @endif
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
    </div>

    <!-- Available Rooms -->
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-door-open me-2 text-primary"></i>
                    Available Rooms
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" id="roomViewGrid">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="btn btn-outline-secondary active" id="roomViewList">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Grid View -->
                <div id="roomsGrid" class="row g-3" style="display: none;">
                    @foreach($rooms->where('is_active', true) as $room)
                        <div class="col-lg-4 col-md-6">
                            @include('components.room-card', ['room' => $room, 'todayBookings' => $todayBookings])
                        </div>
                    @endforeach
                </div>

                <!-- List View -->
                <div id="roomsList">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Room</th>
                                    <th>Location</th>
                                    <th>Capacity</th>
                                    <th>Status Today</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rooms->where('is_active', true) as $room)
                                    @php
                                        $roomBookingsToday = $todayBookings->where('room_id', $room->id);
                                        $isAvailableNow = true;
                                        $currentBooking = null;
                                        
                                        foreach($roomBookingsToday as $booking) {
                                            if($booking->status === 'ongoing') {
                                                $isAvailableNow = false;
                                                $currentBooking = $booking;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-door-open text-primary me-2"></i>
                                                <div>
                                                    <div class="fw-bold">{{ $room->name }}</div>
                                                    @if($room->facilities && count($room->facilities) > 0)
                                                        <div class="small text-muted">
                                                            {{ implode(', ', array_slice($room->facilities, 0, 3)) }}
                                                            @if(count($room->facilities) > 3)
                                                                <span class="text-primary">+{{ count($room->facilities) - 3 }} more</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            {{ $room->location }}
                                        </td>
                                        <td>
                                            <i class="fas fa-users text-muted me-1"></i>
                                            {{ $room->capacity }} orang
                                        </td>
                                        <td>
                                            @if($isAvailableNow)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Available
                                                </span>
                                                @if($roomBookingsToday->count() > 0)
                                                    <div class="small text-muted mt-1">
                                                        {{ $roomBookingsToday->count() }} booking(s) today
                                                    </div>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Occupied
                                                </span>
                                                @if($currentBooking)
                                                    <div class="small text-muted mt-1">
                                                        Until {{ $currentBooking->end_time }}
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-calendar-plus me-1"></i>
                                                Book
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        $('#currentTime').text(timeString);
    }
    
    // Update time every minute
    setInterval(updateTime, 60000);

    // Room view toggle
    $('#roomViewGrid').click(function() {
        $(this).addClass('active');
        $('#roomViewList').removeClass('active');
        $('#roomsList').hide();
        $('#roomsGrid').show();
    });

    $('#roomViewList').click(function() {
        $(this).addClass('active');
        $('#roomViewGrid').removeClass('active');
        $('#roomsGrid').hide();
        $('#roomsList').show();
    });

    // Auto-refresh data every 5 minutes
    setInterval(function() {
        // Refresh only if user is still on the page
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 300000); // 5 minutes

    // Load booking stats
    loadBookingStats();
});

function loadBookingStats() {
    $.get('{{ route("booking.stats") }}')
        .done(function(data) {
            // Update stats if needed
            console.log('Stats updated:', data);
        })
        .fail(function() {
            console.log('Failed to load stats');
        });
}
</script>
@endpush