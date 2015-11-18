<?php

namespace App\Http\Controllers;

use App\Pago;
use App\Propietario;
use App\Propiedad;
use \Response, \Input, \Hash;
use App\Http\Requests\PropietarioLoginRequest;

use App\Http\Requests;
use App\Http\Requests\PropietarioRequest\PropietarioRequestCreate;
use App\Http\Requests\PropietarioRequestUpdate;

class PropietarioController extends Controller
{
    protected $data =[];

    protected $model;

    public function __construct(Propietario $model)
    {
        $this->data = Input::all();
        $this->model = $model;
    }

    public function login(PropietarioLoginRequest $request)
    {
        $propietario = $this->model->where('id',$this->data['id'])->first();
        if( Hash::check($this->data['clave'], $propietario->clave) )
        {
            return Response::json($propietario);
        }
        return Response::json(['clave' => 'clave incorrecta']);
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

    public function viewLogin()
    {
        return view('users.propietariologin');
    }

    public function viewHome()
    {
        return view('users.homePropietario');
    }

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

    public function listar()
    {
        return $this->model->all();
    }

    public function borrar($id)
    {
        $propietario = (new Propietario)->find($id);
        $propietario->delete();
        return $propietario;
    }

    public function cobroAdminPendientes()
    {
        return (new Pago)->where('fecha_pago', null)->orderby('fecha_inicial', 'asc')->get();
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
    public function cobroAdmin()
    {
        $mes_actual = \DB::select('select month(NOW()) as mes');
        $mes_actual = $mes_actual[0]->mes;

        $year_actual = \DB::select('select year(NOW()) as year');
        $year_actual = $year_actual[0]->year;

        $cobro = [
            'valor' => 50000,
            'descripcion' => 'Pago a realizar del mes de : '. $mes_actual .' de '. $year_actual,
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
        return Response::json(['status => true']);
    }
}