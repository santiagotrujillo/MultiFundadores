<?php

namespace App\Http\Controllers;

use App\Abono;
use App\Pago;
use App\Propietario;
use App\Propiedad;
use \Response, \Input, \Hash, \Auth;
use App\Http\Requests;
use App\Http\Requests\PropietarioRequestCreate;
use App\Http\Requests\PropietarioRequestUpdate;
use App\Http\Requests\PropietarioLoginRequest;
use App\Http\Requests\PropietarioDeshacerAbonoRequest;

class PropietarioController extends Controller
{
    protected $data =[];

    protected $model;

    /**
     * @param Propietario $model
     */
    public function __construct(Propietario $model)
    {
        $this->data = Input::all();
        $this->model = $model;
    }

    /**
     * @param PropietarioLoginRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(PropietarioLoginRequest $request)
    {
        $propietario = $this->model->where('id',$this->data['id'])->first();
        if( Hash::check($this->data['clave'], $propietario->clave) )
        {
            Auth::owner()->login($propietario);
            return redirect('/propietarios/home');
        }
        return view('users.propietariologin')->withErrors(['clave' => 'clave incorrecta']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return Response::json($this->model->findOrFail($id));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function viewLogin()
    {
        if(Auth::owner()->get()!= null)
        {
            return redirect('/propietarios/home');
        }
        return view('users.propietariologin');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function viewHome()
    {
        if(Auth::owner()->get()!= null)
        {
            return view('users.homePropietario');
        }
        return redirect('/propietarios/login');
    }

    /*
     * @return \Illuminate\Contracts\View\Factory
     */
    public function viewCreate()
    {
        return view('propietarios.create');
    }

    /**
     * @param PropietarioRequestCreate $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(PropietarioRequestCreate $request)
    {
        Propietario::create($request->all());
        return \Redirect::back()->with('propietario.create', 'Propietario Registrado!');
    }

    /**
     * @param PropietarioRequestUpdate $request
     * @return mixed
     */
    public function update(PropietarioRequestUpdate $request)
    {
        $propietario = Propietario::find($this->data['id']);
        $propietario->fill($this->data);
        return $propietario->update();
    }

    /**
     * @return mixed
     */
    public function listar()
    {
        return $this->model->all();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function borrar($id)
    {
        $propietario = (new Propietario)->find($id);
        $propietario->delete();
        return $propietario;
    }

    /**
     * @return mixed
     */
    public function cobroAdminPendientes()
    {
        return (new Pago)->with(['tipo_pago'])
            ->whereRaw('valor_pagado < valor')->orWhere('valor_pagado',null)->orderby('fecha_inicial', 'asc')->get();
    }

    /**
     * @param Requests\PropietarioPagoRequest $request
     * @return mixed
     */
    public function abonar(Requests\PropietarioPagoRequest $request)
    {
        $data = \Input::all();
        $pago = (new Pago)->find($data["id"]);
        if($data['valor_abono'] <= ($pago->valor -$pago->valor_pagado))
        {
                $pago->valor_pagado = $pago->valor_pagado + $data['valor_abono'];
                $pago->update();
                $abono = Abono::create(['valor' => $data['valor_abono'], 'pago_id' => $pago->id, 'forma_pago' => $data['forma_pago']]);
                return Response::json(['status' => 'true', 'abono' => $abono, 'factura' => $pago ], 200);
        }
        else if($pago->valor_pagado == $pago->valor)
            {
                return Response::json(['status' => false, 'message' => 'Usted ya pago la totalidad de la deuda.'],400);
            }
        else
        {
            return Response::json(['status' => false, 'message' => 'Lo sentimos el pago sobrepasa el valor de la deuda'],400);
        }
    }


    /** Actual month last day **/
    private function last_month_day() {
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }

    /** Actual month first day **/
    private function first_month_day() {
        $month = date('m');
        $year = date('Y');
        return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }

    /**
     * @return mixed
     */
    public function cobroAdmin()
    {
        $mes_actual = \DB::select('select month(NOW()) as mes');
        $mes_actual = $mes_actual[0]->mes;

        $year_actual = \DB::select('select year(NOW()) as year');
        $year_actual = $year_actual[0]->year;

        if(!$this->validateDate()){
        $cobro = [
            'valor' => 90000,
            'descripción' => 'Pago a realizar del mes de : '. $mes_actual .' de '. $year_actual,
            'fecha_inicial' =>  $this->first_month_day(),
            'fecha_final' =>  $this->last_month_day(),
            'tipo_pago_id' =>  1
        ];

        $propiedades = (new Propiedad)->all();
        foreach($propiedades as $propiedad)
        {
            $pago = (new Pago);
            $pago->fill($cobro);
            $pago->propiedad_id = $propiedad->id;
            $pago->save();
        }
            return Response::json(['status => true'],200);
        }

        return Response::json(['message'=>'El mes actual ya tiene facturas generadas de administración'],406);
    }

    /**
     * @return mixed
     */
    public function pagosRealizados()
    {
        return (new Pago)->whereRaw('valor = valor_pagado')->with(['tipo_pago'])->get();
    }

    private function validateDate()
    {
        $pago = (new Pago)->whereRaw('date(created_at) = date(now())')->first();
        if( count($pago) > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function abonosPago($id)
    {
        return (new Abono)->where('pago_id',$id)->get();
    }

    /**
     * @param PropietarioDeshacerAbonoRequest $request
     * @return mixed
     */
    public function deshacerAbono(PropietarioDeshacerAbonoRequest $request)
    {
        $data = \Input::all();
        $abono = (new Abono)->find($data['abono_id']);
        $pago = (new Pago)->find($data['pago_id']);
        $pago->valor_pagado = $pago->valor_pagado - $abono->valor;
        $pago->update();
        $abono->delete();
        return Response::json($pago);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function propiedades($id)
    {
        return Response::json( (new Propiedad)->where('propietario_id',$id)->with(['tipo_propiedad'])->get() );
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        if(Auth::owner()->get()!= null)
        {
            Auth::owner()->logout();
            return redirect('/propietarios/login');
        }
        return redirect('/');
    }
}