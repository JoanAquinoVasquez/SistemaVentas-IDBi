<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:255|unique:products',
            'nombre' => 'required|string|max:255',
            'precio_unitario' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ];
    }
}
