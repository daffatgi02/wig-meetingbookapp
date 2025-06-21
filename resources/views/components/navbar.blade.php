{{-- resources/views/components/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="fas fa-calendar-check me-2"></i>
            <span>{{ config('app.name', 'PT WIG - Booking Room Meeting') }}</span>
        </a>

        <!-- Mobile toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Menu -->
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'text-primary fw-bold' : '' }}" 
                           href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    
                    @if(!auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('bookings.*') ? 'text-primary fw-bold' : '' }}" 
                               href="{{ route('bookings.index') }}">
                                <i class="fas fa-calendar-alt me-1"></i>My Bookings
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('calendar.*') ? 'text-primary fw-bold' : '' }}" 
                           href="{{ route('calendar.index') }}">
                            <i class="fas fa-calendar me-1"></i>Calendar
                        </a>
                    </li>

                    @if(auth()->user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'text-primary fw-bold' : '' }}" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Admin Panel
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.bookings.pending') }}">
                                    <i class="fas fa-clock me-2"></i>Pending Approvals
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.bookings.index') }}">
                                    <i class="fas fa-calendar-check me-2"></i>All Bookings
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.rooms.index') }}">
                                    <i class="fas fa-door-open me-2"></i>Rooms
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users me-2"></i>Users
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                    <i class="fas fa-cogs me-2"></i>Settings
                                </a></li>
                            </ul>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('calendar.index') }}">
                            <i class="fas fa-calendar me-1"></i>Calendar
                        </a>
                    </li>
                @endauth
            </ul>

            <!-- Right Menu -->
            <ul class="navbar-nav">
                @auth
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notificationDropdown">
                            <i class="fas fa-bell"></i>
                            @if($unreadNotifications > 0)
                                <span class="notification-badge">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                            @include('notifications.partials.dropdown')
                        </div>
                    </li>

                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ auth()->user()->name }}</h6></li>
                            <li><small class="dropdown-item-text text-muted">{{ auth()->user()->email }}</small></li>
                            <li><hr class="dropdown-divider"></li>
                            
                            @if(!auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('bookings.index') }}">
                                    <i class="fas fa-calendar-alt me-2"></i>My Bookings
                                </a></li>
                            @endif
                            
                            <li><a class="dropdown-item" href="{{ route('notifications.index') }}">
                                <i class="fas fa-bell me-2"></i>Notifications
                                @if($unreadNotifications > 0)
                                    <span class="badge bg-danger ms-1">{{ $unreadNotifications }}</span>
                                @endif
                            </a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>