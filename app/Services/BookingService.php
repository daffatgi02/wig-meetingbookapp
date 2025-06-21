<?php
// app/Services/BookingService.php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected $notificationService;
    protected $activityLogService;

    public function __construct(NotificationService $notificationService, ActivityLogService $activityLogService)
    {
        $this->notificationService = $notificationService;
        $this->activityLogService = $activityLogService;
    }

    public function createBooking(array $data, User $user, bool $isAdminCreated = false)
    {
        return DB::transaction(function () use ($data, $user, $isAdminCreated) {
            // Tentukan status default
            $requireApproval = Setting::get('require_approval', true);
            $autoApproveAdmin = Setting::get('auto_approve_admin_bookings', true);
            
            $status = 'pending';
            $approvedBy = null;
            $approvedAt = null;
            
            if ($isAdminCreated && $autoApproveAdmin) {
                $status = 'approved';
                $approvedBy = auth()->id();
                $approvedAt = now();
            } elseif (!$requireApproval) {
                $status = 'approved';
            }

            // Buat booking
            $booking = Booking::create([
                'user_id' => $user->id,
                'room_id' => $data['room_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'purpose' => $data['purpose'],
                'participant_count' => $data['participant_count'],
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'status' => $status,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'created_by_admin' => $isAdminCreated ? auth()->id() : null,
            ]);

            // Tambahkan participants
            if (!empty($data['participants'])) {
                $this->addParticipants($booking, $data['participants']);
            }

            // Log activity
            $this->activityLogService->log('created', $booking, null, $booking->toArray());

            // Kirim notifikasi
            if ($status === 'pending') {
                $this->notificationService->sendBookingRequestNotification($booking);
            } elseif ($status === 'approved') {
                $this->notificationService->sendBookingApprovedNotification($booking);
            }

            return $booking->load(['room', 'user', 'participants']);
        });
    }

    public function updateBooking(Booking $booking, array $data)
    {
        return DB::transaction(function () use ($booking, $data) {
            $oldData = $booking->toArray();
            
            // Cek apakah ada perubahan yang memerlukan approval ulang
            $requireReapproval = Setting::get('require_reapproval_on_edit', true);
            $significantChanges = $this->hasSignificantChanges($booking, $data);
            
            $updateData = [
                'room_id' => $data['room_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'purpose' => $data['purpose'],
                'participant_count' => $data['participant_count'],
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ];

            // Jika ada perubahan signifikan dan memerlukan approval ulang
            if ($requireReapproval && $significantChanges && !auth()->user()->isAdmin()) {
                $updateData['status'] = 'pending';
                $updateData['approved_by'] = null;
                $updateData['approved_at'] = null;
                $updateData['requires_reapproval'] = true;
            }

            $booking->update($updateData);

            // Update participants
            $booking->participants()->delete();
            if (!empty($data['participants'])) {
                $this->addParticipants($booking, $data['participants']);
            }

            // Log activity
            $this->activityLogService->log('updated', $booking, $oldData, $booking->fresh()->toArray());

            // Kirim notifikasi jika perlu approval ulang
            if ($requireReapproval && $significantChanges && !auth()->user()->isAdmin()) {
                $this->notificationService->sendBookingUpdateNotification($booking);
            }

            return $booking->load(['room', 'user', 'participants']);
        });
    }

    public function approveBooking(Booking $booking, User $approver)
    {
        return DB::transaction(function () use ($booking, $approver) {
            $oldData = $booking->toArray();
            
            $booking->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'requires_reapproval' => false,
            ]);

            // Log activity
            $this->activityLogService->log('approved', $booking, $oldData, $booking->fresh()->toArray());

            // Kirim notifikasi
            $this->notificationService->sendBookingApprovedNotification($booking);

            return $booking;
        });
    }

    public function rejectBooking(Booking $booking, User $approver, string $reason)
    {
        return DB::transaction(function () use ($booking, $approver, $reason) {
            $oldData = $booking->toArray();
            
            $booking->update([
                'status' => 'rejected',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // Log activity
            $this->activityLogService->log('rejected', $booking, $oldData, $booking->fresh()->toArray());

            // Kirim notifikasi
            $this->notificationService->sendBookingRejectedNotification($booking);

            return $booking;
        });
    }

    public function cancelBooking(Booking $booking, User $user)
    {
        return DB::transaction(function () use ($booking, $user) {
            $oldData = $booking->toArray();
            
            $booking->update(['status' => 'cancelled']);

            // Log activity
            $this->activityLogService->log('cancelled', $booking, $oldData, $booking->fresh()->toArray());

            // Kirim notifikasi ke admin jika dibatalkan oleh user
            if (!$user->isAdmin()) {
                $this->notificationService->sendBookingCancelledNotification($booking);
            }

            return $booking;
        });
    }

    public function moveBooking(Booking $booking, array $data, User $admin)
    {
        return DB::transaction(function () use ($booking, $data, $admin) {
            $oldData = $booking->toArray();
            
            $booking->update([
                'room_id' => $data['room_id'],
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);

            // Log activity
            $this->activityLogService->log('moved', $booking, $oldData, $booking->fresh()->toArray());

            // Kirim notifikasi ke user
            $this->notificationService->sendBookingMovedNotification($booking, $admin);

            return $booking;
        });
    }

    public function getAvailableRooms($date, $startTime, $endTime)
    {
        return \App\Models\Room::active()
            ->whereDoesntHave('bookings', function ($query) use ($date, $startTime, $endTime) {
                $query->where('booking_date', $date)
                      ->where('status', '!=', 'cancelled')
                      ->where('status', '!=', 'rejected')
                      ->where(function ($q) use ($startTime, $endTime) {
                          $q->where(function ($subQ) use ($startTime, $endTime) {
                              $subQ->where('start_time', '<', $endTime)
                                   ->where('end_time', '>', $startTime);
                          });
                      });
            })
            ->get();
    }

    public function updateBookingStatuses()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        // Update status menjadi ongoing
        Booking::where('status', 'approved')
               ->where('booking_date', $today)
               ->where('start_time', '<=', $currentTime)
               ->where('end_time', '>', $currentTime)
               ->update(['status' => 'ongoing']);

        // Update status menjadi completed
        Booking::where('status', 'ongoing')
               ->where('booking_date', $today)
               ->where('end_time', '<=', $currentTime)
               ->update(['status' => 'completed']);

        // Update status completed untuk booking yang sudah lewat
        Booking::where('status', 'approved')
               ->where(function ($query) use ($today, $currentTime) {
                   $query->where('booking_date', '<', $today)
                         ->orWhere(function ($q) use ($today, $currentTime) {
                             $q->where('booking_date', $today)
                               ->where('end_time', '<=', $currentTime);
                         });
               })
               ->update(['status' => 'completed']);
    }

    private function addParticipants(Booking $booking, array $participants)
    {
        foreach ($participants as $participant) {
            $userId = null;
            
            // Cek apakah participant adalah user terdaftar
            if (!empty($participant['email'])) {
                $user = User::where('email', $participant['email'])->first();
                if ($user) {
                    $userId = $user->id;
                }
            }

            BookingParticipant::create([
                'booking_id' => $booking->id,
                'name' => $participant['name'],
                'email' => $participant['email'] ?? null,
                'user_id' => $userId,
            ]);
        }
    }

    private function hasSignificantChanges(Booking $booking, array $data)
    {
        return $booking->room_id != $data['room_id'] ||
               $booking->booking_date != $data['booking_date'] ||
               $booking->start_time != $data['start_time'] ||
               $booking->end_time != $data['end_time'] ||
               $booking->participant_count != $data['participant_count'];
    }
}