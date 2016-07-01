<?php

namespace App\Http\Controllers;

use App\Tipo_deuda;
use App\Deuda;
use App\Tipo_pago;
use App\Usuario;
use Illuminate\Http\Request;
use \Response, \Input, \Hash, \Auth, \DB;
use App\Http\Requests\UsuarioLoginRequest;
use App\Http\Requests\EgresoCreateRequest;
use Maatwebsite\Excel\Facades\Excel as Excel;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    /**
     * @var array
     */
    protected $data =[];

    /**
     * UsuarioController constructor.
     */
    public function __construct()
    {
        $this->data = Input::all();
    }

    /**
     * @param UsuarioLoginRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(UsuarioLoginRequest $request)
    {
        $usuario = (new Usuario)->where('id', $this->data['id'])->first();
        if( Hash::check($this->data['clave'], $usuario->clave) )
        {
            Auth::user()->login($usuario);
            if($this->validarRol($usuario))
            {
                return redirect('/usuarios/home');
            }
            else
            {
                return redirect('/usuarios/junta/home');
            }
        }
        return view('users.login')->withErrors(['clave' => 'clave incorrecta']);
    }

    /**
     * @param Usuario $usuario
     * @return bool
     */
    private function validarRol( Usuario $usuario)
    {
        if($usuario->rol_id == 1 || $usuario->rol_id ==3)
        {
            return true;
        }
        return false;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function viewHome()
    {
        $user = Auth::user()->get();
        if($user!= null && ($user-> rol_id == 1 || $user->rol_id == 3))
        {
            return view('users.home');
        }
        else if($user!= null && $user-> rol_id == 2)
        {
            return redirect('/usuarios/junta/home');
        }
        return redirect('/usuarios/login');
    }

    public function salir()
    {
        if(Auth::user()->get()!= null)
        {
            Auth::user()->logout();
            return redirect('/usuarios/login');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return Response::json((new Usuario)->with(['rol'])->findOrFail($id));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function viewLogin()
    {
        $user = Auth::user()->get();
        if($user!= null && ($user-> rol_id == 1 || $user->rol_id == 3))
        {
            return redirect('/usuarios/home');
        }
        else if($user!= null && $user-> rol_id == 2)
        {
            return redirect('/usuarios/junta/home');
        }
        return view('users.login');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function viewJuntaHome()
    {
        $user = Auth::user()->get();
        if($user!= null && $user-> rol_id ==2)
        {
            return view('junta.home');
        }
        else if($user!= null && ($user-> rol_id == 1 || $user->rol_id == 3))
        {
            return redirect('/usuarios/home');
        }
        return redirect('/usuarios/login');
    }

    /**
     * @param date $fecha_inicial
     * @param date $fecha_final
     * @return mixed
     */
    public function obtenerIngresosTotales($fecha_inicial, $fecha_final)
    {
        $query = "select sum(pagos.valor_pagado) as ingresos,tipo_pagos.id, tipo_pagos.concepto, month(pagos.created_at) as month, year(pagos.created_at) as year, concat('prefix',month(pagos.created_at),year(pagos.created_at)) as prefix
                  from pagos, tipo_pagos
                  where date(pagos.created_at) between '$fecha_inicial' and '$fecha_final'
                  and tipo_pagos.id = pagos.tipo_pago_id
                  group by pagos.tipo_pago_id, month(pagos.created_at), year(pagos.created_at)
                  order by month desc, year desc";
        return DB::select($query,[]);
    }

    /**
     * @param $year
     * @param $month
     * @param $concept
     * @return mixed
     */
    public function obtenerIngresosTotalesDetail($year, $month , $concept)
    {
        $query = "select pagos.id as codigo_cobro, pagos.valor_pagado as ingresos, tipo_pagos.concepto, pagos.created_at as fecha , pagos.propiedad_id
                  from pagos, tipo_pagos
                  where year(pagos.created_at) = $year
                  and month(pagos.created_at) = $month
                  and tipo_pagos.id = pagos.tipo_pago_id
                  and valor_pagado >0
                  and tipo_pagos.id = $concept";
        return DB::select($query,[]);
    }

    /**
     * @param $year
     * @param $month
     * @param $concept
     * @param $forma_pago
     * @return mixed
     */
    public function obtenerIngresosTotalesDetailByMethodPayment($year, $month , $concept, $forma_pago)
    {
        $query = "select pagos.id as codigo_cobro, sum(abonos.valor) as ingresos, tipo_pagos.concepto, pagos.created_at as fecha , pagos.propiedad_id
                    from pagos, tipo_pagos, abonos
                    where year(pagos.created_at) = $year
                    and month(pagos.created_at) = $month
                    and tipo_pagos.id = pagos.tipo_pago_id
                    and valor_pagado > 0
                    and abonos.pago_id =  pagos.id
                    and abonos.forma_pago like '$forma_pago'
                    and tipo_pagos.id = $concept
                    group by pagos.id;";
        return DB::select($query,[]);
    }

    /**
     * @param $year
     * @param $month
     * @param $concept
     * @param $forma_pago
     * @return mixed
     */
    public function obtenerIngresosTotalesDetailByMethodPaymentExcel($year, $month , $concept, $forma_pago)
    {
        $valores = json_decode(json_encode($this->obtenerIngresosTotalesDetailByMethodPayment($year, $month , $concept, $forma_pago)), true);

        return Excel::create("Ingresos en $forma_pago por concepto del $year del mes $month", function($excel) use($valores, $year, $month, $forma_pago)
        {
            $excel->sheet('Hoja 1', function($sheet) use($valores, $year, $month, $forma_pago) {
                $sheet->fromArray([['PERIODO'=> "$year - $month", 'FORMA DE PAGO' => $forma_pago ]]);
                $sheet->fromArray($valores);
            });
        })->export('xlsx');
    }

    /**
     * @param $year
     * @param $month
     * @param $concept
     * @return mixed
     */
    public function obtenerIngresosTotalesDetailExcel($year, $month , $concept)
    {
        $valores = json_decode(json_encode($this->obtenerIngresosTotalesDetail($year, $month , $concept)), true);

        return Excel::create("Ingresos por concepto del $year del mes $month", function($excel) use($valores, $year, $month)
        {
            $excel->sheet('Hoja 1', function($sheet) use($valores, $year, $month) {
                $sheet->fromArray([['PERIODO'=> "$year - $month"]]);
                $sheet->fromArray($valores);
            });
        })->export('xlsx');
    }

    /**
     * @param $fecha_inicial
     * @param $fecha_final
     * @return mixed
     */
    public function obtenerIngresosEfectivoTotales($fecha_inicial, $fecha_final)
    {
        $query =    "select sum(abonos.valor) as ingresos,tipo_pagos.id, tipo_pagos.concepto, month(pagos.created_at) as month, year(pagos.created_at) as year, concat('prefix',month(pagos.created_at),year(pagos.created_at)) as prefix
                    from pagos, tipo_pagos, abonos
                    where date(pagos.created_at) between '$fecha_inicial' and '$fecha_final'
                    and tipo_pagos.id = pagos.tipo_pago_id
                    and pagos.id = abonos.pago_id
                    and abonos.forma_pago like 'EFECTIVO'
                    group by pagos.tipo_pago_id, month(pagos.created_at), year(pagos.created_at)";
        return DB::select($query,[]);
    }

    /**
     * @param $fecha_inicial
     * @param $fecha_final
     * @return mixed
     */
    public function obtenerIngresosConsignacionesTotales($fecha_inicial, $fecha_final)
    {
        $query =    "select sum(abonos.valor) as ingresos,tipo_pagos.id, tipo_pagos.concepto, month(pagos.created_at) as month, year(pagos.created_at) as year, concat('prefix',month(pagos.created_at),year(pagos.created_at)) as prefix
                    from pagos, tipo_pagos, abonos
                    where date(pagos.created_at) between '$fecha_inicial' and '$fecha_final'
                    and tipo_pagos.id = pagos.tipo_pago_id
                    and pagos.id = abonos.pago_id
                    and abonos.forma_pago like 'CONSIGNACION'
                    group by pagos.tipo_pago_id, month(pagos.created_at), year(pagos.created_at)";
        return DB::select($query,[]);
    }

    /**
     * @return array
     */
    public function ingresosBlogues()
    {
        $query = "SELECT pagos.*, month(pagos.created_at) as month, year(pagos.created_at) as year,
                  substr(CAST(propiedad_id AS CHAR),1,1) as bloque
                  from pagos where tipo_pago_id in (1,2)
                  and year(pagos.created_at) = year(now())";
        return DB::select($query,[]);
    }

    /**
     * @return array
     */
    public function otrosIngresosBlogues()
    {
        $tipo_pago = Tipo_pago::OTROS;
        $query = "SELECT pagos.*, month(pagos.created_at) as month, year(pagos.created_at) as year,
                  substr(CAST(propiedad_id AS CHAR),1,1) as bloque
                  from pagos where tipo_pago_id = $tipo_pago
                  and year(pagos.created_at) = year(now())";
        return DB::select($query,[]);
    }

    /**
     * @return array
     */
    public function ingresosPorMultasDeIncumplimientoPorBlogues()
    {
        $tipo_pago = Tipo_pago::MULTA;
        $query = "SELECT pagos.*, month(pagos.created_at) as month, year(pagos.created_at) as year,
                  substr(CAST(propiedad_id AS CHAR),1,1) as bloque
                  from pagos where tipo_pago_id = $tipo_pago
                  and year(pagos.created_at) = year(now())";
        return DB::select($query,[]);
    }

    /**
     * @return array
     */
    public function ingresosDeSegurosPorBlogues()
    {
        $tipo_pago = Tipo_pago::SEGURO;
        $query = "SELECT pagos.*, month(pagos.created_at) as month, year(pagos.created_at) as year,
                  substr(CAST(propiedad_id AS CHAR),1,1) as bloque
                  from pagos where tipo_pago_id = $tipo_pago
                  and year(pagos.created_at) = year(now())";
        return DB::select($query,[]);
    }

    /**
     * @param $year
     * @return array
     */
    public function pygYear($year)
    {
        $query = " (select sum(a.valor) valor, month(a.created_at) as month, p.tipo_pago_id as tip, tp.concepto, 'ingreso' as pyg
                    from abonos as a 
                    left join pagos as p on p.id = a.pago_id
                    left join tipo_pagos tp on p.tipo_pago_id = tp.id
                    where year(a.created_at) = $year
                    group by month , tip
                    order by month, tip asc)
                    union
                    (select sum(d.valor) valor, month(d.created_at) as month, tp.id as tip, tp.concepto, 'egreso' as pyg
                    from deudas as d
                    left join tipo_deudas as tp on d.tipo_deuda_id = tp.id
                    where year(d.created_at) = $year
                    group by month, tip
                    order by month, tip asc);";
        return DB::select($query,[]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function tipoDeudas()
    {
        return (new Tipo_deuda)->all();
    }

    /**
     * @param EgresoCreateRequest $request
     * @return mixed
     */
    public function egreso(EgresoCreateRequest $request)
    {
        $data = \Input::all();
        $data["conjunto_id"] = 810004843;
        $deuda = Deuda::create($data);
        return Response::json($deuda);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function verEgreso($id)
    {
        return Response::json((new Deuda)->find($id));
    }

    /**
     * @param $month
     * @return $year
     */
    public function getIngresosMonthYear($month, $year)
    {
        $query =   "select tp.concepto, p.descripcion, abonos_agrupados.num_abonos, 
                    abonos_agrupados.valor, p.id, p.tipo_pago_id, p.propiedad_id
                    from pagos p,
                        (select sum(valor) as valor, pago_id, COUNT(*) num_abonos from abonos
                         where year(created_at) = $year and month(created_at) =$month
                         group by pago_id) as abonos_agrupados,
                         tipo_pagos as tp
                     where p.id = abonos_agrupados.pago_id
                     and p.tipo_pago_id = tp.id
                     order by id asc;";
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
