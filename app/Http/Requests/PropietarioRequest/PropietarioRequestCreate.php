<?php

namespace App\Http\Requests\PropietarioRequest;

use App\Http\Requests\Request;

class PropietarioRequestCreate extends Request
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
            'id'        => 'required|numeric|unique:propietarios,id',
            'nombre'    => 'required',
            'apellido'  => 'required',
            'telefono'  => 'numeric',
            'clave'     => 'required',
        ];
    }

    public function response(array $errors)
    {
        return redirect(null,400)->back()->withErrors($errors);
    }
}