{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Meeting Room Booking') }} - Admin @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-red: #dc2626;
            --secondary-red: #b91c1c;
            --light-red: #fee2e2;
            --dark-red: #991b1b;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-red) !important;
        }

        .btn-primary {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--secondary-red);
            border-color: var(--secondary-red);
        }

        .text-primary { color: var(--primary-red) !important; }
        .bg-primary { background-color: var(--primary-red) !important; }

        .admin-wrapper {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        .admin-sidebar {
            width: var(--sidebar-width);
            background-color: white;
            border-right: 1px solid var(--border-color);
            padding: 1.5rem 0;
            position: fixed;
            height: calc(100vh - 80px);
            overflow-y: auto;
            z-index: 1000;
        }

        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 0;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--light-red);
            color: var(--primary-red);
            border-right: 3px solid var(--primary-red);
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 12px;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: white !important;
            position: relative;
            z-index: 1100;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: var(--light-red); color: var(--dark-red); }
        .status-ongoing { background-color: #dbeafe; color: #1e40af; }
        .status-completed { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-cancelled { background-color: #f3f4f6; color: #374151; }

        /* Mobile responsiveness */
        @media (max-width: 992px) {
            .admin-sidebar {
                margin-left: -280px;
                transition: margin-left 0.3s ease;
            }

            .admin-sidebar.show {
                margin-left: 0;
            }

            .admin-main {
                margin-left: 0;
                padding: 1rem;
            }

            .sidebar-toggle {
                position: fixed;
                top: 100px;
                left: 10px;
                z-index: 1200;
                background-color: var(--primary-red);
                color: white;
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        @include('components.navbar')

        <div class="admin-wrapper">
            @include('components.sidebar')

            <main class="admin-main">
                @include('components.alerts')
                @yield('content')
            </main>
        </div>

        <!-- Mobile sidebar toggle -->
        <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showToast(type, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: type,
                title: message
            });
        }

        function confirmAction(title, text, confirmText = 'Ya, Lanjutkan!') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            });
        }

        // Mobile sidebar toggle
        $(document).ready(function() {
            $('#sidebarToggle').click(function() {
                $('.admin-sidebar').toggleClass('show');
            });

            // Auto hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>