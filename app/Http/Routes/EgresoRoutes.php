<?php

/**
 * Egresos segun rango de fechas y concepto
 */
Route::get('egresos/concept/{date1}/{date2}/{concept}', 'EgresoController@getDeudasBetweenDatesByConcept');

/**
 * Descarga en excel de egresos segun rango de fechas y concepto
 */
Route::get('egresos/concept/{date1}/{date2}/{concept}/excel', 'EgresoController@getDeudasBetweenDatesByConceptExcel');

/**
 * Egresos segun rango de fechas
 */
Route::get('egresos/between/{date1}/{date2}', 'EgresoController@getDeudasBetweenDates');


/**
 * Descarga de archivo en excel de los egresos agrupados por concepto segun fechas
 */
Route::get('egresos/between/{date1}/{date2}/excel', 'EgresoController@getDeudasBetweenDatesExcel');


Route::get('egresos/month/year/{month}/{year}', 'EgresoController@getEgresosMonthYear');