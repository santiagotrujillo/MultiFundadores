<?php

/**
 * Ruta para mostrar la informacion de un propietario por medio del id
 */
Route::get('/propietarios/show/{id}','PropietarioController@show');

/**
 * Ruta para crear un propietario (web service)
 */
Route::post('/propietarios/create','PropietarioController@create');

/**
 * Ruta para actualizar un propietario (web service)
 */
Route::post('/propietarios/update','PropietarioController@update');

/**
 * Ruta para listar los propietarios
 */
Route::get('/propietarios/listar','PropietarioController@listar');

/**
 * Ruta para borrar un propietario
 */
Route::get('/propietarios/borrar/{id}','PropietarioController@borrar');

/**
 * Ruta paraa cargar los propietarios pendientes de pago
 */
Route::get('/propietarios/cobro/admin/pendientes','PropietarioController@cobroAdminPendientes');

/**
 * Ruta paraa cargar los propietarios pendientes de pago
 */
Route::post('/propietarios/cobro/admin','PropietarioController@cobroAdmin');

// @----------------------@

// ruta para login propietario
Route::get('/propietarios/login', 'PropietarioController@viewlogin');

Route::get('/propietarios/home', 'PropietarioController@viewHome');

//Ruta para consultar pagos
Route::get('/propietarios/{id}/pagos','PropietarioController@show');

/**
 * @Ruta para traer la vista de crear un propietario
 */
Route::get('/propietarios/create','PropietarioController@viewCreate');



