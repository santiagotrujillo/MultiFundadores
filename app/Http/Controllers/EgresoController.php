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
        $query = "select deudas.id, deudas.descripcion, deudas.valor,
                  deudas.created_at as fecha, tp.concepto
                  from deudas
                  join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                  where ( date(deudas.created_at)
                  between '$date1' and '$date2')
                  order by deudas.tipo_deuda_id , deudas.created_at asc;";
        return $this->executeQuery($query);
    }

    /**
     * @param $date1
     * @param $date2
     */
    public function getDeudasBetweenDatesExcel($date1, $date2)
    {
        $valores = json_decode(json_encode($this->getDeudasBetweenDates($date1, $date2)),true);
        return Excel::create('Laravel Excel', function($excel) use($valores){
            $excel->sheet('Excel sheet', function($sheet) use($valores) {
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