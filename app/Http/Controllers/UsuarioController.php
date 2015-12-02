<?php

namespace App\Http\Controllers;

use App\Tipo_deuda;
use App\Deuda;
use App\Usuario;
use Illuminate\Http\Request;
use \Response, \Input, \Hash, \Auth, \DB;
use App\Http\Requests\UsuarioLoginRequest;
use App\Http\Requests\EgresoCreateRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    protected $data =[];

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
                  group by pagos.tipo_pago_id, month(pagos.created_at), year(pagos.created_at)";
        return DB::select($query,[]);
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
}
