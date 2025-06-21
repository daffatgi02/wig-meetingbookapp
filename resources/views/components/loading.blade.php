{{-- resources/views/components/loading.blade.php --}}
<div class="d-flex justify-content-center align-items-center p-5" id="loadingSpinner">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted">{{ $message ?? 'Loading...' }}</p>
    </div>
</div>