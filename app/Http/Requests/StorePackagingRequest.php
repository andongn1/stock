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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du conditionnement est obligatoire.',
            'name.unique' => 'Ce conditionnement existe deja.',
        ];
    }
}
