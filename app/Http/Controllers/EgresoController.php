<?php


namespace App\Http\Controllers;

use App\Deuda;
use \Input, \DB;

class EgresoController extends Controller
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var Deuda
     */
    protected $model;

    public function __construct(Deuda $deuda)
    {
        $this->data = Input::all();
        $this->model = $deuda;
    }

    /**
     * @param $date1
     * @param $date2
     * @return mixed
     */
    public function getDeudasBetweenDates($date1, $date2)
    {
        $query = "select deudas.*, tp.concepto from deudas
                  join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                  where ( date(deudas.created_at)
                  between '$date1' and '$date2')
                  order by deudas.tipo_deuda_id , created_at asc;";
        return $this->executeQuery($query);
    }

    /**
     * @param $query
     * @return mixed
     */
    private function executeQuery($query)
    {
        return DB::select($query);
    }
}