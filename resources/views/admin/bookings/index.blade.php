{{-- resources/views/admin/bookings/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'All Bookings')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-dark mb-1">
                    <i class="fas fa-calendar-check text-primary me-2"></i>
                    Booking Management
                </h3>
                <p class="text-muted mb-0">Monitor and manage all room reservations</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.bookings.pending') }}" class="btn btn-warning">
                    <i class="fas fa-clock me-2"></i>
                    Pending ({{ \App\Models\Booking::where('status', 'pending')->count() }})
                </a>
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    New Booking
                </a>
            </div>
        </div>

        <!-- Filters -->
        @include('admin.bookings.partials.filters')

        <!-- Bookings Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-table me-2 text-primary"></i>
                    All Bookings ({{ $bookings->total() }})
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="exportBookings()">
                        <i class="fas fa-download me-1"></i>
                        Export
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="refreshTable()">
                        <i class="fas fa-sync me-1"></i>
                        Refresh
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="25%">Meeting Details</th>
                                <th width="20%">Room & Schedule</th>
                                <th width="20%">Organizer</th>
                                <th width="15%">Status</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr id="booking-{{ $booking->id }}">
                                    <td>
                                        <div class="fw-bold text-primary mb-1">{{ $booking->title }}</div>
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-bullseye me-1"></i>{{ $booking->purpose }}
                                        </div>
                                        <div class="small text-info">
                                            <i class="fas fa-users me-1"></i>{{ $booking->participant_count }} peserta
                                        </div>
                                        @if($booking->description)
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-sticky-note me-1"></i>
                                                {{ Str::limit($booking->description, 50) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $booking->room->name }}</div>
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $booking->room->location }}
                                        </div>
                                        <div class="small text-primary">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $booking->booking_date->format('d/m/Y') }}
                                        </div>
                                        <div class="small text-primary">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $booking->start_time }} - {{ $booking->end_time }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $booking->user->name }}</div>
                                        <div class="small text-muted">{{ $booking->user->email }}</div>
                                        <div class="small text-info mt-1">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $booking->created_at->diffForHumans() }}
                                        </div>
                                        @if($booking->created_by_admin)
                                            <div class="small">
                                                <span class="badge bg-info">Admin Created</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @include('components.status-badge', ['status' => $booking->status])
                                        
                                        @if($booking->requires_reapproval)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark small">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Re-approval Required
                                                </span>
                                            </div>
                                        @endif

                                        @if($booking->status === 'rejected' && $booking->rejection_reason)
                                            <div class="mt-1">
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        data-bs-toggle="tooltip" 
                                                        title="{{ $booking->rejection_reason }}">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @include('admin.bookings.partials.actions', ['booking' => $booking])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                                        <h6 class="text-muted">No bookings found</h6>
                                        <p class="text-muted small mb-3">No bookings match your current filters</p>
                                        <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>
                                            Create First Booking
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($bookings->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} 
                            of {{ $bookings->total() }} results
                        </div>
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('admin.bookings.partials.approval-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Auto-refresh every 60 seconds
    setInterval(function() {
        if (document.visibilityState === 'visible' && !$('.modal.show').length) {
            refreshTable();
        }
    }, 60000);
});

function refreshTable() {
    location.reload();
}

function exportBookings() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = window.location.pathname + '?' + params.toString();
}
</script>
@endpush