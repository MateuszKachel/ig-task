<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AirlineEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_from' => 'sometimes|date_format:Y-m-d',
            'date_to' => 'sometimes|date_format:Y-m-d',
            'next_week_only' => 'sometimes|in:0,1',
            'activity_type' => 'sometimes|in:FLT,DO,SBY,UNK',
            'departure_airport' => 'sometimes|string|max:3',
            'arrival_airport' => 'sometimes|string|max:3',
        ];
    }
}
