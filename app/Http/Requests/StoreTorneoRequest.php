<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTorneoRequest extends FormRequest
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
            'fecha_inicio' => ['nullable', 'date', 'after_or_equal:today'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'nombre' => ['required', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:1000'],
            'sistema_competencia' => ['required', 'exists:sistema_competencia,id'],
            'modalidad_puntaje' => ['nullable', 'string', 'max:100'],
            'costo_inscripcion_organizacion' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'costo_inscripcion_competidor' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'organiza' => ['nullable', 'string', 'max:255'],
            'persona_id' => ['required', 'exists:personas,id'],
            'lugar' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'status' => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
