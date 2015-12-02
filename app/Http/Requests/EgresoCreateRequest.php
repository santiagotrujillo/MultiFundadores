<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EgresoCreateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'valor'  => 'required|numeric',
            'descripcion'   => 'required',
            'tipo_deuda_id'   => 'required|numeric|exists:tipo_deudas,id',
        ];
    }
}