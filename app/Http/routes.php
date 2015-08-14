<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Rutas para usuarios

// Ruta para obtner la info de un usuario
Route::get('/usuarios/{id}','UsuarioController@show');

// ruta para login
Route::post('/usuarios/login', 'UsuarioController@login');

Route::get('test', function ()
{
   return Hash::make('12345');
});