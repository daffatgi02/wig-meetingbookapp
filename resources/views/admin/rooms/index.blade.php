{{-- resources/views/admin/rooms/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Room Management')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-dark mb-1">
                    <i class="fas fa-door-open text-primary me-2"></i>
                    Room Management
                </h3>
                <p class="text-muted mb-0">Manage meeting rooms, capacity, and facilities</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-info" onclick="exportRooms()">
                    <i class="fas fa-download me-2"></i>
                    Export Rooms
                </button>
                <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add New Room
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.rooms.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label small text-muted fw-bold">Status</label>
                            <select class="form-select form-select-sm" name="status" id="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                    🟢 Active
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                    🔴 Inactive
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="capacity_min" class="form-label small text-muted fw-bold">Min Capacity</label>
                            <input type="number" class="form-control form-control-sm" name="capacity_min" 
                                   id="capacity_min" value="{{ request('capacity_min') }}" min="1" max="500">
                        </div>

                        <div class="col-md-3">
                            <label for="capacity_max" class="form-label small text-muted fw-bold">Max Capacity</label>
                            <input type="number" class="form-control form-control-sm" name="capacity_max" 
                                   id="capacity_max" value="{{ request('capacity_max') }}" min="1" max="500">
                        </div>

                        <div class="col-md-3">
                            <label for="search" class="form-label small text-muted fw-bold">Search</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search" id="search" 
                                       value="{{ request('search') }}" placeholder="Name, location...">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i>
                                    Apply Filters
                                </button>
                                <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>
                                    Clear Filters
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-primary bg-opacity-10">
                    <div class="card-body text-center">
                        <h4 class="text-primary mb-1">{{ \App\Models\Room::count() }}</h4>
                        <small class="text-muted">Total Rooms</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-success bg-opacity-10">
                    <div class="card-body text-center">
                        <h4 class="text-success mb-1">{{ \App\Models\Room::where('is_active', true)->count() }}</h4>
                        <small class="text-muted">Active Rooms</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-info bg-opacity-10">
                    <div class="card-body text-center">
                        <h4 class="text-info mb-1">{{ \App\Models\Room::sum('capacity') }}</h4>
                        <small class="text-muted">Total Capacity</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-warning bg-opacity-10">
                    <div class="card-body text-center">
                        <h4 class="text-warning mb-1">{{ round(\App\Models\Room::avg('capacity')) }}</h4>
                        <small class="text-muted">Avg Capacity</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms Grid -->
        @if($rooms->count() > 0)
            <div class="row g-4">
                @foreach($rooms as $room)
                    <div class="col-lg-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100 room-card" data-room-id="{{ $room->id }}">
                            <div class="card-header bg-white border-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 text-primary">{{ $room->name }}</h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $room->location }}
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.rooms.show', $room) }}">
                                                    <i class="fas fa-eye me-2 text-info"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.rooms.edit', $room) }}">
                                                    <i class="fas fa-edit me-2 text-warning"></i>Edit Room
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="toggleRoom({{ $room->id }}, {{ $room->is_active ? 'false' : 'true' }})">
                                                    @if($room->is_active)
                                                        <i class="fas fa-pause me-2 text-warning"></i>Deactivate
                                                    @else
                                                        <i class="fas fa-play me-2 text-success"></i>Activate
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="duplicateRoom({{ $room->id }})">
                                                    <i class="fas fa-copy me-2 text-secondary"></i>Duplicate
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteRoom({{ $room->id }})">
                                                    <i class="fas fa-trash me-2"></i>Delete Room
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Status Badge -->
                                <div class="mb-3">
                                    @if($room->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause me-1"></i>Inactive
                                        </span>
                                    @endif
                                </div>

                                <!-- Capacity Info -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 text-primary mb-1">{{ $room->capacity }}</div>
                                            <small class="text-muted">Max Capacity</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="h5 text-info mb-1">
                                                {{ $room->bookings()->where('booking_date', '>=', today())->count() }}
                                            </div>
                                            <small class="text-muted">Upcoming Bookings</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Facilities -->
                                @if($room->facilities && count($room->facilities) > 0)
                                    <div class="mb-3">
                                        <div class="small text-muted mb-2">
                                            <i class="fas fa-cogs me-1"></i>Facilities:
                                        </div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($room->facilities as $facility)
                                                <span class="badge bg-light text-dark border">{{ $facility }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Description -->
                                @if($room->description)
                                    <div class="mb-3">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-info-circle me-1"></i>Description:
                                        </div>
                                        <div class="small text-dark">
                                            {{ Str::limit($room->description, 100) }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Today's Bookings -->
                                @php
                                    $todayBookings = $room->bookings()
                                        ->where('booking_date', today())
                                        ->whereIn('status', ['approved', 'ongoing', 'completed'])
                                        ->orderBy('start_time')
                                        ->get();
                                @endphp

                                @if($todayBookings->count() > 0)
                                    <div class="mb-3">
                                        <div class="small text-muted mb-2">
                                            <i class="fas fa-calendar-day me-1"></i>Today's Schedule:
                                        </div>
                                        @foreach($todayBookings->take(2) as $booking)
                                            <div class="d-flex justify-content-between align-items-center py-1">
                                                <small class="text-dark">
                                                    {{ $booking->start_time }}-{{ $booking->end_time }}
                                                </small>
                                                @include('components.status-badge', ['status' => $booking->status])
                                            </div>
                                        @endforeach
                                        @if($todayBookings->count() > 2)
                                            <small class="text-muted">+{{ $todayBookings->count() - 2 }} more</small>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-2">
                                        <small class="text-success">
                                            <i class="fas fa-calendar-check me-1"></i>
                                            Available all day
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer bg-white border-0">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.rooms.show', $room) }}" 
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>
                                        View
                                    </a>
                                    <a href="{{ route('admin.rooms.edit', $room) }}" 
                                       class="btn btn-warning btn-sm flex-fill">
                                        <i class="fas fa-edit me-1"></i>
                                        Edit
                                    </a>
                                    <button class="btn btn-sm {{ $room->is_active ? 'btn-outline-secondary' : 'btn-success' }}" 
                                            onclick="toggleRoom({{ $room->id }}, {{ $room->is_active ? 'false' : 'true' }})"
                                            data-bs-toggle="tooltip" 
                                            title="{{ $room->is_active ? 'Deactivate' : 'Activate' }} Room">
                                        <i class="fas fa-{{ $room->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($rooms->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $rooms->appends(request()->query())->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-door-open text-muted mb-3" style="font-size: 4rem;"></i>
                        <h4 class="text-dark">No Rooms Found</h4>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['status', 'capacity_min', 'capacity_max', 'search']))
                                No rooms match your current filters. Try adjusting your search criteria.
                            @else
                                Get started by adding your first meeting room to the system.
                            @endif
                        </p>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Add First Room
                        </a>
                        @if(request()->hasAny(['status', 'capacity_min', 'capacity_max', 'search']))
                            <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Auto-submit filters
    $('#status, #capacity_min, #capacity_max').change(function() {
        $('#filterForm').submit();
    });

    // Validate capacity inputs
    $('#capacity_min, #capacity_max').on('input', function() {
        const min = parseInt($('#capacity_min').val()) || 0;
        const max = parseInt($('#capacity_max').val()) || 0;
        
        if (min > 0 && max > 0 && min > max) {
            $(this).addClass('is-invalid');
            showToast('warning', 'Minimum capacity cannot be greater than maximum capacity');
        } else {
            $('#capacity_min, #capacity_max').removeClass('is-invalid');
        }
    });
});

function toggleRoom(roomId, activate) {
    const action = activate === 'true' ? 'activate' : 'deactivate';
    const actionText = activate === 'true' ? 'Activate' : 'Deactivate';
    
    confirmAction(
        `${actionText} Room?`,
        `Are you sure you want to ${action} this room?`,
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

function deleteRoom(roomId) {
    confirmAction(
        'Delete Room Permanently?',
        'This will permanently delete the room and all its booking history. This action cannot be undone.',
        'Yes, Delete Permanently!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/rooms/${roomId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function(response) {
                showToast('success', 'Room deleted permanently');
                setTimeout(() => location.reload(), 1500);
            })
            .fail(function(xhr) {
                let message = 'Failed to delete room';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast('error', message);
            });
        }
    });
}

function duplicateRoom(roomId) {
    window.location.href = `/admin/rooms/create?duplicate=${roomId}`;
}

function exportRooms() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = window.location.pathname + '?' + params.toString();
}
</script>
@endpush