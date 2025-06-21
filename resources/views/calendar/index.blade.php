{{-- resources/views/calendar/index.blade.php --}}
@extends(auth()->check() ? (auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app') : 'layouts.guest')

@section('title', 'Calendar')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@4.0.0-alpha.3/dist/fullcalendar.min.css' rel='stylesheet' />
<style>
    .fc-event {
        border: none !important;
        border-radius: 6px !important;
        padding: 2px 6px !important;
        font-size: 0.85rem !important;
    }
    
    .fc-event-title {
        font-weight: 600 !important;
    }
    
    .fc-daygrid-event {
        margin: 1px 0 !important;
    }
    
    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }
    
    .event-tooltip {
        position: absolute;
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
        max-width: 200px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .event-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar me-2 text-primary"></i>
                    Meeting Room Calendar
                </h5>
                
                @auth
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>
                        New Booking
                    </a>
                @else
                    <button type="button" class="btn btn-primary btn-sm" onclick="requireLogin()">
                        <i class="fas fa-plus me-2"></i>
                        New Booking
                    </button>
                @endauth
            </div>
            
            <div class="card-body">
                <!-- Calendar Legend -->
                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: var(--primary-red);"></div>
                        <small>Approved</small>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffc107;"></div>
                        <small>Pending</small>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #fd7e14;"></div>
                        <small>Ongoing</small>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #28a745;"></div>
                        <small>Completed</small>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #6c757d;"></div>
                        <small>Rejected/Cancelled</small>
                    </div>
                </div>
                
                <!-- Calendar -->
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <!-- Room Filter -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2 text-primary"></i>
                    Filter by Room
                </h6>
            </div>
            <div class="card-body">
                <select class="form-select" id="roomFilter">
                    <option value="">All Rooms</option>
                    @foreach(\App\Models\Room::active()->orderBy('name')->get() as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Today's Events -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                    Today's Schedule
                </h6>
            </div>
            <div class="card-body p-0" id="todayEvents">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be loaded here -->
            </div>
            <div class="modal-footer" id="eventModalFooter">
                <!-- Action buttons will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar JS - Load setelah jQuery -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>

<script>
// Pastikan jQuery dan FullCalendar sudah loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is required but not loaded');
}

if (typeof FullCalendar === 'undefined') {
    console.error('FullCalendar is required but not loaded');
}

let calendar;

document.addEventListener('DOMContentLoaded', function() {
    // Double check dependencies
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar not loaded, trying to reload...');
        // Fallback: load FullCalendar dynamically
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js';
        script.onload = function() {
            initializeCalendar();
        };
        document.head.appendChild(script);
    } else {
        initializeCalendar();
    }
    
    loadTodayEvents();

    // Room filter change
    const roomFilter = document.getElementById('roomFilter');
    if (roomFilter) {
        roomFilter.addEventListener('change', function() {
            if (calendar) {
                calendar.refetchEvents();
            }
        });
    }
});

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }

    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar is not available');
        return;
    }
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 600,
        locale: 'id', // Indonesian locale
        firstDay: 1, // Monday first
        events: function(info, successCallback, failureCallback) {
            const roomFilter = document.getElementById('roomFilter');
            const roomId = roomFilter ? roomFilter.value : '';
            
            const params = new URLSearchParams({
                start: info.startStr,
                end: info.endStr
            });
            
            if (roomId) {
                params.append('room_id', roomId);
            }
            
            fetch(`{{ route('calendar.events') }}?${params}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    failureCallback(error);
                    showToast('error', 'Failed to load calendar events');
                });
        },
        eventClick: function(info) {
            showEventDetail(info.event);
        },
        eventMouseEnter: function(info) {
            // Show tooltip
            const tooltip = createTooltip(info.event);
            document.body.appendChild(tooltip);
            
            const rect = info.el.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
            
            tooltip.style.left = (rect.left + scrollLeft) + 'px';
            tooltip.style.top = (rect.top + scrollTop - tooltip.offsetHeight - 10) + 'px';
        },
        eventMouseLeave: function(info) {
            // Remove tooltip
            const tooltip = document.querySelector('.event-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        },
        @auth
        @if(auth()->user()->isAdmin() || auth()->user()->is_active)
        selectable: true,
        select: function(info) {
            // Redirect to create booking with pre-filled date
            const date = info.startStr;
            window.location.href = `{{ route('bookings.create') }}?date=${date}`;
        },
        @endif
        @endauth
        eventDrop: function(info) {
            @auth
            if (info.event.extendedProps.can_edit) {
                moveEvent(info.event, info.event.start, info.event.end);
            } else {
                info.revert();
                showToast('error', 'You do not have permission to move this booking');
            }
            @else
            info.revert();
            requireLogin();
            @endauth
        },
        editable: {{ auth()->check() ? 'true' : 'false' }},
        dayMaxEvents: true,
        moreLinkClick: 'popover',
        businessHours: {
            startTime: '{{ getSetting("operating_hours_start", "08:00") }}',
            endTime: '{{ getSetting("operating_hours_end", "18:00") }}',
            daysOfWeek: [1, 2, 3, 4, 5] // Monday - Friday
        },
        slotMinTime: '{{ getSetting("operating_hours_start", "08:00") }}',
        slotMaxTime: '{{ getSetting("operating_hours_end", "18:00") }}',
        eventDisplay: 'block',
        displayEventTime: true,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }
    });
    
    calendar.render();
}

function createTooltip(event) {
    const tooltip = document.createElement('div');
    tooltip.className = 'event-tooltip';
    
    const startTime = event.start ? event.start.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    }) : '';
    
    const endTime = event.end ? event.end.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    }) : '';
    
    tooltip.innerHTML = `
        <div><strong>${event.title}</strong></div>
        <div>Room: ${event.extendedProps.room_name}</div>
        <div>Time: ${startTime} - ${endTime}</div>
        <div>Organizer: ${event.extendedProps.user_name}</div>
        <div>Participants: ${event.extendedProps.participant_count}</div>
        <div>Status: ${event.extendedProps.status_label}</div>
    `;
    
    return tooltip;
}

function showEventDetail(event) {
    const props = event.extendedProps;
    
    document.getElementById('eventModalTitle').innerHTML = `
        <i class="fas fa-calendar-check me-2"></i>
        ${event.title}
    `;
    
    const modalBody = document.getElementById('eventModalBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Event Details</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Room:</strong></td>
                        <td>${props.room_name}</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>${event.start.toLocaleDateString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</td>
                    </tr>
                    <tr>
                        <td><strong>Time:</strong></td>
                        <td>${event.start.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        })} - ${event.end.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge" style="background-color: ${event.backgroundColor}">${props.status_label}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Organizer & Participants</h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Organizer:</strong></td>
                        <td>${props.user_name}</td>
                    </tr>
                    <tr>
                        <td><strong>Participants:</strong></td>
                        <td>${props.participant_count} people</td>
                    </tr>
                    <tr>
                        <td><strong>Purpose:</strong></td>
                        <td>${props.purpose}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        ${props.description ? `
            <div class="mt-3">
                <h6 class="text-primary">Description</h6>
                <p class="text-muted">${props.description}</p>
            </div>
        ` : ''}
    `;
    
    const modalFooter = document.getElementById('eventModalFooter');
    let footerButtons = '';
    
    @auth
    footerButtons += `<a href="/bookings/${props.booking_id}" class="btn btn-primary">View Details</a>`;
    
    if (props.can_edit) {
        footerButtons += `<a href="/bookings/${props.booking_id}/edit" class="btn btn-warning ms-2">Edit</a>`;
    }
    
    if (props.can_cancel) {
        footerButtons += `<button class="btn btn-danger ms-2" onclick="cancelBookingFromModal(${props.booking_id})">Cancel</button>`;
    }
    @else
    footerButtons = `<button class="btn btn-primary" onclick="requireLogin()">Login to Book</button>`;
    @endauth
    
    modalFooter.innerHTML = `
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        ${footerButtons}
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
}

function loadTodayEvents() {
    const today = new Date().toISOString().split('T')[0];
    
    fetch(`{{ route('calendar.events') }}?start=${today}&end=${today}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(events => {
            const container = document.getElementById('todayEvents');
            
            if (events.length > 0) {
                let html = '';
                events.forEach(event => {
                    const startTime = new Date(event.start).toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    html += `
                        <div class="d-flex align-items-center px-3 py-2 border-bottom cursor-pointer" onclick="showEventDetailById(${event.extendedProps.booking_id})">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle" style="width: 12px; height: 12px; background-color: ${event.backgroundColor};"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small">${event.title}</div>
                                <div class="small text-muted">${event.extendedProps.room_name}</div>
                                <div class="small text-muted">${startTime}</div>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-day text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">No events today</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading today events:', error);
            document.getElementById('todayEvents').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0">Failed to load events</p>
                </div>
            `;
        });
}

function showEventDetailById(bookingId) {
    @auth
        window.location.href = `/bookings/${bookingId}`;
    @else
        requireLogin();
    @endauth
}

function moveEvent(event, newStart, newEnd) {
    const bookingId = event.extendedProps.booking_id;
    
    fetch(`{{ route('calendar.move-event') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            booking_id: bookingId,
            new_start: newStart.toISOString(),
            new_end: newEnd.toISOString()
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            if (calendar) {
                calendar.refetchEvents();
            }
            loadTodayEvents();
        } else {
            showToast('error', data.error || 'Failed to move booking');
            if (calendar) {
                calendar.refetchEvents(); // Revert the change
            }
        }
    })
    .catch(error => {
        console.error('Error moving event:', error);
        showToast('error', 'Failed to move booking');
        if (calendar) {
            calendar.refetchEvents(); // Revert the change
        }
    });
}

function cancelBookingFromModal(bookingId) {
    if (typeof confirmAction === 'undefined') {
        if (confirm('Are you sure you want to cancel this booking?')) {
            cancelBookingRequest(bookingId);
        }
    } else {
        confirmAction(
            'Cancel Booking?',
            'Are you sure you want to cancel this booking?',
            'Yes, Cancel!'
        ).then((result) => {
            if (result.isConfirmed) {
                cancelBookingRequest(bookingId);
            }
        });
    }
}

function cancelBookingRequest(bookingId) {
    fetch(`/bookings/${bookingId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        showToast('success', 'Booking cancelled successfully');
        if (calendar) {
            calendar.refetchEvents();
        }
        loadTodayEvents();
        
        // Close modal if open
        const modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
        if (modal) {
            modal.hide();
        }
    })
    .catch(error => {
        console.error('Error cancelling booking:', error);
        showToast('error', 'Failed to cancel booking');
    });
}

@guest
function requireLogin() {
    if (typeof bootstrap !== 'undefined' && document.getElementById('loginModal')) {
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    } else {
        // Fallback jika modal tidak tersedia
        if (confirm('You need to login to perform this action. Go to login page?')) {
            window.location.href = '/login';
        }
    }
}
@endguest

// Auto refresh calendar every 5 minutes
setInterval(function() {
    if (document.visibilityState === 'visible' && calendar) {
        calendar.refetchEvents();
        loadTodayEvents();
    }
}, 300000);

// Handle window resize
window.addEventListener('resize', function() {
    if (calendar) {
        calendar.updateSize();
    }
});
</script>
@endpush