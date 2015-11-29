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

    private function validarRol( Usuario $usuario)
    {
        if($usuario->rol_id == 1 || $usuario->rol_id ==3)
        {
            return true;
        }
        return false;
    }

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

}
