<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:units,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'unite est obligatoire.',
            'name.unique' => 'Cette unite existe deja.',
        ];
    }
}
