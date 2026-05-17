<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('couriers', 'email')->ignore($this->route('courier'))],
            'phone' => ['sometimes', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'level' => ['sometimes', 'integer', 'between:1,5'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'registered_at' => ['nullable', 'date'],
        ];
    }
}
