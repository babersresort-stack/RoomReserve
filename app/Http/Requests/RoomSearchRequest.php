<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'check_in_at' => ['nullable', 'date'],
            'check_out_at' => ['nullable', 'date', 'after:check_in_at'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:20'],
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:available,maintenance,unavailable'],
        ];
    }
}
