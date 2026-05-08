<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorepersonaRequest extends FormRequest
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
            'ci' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'max:50'],
            'sangre' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'string', 'max:600'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
