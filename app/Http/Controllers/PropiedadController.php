<?php

namespace App\Http\Controllers;

use \Input;
use App\Propiedad;

class PropiedadController extends Controller
{
    protected $data =[];

    protected $model;

    /**
     * @param Propiedad $model
     */
    public function __construct(Propiedad $model)
    {
        $this->data = Input::all();
        $this->model = $model;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function ver($id)
    {
        return (new Propiedad)->with(['pagos','pagos.tipo_pago'])->find($id);
    }
}