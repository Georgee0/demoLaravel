<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDriverRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'driver_license' => 'required|string|unique:drivers,driver_license|max:15',
            'phone' => 'nullable|string|max:11',
            'transporter_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Driver name is required.',
            'driver_license.required' => 'Driver license is required.',
            'driver_license.unique' => 'This driver license is already in use.',
            'transporter_id.required' => 'Transporter ID is required.',
            'transporter_id.exists' => 'The selected transporter does not exist.',
        ];
    }
}
