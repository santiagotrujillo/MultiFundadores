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

require __DIR__.'/Routes/BaseRoutes.php';



Route::get('/salon/comunal', function () {
    $reservas_salon = (new \App\Pago)->whereRaw('month(fecha_inicial) = month(current_date)')->where('tipo_pago_id',3)->get();
    return view('salon.index')->with(['reservas'=>$reservas_salon]);
});
