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

Route::get('test', function ()
{
    $pago = \DB::select("select * from pagos where
year(fecha_inicial) = year('2015-10-10') and month(fecha_inicial) = month('2015-10-10') and tipo_pago_id = 4

");

    dd(count($pago));
});
