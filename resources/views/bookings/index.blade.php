{{-- resources/views/bookings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="text-dark mb-1">My Bookings</h3>
                <p class="text-muted mb-0">Manage and track your meeting room reservations</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                New Booking
            </a>
        </div>

        <!-- Filter and Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('bookings.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label small">Status</label>
                            <select class="form-select form-select-sm" name="status" id="status">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_from" class="form-label small">From Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" id="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="date_to" class="form-label small">To Date</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" id="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="search" class="form-label small">Search</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search" id="search" 
                                       placeholder="Search title, purpose..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i>
                                    Apply Filter
                                </button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>
                                    Clear
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" id="viewList">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary active" id="viewCards">
                                    <i class="fas fa-th"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Booking Statistics -->
        <div class="row g-3 mb-4">
            @php
                $stats = [
                    'total' => $bookings->total(),
                    'pending' => $bookings->where('status', 'pending')->count(),
                    'approved' => $bookings->where('status', 'approved')->count(),
                    'completed' => $bookings->where('status', 'completed')->count(),
                ];
            @endphp
            
            <div class="col-md-3">
                <div class="card border-0 bg-primary bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-calendar-check text-primary mb-2" style="font-size: 1.5rem;"></i>
                        <h4 class="text-primary mb-0">{{ $stats['total'] }}</h4>
                        <small class="text-muted">Total Bookings</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 bg-warning bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-clock text-warning mb-2" style="font-size: 1.5rem;"></i>
                        <h4 class="text-warning mb-0">{{ $stats['pending'] }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 bg-success bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-check-circle text-success mb-2" style="font-size: 1.5rem;"></i>
                        <h4 class="text-success mb-0">{{ $stats['approved'] }}</h4>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 bg-info bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-check-double text-info mb-2" style="font-size: 1.5rem;"></i>
                        <h4 class="text-info mb-0">{{ $stats['completed'] }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Bookings ({{ $bookings->total() }} total)
                </h6>
                
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i>
                        Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </a></li>
                    </ul>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if($bookings->count() > 0)
                    <!-- Card View -->
                    <div id="cardsView">
                        <div class="row g-3 p-3">
                            @foreach($bookings as $booking)
                                <div class="col-lg-6">
                                    @include('components.booking-card', ['booking' => $booking])
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- List View -->
                    <div id="listView" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Meeting</th>
                                        <th>Room</th>
                                        <th>Date & Time</th>
                                        <th>Participants</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $booking->title }}</div>
                                                    <small class="text-muted">{{ Str::limit($booking->purpose, 30) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $booking->room->name }}</div>
                                                    <small class="text-muted">{{ $booking->room->location }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $booking->booking_date->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $booking->start_time }} - {{ $booking->end_time }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $booking->participant_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @include('components.status-badge', ['status' => $booking->status])
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('bookings.show', $booking) }}" 
                                                       class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($booking->isEditable())
                                                        <a href="{{ route('bookings.edit', $booking) }}" 
                                                           class="btn btn-outline-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    @if($booking->isCancellable())
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="cancelBooking({{ $booking->id }})" title="Cancel">
                                                            <i class="fas fa-times"></i>
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
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-muted">No bookings found</h5>
                        @if(request()->hasAny(['status', 'date_from', 'date_to', 'search']))
                            <p class="text-muted mb-3">Try adjusting your filter criteria</p>
                            <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-times me-2"></i>
                                Clear Filters
                            </a>
                        @else
                            <p class="text-muted mb-3">You haven't made any bookings yet</p>
                            <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Create Your First Booking
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            
            @if($bookings->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} results
                        </small>
                        {{ $bookings->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // View toggle functionality
    $('#viewCards').click(function() {
        $(this).addClass('active');
        $('#viewList').removeClass('active');
        $('#cardsView').show();
        $('#listView').hide();
        localStorage.setItem('bookingsView', 'cards');
    });

    $('#viewList').click(function() {
        $(this).addClass('active');
        $('#viewCards').removeClass('active');
        $('#listView').show();
        $('#cardsView').hide();
        localStorage.setItem('bookingsView', 'list');
    });

    // Load saved view preference
    const savedView = localStorage.getItem('bookingsView');
    if (savedView === 'list') {
        $('#viewList').click();
    }

    // Auto-submit form when filter changes
    $('#status, #date_from, #date_to').change(function() {
        $('#filterForm').submit();
    });

    // Search with enter key
    $('#search').keypress(function(e) {
        if (e.which == 13) {
            $('#filterForm').submit();
        }
    });
});

function cancelBooking(bookingId) {
    confirmAction(
        'Cancel Booking?',
        'Are you sure you want to cancel this booking?',
        'Yes, Cancel!'
    ).then((result) => {
        if (result.isConfirmed) {
            $.post(`/bookings/${bookingId}/cancel`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                showToast('success', 'Booking cancelled successfully');
                setTimeout(() => location.reload(), 1500);
            })
            .fail(function(xhr) {
                showToast('error', 'Failed to cancel booking');
            });
        }
    });
}

function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'excel');
    window.location.href = '{{ route("bookings.index") }}?' + params.toString();
}

function exportToPDF() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'pdf');
    window.location.href = '{{ route("bookings.index") }}?' + params.toString();
}
</script>
@endpush