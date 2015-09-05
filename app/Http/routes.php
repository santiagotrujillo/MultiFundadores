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
Route::get('/usuarios/show/{id}','UsuarioController@show');

// ruta para login
Route::post('/usuarios/login', 'UsuarioController@login');

Route::get('/usuarios/login', 'UsuarioController@viewLogin');

Route::get('/usuarios/home', [
    'middleware' =>'auth',
    'uses'=>'UsuarioController@viewHome'
]);
Route::get('/usuarios/salir', [
    'middleware' =>'auth',
    'uses'=>'UsuarioController@salir'
]);



//Rutas para Propietarios


// Ruta para obtner la info de un propietario
Route::get('/propietarios/show/{id}','PropietarioController@show');

// ruta para login propietario
Route::get('/propietarios/login', 'PropietarioController@viewlogin');

Route::get('/propietarios/home', 'PropietarioController@viewHome');


//Ruta para consultar pagos
Route::get('/propietarios/{id}/pagos','PropietarioController@show');

Route::get('test', function ()
{
   return Hash::make('ramirez123');
});