<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'family_id' => 'required|exists:families,id',
            'sub_family_id' => 'required|exists:sub_families,id',
            'unit_id' => 'required|exists:units,id',
            'packaging_id' => 'required|exists:packagings,id',
            'packaging_unit_quantity' => 'nullable|numeric|min:0',
            'vat_id' => 'required|exists:vats,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'article est obligatoire.',
            'family_id.required' => 'La famille est obligatoire.',
            'sub_family_id.required' => 'La sous-famille est obligatoire.',
            'unit_id.required' => 'L\'unite est obligatoire.',
            'packaging_id.required' => 'Le conditionnement est obligatoire.',
            'packaging_unit_quantity.numeric' => 'La quantite unitaire doit etre un nombre.',
            'packaging_unit_quantity.min' => 'La quantite ne peut pas etre negative.',
            'vat_id.required' => 'La TVA est obligatoire.',
        ];
    }
}
