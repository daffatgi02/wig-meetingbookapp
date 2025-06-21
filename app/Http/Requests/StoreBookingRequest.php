<?php
// app/Http/Requests/StoreBookingRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Setting;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
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

            // Validasi konflik jadwal
            if ($this->room_id && $this->booking_date && $this->start_time && $this->end_time) {
                $room = \App\Models\Room::find($this->room_id);
                if ($room && !$room->isAvailable($this->booking_date, $this->start_time, $this->end_time)) {
                    $conflicts = $room->getConflictingBookings($this->booking_date, $this->start_time, $this->end_time);
                    $conflictInfo = $conflicts->map(function ($booking) {
                        return $booking->user->name . ' (' . $booking->start_time . '-' . $booking->end_time . ')';
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

    public function messages()
    {
        return [
            'room_id.required' => 'Ruangan harus dipilih.',
            'room_id.exists' => 'Ruangan tidak tersedia.',
            'title.required' => 'Topik kegiatan harus diisi.',
            'purpose.required' => 'Tujuan pemesanan harus diisi.',
            'participant_count.required' => 'Jumlah peserta harus diisi.',
            'participant_count.min' => 'Jumlah peserta minimal 1 orang.',
            'booking_date.required' => 'Tanggal pemesanan harus diisi.',
            'booking_date.after_or_equal' => 'Tidak dapat memesan untuk tanggal yang sudah lewat.',
            'start_time.required' => 'Waktu mulai harus diisi.',
            'start_time.after_or_equal' => 'Waktu mulai harus dalam jam operasional.',
            'end_time.required' => 'Waktu selesai harus diisi.',
            'end_time.after' => 'Waktu selesai harus lebih dari waktu mulai.',
            'end_time.before_or_equal' => 'Waktu selesai harus dalam jam operasional.',
        ];
    }
}