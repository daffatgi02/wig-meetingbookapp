{{-- resources/views/admin/rooms/partials/form.blade.php --}}
<div class="row g-4">
    <!-- Basic Information -->
    <div class="col-md-8">
        <label for="name" class="form-label fw-bold">
            <i class="fas fa-door-open me-1"></i>
            Room Name <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               id="name" 
               name="name" 
               value="{{ old('name', $room->name ?? '') }}" 
               required 
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
        <input type="number" 
               class="form-control @error('capacity') is-invalid @enderror" 
               id="capacity" 
               name="capacity" 
               value="{{ old('capacity', $room->capacity ?? 10) }}" 
               required 
               min="1" 
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
        <input type="text" 
               class="form-control @error('location') is-invalid @enderror" 
               id="location" 
               name="location" 
               value="{{ old('location', $room->location ?? '') }}" 
               required 
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
        <textarea class="form-control @error('description') is-invalid @enderror" 
                  id="description" 
                  name="description" 
                  rows="3" 
                  placeholder="Optional: Brief description of the room, special features, etc.">{{ old('description', $room->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Optional room description or special notes</div>
    </div>
</div>

<!-- Facilities Section -->
<div class="mt-4">
    <h6 class="fw-bold mb-3">
        <i class="fas fa-cogs me-1"></i>
        Room Facilities
    </h6>
    
    <div class="row g-3">
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
                'Natural Light' => 'fas fa-sun'
            ];
            
            $currentFacilities = old('facilities', $room->facilities ?? []);
        @endphp

        @foreach($commonFacilities as $facility => $icon)
            <div class="col-md-4 col-lg-3">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="facilities[]" 
                           value="{{ $facility }}" 
                           id="facility_{{ $loop->index }}"
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
            @if(isset($room) && $room->facilities)
                @php
                    $customFacilities = collect($room->facilities)->diff(array_keys($commonFacilities))->filter();
                @endphp
                @foreach($customFacilities as $facility)
                    <div class="input-group mb-2 custom-facility-item">
                        <input type="text" 
                               class="form-control" 
                               name="custom_facilities[]" 
                               value="{{ $facility }}" 
                               placeholder="Enter custom facility name">
                        <button class="btn btn-outline-danger" type="button" onclick="removeCustomFacility(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
        
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addCustomFacility()">
            <i class="fas fa-plus me-1"></i>
            Add Custom Facility
        </button>
    </div>
</div>

<!-- Settings -->
<div class="mt-4">
    <h6 class="fw-bold mb-3">
        <i class="fas fa-cog me-1"></i>
        Room Settings
    </h6>
    
    <div class="form-check form-switch">
        <input class="form-check-input" 
               type="checkbox" 
               id="is_active" 
               name="is_active" 
               value="1" 
               {{ old('is_active', $room->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label fw-bold" for="is_active">
            <i class="fas fa-power-off me-1"></i>
            Room is Active
        </label>
        <div class="form-text">Only active rooms can be booked by users</div>
    </div>
</div>

<script>
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
    document.getElementById('customFacilities').insertAdjacentHTML('beforeend', html);
}

function removeCustomFacility(button) {
    button.closest('.custom-facility-item').remove();
}
</script>