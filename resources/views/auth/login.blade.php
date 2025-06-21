{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white text-center py-4">
                    <h4 class="text-primary mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login
                    </h4>
                    <p class="text-muted mt-2 mb-0">Masuk ke akun Anda</p>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>
                                Email Address
                            </label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="Masukkan email Anda">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                Password
                            </label>
                            <div class="input-group">
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Masukkan password Anda">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Login
                            </button>
                        </div>

                        <!-- Links -->
                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="text-muted text-decoration-none small" href="{{ route('password.request') }}">
                                    <i class="fas fa-key me-1"></i>
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light text-center py-3">
                    <span class="text-muted">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold">
                        Daftar Sekarang
                    </a>
                </div>
            </div>

            <!-- Demo Accounts Info -->
            <div class="card mt-3 border-info">
                <div class="card-body p-3">
                    <h6 class="text-info mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Demo Accounts
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">
                                <strong>Admin:</strong><br>
                                admin@meetingroom.local<br>
                                admin123
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">
                                <strong>User:</strong><br>
                                user@meetingroom.local<br>
                                user123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Auto-fill demo credentials
    $('.demo-login').click(function(e) {
        e.preventDefault();
        const email = $(this).data('email');
        const password = $(this).data('password');
        
        $('#email').val(email);
        $('#password').val(password);
        showToast('info', 'Demo credentials filled!');
    });
});
</script>
@endpush