<?php

/**
 * Ruta para obtner la info de un usuario
 */
Route::get('/usuarios/show/{id}','UsuarioController@show');

/**
 * Ruta para procesar el login
 */
Route::post('/usuarios/login', 'UsuarioController@login');

/**
 * Ruta para ver la interfaz de login
 */
Route::get('/usuarios/login', 'UsuarioController@viewLogin');

/**
 * Ruta de los usuarios con rol tipo admin y soporte
 */
Route::get('/usuarios/home', [
    'uses'=>'UsuarioController@viewHome'
]);

/**
 * Ruta de los usuarios con rol tipo junta
 */
Route::get('/usuarios/junta/home', [
    'uses'=>'UsuarioController@viewJuntaHome'
]);

/**
 * Ruta para cerrar sesion sin importar el rol
 */
Route::get('/usuarios/salir', [
    'uses'=>'UsuarioController@salir'
]);

/**
 * @module reporte de ingresos totales por consignacion y efectivo
 * @activity 1
 * Ruta para obtener el reporte de ingresos totales
 */
Route::get('/usuarios/ingresos/totales/{fecha_inicial}/{fecha_final}', [
    'uses'=>'UsuarioController@obtenerIngresosTotales'
]);

/**
 * @module reporte de ingresos totales por consignacion y efectivo
 * @activity 1.1
 * Ruta para obtener el reporte de ingresos totales detallado por consignacion y efectivo segun año y mes y concepto
 */
Route::get('/usuarios/ingresos/detail-totales/{year}/{month}/{concept}', [
    'uses'=>'UsuarioController@obtenerIngresosTotalesDetail'
]);

/**
 * @module reporte de ingresos totales por consignacion y efectivo en excel
 * @activity 1.1.1
 * Ruta para obtener el reporte de ingresos totales detallado por consignacion y efectivo segun año y mes y concepto
 */
Route::get('/usuarios/ingresos/detail-totales-excel/{year}/{month}/{concept}', [
    'uses'=>'UsuarioController@obtenerIngresosTotalesDetailExcel'
]);

/**
 * @module Ruta para obtener el reporte de ingresos efectivos totales
 * @activity 2
 */
Route::get('/usuarios/ingresos/efectivos/totales/{fecha_inicial}/{fecha_final}', [
    'uses'=>'UsuarioController@obtenerIngresosEfectivoTotales'
]);

/**
 * @module Ruta para obtener el reporte de ingresos efectivos totales
 * @activity 2.1
 */
Route::get('/usuarios/ingresos/detail-efectivos/totales/{year}/{month}/{concept}/{forma_pago}', [
    'uses'=>'UsuarioController@obtenerIngresosTotalesDetailByMethodPayment'
]);

/**
 * Ruta para obtener el reporte de ingresos consignaciones totales
 */
Route::get('/usuarios/ingresos/consignaciones/totales/{fecha_inicial}/{fecha_final}', [
    'uses'=>'UsuarioController@obtenerIngresosConsignacionesTotales'
]);

/**
 * Ruta para obtener el reporte de ingresos por bloque
 */
Route::get('/usuarios/ingresos/bloques', [
    'uses'=>'UsuarioController@ingresosBlogues'
]);

/**
 * Ruta para obtener la lista de las dueudas
 */
Route::get('/usuarios/tipodeudas', [
    'uses'=>'UsuarioController@tipoDeudas'
]);

/**
 * Ruta para realizar el egreso
 */
Route::post('/usuarios/egreso', [
    'uses'=>'UsuarioController@egreso'
]);


/**
 * Ruta para realizar el egreso
 */
Route::get('/usuarios/egreso/{id}', [
    'uses'=>'UsuarioController@verEgreso'
]);