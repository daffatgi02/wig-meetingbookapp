<?php
// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'title',
        'description',
        'purpose',
        'participant_count',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'requires_reapproval',
        'created_by_admin',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
        'requires_reapproval' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adminCreator()
    {
        return $this->belongsTo(User::class, 'created_by_admin');
    }

    public function participants()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeToday($query)
    {
        return $query->where('booking_date', Carbon::today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', Carbon::today());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function isEditable()
    {
        $now = Carbon::now();
        $bookingDateTime = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
        
        return $bookingDateTime->isFuture() && 
               in_array($this->status, ['pending', 'approved']) &&
               !$this->isOngoing();
    }

    public function isCancellable()
    {
        $now = Carbon::now();
        $bookingDateTime = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
        
        return $bookingDateTime->isFuture() && 
               !in_array($this->status, ['cancelled', 'completed']);
    }

    public function isOngoing()
    {
        $now = Carbon::now();
        $bookingDate = $this->booking_date->format('Y-m-d');
        $startDateTime = Carbon::parse($bookingDate . ' ' . $this->start_time);
        $endDateTime = Carbon::parse($bookingDate . ' ' . $this->end_time);
        
        return $now->between($startDateTime, $endDateTime) && $this->status === 'approved';
    }

    public function getDurationInMinutes()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function getStatusColor()
    {
        $colors = [
            'draft' => '#6c757d',
            'pending' => '#ffc107',
            'approved' => '#1976D2',
            'rejected' => '#dc3545',
            'ongoing' => '#fd7e14',
            'completed' => '#28a745',
            'cancelled' => '#dc3545',
        ];

        return $colors[$this->status] ?? '#6c757d';
    }

    public function getStatusLabel()
    {
        $labels = [
            'draft' => 'Draft',
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'ongoing' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    // Update status otomatis berdasarkan waktu
    public function updateStatusBasedOnTime()
    {
        if ($this->status === 'approved') {
            $now = Carbon::now();
            $bookingDate = $this->booking_date->format('Y-m-d');
            $startDateTime = Carbon::parse($bookingDate . ' ' . $this->start_time);
            $endDateTime = Carbon::parse($bookingDate . ' ' . $this->end_time);

            if ($now->between($startDateTime, $endDateTime)) {
                $this->update(['status' => 'ongoing']);
            } elseif ($now->isAfter($endDateTime)) {
                $this->update(['status' => 'completed']);
            }
        }
    }
}