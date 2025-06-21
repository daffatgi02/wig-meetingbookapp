<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use App\Models\Setting;

class NotificationService
{
    public function sendBookingRequestNotification(Booking $booking)
    {
        if (!Setting::get('notification_website_enabled', true)) {
            return;
        }

        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'booking_request',
                'title' => 'Permintaan Pemesanan Ruangan Baru',
                'message' => "{$booking->user->name} meminta persetujuan untuk memesan {$booking->room->name} pada {$booking->booking_date->format('d/m/Y')} jam {$booking->start_time}-{$booking->end_time}",
                'data' => [
                    'booking_id' => $booking->id,
                    'room_name' => $booking->room->name,
                    'user_name' => $booking->user->name,
                ],
            ]);
        }
    }

    public function sendBookingApprovedNotification(Booking $booking)
    {
        if (!Setting::get('notification_website_enabled', true)) {
            return;
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking_approved',
            'title' => 'Pemesanan Ruangan Disetujui',
            'message' => "Pemesanan Anda untuk {$booking->room->name} pada {$booking->booking_date->format('d/m/Y')} jam {$booking->start_time}-{$booking->end_time} telah disetujui",
            'data' => [
                'booking_id' => $booking->id,
                'room_name' => $booking->room->name,
            ],
        ]);
    }

    public function sendBookingRejectedNotification(Booking $booking)
    {
        if (!Setting::get('notification_website_enabled', true)) {
            return;
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking_rejected',
            'title' => 'Pemesanan Ruangan Ditolak',
            'message' => "Pemesanan Anda untuk {$booking->room->name} pada {$booking->booking_date->format('d/m/Y')} jam {$booking->start_time}-{$booking->end_time} ditolak. Alasan: {$booking->rejection_reason}",
            'data' => [
                'booking_id' => $booking->id,
                'room_name' => $booking->room->name,
                'rejection_reason' => $booking->rejection_reason,
            ],
        ]);
    }

    public function sendBookingUpdateNotification(Booking $booking)
    {
        if (!Setting::get('notification_website_enabled', true)) {
            return;
        }

        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'booking_updated',
                'title' => 'Pemesanan Ruangan Diperbarui',
                'message' => "{$booking->user->name} telah memperbarui pemesanan {$booking->room->name} dan memerlukan persetujuan ulang",
                'data' => [
                    'booking_id' => $booking->id,
                    'room_name' => $booking->room->name,
                    'user_name' => $booking->user->name,
                ],
            ]);
        }
    }

    public function sendBookingCancelledNotification(Booking $booking)
    {
        if (!Setting::get('notification_website_enabled', true)) {
            return;
        }

        $admins = User::admins()->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'booking_cancelled',
                'title' => 'Pemesanan Ruangan Dibatalkan',
                'message' => "{$booking->user->name} telah membatalkan pemesanan {$booking->room->name} pada {$booking->booking_date->format('d/m/Y')} jam {$booking->start_time}-{$booking->end_time}",
                'data' => [
                    'booking_id' => $booking->id,
                    'room_name' => $booking->room->name,
                    'user_name' => $booking->user->name,
                ],
            ]);
        }
    }

    public function sendBookingMovedNotification(Booking $booking, User $admin)
    {
        if (!Setting::get('notification_website_enabled', true)) {
            return;
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking_moved',
            'title' => 'Pemesanan Ruangan Dipindahkan',
            'message' => "Admin {$admin->name} telah memindahkan pemesanan Anda ke {$booking->room->name} pada {$booking->booking_date->format('d/m/Y')} jam {$booking->start_time}-{$booking->end_time}",
            'data' => [
                'booking_id' => $booking->id,
                'room_name' => $booking->room->name,
                'admin_name' => $admin->name,
            ],
        ]);
    }

    public function sendBookingReminderNotification(Booking $booking)
    {
        if (!Setting::get('notification_website_enabled', true)) {
            return;
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking_reminder',
            'title' => 'Pengingat Pemesanan Ruangan',
            'message' => "Pengingat: Pemesanan Anda untuk {$booking->room->name} akan dimulai dalam 30 menit ({$booking->start_time})",
            'data' => [
                'booking_id' => $booking->id,
                'room_name' => $booking->room->name,
            ],
        ]);
    }

    public function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return $notification;
    }

    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }
}
