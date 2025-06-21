{{-- resources/views/admin/bookings/partials/filters.blade.php --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label small text-muted fw-bold">Status</label>
                    <select class="form-select form-select-sm" name="status" id="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            🕐 Pending
                        </option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                            ✅ Approved
                        </option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>
                            🔄 Ongoing
                        </option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                            ✅ Completed
                        </option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                            ❌ Rejected
                        </option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                            🚫 Cancelled
                        </option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="room_id" class="form-label small text-muted fw-bold">Room</label>
                    <select class="form-select form-select-sm" name="room_id" id="room_id">
                        <option value="">All Rooms</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="user_id" class="form-label small text-muted fw-bold">Organizer</label>
                    <select class="form-select form-select-sm" name="user_id" id="user_id">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label small text-muted fw-bold">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" id="date_from" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label small text-muted fw-bold">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" id="date_to" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2">
                    <label for="search" class="form-label small text-muted fw-bold">Search</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="search" id="search" 
                               value="{{ request('search') }}" placeholder="Title, purpose, user...">
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
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>
                            Clear Filters
                        </a>
                        
                        @if(request()->hasAny(['status', 'room_id', 'user_id', 'date_from', 'date_to', 'search']))
                            <div class="ms-auto">
                                <span class="badge bg-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    Filters Applied
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-submit on filter change
    $('#status, #room_id, #user_id, #date_from, #date_to').change(function() {
        $('#filterForm').submit();
    });

    // Set max date for date inputs
    const today = new Date().toISOString().split('T')[0];
    const nextYear = new Date();
    nextYear.setFullYear(nextYear.getFullYear() + 1);
    const maxDate = nextYear.toISOString().split('T')[0];
    
    $('#date_from, #date_to').attr('max', maxDate);
});
</script>