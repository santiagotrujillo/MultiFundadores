<?php


namespace App\Http\Controllers;

use App\Deuda;
use \Input, \DB;
use Maatwebsite\Excel\Facades\Excel as Excel;

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
        $query = "select
                  tp.concepto, sum(deudas.valor) as valor
                  from deudas
                  join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                  where ( date(deudas.created_at)
                  between '$date1' and '$date2')
                  group by deudas.tipo_deuda_id
                  order by tp.concepto asc";
        return $this->executeQuery($query);
    }

    /**
     * @param $date1
     * @param $date2
     * @return mixed
     */
    private function getTotalDeudasBetweenDates($date1, $date2)
    {
        $query = "(select
                  tp.concepto, sum(deudas.valor) as valor
                  from deudas
                  join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                  where ( date(deudas.created_at)
                  between '$date1' and '$date2')
                  group by deudas.tipo_deuda_id
                  order by tp.concepto asc)
                  union

                  (select
                  'total' as concepto, sum(deudas.valor) as valor
                  from deudas
                  join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                  where ( date(deudas.created_at)
                  between '$date1' and '$date2')
                  )";
        return $this->executeQuery($query);
    }

    /**
     * @param $date1
     * @param $date2
     */
    public function getDeudasBetweenDatesExcel($date1, $date2)
    {
        $valores = json_decode(json_encode($this->getTotalDeudasBetweenDates($date1, $date2)),true);
        return Excel::create('Laravel Excel', function($excel) use($valores, $date1, $date2)
        {
            $excel->sheet('Excel sheet', function($sheet) use($valores, $date1, $date2) {
                $sheet->fromArray([['fecha inicial'=> $date1, 'fecha_final'=> $date2 ]]);
                $sheet->fromArray($valores);
            });
        })->export('xlsx');
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