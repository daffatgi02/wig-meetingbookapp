{{-- resources/views/components/booking-card.blade.php --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="card-title mb-0">{{ $booking->title }}</h5>
            @include('components.status-badge', ['status' => $booking->status])
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <p class="text-muted mb-1">
                    <i class="fas fa-door-open me-2"></i>
                    <strong>{{ $booking->room->name }}</strong>
                </p>
                <p class="text-muted mb-1">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $booking->room->location }}
                </p>
            </div>
            <div class="col-md-6">
                <p class="text-muted mb-1">
                    <i class="fas fa-calendar me-2"></i>
                    {{ $booking->booking_date->format('d/m/Y') }}
                </p>
                <p class="text-muted mb-1">
                    <i class="fas fa-clock me-2"></i>
                    {{ $booking->start_time }} - {{ $booking->end_time }}
                </p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <p class="text-muted mb-1">
                    <i class="fas fa-user me-2"></i>
                    {{ $booking->user->name }}
                </p>
            </div>
            <div class="col-md-6">
                <p class="text-muted mb-1">
                    <i class="fas fa-users me-2"></i>
                    {{ $booking->participant_count }} peserta
                </p>
            </div>
        </div>

        <p class="text-muted mb-1">
            <i class="fas fa-bullseye me-2"></i>
            <strong>Tujuan:</strong> {{ $booking->purpose }}
        </p>

        @if($booking->description)
            <p class="text-muted small">
                <i class="fas fa-sticky-note me-2"></i>
                {{ Str::limit($booking->description, 100) }}
            </p>
        @endif

        <!-- Participants -->
        @if($booking->participants->count() > 0)
            <div class="mt-3">
                <h6 class="text-muted small mb-2">Peserta:</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($booking->participants->take(3) as $participant)
                        <span class="badge bg-light text-dark">{{ $participant->name }}</span>
                    @endforeach
                    @if($booking->participants->count() > 3)
                        <span class="badge bg-secondary">+{{ $booking->participants->count() - 3 }} lainnya</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="card-footer bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="fas fa-clock me-1"></i>
                Dibuat {{ $booking->created_at->diffForHumans() }}
            </small>

            <div class="btn-group btn-group-sm">
                @auth
                    @can('view', $booking)
                        <a href="{{ route('bookings.show', $booking) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    @endcan

                    @can('update', $booking)
                        @if($booking->isEditable())
                            <a href="{{ route('bookings.edit', $booking) }}" 
                               class="btn btn-outline-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif
                    @endcan

                    @can('delete', $booking)
                        @if($booking->isCancellable())
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="cancelBooking({{ $booking->id }})">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    @endcan
                @endauth
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelBooking(bookingId) {
    confirmAction(
        'Batalkan Booking?',
        'Apakah Anda yakin ingin membatalkan booking ini?',
        'Ya, Batalkan!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.post(`/bookings/${bookingId}/cancel`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                showToast('success', 'Booking berhasil dibatalkan');
                setTimeout(() => location.reload(), 1500);
            })
            .fail(function(xhr) {
                showToast('error', 'Gagal membatalkan booking');
            });
        }
    });
}
</script>
@endpush