<?php
// app/helpers.php

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('timeSlots')) {
    function timeSlots($start = '08:00', $end = '18:00', $interval = 30)
    {
        $slots = [];
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $start);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $end);
        
        while ($startTime->lt($endTime)) {
            $slots[$startTime->format('H:i')] = $startTime->format('H:i');
            $startTime->addMinutes($interval);
        }
        
        return $slots;
    }
}

if (!function_exists('formatDuration')) {
    function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return $hours . ' jam' . ($mins > 0 ? ' ' . $mins . ' menit' : '');
        }
        
        return $mins . ' menit';
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status)
    {
        $badges = [
            'draft' => 'badge-secondary',
            'pending' => 'badge-warning',
            'approved' => 'badge-primary',
            'rejected' => 'badge-danger',
            'ongoing' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
        ];
        
        return $badges[$status] ?? 'badge-secondary';
    }
}

if (!function_exists('canBook')) {
    function canBook()
    {
        if (!auth()->check()) {
            return false;
        }
        
        return auth()->user()->is_active;
    }
}

if (!function_exists('isOperatingHours')) {
    function isOperatingHours($time = null)
    {
        $time = $time ?: now()->format('H:i');
        $start = getSetting('operating_hours_start', '08:00');
        $end = getSetting('operating_hours_end', '18:00');
        
        return $time >= $start && $time <= $end;
    }
}