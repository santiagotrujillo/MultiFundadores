<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use \Response, \Input, \Hash;
use App\Http\Requests\UsuarioLoginRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    protected $data =[];

    public function __construct()
    {
        $this->data = Input::all();
    }
    public function login(UsuarioLoginRequest $request)
    {
        $usuario = (new Usuario)->where('id',$this->data['id'])->first();
        if( Hash::check($this->data['clave'], $usuario->clave) )
        {
            return Response::json($usuario);
        }
        return view('users.login')->withErrors(['clave' => 'clave incorrecta']);
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

    public function viewLogin()
    {
        return view('users.login');
    }

}
