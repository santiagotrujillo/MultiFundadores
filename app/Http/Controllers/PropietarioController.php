<?php
namespace App\Http\Controllers;

use App\Propietario;
use \Response, \Input, \Hash;
use App\Http\Requests\PropietarioLoginRequest;

use App\Http\Requests;
use App\Http\Requests\PropietarioRequest\PropietarioRequestCreate;

class PropietarioController extends Controller
{
    protected $data =[];

    public function __construct()
    {
        $this->data = Input::all();
    }

    public function login(PropietarioLoginRequest $request)
    {
        $propietario = (new Propietario)->where('id',$this->data['id'])->first();
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
        return Response::json((new Propietario)->findOrFail($id));
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

    public function create(PropietarioRequestCreate $request)
    {
        Propietario::create($request->all());
        return \Redirect::back()->with('propietario.create', 'Propietario Registrado!');
    }
}
