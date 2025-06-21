{{-- resources/views/admin/rooms/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Room')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-dark mb-1">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Edit Room: {{ $room->name }}
                    </h3>
                    <p class="text-muted mb-0">Update room information and settings</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.rooms.show', $room) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>
                        View Room
                    </a>
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Rooms
                    </a>
                </div>
            </div>

            <!-- Current Room Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1">Current Room Status</h6>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $room->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas fa-{{ $room->is_active ? 'check' : 'pause' }} me-1"></i>
                                    {{ $room->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $room->bookings()->where('booking_date', '>=', today())->count() }} upcoming bookings
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <small class="text-muted">Last updated: {{ $room->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.rooms.update', $room) }}" method="POST" id="roomForm">
                @csrf
                @method('PUT')

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Room Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Basic Information -->
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-bold">
                                    <i class="fas fa-door-open me-1"></i>
                                    Room Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $room->name) }}" required
                                    placeholder="e.g., Conference Room A, Meeting Room 1">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Enter a unique name for this meeting room</div>
                            </div>

                            <div class="col-md-4">
                                <label for="capacity" class="form-label fw-bold">
                                    <i class="fas fa-users me-1"></i>
                                    Capacity <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                    id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" required
                                    min="1" max="1000">
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum number of people</div>

                                @if ($room->bookings()->where('booking_date', '>=', today())->where('participant_count', '>', old('capacity', $room->capacity))->exists())
                                    <div class="alert alert-warning border-0 mt-2 py-2">
                                        <small>
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <strong>Warning:</strong> Some upcoming bookings exceed this capacity
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label for="location" class="form-label fw-bold">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Location <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                    id="location" name="location" value="{{ old('location', $room->location) }}" required
                                    placeholder="e.g., 2nd Floor, Building A, Near Elevator">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Specific location or floor information</div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-bold">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Description
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="Optional: Brief description of the room, special features, etc.">{{ old('description', $room->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional room description or special notes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facilities Section -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2 text-primary"></i>
                            Room Facilities
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Common Facilities -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-list-check me-1"></i>
                                    Available Facilities
                                </label>
                                <div class="form-text mb-3">Select all facilities available in this room</div>
                            </div>

                            @php
                                $commonFacilities = [
                                    'Projector' => 'fas fa-video',
                                    'Smart TV' => 'fas fa-tv',
                                    'Whiteboard' => 'fas fa-chalkboard',
                                    'Air Conditioning' => 'fas fa-snowflake',
                                    'Wi-Fi' => 'fas fa-wifi',
                                    'Sound System' => 'fas fa-volume-up',
                                    'Microphone' => 'fas fa-microphone',
                                    'Video Conference' => 'fas fa-video',
                                    'Flipchart' => 'fas fa-chart-line',
                                    'Markers' => 'fas fa-pen',
                                    'Power Outlets' => 'fas fa-plug',
                                    'Natural Light' => 'fas fa-sun',
                                ];

                                $currentFacilities = old('facilities', $room->facilities ?? []);
                                $customFacilities = collect($currentFacilities)
                                    ->diff(array_keys($commonFacilities))
                                    ->filter();
                            @endphp

                            @foreach ($commonFacilities as $facility => $icon)
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]"
                                            value="{{ $facility }}" id="facility_{{ $loop->index }}"
                                            {{ in_array($facility, $currentFacilities) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="facility_{{ $loop->index }}">
                                            <i class="{{ $icon }} me-2 text-primary"></i>
                                            {{ $facility }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Custom Facilities -->
                        <div class="mt-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-plus-circle me-1"></i>
                                Custom Facilities
                            </label>
                            <div class="form-text mb-3">Add any additional facilities not listed above</div>

                            <div id="customFacilities">
                                @foreach ($customFacilities as $facility)
                                    <div class="input-group mb-2 custom-facility-item">
                                        <input type="text" class="form-control" name="custom_facilities[]"
                                            value="{{ $facility }}" placeholder="Enter custom facility name">
                                        <button class="btn btn-outline-danger" type="button"
                                            onclick="removeCustomFacility(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addCustomFacility()">
                                <i class="fas fa-plus me-1"></i>
                                Add Custom Facility
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-cog me-2 text-primary"></i>
                            Room Settings
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">
                                        <i class="fas fa-power-off me-1"></i>
                                        Room is Active
                                    </label>
                                    <div class="form-text">Only active rooms can be booked by users</div>

                                    @if ($room->bookings()->where('booking_date', '>=', today())->where('status', 'approved')->exists())
                                        <div class="alert alert-info border-0 mt-2 py-2">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                <strong>Note:</strong> This room has upcoming approved bookings.
                                                Deactivating will not cancel existing bookings.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Booking Statistics
                                    </div>
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <div class="fw-bold text-primary">{{ $room->bookings()->count() }}</div>
                                            <small class="text-muted">Total</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold text-warning">
                                                {{ $room->bookings()->where('booking_date', '>=', today())->count() }}
                                            </div>
                                            <small class="text-muted">Upcoming</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold text-success">
                                                {{ $room->bookings()->where('status', 'completed')->count() }}</div>
                                            <small class="text-muted">Completed</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Log -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2 text-primary"></i>
                            Change Summary
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div id="changeLog" class="alert alert-info border-0" style="display: none;">
                            <strong>Pending Changes:</strong>
                            <ul id="changeList" class="mb-0 mt-2"></ul>
                        </div>
                        <div id="noChanges" class="text-muted text-center py-2">
                            <i class="fas fa-info-circle me-1"></i>
                            No changes made yet
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.rooms.show', $room) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancel
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="previewChanges()">
                                    <i class="fas fa-eye me-2"></i>
                                    Preview Changes
                                </button>
                                <button type="submit" class="btn btn-primary" id="saveButton" disabled>
                                    <i class="fas fa-save me-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 bg-primary bg-opacity-10">
                    <h5 class="modal-title text-primary" id="previewModalLabel">
                        <i class="fas fa-eye me-2"></i>
                        Preview Changes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Current</h6>
                            <div id="currentPreview"></div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">After Changes</h6>
                            <div id="updatedPreview"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
                    <button type="button" class="btn btn-primary" onclick="$('#roomForm').submit()">
                        <i class="fas fa-save me-2"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Store original values
            const originalValues = {
                name: @json($room->name),
                location: @json($room->location),
                capacity: {{ $room->capacity }},
                description: @json($room->description),
                facilities: @json($room->facilities ?? []),
                is_active: {{ $room->is_active ? 'true' : 'false' }}
            };

            // Track changes
            function trackChanges() {
                const changes = [];

                // Check each field
                if ($('#name').val() !== originalValues.name) {
                    changes.push(`Name: "${originalValues.name}" → "${$('#name').val()}"`);
                }

                if ($('#location').val() !== originalValues.location) {
                    changes.push(`Location: "${originalValues.location}" → "${$('#location').val()}"`);
                }

                if (parseInt($('#capacity').val()) !== originalValues.capacity) {
                    changes.push(`Capacity: ${originalValues.capacity} → ${$('#capacity').val()}`);
                }

                if ($('#description').val() !== originalValues.description) {
                    changes.push(`Description updated`);
                }

                // Check facilities
                const currentFacilities = [];
                $('input[name="facilities[]"]:checked').each(function() {
                    currentFacilities.push($(this).val());
                });
                $('input[name="custom_facilities[]"]').each(function() {
                    if ($(this).val().trim()) {
                        currentFacilities.push($(this).val().trim());
                    }
                });

                const facilityDiff = $(originalValues.facilities).not(currentFacilities).get()
                    .concat($(currentFacilities).not(originalValues.facilities).get());

                if (facilityDiff.length > 0) {
                    changes.push('Facilities updated');
                }

                // Check active status
                const isActive = $('#is_active').is(':checked');
                if (isActive !== originalValues.is_active) {
                    changes.push(
                        `Status: ${originalValues.is_active ? 'Active' : 'Inactive'} → ${isActive ? 'Active' : 'Inactive'}`
                        );
                }

                // Update UI
                if (changes.length > 0) {
                    $('#changeList').empty();
                    changes.forEach(change => {
                        $('#changeList').append(`<li>${change}</li>`);
                    });
                    $('#changeLog').show();
                    $('#noChanges').hide();
                    $('#saveButton').prop('disabled', false);
                } else {
                    $('#changeLog').hide();
                    $('#noChanges').show();
                    $('#saveButton').prop('disabled', true);
                }

                return changes;
            }

            // Bind change tracking to form inputs
            $('#name, #location, #capacity, #description, #is_active').on('input change', trackChanges);
            $(document).on('change', 'input[name="facilities[]"], input[name="custom_facilities[]"]', trackChanges);

            // Initial check
            trackChanges();

            // Form validation
            $('#roomForm').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }

                // Show loading state
                $('#saveButton').html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop(
                    'disabled', true);
            });
        });

        function addCustomFacility() {
            const html = `
       <div class="input-group mb-2 custom-facility-item">
           <input type="text" 
                  class="form-control" 
                  name="custom_facilities[]" 
                  placeholder="Enter custom facility name">
           <button class="btn btn-outline-danger" type="button" onclick="removeCustomFacility(this)">
               <i class="fas fa-times"></i>
           </button>
       </div>
   `;
            $('#customFacilities').append(html);
        }

        function removeCustomFacility(button) {
            $(button).closest('.custom-facility-item').remove();
            trackChanges(); // Re-track changes after removal
        }

        function validateForm() {
            let isValid = true;

            // Reset previous validation states
            $('.is-invalid').removeClass('is-invalid');

            // Validate required fields
            const requiredFields = ['name', 'location', 'capacity'];
            requiredFields.forEach(field => {
                const input = $(`#${field}`);
                if (!input.val().trim()) {
                    input.addClass('is-invalid');
                    isValid = false;
                }
            });

            // Validate capacity
            const capacity = parseInt($('#capacity').val());
            if (capacity < 1 || capacity > 1000) {
                $('#capacity').addClass('is-invalid');
                showToast('error', 'Capacity must be between 1 and 1000');
                isValid = false;
            }

            if (!isValid) {
                showToast('error', 'Please fix the errors and try again');
            }

            return isValid;
        }

        function previewChanges() {
            if (!validateForm()) {
                return;
            }

            // Generate current room preview
            const currentRoom = {
                name: @json($room->name),
                location: @json($room->location),
                capacity: {{ $room->capacity }},
                description: @json($room->description),
                facilities: @json($room->facilities ?? []),
                is_active: {{ $room->is_active ? 'true' : 'false' }}
            };

            // Generate updated room preview
            const currentFacilities = [];
            $('input[name="facilities[]"]:checked').each(function() {
                currentFacilities.push($(this).val());
            });
            $('input[name="custom_facilities[]"]').each(function() {
                if ($(this).val().trim()) {
                    currentFacilities.push($(this).val().trim());
                }
            });

            const updatedRoom = {
                name: $('#name').val(),
                location: $('#location').val(),
                capacity: $('#capacity').val(),
                description: $('#description').val(),
                facilities: currentFacilities,
                is_active: $('#is_active').is(':checked')
            };

            $('#currentPreview').html(generateRoomPreviewHTML(currentRoom));
            $('#updatedPreview').html(generateRoomPreviewHTML(updatedRoom));

            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        }

        function generateRoomPreviewHTML(room) {
            const facilitiesHTML = room.facilities.length > 0 ?
                room.facilities.map(f => `<span class="badge bg-light text-dark border me-1 mb-1">${f}</span>`).join('') :
                '<span class="text-muted">No facilities</span>';

            return `
       <div class="card border">
           <div class="card-header bg-light">
               <div class="d-flex justify-content-between align-items-start">
                   <div>
                       <h6 class="text-primary mb-1">${room.name}</h6>
                       <small class="text-muted">
                           <i class="fas fa-map-marker-alt me-1"></i>
                           ${room.location}
                       </small>
                   </div>
                   <span class="badge ${room.is_active ? 'bg-success' : 'bg-secondary'}">
                       ${room.is_active ? 'Active' : 'Inactive'}
                   </span>
               </div>
           </div>
           <div class="card-body p-3">
               <div class="mb-2">
                   <strong>Capacity:</strong> ${room.capacity} people
               </div>
               ${room.description ? `
                       <div class="mb-2">
                           <strong>Description:</strong> ${room.description}
                       </div>
                   ` : ''}
               <div>
                   <strong>Facilities:</strong><br>
                   ${facilitiesHTML}
               </div>
           </div>
       </div>
   `;
        }

        // Auto-save functionality
        let autoSaveTimeout;

        function autoSave() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                    const changes = trackChanges();
                    if (changes.length > 0) {
                        // Save as draft
                        const formData = {
                            name: $('#name').val(),
                            location: $('#location').val(),
                            capacity: $('#capacity').val(),
                            description: $('#description').val(),
                            facilities: [],
                            custom_facilities: [],
                            is_active: $('#is_active').is(':checked()
                            };

                            // Get facilities
                            $('input[name="facilities[]"]:checked').each(function() {
                                formData.facilities.push($(this).val());
                            });
                            $('input[name="custom_facilities[]"]').each(function() {
                                if ($(this).val().trim()) {
                                    formData.custom_facilities.push($(this).val().trim());
                                }
                            });

                            localStorage.setItem('room_edit_draft_{{ $room->id }}', JSON.stringify(formData));
                        }
                    }, 5000); // Save after 5 seconds of inactivity
            }

            // Bind auto-save to form changes
            $('#name, #location, #capacity, #description, #is_active').on('input change', autoSave);
            $(document).on('change', 'input[name="facilities[]"], input[name="custom_facilities[]"]', autoSave);

            // Restore draft on page load
            $(document).ready(function() {
                const draft = localStorage.getItem('room_edit_draft_{{ $room->id }}');
                if (draft) {
                    try {
                        const data = JSON.parse(draft);

                        // Check if draft is different from current values
                        let isDifferent = false;
                        if (data.name !== $('#name').val() ||
                            data.location !== $('#location').val() ||
                            data.capacity != $('#capacity').val() ||
                            data.description !== $('#description').val()) {
                            isDifferent = true;
                        }

                        if (isDifferent) {
                            Swal.fire({
                                title: 'Restore Draft?',
                                text: 'We found unsaved changes. Would you like to restore them?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, restore',
                                cancelButtonText: 'No, discard'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    restoreEditDraft(data);
                                } else {
                                    localStorage.removeItem('room_edit_draft_{{ $room->id }}');
                                }
                            });
                        }
                    } catch (e) {
                        localStorage.removeItem('room_edit_draft_{{ $room->id }}');
                    }
                }
            });

            function restoreEditDraft(data) {
                $('#name').val(data.name || '');
                $('#location').val(data.location || '');
                $('#capacity').val(data.capacity || '');
                $('#description').val(data.description || '');
                $('#is_active').prop('checked', data.is_active !== false);

                // Restore facilities
                $('input[name="facilities[]"]').prop('checked', false);
                if (data.facilities) {
                    data.facilities.forEach(facility => {
                        $(`input[name="facilities[]"][value="${facility}"]`).prop('checked', true);
                    });
                }

                // Restore custom facilities
                $('#customFacilities').empty();
                if (data.custom_facilities) {
                    data.custom_facilities.forEach(facility => {
                        if (facility) {
                            addCustomFacility();
                            $('#customFacilities .custom-facility-item:last input').val(facility);
                        }
                    });
                }

                trackChanges();
                showToast('success', 'Draft restored successfully');
            }

            // Clear draft on successful submission
            $('#roomForm').on('submit', function() {
                localStorage.removeItem('room_edit_draft_{{ $room->id }}');
            });

            // Warning for unsaved changes
            let hasUnsavedChanges = false;
            $('#roomForm input, #roomForm textarea, #roomForm select').on('change input', function() {
                hasUnsavedChanges = $('#saveButton').prop('disabled') === false;
            });

            $(window).on('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    return 'You have unsaved changes. Are you sure you want to leave?';
                }
            });

            // Remove warning when form is submitted
            $('#roomForm').on('submit', function() {
                hasUnsavedChanges = false;
            });
    </script>
@endpush
