{{-- resources/views/components/modal-login.blade.php --}}
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-primary" id="loginModalLabel">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login Required
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="fas fa-lock text-primary" style="font-size: 3rem;"></i>
                </div>
                <h6 class="mb-3">Untuk melakukan booking ruangan, Anda perlu login terlebih dahulu</h6>
                <p class="text-muted mb-4">
                    Silakan login dengan akun Anda atau daftar jika belum memiliki akun.
                </p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Daftar Akun Baru
                    </a>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Anda tetap bisa melihat jadwal dan ketersediaan ruangan tanpa login
                </small>
            </div>
        </div>
    </div>
</div>