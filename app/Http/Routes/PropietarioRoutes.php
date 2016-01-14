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
 * Ruta para realizar la cuenta de cobro a los propietarios en el mes actual por concepto de admin - mensualmente
 */
Route::post('/propietarios/cobro/admin','PropietarioController@cobroAdmin');

/**
 * Ruta para realizar la cuenta de cobro a los propietarios en el mes actual por concepto de multa - mensualmente
 */
Route::post('/propietarios/cobro/multa','PropietarioController@cobroMulta');

/**
 * Ruta para realizar la cuenta de cobro a los propietarios en el año actual por concepto de admin - anualmente
 */
Route::post('/propietarios/cobro/seguro','PropietarioController@cobroSeguro');

/**
 * Ruta para realizar la cuenta de cobro a los propietarios en el año actual por concepto de admin - anualmente
 */
Route::post('/propietarios/cobro/salon','PropietarioController@cobroSalon');

/**
 * Ruta para realizar la cuenta de cobro a los propietarios en el año actual por concepto de parqueadero
 */
Route::post('/propietarios/cobro/parqueadero','PropietarioController@cobroParqueadero');

/**
 * Ruta para realizar la cuenta de cobro a los propietarios en el año actual por concepto de otros
 */
Route::post('/propietarios/cobro/otros','PropietarioController@cobroOtros');

/**
 * Ruta para realizar la cuenta de cobro a los propietarios en el año actual por concepto de cuenta de cobro
 */
Route::post('/propietarios/cobro/cuentacobro','PropietarioController@cuentaCobro');

/**
 * Ruta para realizar el pago de una deuda
 */
Route::post('/propietarios/abonar','PropietarioController@abonar');

/**
 * Ruta para ver el detalle del pago de una deuda
 */
Route::get('/propietarios/abono/{id}','PropietarioController@mostrarAbono');

/**
 * @Ruta para traer la vista de crear un propietario
 */
Route::get('/propietarios/create','PropietarioController@viewCreate');

/**
 * @Ruta para traer los pagos  realizados
 */
Route::get('/propietarios/pagos/relizados','PropietarioController@pagosRealizados');

/**
 * @Ruta para traer los abonos cargados a un pago
 */
Route::get('/propietarios/abonos/pago/{id}','PropietarioController@abonosPago');

/**
 * @Ruta para deshacer el abono realizado a un pago
 */
Route::post('/propietarios/deshacer/abono','PropietarioController@deshacerAbono');

/**
 * @Ruta para cargar la vista de login de propietario
 */
Route::get('/propietarios/login', 'PropietarioController@viewlogin');

/**
 * @Ruta para cerrar la session del propietario
 */
Route::get('/propietarios/logout', 'PropietarioController@logout');

/**
 * @Ruta para cargar la lista de propiedades de un propietario
 */
Route::get('/propietarios/propiedades/{id}', 'PropietarioController@propiedades');

/**
 * @Ruta para cargar el paz y salvo de una propiedad
 */
Route::get('/propietarios/pazysalvo/{id}','PropietarioController@pazysalvo');

// @----------------------@

/**
 * @Ruta para procesar el login de propietarios
 */
Route::post('/propietarios/login', 'PropietarioController@login');

Route::get('/propietarios/home', 'PropietarioController@viewHome');

//Ruta para consultar pagos
Route::get('/propietarios/{id}/pagos','PropietarioController@show');



