<?php

Route::get('/propietarios/show/{id}','PropietarioController@show');

// ruta para login propietario
Route::get('/propietarios/login', 'PropietarioController@viewlogin');

Route::get('/propietarios/home', 'PropietarioController@viewHome');

//Ruta para consultar pagos
Route::get('/propietarios/{id}/pagos','PropietarioController@show');

/**
 * Ruta para traer la vista de crear un propietario
 */
Route::get('/propietarios/create','PropietarioController@viewCreate');

Route::post('/propietarios/create','PropietarioController@create');

Route::get('/propietarios/listar','PropietarioController@listar');
