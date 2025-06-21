<?php
// app/Http/Requests/UpdateRoomRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isAdmin();
    }

    public function rules()
    {
        $roomId = $this->route('room')->id;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms', 'name')->ignore($roomId)
            ],
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:1000',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:100',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }
}