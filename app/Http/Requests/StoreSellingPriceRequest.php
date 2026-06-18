<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellingPriceRequest extends FormRequest
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
            'price_ttc' => 'required|numeric|min:0',
            'sale_type' => 'required|string|in:per_unit,per_packaging',
            'quantity' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price_ttc.required' => 'Le prix TTC est obligatoire.',
            'price_ttc.numeric' => 'Le prix TTC doit etre un nombre.',
            'price_ttc.min' => 'Le prix TTC doit etre superieur ou egal a 0.',
            'sale_type.required' => 'Le type de vente est obligatoire.',
            'sale_type.in' => 'Le type de vente doit etre soit "Par unite" soit "Par conditionnement".',
            'quantity.numeric' => 'La quantite doit etre un nombre.',
            'quantity.min' => 'La quantite doit etre superieure ou egale a 0.',
            'currency.required' => 'La devise est obligatoire.',
            'currency.size' => 'La devise doit comporter 3 caracteres.',
        ];
    }
}
