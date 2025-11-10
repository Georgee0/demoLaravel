<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:50',
            'body' => 'required|string|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title MUST BE a string.',
            'title.min' => 'The title MUST BE at least :min characters.',
            'title.max' => 'The title may not be greater than :max characters.',
            
            'body.required' => 'The body field is required.',
            'body.string' => 'The body MUST BE a string.',
            'body.min' => 'The body MUST BE at least :min characters.',
        ];
    }
}
