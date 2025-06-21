{{-- resources/views/components/room-card.blade.php --}}
<div class="card h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="card-title text-primary mb-0">
                <i class="fas fa-door-open me-2"></i>
                {{ $room->name }}
            </h5>
            @if($room->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Inactive</span>
            @endif
        </div>

        <div class="mb-3">
            <p class="text-muted mb-2">
                <i class="fas fa-map-marker-alt me-2"></i>
                {{ $room->location }}
            </p>
            <p class="text-muted mb-2">
                <i class="fas fa-users me-2"></i>
                Kapasitas: {{ $room->capacity }} orang
            </p>
        </div>

        @if($room->facilities && count($room->facilities) > 0)
            <div class="mb-3">
                <h6 class="text-muted small mb-2">Fasilitas:</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($room->facilities as $facility)
                        <span class="badge bg-light text-dark">{{ $facility }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($room->description)
            <p class="card-text small text-muted">{{ $room->description }}</p>
        @endif

        <!-- Today's bookings -->
        @if(isset($todayBookings) && $todayBookings->where('room_id', $room->id)->count() > 0)
            <div class="mt-3">
                <h6 class="text-muted small mb-2">Booking Hari Ini:</h6>
                @foreach($todayBookings->where('room_id', $room->id) as $booking)
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <small class="text-muted">
                            {{ $booking->start_time }}-{{ $booking->end_time }}
                        </small>
                        @include('components.status-badge', ['status' => $booking->status])
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @auth
        @if(auth()->user()->is_active)
            <div class="card-footer bg-transparent">
                <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}" 
                   class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-calendar-plus me-1"></i>
                    Book Room
                </a>
            </div>
        @endif
    @else
        <div class="card-footer bg-transparent">
            <button type="button" class="btn btn-primary btn-sm w-100" onclick="requireLogin()">
                <i class="fas fa-calendar-plus me-1"></i>
                Book Room
            </button>
        </div>
    @endauth
</div>