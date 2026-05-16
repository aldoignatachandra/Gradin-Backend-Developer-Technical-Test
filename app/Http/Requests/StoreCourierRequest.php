<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourierRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('couriers', 'email')],
            'phone' => ['required', 'string', 'max:20'],
            'level' => ['required', 'integer', 'between:1,5'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'registered_at' => ['nullable', 'date'],
        ];
    }
}
