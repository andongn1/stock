<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePriceHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_id' => 'required|exists:articles,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ];
    }

    public function messages(): array
    {
        return [
            'article_id.required' => 'L\'article est obligatoire.',
            'supplier_id.required' => 'Le fournisseur est obligatoire.',
            'price.required' => 'Le prix est obligatoire.',
            'price.min' => 'Le prix ne peut pas etre negatif.',
            'currency.required' => 'La devise est obligatoire.',
            'valid_from.required' => 'La date de debut est obligatoire.',
            'valid_to.after_or_equal' => 'La date de fin doit etre posterieure ou egale a la date de debut.',
        ];
    }
}
