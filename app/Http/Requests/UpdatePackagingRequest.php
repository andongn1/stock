<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackagingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:packagings,name,'.$this->packaging->id,
            'unit_id' => 'nullable|exists:units,id',
            'unit_quantity' => 'nullable|numeric|min:0',
        ];
    }
}
