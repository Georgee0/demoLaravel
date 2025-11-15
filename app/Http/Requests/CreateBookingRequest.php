<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
            'transporter_id' => 'required|exists:users,id',
            'driver_id' => 'required|exists:drivers,id',
            'truck_id' => 'required|exists:trucks,id',
            'terminal' => 'required|string|max:100',
            'booking_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'transporter_id.required' => 'Transporter ID is required.',
            'transporter_id.exists' => 'The selected transporter does not exist.',
            'driver_id.required' => 'Driver ID is required.',
            'driver_id.exists' => 'The selected driver does not exist.',
            'truck_id.required' => 'Truck ID is required.',
            'truck_id.exists' => 'The selected truck does not exist.',
            'terminal.required' => 'Terminal is required.',
            'booking_date.required' => 'Booking date is required.',
            'booking_date.date' => 'Booking date must be a valid date.',
        ];
}
}