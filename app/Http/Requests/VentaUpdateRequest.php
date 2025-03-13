<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaUpdateRequest extends FormRequest
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
        $rules = [
            'codigo'        => 'sometimes|string|max:50|unique:ventas,codigo,' . $this->route('venta')->codigo,
            'nombre_cl'     => 'sometimes|string|max:255',
            'num_iden_cl'   => 'sometimes|string|regex:/^\d+$/|min:8|max:20',
            'correo_cl'     => 'nullable|email|max:255',
            'user_id'       => 'exists:users,id',
            'fecha_hora'    => 'sometimes|date',
        ];

        if ($this->has('detalles')) {
            $rules['detalles'] = 'sometimes|array|min:1';
            $rules['detalles.*.product_id']   = 'sometimes|exists:products,id';
            $rules['detalles.*.cantidad']      = 'sometimes|integer|min:1';
        }

        return $rules;
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages(): array
    {
        return [
            'codigo.required' => 'El código de la venta es obligatorio.',
            'nombre_cl.required' => 'El nombre del cliente es obligatorio.',
            'num_iden_cl.required' => 'El número de identificación del cliente es obligatorio.',
            'num_iden_cl.regex' => 'El número de identificación debe contener solo números.',
            'num_iden_cl.min' => 'El número de identificación debe tener al menos 8 caracteres.',
            'num_iden_cl.max' => 'El número de identificación no debe superar los 20 caracteres.',
            'correo_cl.email' => 'El correo debe ser una dirección de email válida.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'fecha_hora.required' => 'La fecha y hora de la venta son obligatorias.',
            'fecha_hora.date' => 'La fecha y hora deben ser válidas.',
            'detalles.required' => 'Debe incluir al menos un detalle de venta.',
            'detalles.array' => 'Los detalles deben ser un arreglo.',
            'detalles.*.product_id.required' => 'El ID del producto es obligatorio.',
            'detalles.*.product_id.exists' => 'El producto seleccionado no existe.',
            'detalles.*.cantidad.required' => 'La cantidad del producto es obligatoria.',
            'detalles.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}
