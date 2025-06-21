{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Book New Room')

@push('styles')
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@4.0.0-alpha.3/dist/fullcalendar.min.css' rel='stylesheet' />
    <!-- Flatpickr CSS for date/time picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-plus me-2 text-primary"></i>
                        Book New Room
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-1"></i>
                                    Meeting Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" required
                                    placeholder="e.g., Weekly Team Meeting">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="purpose" class="form-label">
                                    <i class="fas fa-bullseye me-1"></i>
                                    Purpose <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('purpose') is-invalid @enderror" id="purpose"
                                    name="purpose" required>
                                    <option value="">Select Purpose</option>
                                    <option value="Meeting" {{ old('purpose') === 'Meeting' ? 'selected' : '' }}>Meeting
                                    </option>
                                    <option value="Training" {{ old('purpose') === 'Training' ? 'selected' : '' }}>Training
                                    </option>
                                    <option value="Presentation" {{ old('purpose') === 'Presentation' ? 'selected' : '' }}>
                                        Presentation</option>
                                    <option value="Workshop" {{ old('purpose') === 'Workshop' ? 'selected' : '' }}>Workshop
                                    </option>
                                    <option value="Conference" {{ old('purpose') === 'Conference' ? 'selected' : '' }}>
                                        Conference</option>
                                    <option value="Interview" {{ old('purpose') === 'Interview' ? 'selected' : '' }}>
                                        Interview</option>
                                    <option value="Other" {{ old('purpose') === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Date & Time Selection -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="booking_date" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>
                                    Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('booking_date') is-invalid @enderror"
                                    id="booking_date" name="booking_date"
                                    value="{{ old('booking_date', request('date', date('Y-m-d'))) }}" required
                                    min="{{ date('Y-m-d') }}">
                                @error('booking_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="start_time" class="form-label">
                                    <i class="fas fa-clock me-1"></i>
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time"
                                    value="{{ old('start_time', request('start_time')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="end_time" class="form-label">
                                    <i class="fas fa-clock me-1"></i>
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                    id="end_time" name="end_time" value="{{ old('end_time', request('end_time')) }}"
                                    required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Room Selection -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label for="room_id" class="form-label">
                                    <i class="fas fa-door-open me-1"></i>
                                    Select Room <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('room_id') is-invalid @enderror" id="room_id"
                                    name="room_id" required>
                                    <option value="">Choose a room...</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" data-capacity="{{ $room->capacity }}"
                                            data-location="{{ $room->location }}"
                                            data-facilities="{{ implode(', ', $room->facilities ?? []) }}"
                                            {{ old('room_id', request('room_id')) == $room->id ? 'selected' : '' }}>
                                            {{ $room->name }} - {{ $room->location }} ({{ $room->capacity }} people)
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Room Info Display -->
                                <div id="roomInfo" class="mt-2 p-3 bg-light rounded" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <span id="roomLocation"></span>
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-users me-1"></i>
                                                Capacity: <span id="roomCapacity"></span> people
                                            </small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-cogs me-1"></i>
                                            Facilities: <span id="roomFacilities"></span>
                                        </small>
                                    </div>
                                </div>

                                <!-- Availability Check -->
                                <div id="availabilityCheck" class="mt-2"></div>
                            </div>

                            <div class="col-md-4">
                                <label for="participant_count" class="form-label">
                                    <i class="fas fa-users me-1"></i>
                                    Participants <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control @error('participant_count') is-invalid @enderror"
                                    id="participant_count" name="participant_count"
                                    value="{{ old('participant_count', 1) }}" required min="1" max="100">
                                @error('participant_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-sticky-note me-1"></i>
                                Additional Notes
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3" placeholder="Any additional information about the meeting...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Participants Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">
                                    <i class="fas fa-user-friends me-1"></i>
                                    Meeting Participants
                                </label>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addParticipant">
                                    <i class="fas fa-plus me-1"></i>
                                    Add Participant
                                </button>
                            </div>

                            <div id="participantsList">
                                @if (old('participants'))
                                    @foreach (old('participants') as $index => $participant)
                                        <div class="participant-item mb-2">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" class="form-control"
                                                        name="participants[{{ $index }}][name]"
                                                        value="{{ $participant['name'] }}" placeholder="Participant name"
                                                        required>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="email" class="form-control"
                                                        name="participants[{{ $index }}][email]"
                                                        value="{{ $participant['email'] ?? '' }}"
                                                        placeholder="Email (optional)">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm remove-participant">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Add participants to get better meeting coordination. Email is optional.
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Cancel
                            </a>

                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" id="previewBooking">
                                    <i class="fas fa-eye me-2"></i>
                                    Preview
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBooking">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Submit Booking
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Calendar & Quick Slots -->
        <div class="col-lg-4">
            <!-- Mini Calendar -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar me-2 text-primary"></i>
                        Available Time Slots
                    </h6>
                </div>
                <div class="card-body">
                    <div id="quickSlots">
                        <div class="text-center text-muted">
                            <i class="fas fa-clock mb-2" style="font-size: 2rem;"></i>
                            <p class="mb-0">Select date and room to see available slots</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Bookings for Selected Date -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Existing Bookings
                    </h6>
                </div>
                <div class="card-body">
                    <div id="existingBookings">
                        <div class="text-center text-muted">
                            <i class="fas fa-calendar-check mb-2" style="font-size: 2rem;"></i>
                            <p class="mb-0">Select date to see existing bookings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2 text-primary"></i>
                        Booking Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmBooking">
                        <i class="fas fa-check me-2"></i>
                        Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let participantIndex = {{ old('participants') ? count(old('participants')) : 0 }};

        $(document).ready(function() {
            // Initialize date picker
            flatpickr("#booking_date", {
                minDate: "today",
                maxDate: new Date().fp_incr(90), // 90 days from now
                onChange: function(selectedDates, dateStr) {
                    checkAvailability();
                    loadExistingBookings();
                }
            });

            // Initialize time pickers
            flatpickr("#start_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 30,
                minTime: "{{ getSetting('operating_hours_start', '08:00') }}",
                maxTime: "{{ getSetting('operating_hours_end', '18:00') }}",
                onChange: function(selectedDates, dateStr) {
                    updateEndTime();
                    checkAvailability();
                }
            });

            flatpickr("#end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 30,
                onChange: function() {
                    checkAvailability();
                }
            });

            // Room selection change
            $('#room_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    showRoomInfo(selectedOption);
                    checkAvailability();
                    loadQuickSlots();
                } else {
                    hideRoomInfo();
                    clearAvailability();
                }
            });

            // Participant count validation
            $('#participant_count, #room_id').change(function() {
                validateParticipantCount();
            });

            // Add participant functionality
            $('#addParticipant').click(function() {
                addParticipantRow();
            });

            // Remove participant functionality
            $(document).on('click', '.remove-participant', function() {
                $(this).closest('.participant-item').remove();
            });

            // Preview functionality
            $('#previewBooking').click(function() {
                if (validateForm()) {
                    showPreview();
                }
            });

            // Confirm booking from preview
            $('#confirmBooking').click(function() {
                $('#bookingForm').submit();
            });

            // Initial load
            if ($('#room_id').val()) {
                showRoomInfo($('#room_id').find('option:selected'));
                checkAvailability();
            }

            if ($('#booking_date').val()) {
                loadExistingBookings();
            }
        });

        function updateEndTime() {
            const startTime = $('#start_time').val();
            if (startTime) {
                const start = new Date('2000-01-01 ' + startTime);
                start.setHours(start.getHours() + 1); // Default 1 hour duration
                const endTime = start.toTimeString().slice(0, 5);
                $('#end_time').val(endTime);
            }
        }

        function showRoomInfo(option) {
            $('#roomLocation').text(option.data('location'));
            $('#roomCapacity').text(option.data('capacity'));
            $('#roomFacilities').text(option.data('facilities') || 'None specified');
            $('#roomInfo').show();
        }

        function hideRoomInfo() {
            $('#roomInfo').hide();
        }

        function validateParticipantCount() {
            const participantCount = parseInt($('#participant_count').val());
            const roomCapacity = parseInt($('#room_id option:selected').data('capacity'));

            if (participantCount && roomCapacity && participantCount > roomCapacity) {
                $('#participant_count').addClass('is-invalid');
                if (!$('#participant_count').next('.invalid-feedback').length) {
                    $('#participant_count').after(`<div class="invalid-feedback">
                Participant count exceeds room capacity (${roomCapacity} people)
            </div>`);
                }
                return false;
            } else {
                $('#participant_count').removeClass('is-invalid');
                $('#participant_count').next('.invalid-feedback').remove();
                return true;
            }
        }

        function checkAvailability() {
            const roomId = $('#room_id').val();
            const date = $('#booking_date').val();
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();

            if (!roomId || !date || !startTime || !endTime) {
                clearAvailability();
                return;
            }

            // Show loading
            $('#availabilityCheck').html(`
        <div class="alert alert-info d-flex align-items-center">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Checking availability...
        </div>
    `);

            $.post('{{ route('bookings.check-availability') }}', {
                    room_id: roomId,
                    date: date,
                    start_time: startTime,
                    end_time: endTime,
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(function(response) {
                    if (response.available) {
                        $('#availabilityCheck').html(`
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    Room is available for the selected time
                </div>
            `);
                    } else {
                        let conflictInfo = '';
                        if (response.conflicts && response.conflicts.length > 0) {
                            conflictInfo = '<ul class="mb-0 mt-2">';
                            response.conflicts.forEach(conflict => {
                                conflictInfo +=
                                    `<li>${conflict.user} (${conflict.start_time}-${conflict.end_time}): ${conflict.title}</li>`;
                            });
                            conflictInfo += '</ul>';
                        }

                        $('#availabilityCheck').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i>
                    Room is not available for the selected time
                    ${conflictInfo}
                </div>
            `);
                    }
                })
                .fail(function() {
                    $('#availabilityCheck').html(`
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Unable to check availability. Please try again.
            </div>
        `);
                });
        }

        function clearAvailability() {
            $('#availabilityCheck').empty();
        }

        function loadQuickSlots() {
            const roomId = $('#room_id').val();
            const date = $('#booking_date').val();

            if (!roomId || !date) {
                return;
            }

            $('#quickSlots').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading available slots...</p>
        </div>
    `);

            $.get('{{ route('calendar.available-slots') }}', {
                    room_id: roomId,
                    date: date,
                    duration: 60 // 1 hour default
                })
                .done(function(response) {
                    if (response.available_slots && response.available_slots.length > 0) {
                        let slotsHtml = '<div class="row g-2">';
                        response.available_slots.forEach(slot => {
                            slotsHtml += `
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100 slot-btn" 
                                data-start="${slot.start}" data-end="${slot.end}">
                            ${slot.label}
                        </button>
                    </div>
                `;
                        });
                        slotsHtml += '</div>';
                        $('#quickSlots').html(slotsHtml);

                        // Handle slot selection
                        $('.slot-btn').click(function() {
                            $('#start_time').val($(this).data('start'));
                            $('#end_time').val($(this).data('end'));
                            checkAvailability();
                        });
                    } else {
                        $('#quickSlots').html(`
                <div class="text-center text-muted">
                    <i class="fas fa-calendar-times mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0">No available slots for this date</p>
                </div>
            `);
                    }
                })
                .fail(function() {
                    $('#quickSlots').html(`
            <div class="text-center text-danger">
                <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                <p class="mb-0">Unable to load available slots</p>
            </div>
        `);
                });
        }

        function loadExistingBookings() {
            const date = $('#booking_date').val();

            if (!date) {
                return;
            }

            $('#existingBookings').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading bookings...</p>
        </div>
    `);

            $.get('{{ route('room.availability') }}', {
                    date: date
                })
                .done(function(response) {
                    if (response && response.length > 0) {
                        let bookingsHtml = '';

                        response.forEach(room => {
                            if (room.bookings && room.bookings.length > 0) {
                                bookingsHtml += `<h6 class="small fw-bold text-primary">${room.room.name}</h6>`;
                                room.bookings.forEach(booking => {
                                    bookingsHtml += `
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div>
                                    <div class="small fw-bold">${booking.title}</div>
                                    <div class="small text-muted">${booking.user.name}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-primary">${booking.start_time}-${booking.end_time}</div>
                                </div>
                            </div>
                        `;
                                });
                                bookingsHtml += '<div class="mb-3"></div>';
                            }
                        });

                        if (bookingsHtml) {
                            $('#existingBookings').html(bookingsHtml);
                        } else {
                            $('#existingBookings').html(`
                    <div class="text-center text-muted">
                        <i class="fas fa-calendar-check mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0">No bookings for this date</p>
                    </div>
                `);
                        }
                    } else {
                        $('#existingBookings').html(`
                <div class="text-center text-muted">
                    <i class="fas fa-calendar-check mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0">No bookings for this date</p>
                </div>
            `);
                    }
                })
                .fail(function() {
                    $('#existingBookings').html(`
            <div class="text-center text-danger">
                <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                <p class="mb-0">Unable to load bookings</p>
            </div>
        `);
                });
        }

        function addParticipantRow() {
            const html = `
       <div class="participant-item mb-2">
           <div class="row">
               <div class="col-md-5">
                   <input type="text" class="form-control" 
                          name="participants[${participantIndex}][name]" 
                          placeholder="Participant name" required>
               </div>
               <div class="col-md-5">
                   <input type="email" class="form-control" 
                          name="participants[${participantIndex}][email]" 
                          placeholder="Email (optional)">
               </div>
               <div class="col-md-2">
                   <button type="button" class="btn btn-outline-danger btn-sm remove-participant">
                       <i class="fas fa-trash"></i>
                   </button>
               </div>
           </div>
       </div>
   `;

            $('#participantsList').append(html);
            participantIndex++;
        }

        function validateForm() {
            let isValid = true;

            // Clear previous validation
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Required fields
            const requiredFields = ['title', 'purpose', 'booking_date', 'start_time', 'end_time', 'room_id',
                'participant_count'
            ];

            requiredFields.forEach(field => {
                const value = $(`#${field}`).val();
                if (!value || value.trim() === '') {
                    $(`#${field}`).addClass('is-invalid');
                    $(`#${field}`).after('<div class="invalid-feedback">This field is required</div>');
                    isValid = false;
                }
            });

            // Validate participant count against room capacity
            if (!validateParticipantCount()) {
                isValid = false;
            }

            // Validate time range
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();

            if (startTime && endTime && startTime >= endTime) {
                $('#end_time').addClass('is-invalid');
                $('#end_time').after('<div class="invalid-feedback">End time must be after start time</div>');
                isValid = false;
            }

            // Validate date is not in the past
            const bookingDate = new Date($('#booking_date').val());
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (bookingDate < today) {
                $('#booking_date').addClass('is-invalid');
                $('#booking_date').after('<div class="invalid-feedback">Booking date cannot be in the past</div>');
                isValid = false;
            }

            return isValid;
        }

        function showPreview() {
            const formData = {
                title: $('#title').val(),
                purpose: $('#purpose').val(),
                booking_date: $('#booking_date').val(),
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val(),
                room: $('#room_id option:selected').text(),
                participant_count: $('#participant_count').val(),
                description: $('#description').val(),
                participants: []
            };

            // Collect participants
            $('.participant-item').each(function() {
                const name = $(this).find('input[name*="[name]"]').val();
                const email = $(this).find('input[name*="[email]"]').val();
                if (name) {
                    formData.participants.push({
                        name,
                        email
                    });
                }
            });

            const date = new Date(formData.booking_date);
            const formattedDate = date.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            let participantsHtml = '';
            if (formData.participants.length > 0) {
                participantsHtml = `
           <div class="mt-3">
               <h6>Participants:</h6>
               <ul class="list-unstyled">
                   ${formData.participants.map(p => 
                       `<li><i class="fas fa-user me-2"></i>${p.name}${p.email ? ` (${p.email})` : ''}</li>`
                   ).join('')}
               </ul>
           </div>
       `;
            }

            const previewHtml = `
       <div class="row">
           <div class="col-md-6">
               <h6 class="text-primary">Meeting Details</h6>
               <table class="table table-borderless table-sm">
                   <tr>
                       <td><strong>Title:</strong></td>
                       <td>${formData.title}</td>
                   </tr>
                   <tr>
                       <td><strong>Purpose:</strong></td>
                       <td>${formData.purpose}</td>
                   </tr>
                   <tr>
                       <td><strong>Date:</strong></td>
                       <td>${formattedDate}</td>
                   </tr>
                   <tr>
                       <td><strong>Time:</strong></td>
                       <td>${formData.start_time} - ${formData.end_time}</td>
                   </tr>
                   <tr>
                       <td><strong>Duration:</strong></td>
                       <td>${calculateDuration(formData.start_time, formData.end_time)}</td>
                   </tr>
               </table>
           </div>
           <div class="col-md-6">
               <h6 class="text-primary">Room & Participants</h6>
               <table class="table table-borderless table-sm">
                   <tr>
                       <td><strong>Room:</strong></td>
                       <td>${formData.room}</td>
                   </tr>
                   <tr>
                       <td><strong>Participants:</strong></td>
                       <td>${formData.participant_count} people</td>
                   </tr>
               </table>
               
               ${participantsHtml}
           </div>
       </div>
       
       ${formData.description ? `
               <div class="mt-3">
                   <h6 class="text-primary">Additional Notes</h6>
                   <p class="text-muted">${formData.description}</p>
               </div>
           ` : ''}
       
       <div class="alert alert-info mt-3">
           <i class="fas fa-info-circle me-2"></i>
           Your booking will be submitted for approval. You will receive a notification once it's reviewed.
       </div>
   `;

            $('#previewContent').html(previewHtml);

            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        }

        function calculateDuration(startTime, endTime) {
            const start = new Date('2000-01-01 ' + startTime);
            const end = new Date('2000-01-01 ' + endTime);
            const diffMs = end - start;
            const diffMins = Math.floor(diffMs / 60000);
            const hours = Math.floor(diffMins / 60);
            const minutes = diffMins % 60;

            if (hours > 0) {
                return minutes > 0 ? `${hours} jam ${minutes} menit` : `${hours} jam`;
            }
            return `${minutes} menit`;
        }

        // Auto-suggest for participant names (if user exists in system)
        $(document).on('input', 'input[name*="[name]"]', function() {
            const input = $(this);
            const query = input.val();

            if (query.length > 2) {
                $.get('{{ route('api.users.search') }}', {
                        q: query
                    })
                    .done(function(users) {
                        // Simple autocomplete implementation
                        // You can enhance this with a proper autocomplete library
                        if (users.length > 0) {
                            // Show suggestions
                            console.log('Found users:', users);
                        }
                    });
            }
        });
    </script>
@endpush
