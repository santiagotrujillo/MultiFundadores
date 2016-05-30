<?php

namespace App\Http\Requests;

class ChargeByOtherConceptsToAllPropertiesRequest extends Request
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
            'value'         => 'required|numeric',
            'description'   => 'required',
            'date1'         => 'required|date',
            'date2'         => 'required|date'
        ];
    }

}