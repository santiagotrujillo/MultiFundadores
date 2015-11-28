<?php

// Ruta para obtner la info de un usuario
Route::get('/usuarios/show/{id}','UsuarioController@show');

// ruta para login
Route::post('/usuarios/login', 'UsuarioController@login');

Route::get('/usuarios/login', 'UsuarioController@viewLogin');

Route::get('/usuarios/home', [
    'uses'=>'UsuarioController@viewHome'
]);
Route::get('/usuarios/salir', [
    'uses'=>'UsuarioController@salir'
]);