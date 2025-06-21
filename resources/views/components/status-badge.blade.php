{{-- resources/views/components/status-badge.blade.php --}}
@php
    $statusClasses = [
        'draft' => 'status-cancelled',
        'pending' => 'status-pending',
        'approved' => 'status-approved',
        'rejected' => 'status-rejected',
        'ongoing' => 'status-ongoing',
        'completed' => 'status-completed',
        'cancelled' => 'status-cancelled',
    ];
    
    $statusLabels = [
        'draft' => 'Draft',
        'pending' => 'Menunggu Persetujuan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'ongoing' => 'Sedang Berlangsung',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
    
    $statusIcons = [
        'draft' => 'fas fa-edit',
        'pending' => 'fas fa-clock',
        'approved' => 'fas fa-check-circle',
        'rejected' => 'fas fa-times-circle',
        'ongoing' => 'fas fa-play-circle',
        'completed' => 'fas fa-check-double',
        'cancelled' => 'fas fa-ban',
    ];
@endphp

<span class="status-badge {{ $statusClasses[$status] ?? 'status-cancelled' }}">
    <i class="{{ $statusIcons[$status] ?? 'fas fa-question' }} me-1"></i>
    {{ $statusLabels[$status] ?? 'Unknown' }}
</span>