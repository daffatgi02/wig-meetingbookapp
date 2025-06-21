{{-- resources/views/admin/rooms/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Room Details')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-dark mb-1">
                        <i class="fas fa-door-open text-primary me-2"></i>
                        {{ $room->name }}
                    </h3>
                    <p class="text-muted mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $room->location }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        Edit Room
                    </a>
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Rooms
                    </a>
                </div>
            </div>

            <!-- Room Status & Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Room Information
                        </h6>
                        <div>
                            @if ($room->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Active
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-pause me-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                        <div class="h3 text-primary mb-1">{{ $room->capacity }}</div>
                                        <small class="text-muted">Max Capacity</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                        <div class="h3 text-info mb-1">
                                            {{ $room->bookings()->where('booking_date', '>=', today())->count() }}
                                        </div>
                                        <small class="text-muted">Upcoming Bookings</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            @if ($room->description)
                                <div class="mb-3">
                                    <h6 class="text-dark mb-2">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        Description
                                    </h6>
                                    <p class="text-muted mb-0">{{ $room->description }}</p>
                                </div>
                            @endif

                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Last updated {{ $room->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Facilities -->
            @if ($room->facilities && count($room->facilities) > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2 text-primary"></i>
                            Available Facilities
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-2">
                            @foreach ($room->facilities as $facility)
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-check me-2 text-success"></i>
                                        {{ $facility }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Today's Schedule -->
            @php
                $todayBookings = $room
                    ->bookings()
                    ->with(['user'])
                    ->where('booking_date', today())
                    ->whereIn('status', ['approved', 'ongoing', 'completed'])
                    ->orderBy('start_time')
                    ->get();
            @endphp

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-day me-2 text-primary"></i>
                        Today's Schedule ({{ today()->format('D, d M Y') }})
                    </h6>
                    <span class="badge bg-info">{{ $todayBookings->count() }} bookings</span>
                </div>
                <div class="card-body p-0">
                    @if ($todayBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Meeting</th>
                                        <th>Organizer</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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
                                                <small class="text-muted">{{ $booking->purpose }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $booking->user->name }}</div>
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $booking->participant_count }} people
                                                </small>
                                            </td>
                                            <td>
                                                @include('components.status-badge', [
                                                    'status' => $booking->status,
                                                ])
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check text-success mb-2" style="font-size: 2rem;"></i>
                            <h6 class="text-success">Room Available All Day</h6>
                            <p class="text-muted mb-3">No bookings scheduled for today</p>
                            <a href="{{ route('admin.bookings.create', ['room_id' => $room->id]) }}"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>
                                Create Booking
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Bookings -->
            @php
                $upcomingBookings = $room
                    ->bookings()
                    ->with(['user'])
                    ->where('booking_date', '>', today())
                    ->whereIn('status', ['pending', 'approved'])
                    ->orderBy('booking_date')
                    ->orderBy('start_time')
                    ->limit(10)
                    ->get();
            @endphp

            @if ($upcomingBookings->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Upcoming Bookings
                        </h6>
                        <a href="{{ route('admin.bookings.index', ['room_id' => $room->id]) }}"
                            class="btn btn-outline-primary btn-sm">
                            View All
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($upcomingBookings as $booking)
                                <div class="list-group-item border-0 px-4 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-3">{{ $booking->title }}</h6>
                                                @include('components.status-badge', [
                                                    'status' => $booking->status,
                                                ])
                                            </div>

                                            <div class="row text-muted small">
                                                <div class="col-md-6">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $booking->booking_date->format('d/m/Y') }}
                                                </div>
                                                <div class="col-md-6">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $booking->start_time }} - {{ $booking->end_time }}
                                                </div>
                                            </div>

                                            <div class="row text-muted small mt-1">
                                                <div class="col-md-6">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $booking->user->name }}
                                                </div>
                                                <div class="col-md-6">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $booking->participant_count }} people
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.bookings.show', $booking) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($booking->status === 'pending')
                                                <button class="btn btn-outline-success btn-sm"
                                                    onclick="approveBooking({{ $booking->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2 text-primary"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.bookings.create', ['room_id' => $room->id]) }}" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Create Booking
                        </a>

                        <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Edit Room
                        </a>

                        <button class="btn {{ $room->is_active ? 'btn-outline-secondary' : 'btn-success' }}"
                            onclick="toggleRoom({{ $room->id }}, {{ $room->is_active ? 'false' : 'true' }})">
                            <i class="fas fa-{{ $room->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $room->is_active ? 'Deactivate' : 'Activate' }} Room
                        </button>

                        <a href="{{ route('calendar.index', ['room_id' => $room->id]) }}" class="btn btn-outline-info">
                            <i class="fas fa-calendar me-2"></i>
                            View in Calendar
                        </a>

                        <button class="btn btn-outline-secondary" onclick="exportRoomData()">
                            <i class="fas fa-download me-2"></i>
                            Export Room Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Room Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Statistics
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $stats = [
                            'total' => $room->bookings()->count(),
                            'pending' => $room->bookings()->where('status', 'pending')->count(),
                            'approved' => $room->bookings()->where('status', 'approved')->count(),
                            'completed' => $room->bookings()->where('status', 'completed')->count(),
                            'cancelled' => $room->bookings()->where('status', 'cancelled')->count(),
                            'this_month' => $room->bookings()->whereMonth('booking_date', now()->month)->count(),
                            'next_month' => $room
                                ->bookings()
                                ->whereMonth('booking_date', now()->addMonth()->month)
                                ->count(),
                        ];
                    @endphp

                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-primary bg-opacity-10 rounded">
                                <div class="h5 text-primary mb-0">{{ $stats['total'] }}</div>
                                <small class="text-muted">Total Bookings</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                <div class="h5 text-success mb-0">{{ $stats['completed'] }}</div>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-warning bg-opacity-10 rounded">
                                <div class="h5 text-warning mb-0">{{ $stats['pending'] }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-info bg-opacity-10 rounded">
                                <div class="h5 text-info mb-0">{{ $stats['this_month'] }}</div>
                                <small class="text-muted">This Month</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Usage Trend -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Monthly Usage</small>
                            <small class="text-muted">{{ $stats['this_month'] }}/{{ $stats['next_month'] }}</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            @php
                                $maxBookings = max($stats['this_month'], $stats['next_month'], 1);
                                $thisMonthPercent = ($stats['this_month'] / $maxBookings) * 100;
                            @endphp
                            <div class="progress-bar bg-primary" style="width: {{ $thisMonthPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Average Rating (if you have reviews) -->
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-chart-line me-1"></i>
                            Room utilization rate:
                            {{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}%
                        </small>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            @php
                $recentActivity = $room
                    ->bookings()
                    ->with(['user'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @if ($recentActivity->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2 text-primary"></i>
                            Recent Activity
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($recentActivity as $booking)
                                <div class="list-group-item border-0 px-3 py-2">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-2">
                                            @include('components.status-badge', [
                                                'status' => $booking->status,
                                            ])
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small">{{ $booking->title }}</div>
                                            <div class="text-muted small">
                                                by {{ $booking->user->name }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $booking->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleRoom(roomId, activate) {
            const action = activate === 'true' ? 'activate' : 'deactivate';
            const actionText = activate === 'true' ? 'Activate' : 'Deactivate';

            let warningText = `Are you sure you want to ${action} this room?`;

            @if ($room->bookings()->where('booking_date', '>=', today())->where('status', 'approved')->exists())
                if (activate === 'false') {
                    warningText +=
                        '\n\nNote: This room has upcoming approved bookings. Deactivating will not cancel existing bookings, but will prevent new bookings.';
                }
            @endif

            confirmAction(
                `${actionText} Room?`,
                warningText,
                `Yes, ${actionText}!`
            ).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/admin/rooms/${roomId}/toggle`, {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        })
                        .done(function(response) {
                            showToast('success', `Room ${action}d successfully`);
                            setTimeout(() => location.reload(), 1500);
                        })
                        .fail(function(xhr) {
                            let message = `Failed to ${action} room`;
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showToast('error', message);
                        });
                }
            });
        }

        function exportRoomData() {
            const params = new URLSearchParams({
                room_id: {{ $room->id }},
                export: 'excel'
            });

            window.location.href = `/admin/rooms/{{ $room->id }}/export?${params.toString()}`;
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
                            let message = 'Failed to approve booking';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showToast('error', message);
                        });
                }
            });
        }

        // Auto-refresh for real-time updates
        $(document).ready(function() {
            // Refresh every 60 seconds
            setInterval(function() {
                if (document.visibilityState === 'visible' && !$('.modal.show').length) {
                    // Only refresh if we're on the same room page
                    if (window.location.pathname.includes('/admin/rooms/{{ $room->id }}')) {
                        location.reload();
                    }
                }
            }, 60000);

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Print room details
        function printRoomDetails() {
            const printWindow = window.open('', '_blank');
            const roomData = {
                name: @json($room->name),
                location: @json($room->location),
                capacity: {{ $room->capacity }},
                description: @json($room->description),
                facilities: @json($room->facilities ?? []),
                is_active: {{ $room->is_active ? 'true' : 'false' }}
            };

            const facilitiesHTML = roomData.facilities.length > 0 ?
                roomData.facilities.map(f => `<li>${f}</li>`).join('') :
                '<li>No facilities specified</li>';

            printWindow.document.write(`
       <!DOCTYPE html>
       <html>
       <head>
           <title>Room Details - ${roomData.name}</title>
           <style>
               body { font-family: Arial, sans-serif; margin: 20px; }
               .header { border-bottom: 2px solid #dc2626; padding-bottom: 10px; margin-bottom: 20px; }
               .status { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
               .status.active { background-color: #d1fae5; color: #065f46; }
               .status.inactive { background-color: #f3f4f6; color: #374151; }
               .info-row { margin: 10px 0; }
               .label { font-weight: bold; }
               ul { margin: 5px 0; padding-left: 20px; }
           </style>
       </head>
       <body>
           <div class="header">
               <h1>${roomData.name}</h1>
               <p>${roomData.location}</p>
               <span class="status ${roomData.is_active ? 'active' : 'inactive'}">
                   ${roomData.is_active ? 'Active' : 'Inactive'}
               </span>
           </div>
           
           <div class="info-row">
               <span class="label">Capacity:</span> ${roomData.capacity} people
           </div>
           
           ${roomData.description ? `
                   <div class="info-row">
                       <span class="label">Description:</span> ${roomData.description}
                   </div>
               ` : ''}
           
           <div class="info-row">
               <span class="label">Facilities:</span>
               <ul>${facilitiesHTML}</ul>
           </div>
           
           <div class="info-row">
               <span class="label">Generated:</span> ${new Date().toLocaleString()}
           </div>
       </body>
       </html>
   `);

            printWindow.document.close();
            printWindow.print();
        }

        // Add print button to quick actions (you can call this function)
        if (document.querySelector('.card .btn-outline-secondary:last-child')) {
            const printButton = document.createElement('button');
            printButton.className = 'btn btn-outline-secondary';
            printButton.innerHTML = '<i class="fas fa-print me-2"></i>Print Details';
            printButton.onclick = printRoomDetails;

            const lastButton = document.querySelector('.card .d-grid .btn-outline-secondary:last-child');
            if (lastButton) {
                lastButton.parentNode.appendChild(printButton);
            }
        }
    </script>
@endpush
