<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTruckRequest extends FormRequest
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
            'plate_number' => 'required|string|unique:trucks,plate_number|max:15',
            'model' => 'required|string|max:50',
            'color' => 'required|string|max:10',
            'transporter_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'plate_number.required' => 'Plate number is required.',
            'plate_number.unique' => 'This plate number is already in use.',
            'model.required' => 'Truck model is required.',
            'color.required' => 'Truck color is required.',
            'transporter_id.required' => 'Transporter ID is required.',
            'transporter_id.exists' => 'The selected transporter does not exist.',
        ];
    }
}
