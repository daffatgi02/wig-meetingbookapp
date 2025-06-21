<?php
// app/Http/Requests/UpdateBookingRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Setting;
use Carbon\Carbon;

class UpdateBookingRequest extends FormRequest
{
    public function authorize()
    {
        $booking = $this->route('booking');
        
        // Admin dapat mengedit semua booking
        if (auth()->user()->isAdmin()) {
            return true;
        }
        
        // User hanya dapat mengedit booking miliknya dan yang masih bisa diedit
        return $booking->user_id === auth()->id() && $booking->isEditable();
    }

    public function rules()
    {
        $operatingStart = Setting::get('operating_hours_start', '08:00');
        $operatingEnd = Setting::get('operating_hours_end', '18:00');
        $minDuration = Setting::get('minimum_booking_duration', 30);
        $maxDuration = Setting::get('maximum_booking_duration', 180);

        return [
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'purpose' => 'required|string|max:255',
            'participant_count' => 'required|integer|min:1|max:100',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => [
                'required',
                'date_format:H:i',
                "after_or_equal:{$operatingStart}",
                "before:{$operatingEnd}"
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                "before_or_equal:{$operatingEnd}"
            ],
            'participants' => 'nullable|array',
            'participants.*.name' => 'required_with:participants|string|max:255',
            'participants.*.email' => 'nullable|email|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $booking = $this->route('booking');
            
            // Validasi durasi
            if ($this->start_time && $this->end_time) {
                $start = Carbon::createFromFormat('H:i', $this->start_time);
                $end = Carbon::createFromFormat('H:i', $this->end_time);
                $duration = $start->diffInMinutes($end);
                
                $minDuration = Setting::get('minimum_booking_duration', 30);
                $maxDuration = Setting::get('maximum_booking_duration', 180);
                
                if ($duration < $minDuration) {
                    $validator->errors()->add('end_time', "Durasi minimum pemesanan adalah {$minDuration} menit.");
                }
                
                if ($duration > $maxDuration) {
                    $validator->errors()->add('end_time', "Durasi maksimum pemesanan adalah {$maxDuration} menit.");
                }
            }

            // Validasi konflik jadwal (exclude booking yang sedang diedit)
            if ($this->room_id && $this->booking_date && $this->start_time && $this->end_time) {
                $room = \App\Models\Room::find($this->room_id);
                if ($room && !$room->isAvailable($this->booking_date, $this->start_time, $this->end_time, $booking->id)) {
                    $conflicts = $room->getConflictingBookings($this->booking_date, $this->start_time, $this->end_time, $booking->id);
                    $conflictInfo = $conflicts->map(function ($conflictBooking) {
                        return $conflictBooking->user->name . ' (' . $conflictBooking->start_time . '-' . $conflictBooking->end_time . ')';
                    })->implode(', ');
                    
                    $validator->errors()->add('start_time', "Ruangan sudah dipesan pada waktu tersebut oleh: {$conflictInfo}");
                }
            }

            // Validasi kapasitas ruangan
            if ($this->room_id && $this->participant_count) {
                $room = \App\Models\Room::find($this->room_id);
                if ($room && $this->participant_count > $room->capacity) {
                    $validator->errors()->add('participant_count', "Jumlah peserta melebihi kapasitas ruangan ({$room->capacity} orang).");
                }
            }
        });
    }
}