<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ci' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'string', 'max:50'],
            'sangre' => ['nullable', 'string', 'max:20', 'in:A Rh (+),A Rh (-),B Rh (+),B Rh (-),AB Rh (+),AB Rh (-),O Rh (+),O Rh (-)'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:3072'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
