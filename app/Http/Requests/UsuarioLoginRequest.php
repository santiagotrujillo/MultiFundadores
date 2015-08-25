<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UsuarioLoginRequest extends Request
{
    public $autenticable = true;

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
            'id' => 'required|numeric|exists:usuarios,id',
            'clave' => 'required'
        ];
    }

    public function response(array $errors)
    {
        if($this->autenticable)
        {
            return redirect(null,400)->back()->withErrors($errors);
        }
        return \Response::json($errors, 400);
    }
}
