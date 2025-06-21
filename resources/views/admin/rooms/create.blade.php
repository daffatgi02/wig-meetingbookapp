{{-- resources/views/admin/rooms/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Add New Room')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="text-dark mb-1">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Add New Room
                    </h3>
                    <p class="text-muted mb-0">Create a new meeting room for booking</p>
                </div>
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Rooms
                </a>
            </div>

            <form action="{{ route('admin.rooms.store') }}" method="POST" id="roomForm">
                @csrf

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
                                    id="name" name="name" value="{{ old('name') }}" required
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
                                    id="capacity" name="capacity" value="{{ old('capacity', 10) }}" required min="1"
                                    max="1000">
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum number of people</div>
                            </div>

                            <div class="col-12">
                                <label for="location" class="form-label fw-bold">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Location <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                    id="location" name="location" value="{{ old('location') }}" required
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
                                    rows="3" placeholder="Optional: Brief description of the room, special features, etc.">{{ old('description') }}</textarea>
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
                            @endphp

                            @foreach ($commonFacilities as $facility => $icon)
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]"
                                            value="{{ $facility }}" id="facility_{{ $loop->index }}"
                                            {{ in_array($facility, old('facilities', [])) ? 'checked' : '' }}>
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
                                @if (old('custom_facilities'))
                                    @foreach (old('custom_facilities') as $index => $facility)
                                        @if ($facility)
                                            <div class="input-group mb-2 custom-facility-item">
                                                <input type="text" class="form-control" name="custom_facilities[]"
                                                    value="{{ $facility }}" placeholder="Enter custom facility name">
                                                <button class="btn btn-outline-danger" type="button"
                                                    onclick="removeCustomFacility(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
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
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">
                                        <i class="fas fa-power-off me-1"></i>
                                        Room is Active
                                    </label>
                                    <div class="form-text">Only active rooms can be booked by users</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Room Preview
                                    </div>
                                    <div class="fw-bold text-dark" id="roomPreviewName">Room Name</div>
                                    <div class="small text-muted" id="roomPreviewLocation">Location</div>
                                    <div class="small text-primary" id="roomPreviewCapacity">
                                        <i class="fas fa-users me-1"></i>Capacity: 0 people
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancel
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="previewRoom()">
                                    <i class="fas fa-eye me-2"></i>
                                    Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Create Room
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
                        Room Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="roomPreviewContent">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
                    <button type="button" class="btn btn-primary" onclick="$('#roomForm').submit()">
                        <i class="fas fa-save me-2"></i>
                        Create Room
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Real-time preview update
            updatePreview();

            $('#name, #location, #capacity').on('input', function() {
                updatePreview();
            });

            // Form validation
            $('#roomForm').on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
            });

            // Auto-generate room name suggestions
            $('#location').on('blur', function() {
                const location = $(this).val();
                const name = $('#name').val();

                if (location && !name) {
                    const suggestions = generateNameSuggestions(location);
                    if (suggestions.length > 0) {
                        showNameSuggestions(suggestions);
                    }
                }
            });
        });

        function updatePreview() {
            const name = $('#name').val() || 'Room Name';
            const location = $('#location').val() || 'Location';
            const capacity = $('#capacity').val() || '0';

            $('#roomPreviewName').text(name);
            $('#roomPreviewLocation').text(location);
            $('#roomPreviewCapacity').html(`<i class="fas fa-users me-1"></i>Capacity: ${capacity} people`);
        }

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

            // Validate unique name (you might want to add AJAX check here)

            if (!isValid) {
                showToast('error', 'Please fix the errors and try again');
            }

            return isValid;
        }

        function previewRoom() {
            if (!validateForm()) {
                return;
            }

            const formData = new FormData($('#roomForm')[0]);
            const facilities = [];

            // Get selected facilities
            $('input[name="facilities[]"]:checked').each(function() {
                facilities.push($(this).val());
            });

            // Get custom facilities
            $('input[name="custom_facilities[]"]').each(function() {
                if ($(this).val().trim()) {
                    facilities.push($(this).val().trim());
                }
            });

            const roomData = {
                name: $('#name').val(),
                location: $('#location').val(),
                capacity: $('#capacity').val(),
                description: $('#description').val(),
                facilities: facilities,
                is_active: $('#is_active').is(':checked')
            };

            generatePreviewHTML(roomData);

            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        }

        function generatePreviewHTML(room) {
            const facilitiesHTML = room.facilities.length > 0 ?
                room.facilities.map(f => `<span class="badge bg-light text-dark border me-1 mb-1">${f}</span>`).join('') :
                '<span class="text-muted">No facilities specified</span>';

            const html = `
       <div class="card border-0">
           <div class="card-header bg-white">
               <div class="d-flex justify-content-between align-items-start">
                   <div>
                       <h5 class="text-primary mb-1">${room.name}</h5>
                       <div class="text-muted">
                           <i class="fas fa-map-marker-alt me-1"></i>
                           ${room.location}
                       </div>
                   </div>
                   <span class="badge ${room.is_active ? 'bg-success' : 'bg-secondary'}">
                       <i class="fas fa-${room.is_active ? 'check' : 'pause'} me-1"></i>
                       ${room.is_active ? 'Active' : 'Inactive'}
                   </span>
               </div>
           </div>
           <div class="card-body">
               <div class="row g-3 mb-3">
                   <div class="col-6">
                       <div class="text-center p-3 bg-light rounded">
                           <div class="h4 text-primary mb-1">${room.capacity}</div>
                           <small class="text-muted">Max Capacity</small>
                       </div>
                   </div>
                   <div class="col-6">
                       <div class="text-center p-3 bg-light rounded">
                           <div class="h4 text-success mb-1">0</div>
                           <small class="text-muted">Current Bookings</small>
                       </div>
                   </div>
               </div>
               
               ${room.description ? `
                       <div class="mb-3">
                           <h6 class="text-dark">Description</h6>
                           <p class="text-muted">${room.description}</p>
                       </div>
                   ` : ''}
               
               <div class="mb-3">
                   <h6 class="text-dark">
                       <i class="fas fa-cogs me-1"></i>
                       Facilities
                   </h6>
                   <div class="mt-2">
                       ${facilitiesHTML}
                   </div>
               </div>
           </div>
       </div>
   `;

            $('#roomPreviewContent').html(html);
        }

        function generateNameSuggestions(location) {
            const suggestions = [];
            const words = location.split(' ');
            const floorMatch = location.match(/(\d+)(st|nd|rd|th)?\s*floor/i);

            if (floorMatch) {
                const floor = floorMatch[1];
                suggestions.push(`Conference Room ${floor}A`);
                suggestions.push(`Meeting Room ${floor}B`);
                suggestions.push(`${floor}F Conference Room`);
            }

            if (words.length > 0) {
                suggestions.push(`${words[0]} Conference Room`);
                suggestions.push(`${words[0]} Meeting Room`);
            }

            return suggestions.slice(0, 3);
        }

        function showNameSuggestions(suggestions) {
            const html = suggestions.map(name =>
                `<button type="button" class="btn btn-outline-primary btn-sm me-2 mb-2" onclick="selectSuggestion('${name}')">${name}</button>`
            ).join('');

            Swal.fire({
                title: 'Room Name Suggestions',
                html: `
           <div class="text-start mb-3">
               <p class="text-muted">Based on the location, here are some name suggestions:</p>
               ${html}
           </div>
       `,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'No thanks',
                allowOutsideClick: true
            });
        }

        function selectSuggestion(name) {
            $('#name').val(name);
            updatePreview();
            Swal.close();
            showToast('success', 'Room name updated');
        }

        // Auto-save draft functionality (optional)
        function saveDraft() {
            const formData = {
                name: $('#name').val(),
                location: $('#location').val(),
                capacity: $('#capacity').val(),
                description: $('#description').val(),
                facilities: $('input[name="facilities[]"]:checked').map(function() {
                    return this.value;
                }).get(),
                custom_facilities: $('input[name="custom_facilities[]"]').map(function() {
                    return this.value;
                }).get().filter(v => v),
                is_active: $('#is_active').is(':checked')
            };

            localStorage.setItem('room_draft', JSON.stringify(formData));
        }

        // Auto-save every 30 seconds
        setInterval(saveDraft, 30000);

        // Load draft on page load
        $(document).ready(function() {
            const draft = localStorage.getItem('room_draft');
            if (draft) {
                try {
                    const data = JSON.parse(draft);

                    // Ask user if they want to restore
                    if (data.name || data.location) {
                        Swal.fire({
                            title: 'Restore Draft?',
                            text: 'We found a saved draft. Would you like to restore it?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, restore',
                            cancelButtonText: 'No, start fresh'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                restoreDraft(data);
                            } else {
                                localStorage.removeItem('room_draft');
                            }
                        });
                    }
                } catch (e) {
                    localStorage.removeItem('room_draft');
                }
            }
        });

        function restoreDraft(data) {
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
            if (data.custom_facilities) {
                data.custom_facilities.forEach(facility => {
                    if (facility) {
                        addCustomFacility();
                        $('#customFacilities .custom-facility-item:last input').val(facility);
                    }
                });
            }

            updatePreview();
            showToast('success', 'Draft restored successfully');
        }

        // Clear draft on successful submission
        $('#roomForm').on('submit', function() {
            localStorage.removeItem('room_draft');
        });
    </script>
@endpush
