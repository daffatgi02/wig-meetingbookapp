<?php
// app/Http/Requests/StoreRoomRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:rooms,name',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:1000',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:100',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama ruangan harus diisi.',
            'name.unique' => 'Nama ruangan sudah digunakan.',
            'location.required' => 'Lokasi ruangan harus diisi.',
            'capacity.required' => 'Kapasitas ruangan harus diisi.',
            'capacity.min' => 'Kapasitas ruangan minimal 1 orang.',
        ];
    }
}