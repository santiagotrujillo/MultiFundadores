<?php

namespace App\Http\Controllers;

use App\Abono;
use App\Pago;
use App\Propietario;
use App\Propiedad;
use App\Tipo_pago;
use \Response, \Input, \Hash, \Auth;
use App\Http\Requests;
use App\Http\Requests\PropietarioRequestCreate;
use App\Http\Requests\PropietarioRequestUpdate;
use App\Http\Requests\CobroSalonRequest;
use App\Http\Requests\PropietarioLoginRequest;
use App\Http\Requests\PropietarioDeshacerAbonoRequest;
use App\Http\Requests\CobroParqueaderoRequest;
use App\Http\Requests\CobroOtrosRequest;

class PropietarioController extends Controller
{
    protected $data =[];

    protected $model;

    /**
     * valor del pago de administracion
     * @var int
     */
    protected $valorAdmin = 90000;

    /**
     * valor de la multa
     * @var int
     */
    protected $valorMulta = 2000;

    /**
     * valor del pago de mensualidad de la junta
     * @var int
     */
    protected $valorJunta = 67500;

    /**
     * valor del pago del cobro del seguro
     * @var int
     */
    protected $cobroSeguro = 75000;

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

        if( $data['valor_abono'] <= ($pago->valor -$pago->valor_pagado) ) {
                $pago->valor_pagado = $pago->valor_pagado + $data['valor_abono'];
                $pago->update();
                $abono = Abono::create(['valor' => $data['valor_abono'], 'pago_id' => $pago->id, 'forma_pago' => $data['forma_pago']]);
                return Response::json(['status' => 'true', 'abono' => $abono, 'factura' => $pago ], 200);
        }
        else if( $pago->valor_pagado == $pago->valor ) {
            return Response::json(['status' => false, 'message' => 'Usted ya pago la totalidad de la deuda.'],400);
        }
        else {
            return Response::json(['status' => false, 'message' => 'Lo sentimos el pago sobrepasa el valor de la deuda'],400);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function mostrarAbono($id)
    {
        $abono = (new Abono)->find($id);
        $pago = (new Pago)->find($abono->pago_id);
        $tipo_pago = (new Tipo_pago)->find($pago->tipo_pago_id);
        $propiedad = (new Propiedad)->find($pago->propiedad_id);
        $propietario = (new Propietario)->find($propiedad->propietario_id);
        return Response::json(['abono' => $abono, 'factura' => $pago, 'propiedad' => $propiedad,'tipo' => $tipo_pago, 'propietario' => $propietario]);
    }

    /**
     * @param CobroSalonRequest $request
     * @return mixed
     */
    public function cobroSalon(CobroSalonRequest $request)
    {
        $data = \Input::all();
        $cobros_anteriores = (new Pago)->where('fecha_inicial',$data["fecha"])->where('tipo_pago_id',3)->get();
        if(count($cobros_anteriores) > 0)
        {
            return Response::json(['message' => 'Ya se encuentra reservado para esta fecha'],400);
        }
        $cobro = ['tipo_pago_id' => 3, 'valor' => $data["valor"], 'descripcion' => $data['descripcion'] ,
            'fecha_inicial' => $data["fecha"], 'fecha_final' => $data["fecha"], 'propiedad_id' => 1234567890, 'valor_pagado' => 0];
        $pago = (new Pago);
        $pago->fill($cobro);
        $pago->save();
        return Response::json(['pago' => $pago->toArray()]);
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

        $propiedades = (new Propiedad)->all();
        foreach($propiedades as $propiedad)
        {
            $cobro = [
                'valor' => $this->valorAdmin,
                'descripcion' => 'Pago a realizar del mes de : '. $mes_actual .' de '. $year_actual,
                'fecha_inicial' =>  $this->first_month_day(),
                'fecha_final' =>  $this->last_month_day(),
                'tipo_pago_id' =>  1
            ];
            // se cobra una parte de admin a estas propiedades
            if($propiedad->id == 3201 || $propiedad->id == 5301 || $propiedad->id == 6302)
            {
                $cobro['valor'] = $this->valorJunta;
            }
            if(     $propiedad->id != 1201 && (
                    $propiedad->id != 1234567890 &&
                    $propiedad->id != 1234567891 ))
            {
                $pago = (new Pago);
                $pago->fill($cobro);
                $pago->propiedad_id = $propiedad->id;
                $pago->save();
            }
        }
            return Response::json(['status => true'],200);
        }
        return Response::json(['message'=>'El mes actual ya tiene facturas generadas de administración'],406);
    }

    public function cobroMulta()
    {
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
        if(!$this->validateDateMulta($nuevafecha))
        {
            $cobro = [
                'valor' => $this->valorMulta,
                'descripcion' => "Multa por pago atrazado de admin : $nuevafecha",
                'fecha_inicial' =>  $nuevafecha,
                'fecha_final' =>  $this->last_month_day(),
                'tipo_pago_id' =>  4
            ];

            $pagosAtrazados = (new Pago)->whereRaw("valor <> valor_pagado and year(fecha_inicial) = year('$nuevafecha')
                            and month(fecha_inicial) = month('$nuevafecha') and tipo_pago_id = 1")->get();
            foreach($pagosAtrazados as $pagoAtrazado)
            {
                $pago = (new Pago);
                $pago->fill($cobro);
                $pago->propiedad_id = $pagoAtrazado->propiedad_id;
                $pago->save();
            }
            return Response::json(['status => true'],200);
        }
        return Response::json(['message'=>'El mes anterior ya se le hizo el cobro de multas'],406);
    }

    /**
     * @return mixed
     */
    public function cobroSeguro()
    {
        $year_actual = \DB::select('select year(NOW()) as year');
        $year_actual = $year_actual[0]->year;
        if(!$this->validateYearSeguro())
        {
            $cobro = [
                'valor' => $this->cobroSeguro,
                'descripcion' => 'Pago a realizar del seguro en el año : '. $year_actual,
                'fecha_inicial' =>  "$year_actual-01-01",
                'fecha_final' =>  "$year_actual-12-31",
                'tipo_pago_id' => 2
            ];
            $propiedades = (new Propiedad)->all();
            foreach($propiedades as $propiedad)
            {
                if($propiedad->id != 1234567890 && $propiedad->id != 1234567891)
                {
                    $pago = (new Pago);
                    $pago->fill($cobro);
                    $pago->propiedad_id = $propiedad->id;
                    $pago->save();
                }
            }
            return Response::json(['status => true'],200);
        }
        return Response::json(['message'=>'Ya se tienen facturas generadas de seguro'],406);
    }

    /**
     * @param CobroParqueaderoRequest $request
     * @return mixed
     */
    public function cobroParqueadero(CobroParqueaderoRequest $request)
    {
        $cobro = [
            'valor' => $this->data["valor"],
            'descripcion' => $this->data["descripcion"],
            'fecha_inicial' =>  $this->data["fecha_inicial"],
            'fecha_final' =>  $this->data["fecha_final"],
            'tipo_pago_id' => 5
        ];

        $pago = (new Pago);
        $pago->fill($cobro);
        $pago->propiedad_id = 1234567891;
        $pago->save();
        return Response::json(['status => true'],200);
    }

    /**
     * @param CobroOtrosRequest $request
     * @return mixed
     */
    public function cobroOtros(CobroOtrosRequest $request)
    {
        $cobro = [
            'valor' => $this->data["valor"],
            'descripcion' => $this->data["descripcion"],
            'fecha_inicial' =>  $this->data["fecha_inicial"],
            'fecha_final' =>  $this->data["fecha_final"],
            'propiedad_id' =>  $this->data["propiedad_id"],
            'tipo_pago_id' => 6
        ];
        $pago = (new Pago);
        $pago->fill($cobro);
        $pago->save();
        return Response::json(['status => true'],200);
    }

    /**
     * @param CobroOtrosRequest $request
     * @return mixed
     */
    public function cuentaCobro(CobroOtrosRequest $request)
    {
        $cobro = [
            'valor' => $this->data["valor"],
            'descripcion' => $this->data["descripcion"],
            'fecha_inicial' =>  $this->data["fecha_inicial"],
            'fecha_final' =>  $this->data["fecha_final"],
            'propiedad_id' =>  $this->data["propiedad_id"],
            'tipo_pago_id' => 1
        ];
        $pago = (new Pago);
        $pago->fill($cobro);
        $pago->save();
        return Response::json(['status => true'],200);
    }

    /**
     * @return mixed
     */
    public function pagosRealizados()
    {
        return (new Pago)->whereRaw('valor = valor_pagado')->with(['tipo_pago'])->get();
    }

    /**
     * @return bool
     */
    private function validateDate()
    {
        $pago = (new Pago)->whereRaw('date(created_at) = date(now()) and tipo_pago_id = 1')->first();
        if( count($pago) > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * @param $fecha
     * @return bool
     */
    private function validateDateMulta($fecha)
    {
        $pago = \DB::select("select * from pagos where year(fecha_inicial) = year('$fecha')
                            and month(fecha_inicial) = month('$fecha') and tipo_pago_id = 4");
        if( count($pago) > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function validateYearSeguro()
    {
        $pago = (new Pago)->whereRaw('year(created_at) = year(now()) and tipo_pago_id = 2')->first();
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
        $abono->forceDelete();
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

    /**
     * @param int $id
     * @return mixed
     */
    public function pazysalvo($id)
    {
        $query = \DB::select("select pagos.* from
                            propiedades, pagos
                            where propiedades.id = pagos.propiedad_id
                            and valor_pagado < valor
                            and propiedad_id = $id;");
        if( count($query) > 0)
        {
            return Response::json(['pazysalvo' => false,'deudas' => $query],400);
        }
        return Response::json(['pazysalvo' => true]);
    }

    /**
     * @return mixed
     */
    public function eventsCurrentMonth()
    {
        $query = "select * from pagos where tipo_pago_id = 3 and month(fecha_inicial) = month(current_date) and
                  year(fecha_inicial) = year(current_date);";

        return \DB::select($query);
    }
}