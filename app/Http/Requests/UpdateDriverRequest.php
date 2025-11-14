<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
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
        $driverId = optional($this->route('driver'))->id ?? ($this->driver->id ?? null);
        return [
            'name' => 'sometimes|required|string|max:255',
            'driver_license' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('drivers', 'driver_license')->ignore($driverId),
            ],
            'phone' => 'sometimes|required|string|max:20',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('drivers', 'email')->ignore($driverId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Driver name is required.',
            'driver_license.required' => 'Driver license is required.',
            'driver_license.unique' => 'This driver license is already in use.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
        ];
    }
}
