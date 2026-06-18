<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
}
