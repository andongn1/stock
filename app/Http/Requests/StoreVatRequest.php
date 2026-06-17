<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rate' => 'required|numeric|min:0|max:100',
            'label' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'rate.required' => 'Le taux de TVA est obligatoire.',
            'rate.numeric' => 'Le taux doit etre un nombre.',
            'rate.min' => 'Le taux ne peut pas etre negatif.',
            'rate.max' => 'Le taux ne peut pas depasser 100.',
            'label.required' => 'Le libelle de la TVA est obligatoire.',
        ];
    }
}
