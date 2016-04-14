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
     * @param $concepto
     * @return mixed
     */
    public function getDeudasBetweenDatesByConcept($date1, $date2, $concepto)
    {
        $query = "select deudas.id, deudas.descripcion, deudas.valor,
                  deudas.created_at as fecha, tp.concepto
                  from deudas
                  join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                  where ( date(deudas.created_at)
                  between '$date1' and '$date2')
                  and tp.concepto like '$concepto'
                  order by deudas.created_at asc;";
        return $this->executeQuery($query);
    }

    /**
     * @param $date1
     * @param $date2
     * @param $concepto
     * @return mixed
     */
    public function getDeudasBetweenDatesByConceptExcelQuery($date1, $date2, $concepto)
    {
        $query = "(select deudas.id, deudas.descripcion, tp.concepto,
                  deudas.created_at as fecha, deudas.valor
                  from deudas
                  join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                  where ( date(deudas.created_at)
                  between '$date1' and '$date2')
                  and tp.concepto like '$concepto'
                  order by deudas.created_at asc)
                  union
                  ( select '' as id, ''as descripcion, '' as valor,
                    'TOTAL' as fecha, sum(deudas.valor) as concepto
                    from deudas
                    join tipo_deudas tp on tp.id = deudas.tipo_deuda_id
                    where ( date(deudas.created_at)
                    between '$date1' and '$date2')
                    and tp.concepto like '$concepto'
                  )";
        return $this->executeQuery($query);
    }

    /**
     * @param $date1
     * @param $date2
     * @param $concept
     * @return mixed
     */
    public function getDeudasBetweenDatesByConceptExcel($date1, $date2, $concept)
    {
        $valores = json_decode(json_encode($this->getDeudasBetweenDatesByConceptExcelQuery($date1, $date2, $concept)),true);
        return Excel::create("Deudas por concepto de $concept de $date1 hasta $date2", function($excel) use($valores, $date1, $date2)
        {
            $excel->sheet('Hoja 1', function($sheet) use($valores, $date1, $date2) {
                $sheet->fromArray([['fecha inicial'=> $date1, 'fecha_final'=> $date2 ]]);
                $sheet->fromArray($valores);
            });
        })->export('xlsx');
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
        return Excel::create("Deudas Generales de $date1 hasta $date2", function($excel) use($valores, $date1, $date2)
        {
            $excel->sheet('Hoja 1', function($sheet) use($valores, $date1, $date2) {
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

    public function getEgresosMonthYear($month, $year)
    {
      $query = "select ds.*, td.concepto from deudas ds join tipo_deudas td on td.id = ds.tipo_deuda_id 
      where year(ds.created_at) = $year and month(ds.created_at) = $month";
      return $this->executeQuery($query);
    }
}