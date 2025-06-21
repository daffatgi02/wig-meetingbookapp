<?php
// app/Policies/BookingPolicy.php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Semua user bisa lihat list booking
    }

    public function view(User $user, Booking $booking)
    {
        // Admin bisa lihat semua, user hanya bisa lihat miliknya
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    public function create(User $user)
    {
        return $user->is_active;
    }

    public function update(User $user, Booking $booking)
    {
        // Admin bisa edit semua
        if ($user->isAdmin()) {
            return true;
        }

        // User hanya bisa edit miliknya dan yang masih bisa diedit
        return $user->id === $booking->user_id && 
               $booking->isEditable() && 
               $user->is_active;
    }

    public function delete(User $user, Booking $booking)
    {
        // Admin bisa cancel/delete semua
        if ($user->isAdmin()) {
            return true;
        }

        // User hanya bisa cancel miliknya dan yang masih bisa dibatalkan
        return $user->id === $booking->user_id && 
               $booking->isCancellable() && 
               $user->is_active;
    }

    public function approve(User $user, Booking $booking)
    {
        return $user->isAdmin() && $booking->status === 'pending';
    }

    public function reject(User $user, Booking $booking)
    {
        return $user->isAdmin() && $booking->status === 'pending';
    }

    public function move(User $user, Booking $booking)
    {
        return $user->isAdmin();
    }
}