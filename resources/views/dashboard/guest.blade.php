{{-- resources/views/dashboard/guest.blade.php --}}
@extends('layouts.guest')

@section('title', 'Public Dashboard')

@section('content')
    <div class="row">
        <!-- Today's Bookings -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2 text-primary"></i>
                        Today's Meeting Schedule
                    </h5>
                    <span class="badge bg-primary">{{ $todayBookings->count() }} Sessions</span>
                </div>
                <div class="card-body p-0">
                    @if ($todayBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Meeting</th>
                                        <th>Room</th>
                                        <th>Organizer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($todayBookings as $booking)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-primary">{{ $booking->start_time }}</div>
                                                <small class="text-muted">{{ $booking->end_time }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $booking->title }}</div>
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $booking->participant_count }} participants
                                                </small>
                                            </td>
                                            <td>
                                                <div>{{ $booking->room->name }}</div>
                                                <small class="text-muted">{{ $booking->room->location }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 30px; height: 30px; font-size: 12px;">
                                                        {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small">{{ $booking->user->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @include('components.status-badge', [
                                                    'status' => $booking->status,
                                                ])
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted">No meetings scheduled today</h6>
                            <p class="text-muted">Check back tomorrow for upcoming sessions</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Room Status & Quick Info -->
        <div class="col-lg-4 mb-4">
            <!-- Room Availability -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-door-open me-2 text-primary"></i>
                        Room Availability
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach ($rooms->where('is_active', true) as $room)
                            @php
                                $roomBookingsToday = $todayBookings->where('room_id', $room->id);
                                $isAvailableNow = true;
                                $nextBooking = null;

                                foreach ($roomBookingsToday as $booking) {
                                    if ($booking->status === 'ongoing') {
                                        $isAvailableNow = false;
                                        break;
                                    } elseif (
                                        $booking->status === 'approved' &&
                                        $booking->start_time > now()->format('H:i')
                                    ) {
                                        if (!$nextBooking || $booking->start_time < $nextBooking->start_time) {
                                            $nextBooking = $booking;
                                        }
                                    }
                                }
                            @endphp

                            <div class="list-group-item border-0 px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold small">{{ $room->name }}</div>
                                        <small class="text-muted">{{ $room->location }}</small>
                                    </div>
                                    <div class="text-end">
                                        @if ($isAvailableNow)
                                            <span class="badge bg-success small">Available</span>
                                            @if ($nextBooking)
                                                <div class="small text-muted">
                                                    Next: {{ $nextBooking->start_time }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-danger small">Occupied</span>
                                            <div class="small text-muted">In use</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ $rooms->where('is_active', true)->count() }}</h4>
                                <small class="text-muted">Available Rooms</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">{{ $upcomingBookings->count() }}</h4>
                            <small class="text-muted">Upcoming Sessions</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-plus text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h6 class="mb-2">Want to book a room?</h6>
                    <p class="text-muted small mb-3">
                        Login to your account to start booking meeting rooms
                    </p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login to Book
                    </button>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings (Next 7 Days) -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week me-2 text-primary"></i>
                        Upcoming Meetings (Next 7 Days)
                    </h5>
                    <a href="{{ route('calendar.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar me-1"></i>
                        Calendar View
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($upcomingBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Meeting</th>
                                        <th>Room</th>
                                        <th>Organizer</th>
                                        <th>Participants</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingBookings as $booking)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $booking->booking_date->format('D, M j') }}</div>
                                                <small class="text-muted">{{ $booking->booking_date->format('Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="text-primary fw-bold">{{ $booking->start_time }}</div>
                                                <small class="text-muted">{{ $booking->end_time }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $booking->title }}</div>
                                                <small class="text-muted">{{ Str::limit($booking->purpose, 40) }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $booking->room->name }}</div>
                                                <small class="text-muted">{{ $booking->room->location }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 30px; height: 30px; font-size: 12px;">
                                                        {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small">{{ $booking->user->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $booking->participant_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-plus text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted">No upcoming meetings</h6>
                            <p class="text-muted mb-3">The schedule is clear for the next 7 days</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Update time every minute
                function updateTime() {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    $('.current-time').text(timeString);
                }

                setInterval(updateTime, 60000);

                // Auto-refresh every 3 minutes
                setInterval(function() {
                    if (document.visibilityState === 'visible') {
                        location.reload();
                    }
                }, 180000);
            });
        </script>
    @endpush
@endsection