<?php


namespace App\Http\Requests;


class CobroOtrosRequest extends Request
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
        'valor'         => 'required|numeric',
        'descripcion'   => 'required',
        'fecha_inicial' => 'required|date',
        'fecha_final'   => 'required|date',
        'propiedad_id'   => 'required|numeric|exists:propiedades,id',
    ];
}
}