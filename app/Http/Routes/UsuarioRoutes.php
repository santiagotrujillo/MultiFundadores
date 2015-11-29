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