{{-- resources/views/components/sidebar.blade.php --}}
<aside class="admin-sidebar">
    <div class="px-3 mb-4">
        <h6 class="text-muted fw-bold text-uppercase small">Admin Panel</h6>
    </div>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('admin.dashboard') }}" 
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>

        <!-- Bookings Section -->
        <li class="mt-3">
            <div class="px-3 mb-2">
                <small class="text-muted fw-bold text-uppercase">Booking Management</small>
            </div>
        </li>
        
        <li>
            <a href="{{ route('admin.bookings.pending') }}" 
               class="{{ request()->routeIs('admin.bookings.pending') ? 'active' : '' }}">
                <i class="fas fa-clock text-warning"></i>
                Pending Approvals
                @php
                    $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        
        <li>
            <a href="{{ route('admin.bookings.index') }}" 
               class="{{ request()->routeIs('admin.bookings.index') || request()->routeIs('admin.bookings.show') || request()->routeIs('admin.bookings.edit') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                All Bookings
            </a>
        </li>
        
        <li>
            <a href="{{ route('admin.bookings.create') }}" 
               class="{{ request()->routeIs('admin.bookings.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i>
                Create Booking
            </a>
        </li>

        <!-- Rooms Section -->
        <li class="mt-3">
            <div class="px-3 mb-2">
                <small class="text-muted fw-bold text-uppercase">Room Management</small>
            </div>
        </li>
        
        <li>
            <a href="{{ route('admin.rooms.index') }}" 
               class="{{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                <i class="fas fa-door-open"></i>
                Manage Rooms
            </a>
        </li>

        <!-- Users Section -->
        <li class="mt-3">
            <div class="px-3 mb-2">
                <small class="text-muted fw-bold text-uppercase">User Management</small>
            </div>
        </li>
        
        <li>
            <a href="{{ route('admin.users.index') }}" 
               class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                Manage Users
            </a>
        </li>

        <!-- System Section -->
        <li class="mt-3">
            <div class="px-3 mb-2">
                <small class="text-muted fw-bold text-uppercase">System</small>
            </div>
        </li>
        
        <li>
            <a href="{{ route('admin.settings.index') }}" 
               class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i>
                Settings
            </a>
        </li>

        <!-- Quick Links -->
        <li class="mt-3">
            <div class="px-3 mb-2">
                <small class="text-muted fw-bold text-uppercase">Quick Links</small>
            </div>
        </li>
        
        <li>
            <a href="{{ route('calendar.index') }}">
                <i class="fas fa-calendar"></i>
                Calendar View
            </a>
        </li>
        
        <li>
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-eye"></i>
                Public Dashboard
            </a>
        </li>
    </ul>

    <!-- Admin Stats Summary -->
    <div class="px-3 mt-4">
        <div class="card bg-light border-0">
            <div class="card-body p-3">
                <h6 class="card-title text-muted small mb-3">Quick Stats</h6>
                
                @php
                    $todayBookings = \App\Models\Booking::where('booking_date', today())->count();
                    $activeRooms = \App\Models\Room::where('is_active', true)->count();
                    $totalUsers = \App\Models\User::where('is_active', true)->count();
                @endphp
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Today's Bookings</small>
                    <span class="badge bg-primary">{{ $todayBookings }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Active Rooms</small>
                    <span class="badge bg-success">{{ $activeRooms }}</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Total Users</small>
                    <span class="badge bg-info">{{ $totalUsers }}</span>
                </div>
            </div>
        </div>
    </div>
</aside>