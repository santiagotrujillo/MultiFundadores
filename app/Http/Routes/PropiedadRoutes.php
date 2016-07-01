<?php

/**
 * Ruta para mostrar la informacion de los facturas de la propiedad
 */
Route::get('/propiedad/ver/{id}','PropiedadController@ver');

/**
 * Ruta para mostrar la informacion de los facturas de la propiedad
 */
Route::get('/propiedad/with_owner/{id}','PropiedadController@withOwner');
