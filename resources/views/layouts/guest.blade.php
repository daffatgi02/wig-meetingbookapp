{{-- resources/views/layouts/guest.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Meeting Room Booking') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">

    <!-- Custom CSS (sama seperti app.blade.php) -->
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

        .text-primary {
            color: var(--primary-red) !important;
        }

        .bg-primary {
            background-color: var(--primary-red) !important;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 12px;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: white !important;
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

        .guest-banner {
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            color: white;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .main-content {
            padding-top: 2rem;
            padding-bottom: 2rem;
            min-height: calc(100vh - 80px);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        @include('components.navbar')

        <main class="main-content">
            <div class="container-fluid">
                @include('components.alerts')
                
                <!-- Guest Banner -->
                <div class="guest-banner text-center">
                    <h4 class="mb-2">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Sistem Pemesanan Ruang Meeting
                    </h4>
                    <p class="mb-3">Lihat ketersediaan ruangan dan jadwal meeting yang sedang berlangsung</p>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Login untuk Booking
                    </button>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    @include('components.modal-login')

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

        // Show login modal when trying to book
        function requireLogin() {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }

        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>