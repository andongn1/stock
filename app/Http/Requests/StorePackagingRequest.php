<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackagingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:packagings,name',
            'unit_id' => 'nullable|exists:units,id',
            'unit_quantity' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du conditionnement est obligatoire.',
            'name.unique' => 'Ce conditionnement existe deja.',
            'unit_id.exists' => 'L\'unite selectionnee n\'existe pas.',
            'unit_quantity.numeric' => 'La quantite doit etre un nombre.',
            'unit_quantity.min' => 'La quantite ne peut pas etre negative.',
        ];
    }
}
