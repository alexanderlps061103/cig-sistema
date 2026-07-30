<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombres'   => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'email'     => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($this->user()->id),
            ],
            'username'  => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('usuarios', 'username')->ignore($this->user()->id),
            ],
            // Puedes agregar más campos de persona si lo deseas (teléfono, sexo, etc.)
        ];
    }

    /**
     * Permite obtener el usuario autenticado como instancia de Usuario.
     */
    public function usuarioAutenticado(): Usuario
    {
        return $this->user();
    }
}
