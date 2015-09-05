<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use \Response, \Input, \Hash, \Auth;
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
            Auth::login($usuario);
            return redirect('/usuarios/home');
        }
        return view('users.login')->withErrors(['clave' => 'clave incorrecta']);
    }

    public function viewHome()
    {
        return view('users.home');
    }

    public function salir()
    {
        if(Auth::user()!= null)
        {
            Auth::logout();
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

    public function viewLogin()
    {
        if(Auth::user()!= null)
        {
            return redirect('/usuarios/home');
        }
        return view('users.login');
    }

}
